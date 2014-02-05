<?php

if(is_admin()){
        add_action( 'wp_ajax_add_order', 'my_action_callback' );
        add_action( 'wp_ajax_nopriv_add_order', 'my_action_callback' );
}


function my_action_callback(){
        $p=$_POST;
        $order=json_decode(str_replace('\\','',$p['order']), true);
        if($order == null) { echo "Data = " . $p['order']; }

        echo 'nuovo ordine';
        echo 'tipo cavo: '.$order['type'];
        die();
}

?>