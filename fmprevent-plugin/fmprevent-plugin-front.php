<?php



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
<div id="orderform">
  <form>
    <label for="order_name">Nome*:</label>
    <input type="text" name="order_name" id="order_name" minlength="5" required>
    <label for="order_mail">Email*:</label>
    <input type="email" name="order_mail" id="order_mail" required>
    <label for="order_phone">Telefono*:</label>
    <input type="text" name="order_phone" id="order_phone" required>
    <label for="order_message">Messaggio(opzionale):</label>
    <input type="text" name="order_message" id="order_message">
    <label for="order_quant">Quantità cavi*:</label>
    <input type="text" name="order_quant" id="order_quant" required>
    <input type="submit" id="sendorder">Invia ordine</button>
</form>
</div>
<script>
jQuery(document).ready(function($) {
thecable = new FMPrevent.Views.Cable({model:new FMPrevent.Models.Cable({type:cable_types[0]})});

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