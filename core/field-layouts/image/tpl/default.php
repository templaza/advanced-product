<?php

defined('ADVANCED_PRODUCT') or exit();

if(!empty($value)){
    $url    = isset($value['url'])?$value['url']:'';
    if(!empty($url)){
?>
<img src="<?php echo $url?>" alt="<?php echo isset($value['title'])?$value['title']:''; ?>"/>
<?php }
} ?>