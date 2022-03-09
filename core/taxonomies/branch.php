<?php

namespace Advanced_Product\Taxonomy;

use Advanced_Product\Taxonomy;

defined('ADVANCED_PRODUCT') or exit();

class Branch extends Taxonomy {

    public function hooks()
    {
        parent::hooks(); // TODO: Change the autogenerated stub

        add_action( 'admin_menu', array( $this, 'remove_taxonomy_metaboxes' ) );
        add_action( 'saved_'.$this ->get_taxonomy_name(), array($this,'saved_taxonomy'), 10, 2 );
//        add_action( 'edited_'.$this ->get_taxonomy_name(), array($this,'edited_taxonomy'), 10, 2 );
    }

    public function register(){

        $singular  = __( 'Branch', $this -> text_domain );
        $plural    = __( 'Branches', $this -> text_domain );

        return array(
            'object_type'   => 'ap_product',
            'args'          => array(
                'label' 					=> $plural,
                'labels' => array(
                    'name' 					=> $singular,
                    'singular_name' 		=> $singular,
                    'menu_name'				=> $plural,
                    'search_items' 			=> sprintf( __( 'Search %s', $this -> text_domain ), $plural ),
                    'all_items' 			=> sprintf( __( 'All %s', $this -> text_domain ), $plural ),
                    'parent_item' 			=> sprintf( __( 'Parent %s', $this -> text_domain ), $singular ),
                    'parent_item_colon'		=> sprintf( __( 'Parent %s:', $this -> text_domain ), $singular ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', $this -> text_domain ), $singular ),
                    'update_item' 			=> sprintf( __( 'Update %s', $this -> text_domain ), $singular ),
                    'add_new_item' 			=> sprintf( __( 'Add New %s', $this -> text_domain ), $singular ),
                    'new_item_name' 		=> sprintf( __( 'New %s Name', $this -> text_domain ),  $singular ),
                    'not_found' 		    => sprintf( __( 'No %s found.', $this -> text_domain ),  $plural ),
                ),
                'hierarchical'               => true,
                'show_admin_column'          => true,
                'exclude_from_search'        => true,
//                'query_var'                 => false,
            )
        );
    }

    public function __get_core_fields(){
        $fields = parent::__get_core_fields();

        $fields[]   = array(
                'key'       => 'field_'.md5($this -> get_taxonomy_name().'__group_field'),
                'label'     => __('Group Fields Assignment', $this->text_domain),
                'name'      => 'group_field_assigned',
                'type'      => 'taxonomy',
                'taxonomy'  => 'ap_group_field',
                'load_save_terms'  => false,
                'group'     => $this -> __get_core_field_group_id()
        );
//        $fields = array(
//            array(
//                'key' => 'field_'.md5($this -> get_taxonomy_name()),
//                'label' => __('Image', $this->text_domain),
//                'name' => 'image',
//                'type' => 'image',
//                'default_value' => '',
//                'group' => $this -> __get_core_field_group_id()
//            )
//        );

        return apply_filters('advanced-product/'.$this -> get_taxonomy_name().'/fields/create', $fields);
    }

    public function saved_taxonomy($term_id, $tt_id ){
        $term       = get_term( $term_id );
        $tax_slug   = $term->slug;

        $group_field_assigned    = \get_field('group_field_assigned', $this -> get_taxonomy_name().'_'.$term_id);

        $group_field_taxs    = get_terms(array(
            'taxonomy'      => 'ap_group_field',
            'hide_empty'    => false,
        ));

        if(!is_wp_error($group_field_taxs) && !empty($group_field_taxs)){
            $field_key  = 'field_'.md5('ap_group_field__branch');
            foreach($group_field_taxs as $group_field){
                $branch_assigned    = \get_field('branch_assigned', 'ap_group_field_' . $group_field->term_id);
                $branch_assigned    = $branch_assigned?$branch_assigned:array();

                if(is_array($group_field_assigned) && in_array($group_field -> slug, $group_field_assigned)){
                    if(!$branch_assigned || (!empty($branch_assigned) && !in_array($tax_slug, $branch_assigned))){
                        $branch_assigned[]   = $tax_slug;
                        if(!empty($branch_assigned)){
                            // Update branch_assigned (field created from group field taxonomy)
                            update_field($field_key, $branch_assigned, 'ap_group_field_'.$group_field -> term_id);
                        }
                    }
                }else{
                    if($branch_assigned && !empty($branch_assigned)){

                        if(in_array($tax_slug, $branch_assigned)) {
                            $branch_assigned = array_diff($branch_assigned, array($tax_slug));
                        }else{
                            $branch_assigned[]   = $tax_slug;
                        }
                        // Update branch_assigned (field created from group field taxonomy)
                        update_field($field_key, $branch_assigned, 'ap_group_field_'.$group_field -> term_id);
                    }
                }
            }
        }
    }

//    public function edited_taxonomy($term_id, $tt_id ){
//        $term       = get_term( $term_id );
//        $tax_slug   = $term->slug;
//
//        $group_field_assigned    = \get_field('group_field_assigned', $this -> get_taxonomy_name().'_'.$term_id);
//
//        $group_field_taxs    = get_terms(array(
//            'taxonomy'      => 'ap_group_field',
//            'hide_empty'    => false,
//        ));
//
//        if(!is_wp_error($group_field_taxs) && !empty($group_field_taxs)){
//            $field_key  = 'field_'.md5('ap_group_field__branch');
//            foreach($group_field_taxs as $group_field){
//                $branch_assigned    = \get_field('branch_assigned', 'ap_group_field_' . $group_field->term_id);
//                $branch_assigned    = $branch_assigned?$branch_assigned:array();
//                if(is_array($group_field_assigned) && in_array($group_field -> slug, $group_field_assigned)){
//                    if(!$branch_assigned || (!empty($branch_assigned) && !in_array($tax_slug, $branch_assigned))){
//                        $branch_assigned[]   = $tax_slug;
//                        if(!empty($branch_assigned)){
//                            // Update group_field_assigned (field created from branch taxonomy)
//                            update_field($field_key, $branch_assigned, 'ap_group_field_' .$group_field -> term_id);
//                        }
//                    }
//                }else{
//                    if($branch_assigned && !empty($branch_assigned) && in_array($tax_slug, $branch_assigned)){
//                        $branch_assigned = array_diff($branch_assigned, array($tax_slug));
//                        // Update group_field_assigned (field created from branch taxonomy)
//                        update_field($field_key, $branch_assigned, 'ap_group_field_' .$group_field -> term_id);
//                    }
//                }
//            }
//        }
//    }

    public function manage_edit_columns($columns){
        $pos            = array_search('name', array_keys($columns)) + 1;
        $new_columns    = array('group_field_assigned' => __('Group Fields Assigned', $this -> text_domain));

        return array_merge(
            array_slice($columns, 0, $pos),
            $new_columns,
            array_slice($columns, $pos)
        );

    }

    public function manage_custom_column($content, $column, $term_id ){
        if($column == 'group_field_assigned'){
            $fval   = get_field( $column, $this -> get_taxonomy_name().'_'.$term_id );

            if(!empty($fval) && count($fval)){
                foreach($fval as $i => $slug){
                    $term   = get_term_by('slug', $slug, 'ap_group_field');
                    if(!is_wp_error($term) && !empty($term)){
                        $content    .= '<a href="term.php?taxonomy=ap_group_field&post_type=ap_product&tag_ID='
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

    /**
     * Removes the default taxonomy metaboxes from the edit screen.
     * We use the advanced custom fields instead and sync the data.
     */
    public function remove_taxonomy_metaboxes(){
        /* Remove meta box is tag */
        \remove_meta_box( 'tagsdiv-'.$this -> get_taxonomy_name(), 'ap_product', 'normal' );
        /* Remove meta box is category */
        \remove_meta_box( $this -> get_taxonomy_name().'div', 'ap_product', 'normal' );
    }
}