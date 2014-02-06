<?php

if(is_admin()){
        add_action( 'wp_ajax_add_order', 'add_order' );
        add_action( 'wp_ajax_nopriv_add_order', 'add_order' );
        add_action( 'wp_ajax_get_cabletypes', 'get_cable_types' );
        add_action( 'wp_ajax_add_cabletypes', 'add_cable_type' );
		add_action( 'wp_ajax_del_cabletypes', 'del_cable_type' );
		add_action( 'wp_ajax_get_orders', 'get_orders' );
}


function add_order(){
        $p=$_POST;
        $order=json_decode(str_replace('\\','',$p['order']), true);
        if($order == null) { echo "Data = " . $p['order']; }

        echo 'nuovo ordine';
        echo 'tipo cavo: '.$order['type'];
        die();
}

function get_cable_types(){
        
		global $wpdb;
		$table_name = $wpdb->prefix . "fmprev_cable_types";
		$result = $wpdb->get_results("select * from ".$table_name);
		$jstr='{"cabletypes":[';

		foreach ( $result as $r ) 
		{

			$jstr.='{"id":'.$r->id.',"sigla":"'.$r->sigla.'"},';
			
		}
		if(count($result)>0)	
			$jstr = substr_replace($jstr, ']', -1, strlen($jstr));
		else
			$jstr.=']';
		$jstr.='}';
		echo json_encode($jstr);
        die();
}

function add_cable_type(){
        $p=$_POST;
        $error = false;
       	//TODO QUOTES
       	$newtype=$p['newtype'];
		global $wpdb;
		$table_name = $wpdb->prefix . "fmprev_cable_types";
		$result = $wpdb->get_results("select * from ".$table_name. ' WHERE sigla = "'.$newtype.'"');
		if($result != NULL){
			echo 'AX';
			die();
		}
		if($wpdb->insert( $table_name, array( 'sigla' => $newtype ) )>0){
			echo 'OK';
		}
		else{
			echo 'NO';
		}
        die();
}

function del_cable_type(){
        $p=$_POST;
        $error = false;
       	//TODO QUOTES
       	$type=$p['type'];
		global $wpdb;
		$table_name = $wpdb->prefix . "fmprev_cable_types";
		$res=$wpdb->query('DELETE FROM '.$table_name.' WHERE sigla = "'.$type.'"');

		if($res>0){
			echo 'OK';
		}
		else{
			echo 'NO';
		}
        die();
}


function get_orders(){
        
		global $wpdb;
		$table_name = $wpdb->prefix . "fmprev_cable_orders";
		$result = $wpdb->get_results("select * from ".$table_name);
		$jstr='{"orders":[';

		foreach ( $result as $r ) 
		{

			$jstr.='{"id":'.$r->id.',"name":"'.$r->name.'"},';
			
		}
		if(count($result)>0)	
			$jstr = substr_replace($jstr, ']', -1, strlen($jstr));
		else
			$jstr.=']';
		$jstr.='}';
		echo json_encode($jstr);
        die();
}

?>