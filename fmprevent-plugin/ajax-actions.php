<?php
if(is_admin()){
	add_action( 'wp_ajax_add_order', 'add_order' );
	add_action( 'wp_ajax_nopriv_add_order', 'add_order' );
	add_action( 'wp_ajax_get_connectors', 'get_connectors' );
	add_action( 'wp_ajax_nopriv_get_connectors', 'get_connectors' );
	
	add_action( 'wp_ajax_get_cabletypes', 'get_cable_types' );
	add_action( 'wp_ajax_add_cabletypes', 'add_cable_type' );
	add_action( 'wp_ajax_add_connector_sz', 'add_connector_sz' );
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

	if (empty($oinfo["order_quant"]) || !is_numeric($oinfo["order_quant"]))
		{echo 'NO5'; die();}
	else
		{$sql['quantity'] = test_input($oinfo["order_quant"]);}
	
	$p=$_POST;
	if(empty($p['order']))
		{echo 'NO6'; die();}

	$o=str_replace('\\','',$p['order']);
	$order=json_decode($o, true);
	if($order == null) { echo 'NO7'; die(); }
	//costruisco elenco componenti
	$html='<table style="border:1px solid #666"><tr><th>Componente</th><th>Unita</th><th>Prezzo-u</th><th>Prezzo totale</th>';
	global $wpdb;
	$wpdb->show_errors();
	$table_name = $wpdb->prefix . "fmprev_cable_types";
	$cabtype=$order['type'];
	$result = $wpdb->get_results("select * from ".$table_name." WHERE sigla='".test_input($cabtype)."'");
	if (count($result)<1)
		{echo 'NOval'; die();}
	$cprice=$result[0]->prezzo_metro*$order['t_length']/1000;
	$html.='<tr><td>Cavo '.$cabtype.'</td><td>'.$order['t_length'].'mm</td><td>'.$result[0]->prezzo_metro.'</td>'.$cprice.'<td></td></tr>';
	$connectors=array();
	foreach($order['right_end']['conns'] as $c) {
		
		if (array_key_exists($c['size'],$connectors))
			$connectors[$c['size']]+=1;
		else
			$connectors[$c['size']]=1;
	}
	foreach($order['left_end']['conns'] as $c) {
		if (array_key_exists($c['size'],$connectors))
			$connectors[$c['size']]+=1;
		else
			$connectors[$c['size']]=1;
	}

	
	//verifico esistenza e costruisco tabella componenti
	
	foreach ($connectors as $k => $v){
		$table_name = $wpdb->prefix . "fmprev_connettori_sz";
		$result = $wpdb->get_results("select * from ".$table_name." WHERE id=".test_input($k));
		if (count($result)<1)
			{echo 'NO conn'.$k; die();}
		$html.='<tr><td>Conn. '.$result[0]->codice.'</td><td>'.$v.'</td><td>'.$result[0]->prezzo.'</td><td>'.$result[0]->prezzo*$v.'</td></tr>';
		$cprice+=$result[0]->prezzo*$v;
	}

	$html.='<tr><td><strong>Totale</strong></td><td></td><td></td><td>'.$cprice.'</td></tr></table>';

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

	//send mail to owner


	$headers[] = 'From: Preventivatore <preventivatore@fmgroup.it>';
	$headers[] = "Content-type: text/html";
	$message='<p>Ricevuto un nuovo ordine preventivatore. Dati cliente:</p>';
	$message.='<p>Nome:'.$oinfo["order_name"].'</p>';
	$message.='<p>Email:'.$oinfo["order_mail"].'</p>';
	$message.='<p>Tel:'.$oinfo["order_phone"].'</p>';
	$message.='<p>Messaggio:'.$oinfo["order_message"].'</p>';
	$message.='<p>Quantita:'.$oinfo["order_quant"].'</p>';
	$message.='<h3>Materiale richiesto per un singolo pezzo</h3>'.$html;
	$message.='<p>Visualizza gli ordini <a href="http://www.fmgroup.it/wp-admin/admin.php?page=fmprevent_admin"> qui </a>.</p>';



	wp_mail( 'pietro.decaro@wannaup.com', 'nuovo ordine preventivatore', $message, $headers );

	die();
}

function get_cable_types(){

	global $wpdb;
	$table_name = $wpdb->prefix . "fmprev_cable_types";
	$result = $wpdb->get_results("select * from ".$table_name);
	$jstr='{"cabletypes":[';

	foreach ( $result as $r ) 
	{

		$jstr.='{"id":'.$r->id.',"sigla":"'.$r->sigla.'","prezzo":'.$r->prezzo_metro.'},';

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
	$newprice=(float)$p['newprice'];
	if(!is_numeric($newprice)){
		echo 'NOT NUMBER';
		die();
	}

	global $wpdb;
	$table_name = $wpdb->prefix . "fmprev_cable_types";
	$result = $wpdb->get_results("select * from ".$table_name. ' WHERE sigla = "'.$newtype.'"');
	if($result != NULL){
		echo 'AX';
		die();
	}
	if($wpdb->insert( $table_name, array( 'sigla' => $newtype,'prezzo_metro'=>$newprice ) )>0){
		echo 'OK';
	}
	else{
		echo 'NO';
	}
	die();
}

function add_connector_sz(){
	$p=$_POST;
	$error = false;
       	//TODO QUOTES
	$newtype=(int)$p['newtype'];
	$newsz=(float)$p['newsz'];
	$newprice=(float)$p['newprice'];
	if(!is_numeric($newprice) || !is_numeric($newsz)){
		echo 'NO';
		die();
	}

	global $wpdb;
	$table_name = $wpdb->prefix . "fmprev_connettori_sz";
	if($wpdb->insert( $table_name, array( 'tipo' => $newtype,'size'=>$newsz,'prezzo'=>$newprice,'codice'=>$p['codice'] ) )>0){
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



function get_connectors(){
	global $wpdb;
	$table_name = $wpdb->prefix . "fmprev_connettori";
	$result = $wpdb->get_results("SELECT * from ".$table_name);
	$jstr='{"tipo":'.$result[0]->id.',"nome":"'.$result[0]->nome.'","conntypes":[';

	foreach ( $result as $r ) 
	{
		$jstr.='{"id":'.$r->id.',"nome":"'.$r->nome.'","sizes":[';
		$table_name = $wpdb->prefix . "fmprev_connettori_sz";
		$re = $wpdb->get_results("SELECT * from ".$table_name." WHERE tipo=".$r->id);
		foreach( $re as $rr)
			$jstr.='{"id":'.$rr->id.',"size":'.$rr->size.',"prezzo":'.$rr->prezzo.',"codice":"'.$rr->codice.'"},';

		if(count($re)>0)	
		$jstr = substr_replace($jstr, ']', -1, strlen($jstr));
		else
		$jstr.=']';

		$jstr.='},';

	}

	if(count($result)>0)	
		$jstr = substr_replace($jstr, ']', -1, strlen($jstr));
	else
		$jstr.=']';
	$jstr.='}';
	echo json_encode($jstr);
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