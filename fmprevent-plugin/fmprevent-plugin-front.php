<?php


if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	echo $_POST;



}

	global $wpdb;
	$result = $wpdb->get_results("select * from ".$wpdb->prefix . "fmprev_cable_types");
	
	echo '<script type="text/javascript"> cable_types=[';
	foreach ( $result as $r ) 
	{
  		echo '"'.$r->sigla.'",';
  	}	 
	echo ']</script>';
	echo '<div id="fmprevent"></div>';




?>