<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;

$file_url   = '';
if(is_array($value)){
    $file_url   = $value['url'];
}elseif(is_numeric($value)){
    $file_url   = wp_get_attachment_url($value);
}else{
    $file_url   = $value;
}
if(!empty($file_url)){

?>
<a href="<?php echo esc_attr($file_url); ?>" download><?php
echo esc_html__('Download', 'advanced-product')?></a>
<?php } ?>