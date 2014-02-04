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
	global $wpdb;
	$result = $wpdb->get_results("select * from ".$wpdb->prefix . "fmprev_cable_types");
	
	echo '<script type="text/javascript"> cable_types=[';
	foreach ( $result as $r ) 
	{
  		echo '"'.$r->sigla.'",';
  	}	 
	echo ']</script>';
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

/*ADMIN */
/** Step 2 (from text above). */
add_action( 'admin_menu', 'fmprevent_menu' );

/** Step 1. */
function fmprevent_menu() {
	add_menu_page( 'Preventivatore FMGroup', 'Preventivatore FM', 'manage_options', 'fmprevent_admin', 'fmprevent_options' );
}

/** Step 3. */
function fmprevent_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<h2>Preventivatore cavi FMGroup</h2>';
	echo '<p>Da qui puoi gestire i modelli di cavo disponibili per gli utenti.</p>';
	echo '</div>';
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    		$error = false;
    		global $wpdb;
   		$table_name = $wpdb->prefix . "fmprev_cable_types";
    		
		if($wpdb->insert( $table_name, array( 'sigla' => $_POST['sigla'] ) )>0){
			echo '<p style="background-color:white;color:green;font-weight:Bold">Nuovo elemento con sigla '.$_POST['sigla'].' inserito con successo.</p>';
		}
		else{
			echo '<p style="background-color:white;color:red;font-weight:Bold">Errore durante l\'inserimento del nuovo elemento.</p>';
		}
    	}


/*LISTA cavi inseriti*/
	echo '<div class="wrap"><h3>Cavi inseriti</h3><table><tr><th>ID</th><th>Sigla</th></tr>';
	global $wpdb;
	$table_name = $wpdb->prefix . "fmprev_cable_types";
	$result = $wpdb->get_results("select * from ".$wpdb->prefix . "fmprev_cable_types");
	
	foreach ( $result as $r ) 
	{
		echo '<tr><td>'.$r->id.'</td>';
  		echo '<td>'.$r->sigla.'</td></tr>';
  	}	
	echo '</table></div>';
	echo '<div class="wrap"><h3>Aggiungi cavo</h3>';
	$email_form = '<form method="post" action="' . get_permalink() . '">
    <div>
        <label for="sigla">Sigla nuovo cavo:</label>
        <input type="text" name="sigla" id="sigla" size="50" maxlength="50" />
    </div>
      <div>
        <input type="submit" value="Aggiungi" name="send" id="new_send" />
    </div>
</form>';

	echo $email_form;
	echo '</div>';
}
?>