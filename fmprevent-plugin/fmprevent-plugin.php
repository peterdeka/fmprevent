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
register_activation_hook( __FILE__, 'fmprev_db_install_data' );
add_shortcode( 'fmprevent-plugin', 'do_the_page' );

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
wp_enqueue_script('jquery-ui-autocomplete');
wp_enqueue_script('underscore');
wp_enqueue_script('backbone');
wp_enqueue_script('handlebars');
wp_enqueue_script('fmprev');
}

add_action( 'wp_enqueue_scripts','your_css_and_js');


function do_the_page(){


	echo '<div id="fmprevent"></div>';



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


?>