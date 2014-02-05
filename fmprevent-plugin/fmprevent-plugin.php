<?php
/*
Plugin Name: fmprevent-plugin
Version: 0.1
Author: Wannaup srls
License: GPL2
*/

global $fmprev_db_version;
$fmprev_db_version = "1.0";

register_activation_hook( __FILE__, 'fmprev_db_install' );
//register_activation_hook( __FILE__, 'fmprev_db_install_data' );
add_shortcode( 'fmprevent-plugin', 'do_the_page' );


function do_the_page(){

  require 'fmprevent-plugin-front.php';
}

function fmprev_db_install() {
   global $wpdb;
   global $fmprev_db_version;

   $table_name = $wpdb->prefix . "fmprev_cable_types";
      
   $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  sigla VARCHAR(55) DEFAULT '' NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "fmprev_db_version", $fmprev_db_version );
}

function fmprev_db_install_data() {
   global $wpdb;
   
   $table_name = $wpdb->prefix . "fmprev_cable_types";
   $rows_affected = $wpdb->insert( $table_name, array( 'sigla' => 'HEF-3' ) );
   $rows_affected = $wpdb->insert( $table_name, array( 'sigla' => 'HEF-4' ) );
   $rows_affected = $wpdb->insert( $table_name, array( 'sigla' => 'HEF-5' ) );
}

/*ADMIN */
/** Step 2 (from text above). */
add_action( 'admin_menu', 'fmprevent_menu' );

/** Step 1. */
function fmprevent_menu() {
  global $pw_settings_page;
  $pw_settings_page = add_menu_page( 'Preventivatore FMGroup', 'Preventivatore FM', 'manage_options', 'fmprevent_admin', 'fmprevent_options' );
}

/** Step 3. */
function fmprevent_options() {

  require 'fmprevent-plugin-admin.php';
}


if (!is_admin())
  add_action( 'wp_enqueue_scripts','your_css_and_js');
else
  add_action( 'admin_enqueue_scripts','adminjs');

require_once 'ajax-actions.php';

function your_css_and_js() {
wp_register_style('fmprev_prev', plugins_url('prev.css',__FILE__ ));
wp_register_style('fmprev_jqui', 'https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');

wp_enqueue_style('fmprev_prev');
wp_enqueue_style('fmprev_jqui');
//wp_register_script('jquijs', "https://code.jquery.com/ui/1.10.4/jquery-ui.js",null,NULL,true);
//wp_register_script( 'underscore', plugins_url('js/underscore-min.js',__FILE__ ),false,NULL,true);
//wp_register_script( 'backbone', plugins_url('js/backbone-min.js',__FILE__ ),false,NULL,true);
wp_register_script( 'handlebars', plugins_url('js/handlebars.js',__FILE__ ),false,NULL,true);
wp_register_script( 'fmprev', plugins_url('js/script.js',__FILE__ ),false,NULL,true);
wp_localize_script( 'fmprev', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
wp_enqueue_script('jquery-ui-autocomplete');
wp_enqueue_script('underscore');
wp_enqueue_script('backbone');
wp_enqueue_script('handlebars');
wp_enqueue_script('fmprev');
}

function adminjs($hook){
global $pw_settings_page;
 if( $pw_settings_page != $hook )
        return;
wp_register_style('bootstrapcss', plugins_url('css/bootstrap.min.css',__FILE__ ));

wp_enqueue_style('bootstrapcss');

  wp_enqueue_script('jquery');
wp_register_script('bootstrapjs', plugins_url('js/bootstrap.min.js',__FILE__ ),null,NULL,true);
wp_register_script('datatables', plugins_url('js/dataTables.min.js',__FILE__ ),array( 'jquery'),NULL,true);
wp_register_script('dtpaging', plugins_url('js/tablepaging.js',__FILE__ ),null,NULL,true);
  wp_enqueue_script('bootstrapjs');
    wp_enqueue_script('datatables');
wp_enqueue_script('dtpaging');

}

?>