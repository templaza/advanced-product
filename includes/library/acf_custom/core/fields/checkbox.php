<?php

class acf_field_checkbox extends acf_field
{

	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'checkbox';
		$this->label = __("Checkbox",'acf');
		$this->category = __("Choice",'acf');
		$this->defaults = array(
			'layout'		=>	'vertical',
			'choices'		=>	array(),
			'default_value'	=>	'',
		);
		
		
		// do not delete!
    	parent::__construct();
	}
		
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// value must be array
		if( !is_array($field['value']) )
		{
			// perhaps this is a default value with new lines in it?
			if( strpos($field['value'], "\n") !== false )
			{
				// found multiple lines, explode it
				$field['value'] = explode("\n", $field['value']);
			}
			else
			{
				$field['value'] = array( $field['value'] );
			}
		}
		
		
		// trim value
		$field['value'] = array_map('trim', $field['value']);
		
		
		// vars
		$i = 0;
		$e = '<input type="hidden" name="' .  esc_attr($field['name']) . '" value="" />';
		$e .= '<ul class="acf-checkbox-list ' . esc_attr($field['class']) . ' ' . esc_attr($field['layout']) . '">';
		
		
		// checkbox saves an array
		$field['name'] .= '[]';
		
		
		// foreach choices
		foreach( $field['choices'] as $key => $value )
		{
			// vars
			$i++;
			$atts = '';
			
			
			if( in_array($key, $field['value']) )
			{
				$atts = 'checked="yes"';
			}
			if( isset($field['disabled']) && in_array($key, $field['disabled']) )
			{
				$atts .= ' disabled="true"';
			}
			
			
			// each checkbox ID is generated with the $key, however, the first checkbox must not use $key so that it matches the field's label for attribute
			$id = $field['id'];
			
			if( $i > 1 )
			{
				$id .= '-' . $key;
			}
			
			$e .= '<li><label><input id="' . esc_attr($id) . '" type="checkbox" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['name']) . '" value="' . esc_attr($key) . '" ' . $atts . ' />' . $value . '</label></li>';
		}
		
		$e .= '</ul>';
		
		
		// return
		echo $e;
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// vars
		$key = $field['name'];
		
		
		// implode checkboxes so they work in a textarea
        $ap_product_type_value = array();
        if(get_the_excerpt(get_the_ID())=='ap_product_type'){
            if( is_array($field['choices']) ){
                $ap_product_type_value = $field['choices'];
            }
            $new_value_choice = array();
            if(isset($field['sale']) && $field['sale'] !=''){
                $new_value_choice['sale'] = $field['sale'];
            }else{
                if(isset($field['sale'])){
                    $new_value_choice['sale'] = $field['sale'];
                }else{
                    $new_value_choice['sale'] = __('Sale','advanced-product');
                }
            }
            if(isset($field['rental']) && $field['rental'] !=''){
                $new_value_choice['rental'] = $field['rental'];
            }else{
                if(isset($field['rental'])){
                    $new_value_choice['rental'] = $field['rental'];
                }else{
                    $new_value_choice['rental'] = __('Rental','advanced-product');
                }
            }
            if(isset($field['contact']) && $field['contact'] !=''){
                $new_value_choice['contact'] = $field['contact'];
            }else{
                if(isset($field['contact'])){
                    $new_value_choice['contact'] = $field['contact'];
                }else{
                    $new_value_choice['contact'] = __('Contact','advanced-product');
                }
            }
            if(isset($field['sold']) && $field['sold'] !=''){
                $new_value_choice['sold'] = $field['sold'];
            }else{
                if(isset($field['sold'])){
                    $new_value_choice['sold'] = $field['sold'];
                }else{
                    $new_value_choice['sold'] = __('Sold','advanced-product');
                }
            }
        }
		if( is_array($field['choices']) )
		{		
			foreach( $field['choices'] as $k => $v )
			{
				$field['choices'][ $k ] = $k . ' : ' . $v;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}
		
		?>
        <?php

        if(get_the_excerpt(get_the_ID())=='ap_product_type'){
        ?>
            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label for=""><?php _e("Change values",'advanced-product'); ?></label>
                </td>
                <td>
                    <?php
                    foreach( $new_value_choice as $k => $v )
                    {
                        ?>
                        <div class="uk-flex uk-flex-middle uk-margin-small-bottom">
                            <span class="uk-width-1-6"><?php echo esc_attr($k)?></span>
                            <input class="uk-width-5-6" type="text" name="fields[<?php echo $key;?>][<?php echo esc_attr($k)?>]" value="<?php echo esc_attr($v);?>"/>
                        </div>
                        <?php
                    }
                    do_action('acf/create_field', array(
                        'type'	=>	'textarea',
                        'class' => 	'textarea field_option-choices uk-hidden',
                        'name'	=>	'fields['.$key.'][choices]',
                        'value'	=>	$field['choices'],
                    ));
                    ?>
                </td>
            </tr>
        <?php
        }else{
        ?>

<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Choices",'acf'); ?></label>
		<p><?php _e("Enter each choice on a new line.",'acf'); ?></p>
		<p><?php _e("For more control, you may specify both a value and label like this:",'acf'); ?></p>
		<p><?php _e("red : Red",'acf'); ?><br /><?php _e("blue : Blue",'acf'); ?></p>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'	=>	'textarea',
			'class' => 	'textarea field_option-choices',
			'name'	=>	'fields['.$key.'][choices]',
			'value'	=>	$field['choices'],
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Default Value",'acf'); ?></label>
		<p class="description"><?php _e("Enter each default value on a new line",'acf'); ?></p>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'	=>	'textarea',
			'name'	=>	'fields['.$key.'][default_value]',
			'value'	=>	$field['default_value'],
		));
		
		?>
	</td>
</tr>
        <?php
        }
        ?>

<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Layout",'acf'); ?></label>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][layout]',
			'value'	=>	$field['layout'],
			'layout' => 'horizontal', 
			'choices' => array(
				'vertical' => __("Vertical",'acf'), 
				'horizontal' => __("Horizontal",'acf')
			)
		));
		
		?>
	</td>
</tr>
		<?php
		
	}
	
}

new acf_field_checkbox();

?>