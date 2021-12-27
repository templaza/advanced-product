<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Helper\AP_Custom_Field_Helper;
?>
<form role="search" method="get" action="<?php echo esc_url($action); ?>" class="uk-form-stacked advanced-product-search-form">
    <p class="field field-keyword">
        <label><b><?php _e( 'Keyword:', 'progression-car-dealer' ) ?></b></label><br>
        <input type="search" class="search-field" placeholder="<?php _e( 'Search ...', 'progression-car-dealer' ) ?>" value="<?php echo get_query_var('s') ?>" name="s" />
    </p>
    <?php if(!empty($fields)){
        foreach ($fields as $acf_field){
//            $field  = $acf_f_attr  = AP_Custom_Field_Helper::get_custom_field_option_by_id($acf_field -> ID);
            $field  = $acf_f_attr  = AP_Custom_Field_Helper::get_custom_field_option_by_id($acf_field -> ID);

            $field['name']  = 'field['.$field['name'].']';

//            var_dump($acf_f_attr); die(__METHOD__);
//            var_dump($acf_f_attr); die(__FILE__);
//            if(isset($field['s_choices'])){
//                $field['choices']  = $acf_f_attr['s_choices'];
//            }
//            if(isset($field['s_default_value'])){
//                $field['default_value']  = $acf_f_attr['s_default_value'];
//            }


            $s_field_type = isset($field['s_type'])?$field['s_type']:'';
            $s_field_type = (empty($s_field_type) && isset($field['type']))?$field['type']:$s_field_type;

            if(isset($field['s_type'])){
                if(isset($field['field_type'])){
                    $field['field_type'] = $acf_f_attr['s_type'];
                }else {
                    $field['type'] = $acf_f_attr['s_type'];
                }
            }

            $file_path  = ADVANCED_PRODUCT_CORE_PATH.'/field-layouts/'.$s_field_type.'/'.$s_field_type.'.php';

            if(file_exists($file_path)){
//                $f_class_name   = 'Advanced_Product\Field\Layout\\'.ucfirst($s_field_type);
//                if(!class_exists($f_class_name)){
//                    require_once $file_path;
//                }
//                $f_layout   = new $f_class_name($field);
//
//                if($f_layout && method_exists($f_layout, 'render_form')){
//                    $f_layout -> render_form();
//                }
                do_action('advanced-product/field/create_form/type='.$s_field_type, $field);
            }else {
                ?>
                <div class="uk-margin">
                    <label class="uk-form-label" for="form-s-color"><?php echo $field['label']; ?></label>
                    <div class="uk-form-controls">
                       <?php do_action('acf/create_field', $field); ?>
                    </div>

                </div>
                <?php
            }

//            if($field['type'] == 'select' || (isset($field['field_type']) && $field['field_type'] == 'select')){
//                if(!$field['multiple']){
//                    $field['allow_null']    = false;
//                }
//                unset($field['multiple']);
////                            var_dump($field); die();
//            }
////                        var_dump($field); die();
//
//            // set value
//            if( !isset($field['value']) )
//            {
//                $field['value'] = apply_filters('acf/load_value', false, $group['id'], $field);
//                $field['value'] = apply_filters('acf/format_value', $field['value'], $group['id'], $field);
//            }
//
//            // required
//            $required_class = "";
//            $required_label = "";
//
//            if( $field['required'] )
//            {
//                $required_class = ' required';
//                $required_label = ' <span class="required">*</span>';
//            }
//
//            $fake_name = $field['key'];
//            echo '<div id="acf-' . $field['name'] . '" class="uk-margin field field_type-' . $field['type']
//                . ' field_key-' . $field['key'] . $required_class . '" data-field_name="' . $field['name']
//                . '" data-field_key="' . $field['key'] . '" data-field_type="' . $field['type'] . '">';
//
//            echo '<label for="' . $field['id'] . '" class="uk-form-label uk-display-inline-block" data-uk-tooltip="'
//                .$field['instructions'].'">' . $field['label'] . $required_label . '</label>';
////                        echo $field['instructions'];
//
//            $field['name'] = 'fields[' . $field['key'] . ']';
//
//            $field_type = isset($field['field_type'])?$field['field_type']:'';
//            $field_type = (empty($field_type) && isset($field['type']))?$field['type']:$field_type;
//
//            $file_path  = ADVANCED_PRODUCT_CORE_PATH.'/field-layouts/'.$field_type.'/'.$field_type.'.php';
//
////                        $field['name']  = 'fields['.$field['_name'].']';
//            $field['name']  = $field['_name'];
//            if(file_exists($file_path)){
//                $f_class_name   = 'Advanced_Product\Field\Layout\\'.ucfirst($field_type);
//                if(!class_exists($f_class_name)){
//                    require_once $file_path;
//                }
//                $f_layout   = new $f_class_name($field, $group);
//
//                if($f_layout && method_exists($f_layout, 'render_form')){
//                    $f_layout -> render_form();
//                }
//            }else{
//                echo '<div class="uk-form-controls">';
//                do_action('acf/create_field', $field, $group['id']);
//                echo '</div>';
//            }
//
//            echo '</div>';
        ?>

    <?php }  } ?>
    <input type="hidden" name="post_type" value="ap_product">
    <button class="car-search-submit uk-button uk-margin-top" id="car-search-submit">Search</button>
</form>
