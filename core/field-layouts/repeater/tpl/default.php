<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;

if(!empty($value)){
    $sub_fields = isset($field['sub_fields'])?$field['sub_fields']:[];
?>
    <ul class="uk-list">
        <?php foreach($value as $i => $sub_values){
            $sub_field  = isset($sub_fields[$i])?$sub_fields[$i]:[];
            if(is_array($sub_values)){
                foreach ($sub_values as $j => $sub_value){
            ?>
        <li><?php echo $sub_value; ?></li>
        <?php }
            }else{
                ?>
                <li><?php echo $value; ?></li>
                <?php
            }
        } ?>
    </ul>
<?php } ?>