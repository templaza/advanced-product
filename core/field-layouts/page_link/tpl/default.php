<?php

defined('ADVANCED_PRODUCT') or exit();

if(!empty($value)){
    if(is_array($value)){
        foreach ($value as $url) {
            ?>
            <a href="<?php echo esc_attr($url); ?>"><?php
                echo $url; ?></a>
            <?php
        }
    }else {
        ?>
        <a href="<?php echo esc_attr($value); ?>"><?php
            echo $value; ?></a>
        <?php
    }
} ?>