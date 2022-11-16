<?php

namespace Advanced_Product\Post_Type;

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Helper\AP_Product_Helper;
use Advanced_Product\Helper\FieldHelper;
use Advanced_Product\Post_Type;
use Advanced_Product\AP_Functions;

if(!class_exists('Advanced_Product\Post_Type\Custom_Field')){
    class Custom_Field extends Post_Type {

        protected $fields;
        protected $built_in;

        public function __construct($core = null, $post_type = null)
        {
            parent::__construct($core, $post_type);

            $this -> fields     = array();
            $this -> built_in   = FieldHelper::get_core_fields();
        }

        public function hooks()
        {
            parent::hooks();

            add_action( 'admin_init', array($this, 'disable_autosave') );
            add_action( 'save_post_'.$this -> get_post_type(), array($this, 'save_post'), 10,3 );

            add_filter('pre_trash_post', array($this, 'pre_trash_post'), 10, 2);
            add_filter('pre_delete_post', array($this, 'pre_delete_post'), 10, 3);
            add_filter('post_row_actions', array($this, 'post_row_actions'), 10, 2);

            add_filter('posts_orderby', array($this, 'posts_orderby'), 100, 2);

            add_filter( 'manage_edit-'.$this -> get_post_type().'_sortable_columns', array($this,'sortable_columns') );
            add_action('parse_query',array($this, 'parse_query'));
            add_action('restrict_manage_posts',array($this, 'restrict_manage_posts'));

            add_action( 'wp_ajax_ap_post_type_ap_custom_field_archive_sortable', array($this, 'saveAjaxOrder'));
            add_action( 'wp_ajax_nopriv_ap_post_type_ap_custom_field_archive_sortable', array($this, 'saveAjaxOrder'));
        }

        public function disable_autosave() {
            wp_deregister_script( 'autosave' );
        }

        public function save_post($post_ID, $post, $update){
            global $wpdb;

            // Get acf field attribute
            $acf_fields     = (!empty($_POST) && isset($_POST['fields']))?$_POST['fields']:array();
            $key            = !empty($acf_fields)?array_key_first($acf_fields):'';
            $acf_attribs    = !empty($acf_fields) && $key?$acf_fields[$key]:array();

            if(!empty($acf_attribs)) {
                // Update some info to custom field post
                $my_post = array(
                    'post_name'     => $key,
                    'post_content'  => serialize($acf_attribs),
                    'post_excerpt'  => $acf_attribs['name']
                );
                $wpdb -> update($wpdb -> posts, $my_post, array('ID' => $post_ID));

                // Update load save term of product
                if($post_ID && isset($acf_attribs['type']) && $acf_attribs['type'] == 'taxonomy'){
                    $load_save_term = isset($acf_attribs['load_save_terms'])?$acf_attribs['load_save_terms']:false;
                    if($load_save_term){
                        // Get term_relationships added
                        $subSql = " SELECT _ta.object_id";
                        $subSql.= " FROM {$wpdb -> term_relationships} AS _ta";
                        $subSql.= " INNER JOIN {$wpdb -> posts} AS _p ON _p.ID = _ta.object_id AND _p.post_type='ap_product'";
                        $subSql.= " INNER JOIN {$wpdb -> term_taxonomy} AS _tt ON _tt.term_taxonomy_id=_ta.term_taxonomy_id AND _tt.taxonomy='{$acf_attribs['taxonomy']}'";

                        // Insert term relationships of taxonomy
                        $sql    = " INSERT INTO {$wpdb -> term_relationships}(object_id,term_taxonomy_id)";
//                        $sql    = "";
                        $sql   .= " SELECT p.ID, t.term_id";
                        $sql   .= " FROM {$wpdb -> posts} AS p";
                        $sql   .= " INNER JOIN {$wpdb -> term_taxonomy} AS t ON t.taxonomy = '{$acf_attribs['taxonomy']}'";
                        $sql   .= " INNER JOIN {$wpdb -> terms} AS te ON te.term_id = t.term_id";
                        $sql   .= " WHERE p.post_type='ap_product'";
                        $sql   .= " AND p.ID NOT IN($subSql)";

                       $wpdb -> query($sql);
                    }else{
                        // Get term_relationships added
                        $subSql = " SELECT DISTINCT _ta.term_taxonomy_id";
                        $subSql.= " FROM {$wpdb -> term_relationships} AS _ta";
                        $subSql.= " INNER JOIN {$wpdb -> posts} AS _p ON _p.ID = _ta.object_id AND _p.post_type='ap_product'";
                        $subSql.= " INNER JOIN {$wpdb -> term_taxonomy} AS _tt ON _tt.term_taxonomy_id=_ta.term_taxonomy_id AND _tt.taxonomy='{$acf_attribs['taxonomy']}'";

                        // Delete term relationships of taxonomy
                        $sql    = " DELETE FROM {$wpdb -> term_relationships}";
                        $sql   .= " WHERE term_taxonomy_id IN(SELECT _ta2.term_taxonomy_id FROM($subSql) AS _ta2)";

                        $wpdb -> query($sql);
                    }
                }
            }
        }

        public function register(){
            /**
             * Post types
             */
            $singular  = __( 'Custom Field', 'advanced-product' );
            $plural    = __( 'Custom Fields', 'advanced-product' );

            $args = array(
                'description'         => __( 'This is where you can create and manage custom fields.', 'advanced-product' ),
                'labels' => array(
                    'name' 					=> $plural,
                    'singular_name' 		=> $singular,
                    'menu_name'             => $plural,
                    'all_items'             => $plural,
                    'add_new' 				=> __( 'Add New', 'advanced-product' ),
                    'add_new_item' 			=> sprintf( __( 'Add %s', 'advanced-product' ), $singular ),
                    'edit' 					=> __( 'Edit', 'advanced-product' ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', 'advanced-product' ), $singular ),
                    'new_item' 				=> sprintf( __( 'New %s', 'advanced-product' ), $singular ),
                    'view' 					=> sprintf( __( 'View %s', 'advanced-product' ), $singular ),
                    'view_item' 			=> sprintf( __( 'View %s', 'advanced-product' ), $singular ),
                    'search_items' 			=> sprintf( __( 'Search %s', 'advanced-product' ), $plural ),
                    'not_found' 			=> sprintf( __( 'No %s found', 'advanced-product' ), $plural ),
                    'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'advanced-product' ), $plural ),
                    'parent' 				=> sprintf( __( 'Parent %s', 'advanced-product' ), $singular )
                ),
                'supports'            => array( 'title', ),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => 'edit.php?post_type='.$this -> prefix.'product',
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => true,
                'menu_position'       => 20,
//                'menu_icon'           => AP_Functions::get_my_url() . '/assets/images/icon.svg',
                'menu_icon'           => 'dashicons-store',
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => false,
                'query_var'           => false,
                '_builtin' =>  false,
//                'capability_type'     => 'page',
                'capability_type'     => 'post',
                'rewrite' => false,
//                'rewrite'			  => array( 'slug' => 'ap-custom-field' )
            );
            return $args;
        }

        public function manage_edit_columns($columns){
//            $columns    = array('__order' => '<span class="dashicons dashicons-sort"></span>') + $columns;
//            return $columns;
            $keys   = array_keys($columns);
//            $first_columns  = array_splice($columns, 0, array_search('title', $keys));
//            $first_columns  +=  array(
//                '__order check-column' => '<span class="dashicons dashicons-sort"></span>',
//            );
            $first_columns  = array();
            $first_columns['cb']    = $columns['cb'];
            unset($columns['cb']);
            $first_columns  +=  array(
//                '__order' => '<a href="'.admin_url('edit.php?post_type='.$this ->get_post_type().'&orderby=menu_order&order=asc')
//                    .'"><span class="dashicons dashicons-image-flip-vertical"></span><span class="sorting-indicator"></span></a> '
//                '__order' => '<span class="dashicons dashicons-sort"></span>'
                'menu_order' => '<span class="dashicons dashicons-image-flip-vertical"></span>'
            );
            $first_columns['title'] = $columns['title'];
            $first_columns  +=  array(
                'protected' => __('Protected', 'advanced-product')
            );
            unset($columns['title']);
//            $first_columns  +=  $columns;
            $second_columns  = array_splice($columns, 0, array_search('date', array_keys($columns)));
//            var_dump($first_columns);
//            var_dump($second_columns);
//            var_dump($columns);
//            die(__FILE__);
            $new_columns                = array();
//            $new_columns['__order']     = '';
//            $new_columns['__order check-column']   = '<span class="dashicons dashicons-sort"></span>';
            $new_columns['title']          = $columns['title'];
//            $new_columns['protected']   = __('Protected', 'advanced-product');
            $new_columns['in_listing']  = __('In Listing', 'advanced-product');
            $new_columns['in_search']   = __('In Search', 'advanced-product');

//            var_dump($columns); die(__FILE__);

//            return array_merge($new_columns, $columns);
            return $first_columns + $second_columns + $new_columns + $columns;

        }

        public function manage_custom_column($column, $post_id ){
            if($column == 'menu_order') {
                $order_by   = isset($_REQUEST['orderby'])?$_REQUEST['orderby']:'';
                $inactive   = $order_by != 'menu_order'?' ap-inactive':'';

                echo '<span class="dashicons dashicons-menu-alt2 ap-handle'.$inactive.'"></span>';
            }
            if($column == 'protected') {
                $protected  = get_post_meta($post_id, '__protected', true);
                if($protected) {
                    echo '<span class="dashicons dashicons-lock"></span>';
                }
            }
            if($column == 'in_listing'){
                // Get post meta
                $in_listing = get_post_meta($post_id, 'show_in_listing', true);
                $in_listing = filter_var($in_listing, FILTER_VALIDATE_BOOLEAN);
                if($in_listing){
                    echo '<span class="dashicons dashicons-yes"></span>';
                }
            }
            if($column == 'in_search'){
                // Get post meta
                $in_search  = get_post_meta($post_id, 'show_in_search', true);
                $in_search  = filter_var($in_search, FILTER_VALIDATE_BOOLEAN);
                if($in_search){
                    echo '<span class="dashicons dashicons-yes"></span>';
                }
            }
        }


        public function sortable_columns( $columns ) {
            $columns['menu_order'] = 'menu_order';
            return $columns;
        }

        public function admin_enqueue_scripts($hook){
            if($this -> get_post_type() == $this -> get_current_screen_post_type()) {
                wp_enqueue_script('advanced-product_admin_sanitize-title-script');
                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery-ui-sortable');
            }
        }

        /**
         * Deny trash post is protected
         * @param bool|null $trash Whether to go forward with trashing.
         * @param WP_Post   $post  Post object.
         * @return bool|null
         * */
        public function pre_trash_post($trash, $post){
            if(!empty($post) && $post -> post_type == $this -> get_post_type()){
                $is_protected    = (bool) get_post_meta($post -> ID, '__protected', true);
                if($is_protected){
                    $trash  = false;
                }
            }

            return $trash;
        }

        /**
         * Deny delete post is protected
         * @param bool|null $trash Whether to go forward with trashing.
         * @param WP_Post   $post  Post object.
         * @return bool|null
         * */
        public function pre_delete_post($delete, $post){
            if(!empty($post) && $post -> post_type == $this -> get_post_type()){
                $is_protected    = (bool) get_post_meta($post -> ID, '__protected', true);
                if($is_protected){
                    $delete  = false;
                }
            }

            return $delete;
        }

        /**
         * Deny trash action with field is protected
         * @param string[] $actions An array of row action links. Defaults are
         *                          'Edit', 'Quick Edit', 'Restore', 'Trash',
         *                          'Delete Permanently', 'Preview', and 'View'.
         * @param WP_Post  $post    The post object.
         * @return bool|null
         * */
        public function post_row_actions($actions, $post){
            if (!empty($post) && $post->post_type == $this -> get_post_type() && isset($actions['trash']) ) {
                $is_protected    = (bool) get_post_meta($post -> ID, '__protected', true);
                if($is_protected){
                    unset($actions['trash']);
                }
            }

            return $actions;
        }

        public function saveAjaxOrder(){

            set_time_limit(600);

            global $wpdb, $userdata;

            $post_type  =   filter_var ( $_POST['post_type'], FILTER_SANITIZE_STRING);
            $paged      =   filter_var ( $_POST['paged'], FILTER_SANITIZE_NUMBER_INT);
            $nonce      =   $_POST['archive_sort_nonce'];

            //verify the nonce
            if (! wp_verify_nonce( $nonce, 'ap_archive_sort_nonce_' . $userdata->ID ) )
                die();

            parse_str($_POST['order'], $data);

            if (!is_array($data)    ||  count($data)    <   1)
                die();

            //retrieve a list of all objects
            $mysql_query    =   $wpdb->prepare("SELECT ID FROM ". $wpdb->posts ." 
                                                            WHERE post_type = %s AND post_status IN ('publish', 'pending', 'draft', 'private', 'future', 'inherit')
                                                            ORDER BY menu_order, post_date DESC", $post_type);
            $results        =   $wpdb->get_results($mysql_query);

            if (!is_array($results)    ||  count($results)    <   1)
                die();

            //create the list of ID's
            $objects_ids    =   array();
            foreach($results    as  $result)
            {
                $objects_ids[]  =   (int)$result->ID;
            }

            global $userdata;
            $objects_per_page   =   get_user_meta($userdata->ID ,'edit_' .  $post_type  .'_per_page', TRUE);
            if(empty($objects_per_page))
                $objects_per_page   =   20;

            $edit_start_at      =   $paged  *   $objects_per_page   -   $objects_per_page;
            $index              =   0;
            for($i  =   $edit_start_at; $i  <   ($edit_start_at +   $objects_per_page); $i++)
            {
                if(!isset($objects_ids[$i]))
                    break;

                $objects_ids[$i]    =   (int)$data['post'][$index];
                $index++;
            }

            //update the menu_order within database
            foreach( $objects_ids as $menu_order   =>  $id )
            {
                $data = array(
                    'menu_order' => $menu_order
                );

                //Deprecated, rely on pto/save-ajax-order
                $data = apply_filters('advanced-product/post-types-order_save-ajax-order', $data, $menu_order, $id);

                $data = apply_filters('advanced-product/save-ajax-order', $data, $menu_order, $id);

                $wpdb->update( $wpdb->posts, $data, array('ID' => $id) );

                clean_post_cache( $id );
            }

            //trigger action completed
            do_action('advanced-product/order_update_complete');
        }

        public function posts_orderby($orderBy, $query)
        {
            global $wpdb;


            // ignore other post type
            if($this -> get_current_screen_post_type() != $this -> get_post_type()){
                return $orderBy;
            }

//            //check for ignore_custom_sort
//            if (isset($query->query_vars['ignore_custom_sort']) && $query->query_vars['ignore_custom_sort'] === TRUE)
//                return $orderBy;
//
//            //ignore the bbpress
//            if (isset($query->query_vars['post_type']) && ((is_array($query->query_vars['post_type']) && in_array("reply", $query->query_vars['post_type'])) || ($query->query_vars['post_type'] == "reply")))
//                return $orderBy;
//            if (isset($query->query_vars['post_type']) && ((is_array($query->query_vars['post_type']) && in_array("topic", $query->query_vars['post_type'])) || ($query->query_vars['post_type'] == "topic")))
//                return $orderBy;
//
//            var_dump($orderBy);
            //check for orderby GET paramether in which case return default data
            if (isset($_GET['orderby']) && $_GET['orderby'] !=  'menu_order')
                return $orderBy;

            //check to ignore
            /**
             * Deprecated filter
             * do not rely on this anymore
             */
            if(apply_filters('advanced-product/posts_orderby', $orderBy, $query) === FALSE)
                return $orderBy;

            $ignore =   apply_filters('advanced-product/posts_orderby/ignore', FALSE, $orderBy, $query);
            if($ignore  === TRUE)
                return $orderBy;

//            //ignore search
//            if( $query->is_search()  &&  isset( $query->query['s'] )   &&  ! empty ( $query->query['s'] ) )
//                return( $orderBy );

            if (is_admin())
            {

//                if ( $options['adminsort'] == "1" || (defined('DOING_AJAX') && isset($_REQUEST['action']) && $_REQUEST['action'] == 'query-attachments') )
//                {

                    global $post;


//                    $order  =   isset($_GET['order'])  ?   " " . $_GET['order'] : '';
                    $order  =   isset($query->query_vars['order'])  ?   " " . $query->query_vars['order'] : '';

                    $order  =   apply_filters('advanced-product/posts_order', $order, $query);

//                    //temporary ignore ACF group and admin ajax calls, should be fixed within ACF plugin sometime later
//                    if (is_object($post) && $post->post_type    ==  "acf-field-group"
//                        ||  (defined('DOING_AJAX') && isset($_REQUEST['action']) && strpos($_REQUEST['action'], 'acf/') === 0))
//                        return $orderBy;

//                    if(isset($_POST['query'])   &&  isset($_POST['query']['post__in'])  &&  is_array($_POST['query']['post__in'])   &&  count($_POST['query']['post__in'])  >   0)
//                        return $orderBy;

//                    $orderBy = "{$wpdb->posts}.menu_order {$order}, {$wpdb->posts}.post_date DESC";
                    $orderBy = "{$wpdb->posts}.menu_order {$order}";
//                    var_dump($orderBy);
//                }
            }
//            else
//            {
//                $order  =   '';
////                if ($options['use_query_ASC_DESC'] == "1")
////                    $order  =   isset($query->query_vars['order'])  ?   " " . $query->query_vars['order'] : '';
//
//                $order  =   apply_filters('advanced-product/posts_order', $order, $query);
//
////                if ($options['autosort'] == "1")
////                {
//                    if(trim($orderBy) == '')
//                        $orderBy = "{$wpdb->posts}.menu_order " . $order;
//                    else
//                        $orderBy = "{$wpdb->posts}.menu_order". $order .", " . $orderBy;
////                }
//            }

            return($orderBy);
        }

        /**
         * Filter slugs
         * @since 1.1.0
         * @return void
         */
        public function restrict_manage_posts() {
            global $typenow;
            global $wp_query;
            if ($typenow=='ap_custom_field') {
                $taxonomy   = 'ap_group_field';
                $selected   = isset($_REQUEST['ap_group_field'])?$_REQUEST['ap_group_field']:'';
                $business_taxonomy = get_taxonomy($taxonomy);
                wp_dropdown_categories(array(
                    'show_option_all' =>  __("All {$business_taxonomy->label}"),
                    'taxonomy'        =>  $taxonomy,
                    'name'            =>  'ap_group_field',
                    'orderby'         =>  'name',
                    'selected'        =>  $selected,
                    'hierarchical'    =>  true,
                    'depth'           =>  3,
                    'show_count'      =>  false, // Show # listings in parens
                    'hide_empty'      =>  true, // Don't show businesses w/o listings
                ));
            }
        }

        public function parse_query($query) {
            global $pagenow;
            $qv = &$query->query_vars;
            $filter   = isset($_REQUEST['ap_group_field'])?$_REQUEST['ap_group_field']:'';
            if ($pagenow=='edit.php' &&
                isset($qv['post_type']) && $qv['post_type']=='ap_custom_field' &&
                $filter && is_numeric($filter)) {
                $term = get_term_by('id',$filter,'ap_group_field');
                $qv['ap_group_field'] = $term->slug;
//                $qv['term'] = $term->slug;
            }
            return $query;
        }

    }
}