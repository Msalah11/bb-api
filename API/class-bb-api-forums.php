<?php


class Bb_Api_Forums extends Bb_Api_Base
{

    public function forum(WP_REST_Request $request)
    {
        $requests = $request->get_params();
        $forumId = $requests['id'];
        if(!bbp_is_forum($forumId)) {
            return $this->sendError('forum_not_found',$forumId . ' is not an ID of a forum');
        }

        $data = array_merge( $this->prepareData($forumId), $this->prepareFullData($forumId) );
        return $this->sendSuccess($data);
    }

    public function parantForums(WP_REST_Request $request)
    {
        $allForums = [];
        $requests = $request->get_params();
        $showChildren = !isset($requests['_embed']) ? false : true;
        if ( bbp_has_forums() ) {
            while ( bbp_forums() ) {
                bbp_the_forum();
                $forumId = bbp_get_forum_id();
                $forumData = $this->prepareData($forumId);

                if( $showChildren ) {
                    $forumData['children'] = [];
                    $child = $this->prepareChildren($forumId);

                    array_push($forumData['children'], $child);
                }
                array_push($allForums, $forumData);
            }
        }

        return $this->sendSuccess($allForums);
    }

    public function childrenForums(WP_REST_Request $request)
    {

        $requests = $request->get_params();
        $forumId = bbp_get_forum_id( $requests['id'] );

        if(!bbp_is_forum($forumId)) {
            return $this->sendError('forum_not_found',$forumId . ' is not an ID of a forum');
        }
        $children = $this->prepareChildren($forumId);
        if(!$children) {
            return $this->sendError('forum_children_fetched_error');
        }
        return $this->sendSuccess($children);
    }

    private function prepareChildren($id)
    {
        $children = bbp_forum_get_subforums($id);
        $childrenData = [];
        foreach ($children as $child) {
            $childId = $child->ID;
            $childData = $this->prepareData($childId);

            array_push($childrenData, $childData);
        }

        return $childrenData;
    }

    private function prepareData($forumId)
    {
        $data = [
            'id' => (int) $forumId,
            'title' => bbp_get_forum_title($forumId),
            'content' => bbp_get_forum_content($forumId),
            'topic_count' => (int) bbp_get_forum_topic_count($forumId),
            'reply_count' => (int) bbp_get_forum_reply_count($forumId),
            'type' => bbp_get_forum_type($forumId),
            'permalink' => bbp_get_forum_permalink($forumId),
        ];

        return $data;
    }

    private function prepareFullData($forumId)
    {
        $children = bbp_forum_get_subforums($forumId);
        $childrenData = [];
        $topicsData = [];
        foreach ($children as $child) {
            $childId = $child->ID;
            $childData = $this->prepareData($childId);

            array_push($childrenData, $childData);

            if( bbp_has_topics( ['orderby' => 'date', 'order' => 'DESC', 'post_parent' => $childId] ) ) {
                while ( bbp_topics() ) {
                    bbp_the_topic();
                    $topicId = bbp_get_topic_id();
                    $topicData = (new Bb_Api_Topics())->prepareData($topicId);
                    array_push($topicsData, $topicData);
                }
            }
        }

        return ['children' => $childrenData, 'topics' => $topicsData];
    }
}
