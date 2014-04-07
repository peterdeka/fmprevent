<?php

  global $wpdb;
  $result = $wpdb->get_results("select * from ".$wpdb->prefix . "fmprev_cable_types");
  
  echo '<script type="text/javascript">'; 
  $prices='[';
  $types='[';
  foreach ( $result as $r ) 
  {
      $types.='"'.$r->sigla.'",';
      $prices.=$r->prezzo_metro.',';
    }  
  $types.= '];';
  $prices.='];';
  echo 'cable_prices='.$prices.';';
  echo 'cable_types='.$types.';';

  echo'</script>';
  echo '<div id="fmprevent"></div><div id="priceview"><p>Prezzo di listino stimato:<span id="est_price"></span> euro per cavo</p></div>';

?>

<div id="orderform">
<form >
<table width="450px">
</tr>
<tr>
 <td valign="top">
  <label for="order_name">Nome*:</label>
 </td>
 <td valign="top">
  <input type="text" name="order_name" id="order_name" minlength="5" required>
 </td>
</tr>
 

<tr>
 <td valign="top">
  <label for="order_mail">Email*:</label>
 </td>
 <td valign="top">
  <input type="email" name="order_mail" id="order_mail" required>
 </td>
 
</tr>
<tr>
 <td valign="top">
  <label for="order_phone">Telefono*:</label>
 </td>
 <td valign="top">
   <input type="text" name="order_phone" id="order_phone" required>
 </td>
</tr>
<tr>
 <td valign="top">
  <label for="order_quant">Quantità cavi*:</label>
 </td>
 <td valign="top">
  <input type="text" name="order_quant" id="order_quant" required>
 </td>
</tr>
<tr>
 <td valign="top">
  <label for="order_message">Messaggio(opzionale):</label>
 </td>
 <td valign="top">
  <textarea name="order_message" id="order_message" rows="4"></textarea>
 </td>
 
</tr>
<tr>
 <td colspan="2" style="text-align:center">
  <input type="submit" value="Invia" id="sendorder">  
 </td>
</tr>
</table>
</form>
</div>

<script>
jQuery(document).ready(function($) {
  jQuery.post(ajax_object.ajax_url
,{action:'get_connectors'}).done(function(data){
  conn_type=JSON.parse(JSON.parse(data));
  var m=new FMPrevent.Models.Cable({type:cable_types[0]});
  thecable = new FMPrevent.Views.Cable({model:m});
  pricefield= new FMPrevent.Views.Price({model:m});
});
var orderformval=$( "#orderform form" ).validate({
  
  rules: {
    order_quant: {
      required: true,
      number: true
    },
    order_phone: {
      required: true,
      digits: true
    }
  }

});

jQuery('#sendorder').click(function(ev){
    ev.preventDefault();
    if(!orderformval.valid())
      return;
    var orderinfo=jQuery('#orderform form').serializeObject();
    var a=thecable.model.toJSON();
    var r=jQuery.post(ajax_object.ajax_url,{action:'add_order',order:JSON.stringify(a),orderinfo:orderinfo});
    r.done(function(data){jQuery('#fmprevent').html(data);});
    
});

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};


});
</script>