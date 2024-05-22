<?php

class Comment_Rating_Output {

	/**
     * Holds the class object.
     *
     * @since 	2.1.1
     *
     * @var 	object
     */
    public static $instance;

    /**
     * Holds the base class object.
     *
     * @since 	2.1.1
     *
     * @var 	object
     */
    private $base;

    /**
     * Holds the settings array
     *
     * @since   2.2.1
     *
     * @var     array
     */
    public $settings = array();

	/**
	 * Constructor
	 *
     * @since 	2.1.1
	 */
	public function __construct() {

		// Don't load any actions if we're in the admin interface.
		if ( is_admin() ) {
            return;
        }

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_css' ), 10 );
    	add_action( 'comment_text', array( $this, 'display_comment_rating' ) ); // Displays Rating on Comments

	}

	/**
	 * Register or enqueue any JS and CSS
	 *
     * @since 	2.1.1
	 */
	public function scripts_css() {

		// Get base instance

		// Enqueue JS
		wp_enqueue_script('advanced-product-rating-pack', plugin_dir_url( __FILE__ ) .'assets/js/jquery.rating.pack.js', array( 'jquery' ), false, true );
    	wp_enqueue_script( 'advanced-product-rating-js', plugin_dir_url( __FILE__ ) .'assets/js/frontend.js', array( 'jquery' ),false, true );

		// Enqueue CSS
    	wp_enqueue_style( 'advanced-product-rating-style', plugin_dir_url( __FILE__ ) .'assets/css/rating.css', array(), false );

	}

    /**
     * Displays the Average Rating below the Content, if required
     *
     * @since   2.1.1
     *
     * @param   string  $content    Post Content
     * @return  string              Post Content w/ Ratings HTML
     */
	public function display_average_rating( $content ) {

        global $post;

        // Get settings


        // Bail if average isn't enabled


        // Get average rating
        $average_rating = get_post_meta( $post->ID, 'tz-average-rating', true );

        // Calculate average rating now, if one doesn't exist, and fetch the average rating again
        if ( empty( $average_rating ) ) {
            Comment_Rating_Input::get_instance()->update_post_rating_by_post_id( $post->ID );
            $average_rating = get_post_meta( $post->ID, 'tz-average-rating', true );
        }

        // If the average is still zero or empty, bail
        if ( empty( $average_rating ) || $average_rating == 0 ) {
            return $content;
        }

        // Build rating HTML isset($params["author"]) ? $params["author"] : '';  $this->settings['averageRatingText']
        $rating_html = '<div class="tz-average-rating">' . $tz_rat = isset($this->settings['averageRatingText']) ? $this->settings['averageRatingText'] : ' ' .'<div class="tz-rating tz-rating-' . $average_rating . '"></div></div>';
        
        // Return rating HTML with content
        return $content . $rating_html;   

	}
    /**
     * Displays Count Rating below the Content, if required
     *
     * @since   2.1.1
     *
     * @param   string  $content    Post Content
     * @return  string              Post Content w/ Ratings HTML
     */
	public function display_count_rating( ) {

        global $post, $wpdb;

        if ( ! $post ) {
            $count = 0;
        } else {
            $post_id = $post->ID;
            $query = "SELECT COUNT(comment_post_id) AS count FROM $wpdb->comments as cm LEFT JOIN $wpdb->commentmeta as cmt ON cm.comment_ID = cmt.comment_id WHERE cm.comment_approved = 1 AND cm.comment_post_ID = $post_id AND cm.comment_parent = 0 AND cmt.meta_value !=0";
            $parents = $wpdb->get_row($query);
            $count = $parents->count;
        }
        if( $count > 1 ){
            $review_count_text = esc_html__(' Reviews','advanced-product');
        }elseif($count==0){
            return '';
        }else{
            $review_count_text = esc_html__(' Review','advanced-product');
        }

        $count_rating_html = '<span class="tz-count-rating">' .$count.$review_count_text.'</span>';

        return $count_rating_html;

	}

    /**
     * Appends the rating to the end of the comment text for the given comment ID
     * 
     * @since   2.1.1
     *
     * @param   string  $comment    Comment Text
     * @return  string              Comment Text w/ Ratings HTML
     */
	public function display_comment_rating( $comment ) {

        global $post;

        // Get Comment ID
        $comment_id = get_comment_ID();

        // Check whether the Post can have ratings output


        // Get rating
        $rating = get_comment_meta( $comment_id, 'tz-rating', true );
        $rating = ( empty( $rating ) ? 0 : $rating );


        // Build rating HTML
        if( $rating == 5 ){
            $rating_html = '<div class="tz-average-rating tz-rating-wrap"><div class="tz-rating tz-rating-50">' . $rating . '</div></div>';
        }elseif( $rating == 0 ){
            $rating_html = '';
        }else{
            $rating_html = '<div class="tz-average-rating tz-rating-wrap"><div class="tz-rating tz-rating-' . $rating . '">' . $rating . '</div></div>';
        }

        // Return rating HTML with content
        $posttype = get_post_type();
        if($posttype=="ap_product"){
            return $comment . $rating_html;
        }else {
            return $comment;
        }
       
	}

    /**
     * Returns the singleton instance of the class.
     *
     * @since 	2.1.1
     *
     * @return 	object Class.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
            self::$instance = new self;
        }

        return self::$instance;

    }

}

// Init
$Comment_Rating_Output = Comment_Rating_Output::get_instance();