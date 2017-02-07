<?php
/**
 * Slim Twig Component.
 * 
 * @link https://github.com/bhubr/... for the canonical source repository
 *
 * @copyright Copyright (c) 2017 Benoit Hubert
 */

// use Twig_Extension;
// use Twig_SimpleFunction;

class Twig_WordPress_Widget extends Twig_Extension
{
    /**
     * @var Singleton
     * @access private
     * @static
     */
    private static $_instance = null;

    /**
     * Wordpress WP_Widget instance
     */
    private $_widget;

    /**
     * Constructor
     *
     * @param void
     * @return void
     */
    private function __construct( $widget ) {
        $this->_widget = $widget;
    }

    public static function get_instance( $widget ) {
    
        if(is_null(self::$_instance)) {
            self::$_instance = new Twig_WordPress_Widget( $widget );
        }
        return self::$_instance;
    }


    /**
     * Extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'twig-wordpress-widget';
    }

    /**
     * Callback for twig.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('fieldId', [$this, 'get_field_id'], ['is_safe' => ['html'] ] ),
            new Twig_SimpleFunction('fieldName', [$this, 'get_field_name'], ['is_safe' => ['html'] ] ),
            new Twig_SimpleFunction('checked', [$this, 'checked'], ['is_safe' => ['html'] ] ),
            new Twig_SimpleFunction('selected', [$this, 'selected'], ['is_safe' => ['html'] ] ),
        ];
    }

    /**
     *
     * @param string $id field name
     *
     * @return string field ID
     */
    public function get_field_id( $name ) {
        return $this->_widget->get_field_id( $name );
    }

    /**
     *
     * @param string $name field name
     *
     * @return string field name
     */
    public function get_field_name( $name ) {
       return  $this->_widget->get_field_name( $name );
    }

    /**
     *
     * @param string $name field name
     *
     * @return string checked attr
     */
    public function checked( $attribute, $value ) {
       return checked( $attribute, $value, false);
    }

    /**
     *
     * @param string $name field name
     *
     * @return string selected attr
     */
    public function selected( $attribute, $value ) {
//        return ' selected="selected"';
       return selected( $attribute, $value, false);
    }
}
