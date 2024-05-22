<?php
class Comment_Rating_Input {

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

		if ( is_admin() ) {
            add_action( 'wp_set_comment_status', array( $this, 'update_post_rating_by_comment_id' ) ); // Recalculate average rating on comment approval / hold / spam
            add_action( 'deleted_comment', array( $this, 'update_post_rating_by_comment_id' ) ); // Recalculate average rating on comment delete
        }

        add_action( 'comment_form_logged_in_after', array( $this, 'display_rating_field' ) ); // Logged in
        add_action( 'comment_form_after_fields', array( $this, 'display_rating_field' ) ); // Guest
        add_action( 'comment_post', array( $this, 'save_rating' ) ); // Save Rating Field on Comment Post
        
	}

    /**
     * Displays the rating field on the comments form
     *
     * @since   2.1.1
     */
    public function display_rating_field() {

        // Bail if Post cannot have a rating
        $posttype = get_post_type();
        if($posttype=="ap_product"){
            $setting_rate = true;
        }else {
            $setting_rate = false;
        }

        if ( $setting_rate ==false ) {
            return;
        }
        ?>
        <!-- CRFP Fields: Start -->
        <p class="tz-field">

            <?php
            if ( isset( $this->settings['ratingFieldLabel'] ) && ! empty( $this->settings['ratingFieldLabel'] ) ) {
                ?>
                <label for="rating-star"><?php echo $this->settings['ratingFieldLabel']; ?></label>
                <?php   
            }
            ?>
            <input name="rating-star" type="radio" class="star" value="1" />
            <input name="rating-star" type="radio" class="star" value="2" />
            <input name="rating-star" type="radio" class="star" value="3" />
            <input name="rating-star" type="radio" class="star" value="4" />
            <input name="rating-star" type="radio" class="star" value="5" />
            <input type="hidden" name="tz-rating" value="5" />
        </p>
        <!-- CRFP Fields: End -->
        <?php

    }

    /**
     * Saves the POSTed rating for the given comment ID to the comment meta table,
     * as well as storing the total ratings and average on the post itself.
     *
     * @since   2.1.1
     *
     * @param   int     $comment_id     Comment ID
     */
    public function save_rating( $comment_id ) {

        // Exit if no rating given
        if ( ! isset( $_POST['tz-rating'] ) ) {
            return;
        }

        // Save rating against comment
        add_comment_meta( $comment_id, 'tz-rating', $_POST['tz-rating'], true );

        // Request that the user review the plugin. Notification displayed later,
        // can be called multiple times and won't re-display the notification if dismissed.

        // Get post ID from comment and store total and average ratings against post
        // Run here in case comments are set to always be approved
        $this->update_post_rating_by_comment_id( $comment_id ); 

    }


    /**
     * Passes on the request to calculate the average rating and total number of ratings
     * for the comment's Post.
     *
     * @since   2.1.1
     *
     * @param   int     $comment_id     Comment ID
     */
    public function update_post_rating_by_comment_id( $comment_id ) {

        // Get comment
        $comment = get_comment( $comment_id );

        // Bail if no comment found
        if ( ! $comment || is_wp_error( $comment ) ) {
            return;
        }

        // Update post rating by Post ID
        $this->update_post_rating_by_post_id( $comment->comment_post_ID );

    }

     /**
     * Calculates the average rating and total number of ratings
     * for the given post ID, storing it in the post meta.
     *
     * @since   2.1.1
     * @param   int     $post_id    Post ID
     */
    public function update_post_rating_by_post_id( $post_id ) {

        // Cast Post ID
        $post_id = absint( $post_id );

        // Get all approved comments and total the number of ratings and rating values for fields
        $comments = get_comments( array(
            'post_id'   => absint( $post_id ),
            'status'    => 'approve',
        ) );
        
        // Calculate
        $total_rating   = 0;
        $total_ratings  = 0;
        $average_rating = 0;
        $average_val = 0;

        if ( is_array( $comments ) && count( $comments ) > 0 ) {
            // Iterate through comments
            foreach ( $comments as $comment ) { 
                $rating = get_comment_meta( $comment->comment_ID, 'tz-rating', true );
                if ( $rating > 0 ) {
                    $total_ratings++;
                    $total_rating += $rating;
                }
            }
            
            // Calculate average rating
            $average_rating = ( ( $total_ratings == 0 || $total_rating == 0 ) ? 0 : round( ( $total_rating / $total_ratings ), 2 ) );

            switch($average_rating)
            {
                case ($average_rating <= 0.24):
                    $average_val = "0";
                    break;

                case ($average_rating <= 0.74):
                    $average_val = "5";
                    break;

                case ($average_rating <= 1.24):
                    $average_val = "1";
                    break;

                case ($average_rating <= 1.74):
                    $average_val = "15";
                    break;

                case ($average_rating <= 2.24):
                    $average_val = "2";
                    break;

                case ($average_rating <= 2.74):
                    $average_val = "25";
                    break;

                case ($average_rating <= 3.24):
                    $average_val = "3";
                    break;

                case ($average_rating <= 3.74):
                    $average_val = "35";
                    break;

                case ($average_rating <= 4.24):
                    $average_val = "4";
                    break;

                case ($average_rating <= 4.74):
                    $average_val = "45";
                    break;

                case ($average_rating <= 5):
                    $average_val = "50";
                    break;
                default:
                    $average_val = 0;
            }

        }

        // Update post meta
        update_post_meta( $post_id, 'tz-total-ratings', $total_ratings );
        update_post_meta( $post_id, 'tz-average-rating', $average_val );

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
$Comment_Rating_Input = Comment_Rating_Input::get_instance();