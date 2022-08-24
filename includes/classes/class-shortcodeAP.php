<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

class ShortCodeAP extends Base {

    public function hooks()
    {
        parent::hooks();

        add_shortcode( $this -> get_shortcode_name(), array( $this, 'render' ) );
    }

    public function get_shortcode_name(){
        $name   = $this -> get_name();
        $name   = preg_replace('/scap$/i','',$name);

        return $name;
    }

    public function render($atts){

        ob_start();
        AP_Templates::load_my_layout('shortcodes.'.$this -> get_shortcode_name(), true, false, array(
            'atts'  => $atts
        ));
        $content    = ob_get_contents();
        ob_end_clean();

        return $content;
    }

}