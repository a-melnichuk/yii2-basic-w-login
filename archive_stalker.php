<?php
/*
 *	Plugin Name: Archive Stalker
 *	Plugin URI: http://a-melnichuk-projects.hol.es/
 *	Description: Provides eased browsing of archived data
 *	Version: 1.0
 *	Author: Alex Melnichuk
 *	Author URI: http://a-melnichuk-projects.hol.es/
 *	License: GPL2
 *
*/
require_once('widget.php');

class Archive_Stalker
{
    
   public function __construct() {
       $this->add_actions();
   } 
    
    function add_widget()
    {
        register_widget( 'Archive_Stalker_Widget' );
    }
    
    function add_scripts()
    {
        wp_enqueue_style( 'archive_stalker_frontend_css', ARST_PLUGIN_URL.'/archive_stalker.css' );
        wp_register_script( "archive_stalker", ARST_PLUGIN_URL.'/archive_stalker.js', array('jquery') );
        wp_localize_script( 'archive_stalker', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),  ));        
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-draggable' );
        wp_enqueue_script( 'archive_stalker' );
    }
    
    public function add_actions()
    {
        register_activation_hook(__FILE__ , array( 'Archive_Stalker_Widget','activate') );
        add_action( 'init', array($this,'add_scripts') );
        add_action( 'widgets_init' , array($this,'add_widget') );
    }
}
$arst = new Archive_Stalker();