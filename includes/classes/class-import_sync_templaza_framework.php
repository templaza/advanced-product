<?php
namespace Advanced_Product;

use Advanced_Product\Helper\AP_Custom_Taxonomy_Helper;

defined('ADVANCED_PRODUCT') or exit();

class Import_Sync_Templaza_Framework{

    protected $import_object            = false;
    protected $import_source_terms      = array();
    protected $processed_term_images    = array();
    protected $processed_term_customs   = array();

    public function __construct()
    {
        $this -> hooks();
    }

    public function hooks(){

        add_action('templaza-framework/import/constructor', array($this, 'constructor'));

        add_filter('wp_import_terms', array($this, 'pre_terms'));
        add_action('import_term_meta', array($this, 'pre_term_meta'), 10, 3);
        add_filter('wp_import_existing_post', array($this, 'pre_post_exists'), 10, 2);
        add_action('templaza-framework/import/after_import_posts', array($this, 'after_import_posts'));
    }

    public function constructor($import_object){
        $this -> import_object  = $import_object;
    }

    /**
     * Get source terms from xml data file of parent importer
     * @param array $terms An optional of term list parsed from xml file
     * */
    public function pre_terms($terms){
        if(!empty($terms) && empty($this -> import_source_terms)) {
            $this->import_source_terms = $terms;
        }
        return $terms;
    }

    /**
     * Get source terms from xml data file of parent importer which they have image
     * (an image option of acf field registered)
     * @param int $term_id An optional of term
     * @param string $key An optional of term
     * @param string $value An optional of term
     * */
    public function pre_term_meta($term_id, $key, $value){
        $import_obj = $this -> import_object;
        // Get post by post_id
        if(!empty($import_obj) && $key == '_image') {
            $import_term_id = array_search($term_id, $import_obj -> processed_terms);
            $this ->processed_term_images[$import_term_id]  = $term_id;
        }
    }

    /**
     * Get custom category from post type is ap_custom_category
     * @param int $post_exists An optional of post id
     * @param array $post An optional post info
     * */
    public function pre_post_exists($post_exists, $post){
        if(!empty($post) && isset($post['post_type'])
            && $post['post_type'] == 'ap_custom_category'){
            $this -> processed_term_customs[]   = $post['post_name'];
        }
        return $post_exists;
    }

    /**
     * Get custom category from post type is ap_custom_category
     * @param int $post_exists An optional of post id
     * @param array $post An optional post info
     * */
    public function after_import_posts($import_obj){
        // Get all terms by meta key is image
        $term_ids   = $this ->processed_term_images;

        if(count($term_ids)){
            $avd_terms  = AP_Custom_Taxonomy_Helper::get_taxonomies();
            $avd_terms  = wp_list_pluck($avd_terms, 'post_name');

            $avd_terms[]    = 'ap_branch';
            $avd_terms[]    = 'ap_category';

            foreach ($term_ids as $iterm_id => $new_term_id){
                $term   = get_term($new_term_id);
                if(!empty($term) && !is_wp_error($term) && in_array($term -> slug, $avd_terms)){
                    // Get image meta
                    $image  = get_field('image', 'term_'.$new_term_id);

                    if(!empty($image) && isset($image['id']) && !empty($import_obj -> processed_posts)
                        && isset($import_obj -> processed_posts[$image['id']])){
                        update_field('image', $import_obj -> processed_posts[$image['id']], 'term_'.$new_term_id);
                    }
                }
            }
        }

        // Import terms of post type ap_custom_category
        if(count($this -> import_source_terms) && isset($this -> processed_term_customs)
            && !empty($this -> processed_term_customs)) {

            // Register custom category taxonomy if it doesn't exists
            $args = array(
                'order'       => 'ASC',
                'orderby'     => 'ID',
                'post_status' => 'publish',
                'post_type'   => 'ap_custom_category'
            );

            $categories = get_posts( $args );

            if(!empty($categories) && is_array($categories)){
                foreach($categories as $category){
                    $slug       = get_post_meta($category -> ID, 'slug', true);
                    if(!empty($slug) && !isset($this -> taxonomies[$slug])){
                        $this -> taxonomies[$slug]   = new Custom_Taxonomy($category);
                    }

                    if(!taxonomy_exists($slug) && isset($this -> taxonomies[$slug])){
                        $this -> taxonomies[$slug] -> register_taxonomy();
                    }
                }
            }

            // Filter term taxonomies didn't import
            $new_sterms = array();
            $processed_terms_sids = array_keys($import_obj->processed_terms);
            foreach ($this -> import_source_terms as $term) {
                if (in_array($term['term_id'], $processed_terms_sids)
                    || !in_array($term['term_taxonomy'], $this -> processed_term_customs)) {
                    continue;
                }
                $new_sterms[] = $term;

            }

            // Import custom category taxonomy
            if(count($new_sterms)) {
                $import_obj->terms = $new_sterms;
                $import_obj->process_terms();
            }
        }
    }
}