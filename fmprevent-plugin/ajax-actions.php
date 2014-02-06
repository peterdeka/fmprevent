<?php

if(is_admin()){
	add_action( 'wp_ajax_add_order', 'add_order' );
	add_action( 'wp_ajax_nopriv_add_order', 'add_order' );
	add_action( 'wp_ajax_get_cabletypes', 'get_cable_types' );
	add_action( 'wp_ajax_add_cabletypes', 'add_cable_type' );
	add_action( 'wp_ajax_del_cabletypes', 'del_cable_type' );
	add_action( 'wp_ajax_get_orders', 'get_orders' );
	add_action( 'wp_ajax_get_order_info', 'get_order_info' );
}

function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function add_order(){
	$p=$_POST;

	if (empty($_POST["orderinfo"]))
		{echo 'NO1'; die();}
	$oinfo=$_POST['orderinfo'];

	$sql=array();

	if (empty($oinfo["order_name"]))
		{echo 'NO2'; die();}
	else
		{$sql['name'] = test_input($oinfo["order_name"]);}

	if (empty($oinfo["order_mail"]))
		{echo 'NO3'; die();}
	else
		{$sql['email'] = test_input($oinfo["order_mail"]);}

	if (empty($oinfo["order_phone"]))
		{echo 'NO4'; die();}
	else
		{$sql['phone'] = test_input($oinfo["order_phone"]);}

	if (!empty($oinfo["order_message"]))
		{$sql['msg'] = test_input($oinfo["order_message"]);}
	else
		$sql['msg']='';

	if (empty($oinfo["order_quant"]))
		{echo 'NO5'; die();}
	else
		{$sql['quantity'] = test_input($oinfo["order_quant"]);}
	
	$p=$_POST;
	if(empty($p['order']))
		{echo 'NO6'; die();}

	$o=str_replace('\\','',$p['order']);
	$order=json_decode($o, true);
	if($order == null) { echo 'NO7'; die(); }
	$sql['json_cable']=$o;

	//inserisco
	global $wpdb;
$wpdb->show_errors();
	$table_name = $wpdb->prefix . "fmprev_orders";
	if($wpdb->insert( $table_name, $sql ,array( '%s','%s','%s','%s','%d','%s') )>0){
		echo 'OK';
	}
	else{
		echo $wpdb->print_error();
		echo 'NO8';
	}



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
	$table_name = $wpdb->prefix . "fmprev_orders";
	$result = $wpdb->get_results("select id,name,quantity from ".$table_name);
	$jstr='{"orders":[';

	foreach ( $result as $r ) 
	{

		$jstr.='{"id":'.$r->id.',"name":"'.$r->name.'","quantity":'.$r->quantity.'},';

	}
	if(count($result)>0)	
		$jstr = substr_replace($jstr, ']', -1, strlen($jstr));
	else
		$jstr.=']';
	$jstr.='}';
	echo json_encode($jstr);
	die();
}


function get_order_info(){

	global $wpdb;
	$id=$_POST['id'];
	$table_name = $wpdb->prefix . "fmprev_orders";
	$result = $wpdb->get_results("select * from ".$table_name.' WHERE id='.$id);
	if(!$result){
		echo 'not found';
		die();
	}
	$res=$result[0];
	$cable=$res->json_cable;
	unset($res->json_cable);
	$ret=json_decode($cable);
	$ret->info=$res;
	
	
	echo json_encode($ret);
	
	
	die();
}


?>