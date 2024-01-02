<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

class Page extends Base {

    public static $label        = '';

    protected $menu_slug;
    protected $reg_args     = array();

    protected $parent_slug  = 'edit.php?post_type=ap_product';

    protected static $instances = array();

    public function __construct()
    {
        parent::__construct();

        if(empty(static::$label)){
            static::$label  = ucfirst($this -> get_name());
        }

        $this -> _init_page();
        $position   = isset($this -> reg_args['position'])?$this -> reg_args['position']:11;
        add_action( 'admin_menu', [ $this, 'register_menu' ], $position );
    }

    public static function instance(){
        $class_name = static::class;
        $name       = substr($class_name, strrpos($class_name, '\\')+1);
        $name       = strtolower($name);

        $instances  = &self::$instances;

        if(!isset($instances[$name])) {
            $instances[$name] = new $class_name();
        }
        return $instances[$name];
    }

    public function get_page_name(){
        $name   = $this -> get_name();

        return 'ap_'.$name;
//        return DEALERSHIP_PREFIX.'_'.$name;
    }

    public function my_page_exists(){
        // global
        global $pagenow;


        // vars
        $return = false;

        $page   = isset($_REQUEST['page'])?$_REQUEST['page']:'';

        if($page != $this-> get_page_name()){
            return $return;
        }

        // return
        return true;
    }

    public function render()
    {

        $path   = ADVANCED_PRODUCT_CORE_PATH.'/templates/pages/'.$this -> get_name().'.php';

        ob_start();
        require_once $path;
        $html   = ob_get_contents();

        return $html;
    }

    protected function _init_page(){
        // Register post type to wordpress
        if(method_exists($this, 'register')) {
            $args   = $this -> register();

            $args   = is_array($args)?$args:array();

            $this -> reg_args   = $args;

            $menu_slug  = isset($args['menu_slug'])?$args['menu_slug']:$this -> get_page_name();

            $this -> menu_slug  = $menu_slug;
        }
    }

    public function register_menu(){
        $this -> _load_page();
    }

    protected function _load_page(){
        // Register post type to wordpress
        if(method_exists($this, 'register')) {
            $args   = $this -> register();

            $args   = is_array($args)?$args:array();

            $page_title = isset($args['page_title'])?$args['page_title']:esc_html__(static::$label, 'dealership');
            $menu_title = isset($args['menu_title'])?$args['menu_title']:esc_html__(static::$label, 'dealership');
//            $menu_slug  = isset($args['menu_slug'])?$args['menu_slug']:$this -> get_page_name();
//            $this -> menu_slug  = $menu_slug;
            $capability = isset($args['capability'])?$args['capability']:'manage_options';
            $callback   = isset($args['callback'])?$args['callback']:array($this, 'render');
            $position   = isset($args['position'])?$args['position']:null;

            \add_submenu_page($this -> parent_slug,
                $page_title, $menu_title, $capability,
                $this -> menu_slug, $callback, $position);

            do_action('dealership/page/'.$this -> get_page_name().'/registered', $this -> menu_slug, $this);
        }
    }

    protected function register(){
        return array();
    }
}