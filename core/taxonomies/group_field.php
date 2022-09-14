<?php

namespace Advanced_Product\Taxonomy;

use Advanced_Product\Taxonomy;

defined('ADVANCED_PRODUCT') or exit();

class Group_Field extends Taxonomy {

//    protected $allow_custom_options = false;

    public function hooks()
    {
        parent::hooks(); // TODO: Change the autogenerated stub

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'parent_file', array($this,'menu_highlight' ));
//        add_action( 'edited_'.$this ->get_taxonomy_name(), array($this,'edited_taxonomy'), 10, 2 );
        add_action( 'saved_'.$this ->get_taxonomy_name(), array($this,'saved_taxonomy'), 10, 2 );
//        add_filter( 'acf/update_field/type=taxonomy', array($this,'update_field_value' ),20,2);
//        add_filter( 'acf/load_value/type=taxonomy', array($this,'load_field_value' ),20,3);
    }

    public function register(){

        $singular  = __( 'Custom Field Group', 'advanced-product' );
        $plural    = __( 'Custom Field Groups', 'advanced-product' );

        return array(
            'object_type'   => 'ap_custom_field',
            'args'          => array(
                'label' 					=> $plural,
                'labels' => array(
                    'name' 					=> $singular,
                    'singular_name' 		=> $singular,
                    'menu_name'				=> $plural,
                    'search_items' 			=> sprintf( __( 'Search %s', 'advanced-product' ), $plural ),
                    'all_items' 			=> sprintf( __( 'All %s', 'advanced-product' ), $plural ),
                    'parent_item' 			=> sprintf( __( 'Parent %s', 'advanced-product' ), $singular ),
                    'parent_item_colon'		=> sprintf( __( 'Parent %s:', 'advanced-product' ), $singular ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', 'advanced-product' ), $singular ),
                    'update_item' 			=> sprintf( __( 'Update %s', 'advanced-product' ), $singular ),
                    'add_new_item' 			=> sprintf( __( 'Add New %s', 'advanced-product' ), $singular ),
                    'new_item_name' 		=> sprintf( __( 'New %s Name', 'advanced-product' ),  $singular ),
                    'not_found' 		    => sprintf( __( 'No %s found.', 'advanced-product' ),  $plural ),
                ),
                'hierarchical'              => true,
                'show_admin_column'         => true,
                'show_in_nav_menus'         => false,
//                'exclude_from_search' => true,
                'query_var' => false,
//                'publicly_queryable'        => false,
            )
        );
    }

    public function admin_menu(){
        add_submenu_page('edit.php?post_type=ap_product', 'Custom Field Groups',
            'Custom Field Groups', 'manage_options',
            'edit-tags.php?taxonomy='.$this -> get_taxonomy_name().'&post_type=ap_custom_field'/*,'bsp_students_add'*/);
    }

    /* Set menu highlight because wp not active */
    public function menu_highlight( $parent_file ) {
        global $submenu_file, $post_type, $taxonomy;

        if ( $taxonomy == $this -> get_taxonomy_name() && $post_type == 'ap_custom_field' ) {
            $parent_file = 'edit.php?post_type=ap_product';
            $submenu_file = 'edit-tags.php?taxonomy='.$this -> get_taxonomy_name().'&post_type=ap_custom_field';    // the submenu slug
        }

        return $parent_file;
    }

    public function __get_core_fields(){
        $fields = array(
            array(
                'key'       => 'field_'.md5($this -> get_taxonomy_name().'__branch'),
                'label'     => __('Branches Assignment', 'advanced-product'),
                'name'      => 'branch_assigned',
                'type'      => 'taxonomy',
                'taxonomy'  => 'ap_branch',
                'load_save_terms'  => false,
                'group' => $this -> __get_core_field_group_id()
            )
        );

        return apply_filters('advanced-product/'.$this -> get_taxonomy_name().'/fields/create', $fields);
    }

    public function manage_edit_columns($columns){
        $pos            = array_search('name', array_keys($columns)) + 1;
        $new_columns    = array('branch_assigned' => __('Branch Assigned', 'advanced-product'));

        return array_merge(
            array_slice($columns, 0, $pos),
            $new_columns,
            array_slice($columns, $pos)
        );

    }

    public function manage_custom_column($content, $column, $term_id ){
        if($column == 'branch_assigned'){
//            $fval   = get_field( $column, $this -> get_taxonomy_name().'_'.$term_id );
            $fval   = \get_field( $column, 'term_'.$term_id );

            if(!empty($fval) && count($fval)){
                foreach($fval as $i => $slug){
                    $term   = get_term_by('slug', $slug, 'ap_branch');
                    if(!is_wp_error($term) && !empty($term)){
                        $content    .= '<a href="term.php?taxonomy=ap_branch&post_type=ap_product&tag_ID='
                            .$term -> term_id.'">'.$term -> name.'</a>';
                        if($i < count($fval) - 1){
                            $content    .= ', ';
                        }
                    }
                }
            }
        }
        return $content;
    }

    public function saved_taxonomy($term_id, $tt_id ){
        $term = get_term( $term_id );
        $tax_slug = $term->slug;

        clean_taxonomy_cache($this -> get_taxonomy_name());

//        $branch_assigned    = \get_field('branch_assigned', $this -> get_taxonomy_name().'_'.$term_id);
        $branch_assigned    = \get_field('branch_assigned', 'term_'.$term_id);

        $branch_taxs    = \get_terms(array(
            'taxonomy'      => 'ap_branch',
            'hide_empty'    => false,
        ));

        if(!is_wp_error($branch_taxs) && !empty($branch_taxs)){
            $field_key  = 'field_'.md5('ap_branch__group_field');
            foreach($branch_taxs as $branch){
//                $group_assigned = \get_field('group_field_assigned', 'ap_branch_' . $branch->term_id);
                $group_assigned = \get_field('group_field_assigned', 'term_' . $branch->term_id);

                $group_assigned = $group_assigned?$group_assigned:array();
                if(is_array($branch_assigned) && in_array($branch -> slug, $branch_assigned)){
                    if(!$group_assigned || (!empty($group_assigned) && !in_array($tax_slug, $group_assigned))){
                        $group_assigned[]   = $tax_slug;
                        if(!empty($group_assigned)){
                            // Update group_field_assigned (field created from branch taxonomy)
                            update_field($field_key, $group_assigned, 'term_' .$branch -> term_id);
                        }
                    }
                }else{
                    if($group_assigned && !empty($group_assigned)){
                        if(in_array($tax_slug, $group_assigned)) {
                            $group_assigned = array_diff($group_assigned, array($tax_slug));
                            // Update group_field_assigned (field created from branch taxonomy)
                            update_field($field_key, $group_assigned, 'term_' .$branch -> term_id);
                        }
                    }
                }
            }
        }
    }
}