<?php


class Bb_Api_Topics extends Bb_Api_Base
{
    public function topics(WP_REST_Request $request)
    {
        $requests = $request->get_params();
        $meta = []; $data = [];

        $link = get_site_url() . '/wp-json/tv/v1/topics';

        $perPage = !empty($requests['per_page']) ? (int)$requests['per_page'] : 15;
        if ($perPage > 50) {
            $perPage = 50;
        }
        $page = !empty($requests['page']) ? (int)$requests['page'] : 1;

        $meta['total_topics'] = 0;
        $meta['total_pages'] = 0;
        $meta['current_page'] = 0;
        $meta['per_page'] = 0;
        $meta['next_page'] = null;
        $meta['next_page_url'] = null;
        $meta['prev_page'] = null;
        $meta['prev_page_url'] = null;

        if (bbp_has_topics(['orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => $perPage, 'paged' => $page])) {
            $meta['current_page'] = $page;
            $meta['per_page'] = $perPage;
            $meta['total_topics'] = $this->getTotalTopics();
            $meta['total_pages'] = ceil($meta['total_topics'] / $perPage);

            if ( ( $perPage * $page ) < $meta['total_topics'] ) {
                $meta['next_page'] = $page + 1;
                $meta['next_page_url'] = $link . '?page=' . $meta['next_page'] . '&per_page=' . $perPage;
            }

            if ( $page > 1 ) {
                $meta['prev_page'] = $page - 1;
                $meta['prev_page_url'] = $link . '?page=' . $meta['prev_page'] . '&per_page=' . $perPage;
            }

            while ( bbp_topics() ) {
                bbp_the_topic();
                $topicId = bbp_get_topic_id();
                $topicData = $this->prepareData($topicId);

                array_push($data, $topicData);
            }
        }

        return $this->sendSuccess($data, $meta);
    }
    public function forumTopics(WP_REST_Request $request)
    {
        $requests = $request->get_params();
        $forumId = bbp_get_forum_id( $requests['id'] );
        $meta = []; $data = [];

        $link = get_site_url() . '/wp-json/tv/v1/forums/' . $forumId . '/topics';

        if(!bbp_is_forum($forumId)) {
            return $this->sendError('forum_not_found',$forumId . ' is not an ID of a forum');
        }

        $perPage = !empty($requests['per_page']) ? (int) $requests['per_page'] : 15;
        if($perPage > 50) {
            $perPage = 50;
        }
        $page = !empty($requests['page']) ? (int) $requests['page'] : 1;

        $meta['total_topics'] = 0;
        $meta['total_pages'] = 0;
        $meta['current_page'] = 0;
        $meta['per_page'] = 0;
        $meta['next_page'] = null;
        $meta['next_page_url'] = null;
        $meta['prev_page'] = null;
        $meta['prev_page_url'] = null;

        if( bbp_has_topics( ['orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => $perPage, 'paged' => $page, 'post_parent' => $forumId] ) ) {
            $meta['current_page'] = $page;
            $meta['per_page'] = $perPage;
            $meta['total_topics'] = (int) bbp_get_forum_topic_count($forumId);
            $meta['total_pages'] = ceil($meta['total_topics'] / $perPage);

            if ( ( $perPage * $page ) < $meta['total_topics'] ) {
                $meta['next_page'] = $page + 1;
                $meta['next_page_url'] = $link . '?page=' . $meta['next_page'] . '&per_page=' . $perPage;
            }

            if ( $page > 1 ) {
                $meta['prev_page'] = $page - 1;
                $meta['prev_page_url'] = $link . '?page=' . $meta['prev_page'] . '&per_page=' . $perPage;
            }

            while ( bbp_topics() ) {
                bbp_the_topic();
                $topicId = bbp_get_topic_id();
                $topicData = $this->prepareData($topicId);

                array_push($data, $topicData);
            }
        }

        return $this->sendSuccess($data, $meta);
    }

    public function topic(WP_REST_Request $request)
    {
        $requests = $request->get_params();
        $topicId = $requests['id'];
        $showReply = !isset($requests['_embed']) ? false : true;
        $meta = [];
        if(!bbp_is_topic($topicId)) {
            return $this->sendError('topic_not_found',$topicId . ' is not an ID of a topic');
        }

        $topic = $this->prepareData($topicId);
        $topic['content'] = bbp_get_topic_content($topicId);

        if($showReply) {
            $link = get_site_url() . '/wp-json/tv/v1/topics/' . $topicId . '?_embed';
            $perPage = !empty($requests['per_page']) ? (int) $requests['per_page'] : 15;
            if($perPage > 50) {
                $perPage = 50;
            }
            $page = !empty($requests['page']) ? (int) $requests['page'] : 1;

            $meta['total_replies'] = 0;
            $meta['total_pages'] = 0;
            $meta['current_page'] = 0;
            $meta['per_page'] = 0;
            $meta['next_page'] = null;
            $meta['next_page_url'] = null;
            $meta['prev_page'] = null;
            $meta['prev_page_url'] = null;

            if ( bbp_has_replies ( array( 'orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => $perPage, 'paged' => $page, 'post_parent' => $topicId ) ) ) {
                $meta['current_page'] = $page;
                $meta['per_page'] = $perPage;
                $meta['total_replies'] = (int) bbp_get_topic_reply_count($topicId);
                $meta['total_pages'] = ceil($meta['total_replies'] / $perPage);
                $topic['replies'] = [];
                if ( ( $perPage * $page ) < $meta['total_replies'] ) {
                    $meta['next_page'] = $page + 1;
                    $meta['next_page_url'] = $link . '&page=' . $meta['next_page'] . '&per_page=' . $perPage;
                }

                if ( $page > 1 ) {
                    $meta['prev_page'] = $page - 1;
                    $meta['prev_page_url'] = $link . '&page=' . $meta['prev_page'] . '&per_page=' . $perPage;
                }

                while ( bbp_replies() ) {
                    bbp_the_reply();
                    $replyId = bbp_get_reply_id();
                    if($replyId != $topicId) {
                        $replyData = $this->prepareReplyData($replyId);
                        array_push($topic['replies'], $replyData);
                    }
                }
            }
        }

        return $this->sendSuccess($topic, $meta);
    }

    public function prepareData($topicId)
    {
//        $userAvatar = get_user_meta(bbp_get_topic_author_id($topicId), 'avatar', true);
//        $placeholder = !empty($userAvatar) ? $userAvatar : get_template_directory_uri() . '/images/placeholder.png';

        $data = [
            'id' => (int) $topicId,
            'title' => bbp_get_topic_title( $topicId ),
            'reply_count' => (int) bbp_get_topic_reply_count( $topicId ),
            'permalink' => bbp_get_topic_permalink( $topicId ),
            'post_date' => bbp_get_topic_post_date( $topicId ),
            'author' => [
                'name' => bbp_get_topic_author_display_name($topicId),
                'avatar' => get_avatar_url( bbp_get_topic_author_id($topicId) ),
//                'tv_avatar' => $placeholder,
            ]
        ];
        return $data;
    }

    public function prepareReplyData($replyId)
    {
//        $userAvatar = get_user_meta(bbp_get_reply_author_id($replyId), 'avatar', true);
//        $placeholder = !empty($userAvatar) ? $userAvatar : get_template_directory_uri() . '/images/placeholder.png';

        $data = [
            'id' => (int) $replyId,
            'title' => bbp_get_reply_title($replyId),
            'content' => bbp_get_reply_content($replyId),
            'permalink' => bbp_get_reply_permalink($replyId),
            'post_date' => bbp_get_reply_post_date($replyId),
            'reply_to' => bbp_get_reply_to($replyId),
            'author' => [
                'name' => bbp_get_reply_author_display_name($replyId),
                'avatar' => get_avatar_url( bbp_get_reply_author_id($replyId) ),
//                'tv_avatar' => $placeholder,
            ]
        ];

        return $data;
    }

    private function getTotalTopics()
    {
        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT count(*) as total FROM {$wpdb->prefix}posts WHERE post_type='topic'"
        );

        return (int) $results[0]->total;
    }

}
