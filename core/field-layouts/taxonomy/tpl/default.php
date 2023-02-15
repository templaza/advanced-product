<?php

defined('ADVANCED_PRODUCT') or exit();

$term   = null;
$html   = '';
if(is_object($value)){
    $term   = $value;
}elseif(is_array($value)){
    foreach ($value as $val){
        if(is_numeric($val)) {
            $term   = get_term_by('term_id', $val, $field['taxonomy']);
        }else{
            $term   = get_term_by('slug', $val, $field['taxonomy']);
        }
        ?>
        <a href="<?php echo get_term_link($term); ?>"><?php echo $term -> name; ?></a>
        <?php
    }
}elseif(is_numeric($value)){
    $term   = get_term_by('term_id', $value, $field['taxonomy']);
}else{
    $term   = get_term_by('slug', $value, $field['taxonomy']);
}

if(!is_array($value) && !empty($term) && !is_wp_error($term)) {
    ?>
    <a href="<?php echo get_term_link($term); ?>"><?php echo $term->name;?></a>
<?php
}