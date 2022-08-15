<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\ACF_Taxonomy_Walker')) {

    class ACF_Taxonomy_Walker extends \acf_taxonomy_field_walker
    {
        // start_el
        function start_el(&$output, $term, $depth = 0, $args = array(), $current_object_id = 0)
        {
            $attribs        = array();
            $field          = $this -> field;
            $associate      = '';
            $associate_from   = '';

            if(isset($field['_name']) && $field['_name'] == 'ap_category'){
                $associate_from   = 'ap_branch';
            }elseif($field['_name'] != 'ap_branch'){
                // Get custom categories (custom post) by slug
                global $wpdb;
                $post_id_of = $wpdb->get_var( 'select post_id from '.$wpdb->postmeta.' where'
                    .' meta_key="slug" AND meta_value = "'.$field['taxonomy'].'"' );

                $associate_from = \get_field('associate_to', $post_id_of);

            }


            if(!empty($associate_from)){
//                $f_associate    = get_field($associate_to, $field['_name'].'_'.$term -> term_id.'_'.$associate_to);
                $associate  = \get_field($associate_from, $field['taxonomy'].'_'.$term -> term_id);
                if(is_array($associate) && count($associate)){
                    $associate  = implode(' ', $associate);
                }

                if(!empty($associate)){
                    $attribs[]  = 'data-associate-from="'.esc_attr($associate_from).'"';
                    $attribs[]  = 'data-associate="'.esc_attr($associate).'"';
                }
            }


            // vars
            $is_numeric = false;
            if(is_array($field['value']) && count($field['value'])){
                $index  = array_key_first($field['value']);
                $is_numeric = is_numeric($field['value'][$index]);
                if($is_numeric) {
                    $selected = in_array($term->term_id, $field['value'] );
                }else {
                    $selected = in_array($term->slug, $field['value']);
                }
            }else{
                $is_numeric = is_numeric($field['value']);
                if($is_numeric) {
                    $selected = $term->term_id == $field['value'];
                }else {
                    $selected = $term->slug == $field['value'];
                }
            }

            if( $this->field['field_type'] == 'checkbox' )
            {
                $output .= '<li><label class="selectit"><input type="checkbox" name="' . $this->field['name']
                    . '" value="' . $term->slug . '" ' . ($selected ? 'checked="checked"' : '')
                    .(!empty($attribs)?implode(' ', $attribs):'') . ' /> ' . $term->name . '</label>';
            }
            elseif( $this->field['field_type'] == 'radio' )
            {
                $output .= '<li><label class="selectit"><input type="radio" name="' . $this->field['name']
                    . '" value="' . $term->slug . '" ' . ($selected ? 'checked="checkbox"' : '')
                    .(!empty($attribs)?implode(' ', $attribs):'') . ' /> '
                    . $term->name . '</label>';
            }
            elseif( $this->field['field_type'] == 'select' )
            {
                $indent = str_repeat("&mdash; ", $depth);
                $output .= '<option value="' . $term->slug . '" '
                    . ($selected ? 'selected="selected"' : ''). ' class="level-'.$depth.'"'
                    .(!empty($attribs)?implode(' ', $attribs):'').'>'
                    . $indent . $term->name . '</option>';
            }
        }
    }
}