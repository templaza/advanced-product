<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\ACF_Taxonomy_Walker')) {

    class ACF_Taxonomy_Walker extends \acf_taxonomy_field_walker
    {
        // start_el
        function start_el(&$output, $term, $depth = 0, $args = array(), $current_object_id = 0)
        {
//            $pad = str_repeat('&nbsp;', $depth * 3);
//            $selected = in_array($category->term_id, $this->field['value']);
//            if ('make' == $category->taxonomy) {
//
//                $make1 = get_field('vehicle_type', 'make_' . $category->term_id);
//                if ($make1) {
//                    $car_type = '';
//                    if (is_object($make1)) {
//                        $car_type = $make1->slug;
//                    } else {
//                        if (is_array($make1)) {
//                            $d = 1;
//                            foreach ($make1 as $mitem) {
//                                if (is_numeric($mitem)) {
//                                    $term = get_term($mitem, 'vehicle_type');
//                                } else {
//                                    $term = get_term_by('slug', $mitem, 'vehicle_type');
//                                }
//
//                                if ($d == 1) {
//                                    $car_type = $term->slug;
//                                } else {
//                                    $car_type .= ',' . $term->slug;
//                                }
//                                $d++;
//                            }
//                        } else {
//                            $make1 = explode(',', $make1);
//                            $d = 1;
//                            foreach ($make1 as $mitem) {
//                                if (is_numeric($mitem)) {
//                                    $term = get_term($mitem, 'vehicle_type');
//                                } else {
//                                    $term = get_term_by('slug', $mitem, 'vehicle_type');
//                                }
//                                if ($d == 1) {
//                                    $car_type = $term->slug;
//                                } else {
//                                    $car_type .= ',' . $term->slug;
//                                }
//                                $d++;
//                            }
//                        }
//                    }
//                }
//
//                if (empty($make1)) {
//                    return;
//                }
//
//                $make = (object)array('slug' => $category->slug);
//            } elseif ('model' == $category->taxonomy) {
//                $make = get_field('make', 'model_' . $category->term_id);
//                if ($make) {
//                    if (is_object($make)) {
//                        $make_attr = 'data-make="' . $make->slug . '"';
//                    } else {
//                        $make_attr = '';
//                        if (is_array($make)) {
//                            $d = 1;
//                            foreach ($make as $mitem) {
//                                if (is_numeric($mitem)) {
//                                    $make_term = get_term_by('id', $mitem, 'make');
//                                } else {
//                                    $make_term = get_term_by('slug', $mitem, 'make');
//                                }
//                                if ($d == 1) {
//                                    $make_attr = $make_term->slug;
//                                } else {
//                                    $make_attr .= ',' . $make_term->slug;
//                                }
//                                $d++;
//                            }
//                        } else {
//                            $make = explode(',', $make);
//                            $d = 1;
//                            foreach ($make as $mitem) {
//                                if (is_numeric($mitem)) {
//                                    $make_term = get_term_by('id', $mitem, 'make');
//                                } else {
//                                    $make_term = get_term_by('slug', $mitem, 'make');
//                                }
//                                if ($d == 1) {
//                                    $make_attr = $make_term->slug;
//                                } else {
//                                    $make_attr .= ',' . $make_term->slug;
//                                }
//                                $d++;
//                            }
//                        }
//                    }
//                }
//
//                if (empty($make)) {
//                    return;
//                }
//            } else {
//                $make = (object)array('slug' => $category->slug);
//            }
//
//            $make = (object)array('slug' => $category->slug);
//
//            $cat_name = apply_filters('list_cats', $category->name, $category);
//
//            if ('make' == $category->taxonomy) {
//                $output .= "\t<option data-make=\"$make->slug\"  data-type=\"$car_type\" class=\"level-$depth\" value=\"" . $category->slug . "\"";
//            } elseif (('vehicle_type' == $category->taxonomy)) {
//                $output .= "\t<option data-type=\"$make->slug\" class=\"level-$depth\" value=\"" . $category->slug . "\"";
//            } else {
//                $output .= "\t<option data-make=\"$make_attr\"  class=\"level-$depth\" value=\"" . $category->slug . "\"";
//            }
//
//            if ($selected)
//                $output .= ' selected="selected"';
//            $output .= '>';
//
//            $output .= $pad . $cat_name;
//            if ($args['show_count'])
//                $output .= '&nbsp;&nbsp;(' . $category->count . ')';
//            $output .= "</option>\n";


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
//            $selected = in_array( $term->term_id, $this->field['value'] );
            $selected = in_array( $term->slug, $this->field['value'] );

            if( $this->field['field_type'] == 'checkbox' )
            {
                $output .= '<li><label class="selectit"><input type="checkbox" name="' . $this->field['name']
                    . '" value="' . $term->slug . '" ' . ($selected ? 'checked="checked"' : '')
                    .(is_admin()?implode(' ', $attribs):'') . ' /> ' . $term->name . '</label>';
            }
            elseif( $this->field['field_type'] == 'radio' )
            {
                $output .= '<li><label class="selectit"><input type="radio" name="' . $this->field['name']
                    . '" value="' . $term->slug . '" ' . ($selected ? 'checked="checkbox"' : '')
                    .(is_admin()?implode(' ', $attribs):'') . ' /> '
                    . $term->name . '</label>';
            }
            elseif( $this->field['field_type'] == 'select' )
            {
                $indent = str_repeat("&mdash; ", $depth);
                $output .= '<option value="' . $term->slug . '" '
                    . ($selected ? 'selected="selected"' : ''). ' class="level-'.$depth.'"'
                    .(is_admin()?implode(' ', $attribs):'').'>'
                    . $indent . $term->name . '</option>';
            }
        }
    }
}