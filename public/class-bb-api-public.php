<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.beinmedia.com
 * @since      1.0.0
 *
 * @package    Bb_Api
 * @subpackage Bb_Api/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bb_Api
 * @subpackage Bb_Api/public
 * @author     MSalah <salah@beinmedia.com>
 */
class Bb_Api_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bb_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bb_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

//		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bb-api-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bb_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bb_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	}

    public function endPoints() {
        $forums = new Bb_Api_Forums();
        $topics = new Bb_Api_Topics();
        register_rest_route( 'bb-api/v1', 'forums', array(
            'methods' => 'GET',
            'callback' => array( $forums, 'parantForums' ),
        ) );

        register_rest_route( 'bb-api/v1', 'forums/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array( $forums, 'forum' ),
            'args' => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ),
            ),
        ) );

        register_rest_route( 'bb-api/v1', 'forums/(?P<id>\d+)/forum', array(
            'methods' => 'GET',
            'callback' => array( $forums, 'childrenForums' ),
            'args' => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ),
            ),
        ) );

        register_rest_route( 'bb-api/v1', 'topics', array(
            'methods' => 'GET',
            'callback' => array( $topics, 'topics' ),
            'args' => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ),
            ),
        ) );

        register_rest_route( 'bb-api/v1', 'forums/(?P<id>\d+)/topics', array(
            'methods' => 'GET',
            'callback' => array( $topics, 'forumTopics' ),
            'args' => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ),
            ),
        ) );

        register_rest_route( 'bb-api/v1', 'topics/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array( $topics, 'topic' ),
            'args' => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ),
            ),
        ) );
    }
}
