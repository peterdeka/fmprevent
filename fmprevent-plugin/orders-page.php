		<div class="row"><div class="col-md-8">
			<h4>Da qui puoi visualizzare gli ordini effettuati dai clienti, clicca su un ordine per visualizzare il cavo.</h4>
			<div id="msgarea" style="font-size:17px"></div>

		</div>
	</div>


	<div class="row">
		<div class="col-md-12">
			<h3>Ordini ricevuti</h3>
			<table id="orderstable" class="table data-table">
				<thead><tr><th>ID</th><th>Nome cliente</th><th>Quantità</th><th>Azioni</th></tr></thead>
				<tbody>
				</tbody></table></div>
			
			</div>

<div id="canvastable" style="position:absolute;z-index:-1000"></div>
<div id="ordercontainer" class="panel panel-default">
<div class="panel-body">
	
<div id='fmprevent' ></div>
 </div>
</div><div id="canvas"></div>
<script type="text/javascript">
<?php

  global $wpdb;
  $result = $wpdb->get_results("select * from ".$wpdb->prefix . "fmprev_cable_types");
  
 
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
  echo 'loadingctx=true;';
?>
reload_tableorders=function(tblid){
		jQuery.post(ajaxurl,{action:'get_orders'}).done(function(data){
		if(ordersoTable != null){ordersoTable.fnDestroy();ordersotable=null;}
		var tb=jQuery(tblid+" tbody");
		tb.html('');
		var d=data.replace(/\\/g, '');
		d=d.substring(1,d.length-1);
		var d = JSON.parse(d);
		jQuery.each(d.orders,function(i,el){

			tb.append('<tr><td><a class="orderlink" href="#">'+el.id+'</a></td><td>'+el.name+'</td><td>'+el.quantity+'</td><td><button type="button" class="btn btn-danger btn-xs delrow">Elimina</button></td</tr>');

		});
    	
    	jQuery(tblid+' .delrow').click(function(ev){
    		var id=jQuery(ev.target).parent('td').siblings().first().html();
			var r=jQuery.post(ajaxurl,{action:'del_orders',id:id});
			r.done(function(data){
				if(data=='OK'){
					jQuery('#msgarea').html('<span class="label label-success">'+id+' ELIMINATO con successo.</span>');
				reload_tableorders();
				}
				else
					jQuery('#msgarea').html('<span class="label label-danger">Errore durante l\'eliminazione dell\'ordine.</span>');
			});
    	});

    	jQuery(tblid+' .orderlink').click(function(ev){
    		ev.preventDefault();
    		var id=jQuery(ev.target).parent('td').text();
    		console.log('getting order'+id);
			var r=jQuery.post(ajaxurl,{action:'get_order_info',id:id});
			r.done(function(data){
				var d=data.replace(/\\/g, '');

				console.log(d);
				d = JSON.parse(d);
				var cab=new FMPrevent.Models.Cable({type:d.type});
				cab.loadJSON(d);
				jQuery('#printbtn').remove();
				jQuery('#canvastable').html('');
				jQuery('#orderdetails').remove();
				thecable = new FMPrevent.Views.Cable({model:cab});
				jQuery('#fmprevent').find("input").attr('disabled','disabled').attr('placeholder','');
      			jQuery('#fmprevent').find("select").attr('disabled','disabled');

				//jQuery('#fmprevent').before(FMPrevent.Templates.OrderInfo(d.info));
				jQuery('#fmprevent').after('<button id="printbtn" class="btn btn-primary">Scarica</button>');
				jQuery('#printbtn').click(function(){
				/*html2canvas(jQuery('#ordercontainer')[0], {
  				onrendered: function(canvas) {
   				 	jQuery('#canvas').html('').append(canvas);
    				var oCanvas = jQuery('canvas')[0];  
					Canvas2Image.saveAsPNG(oCanvas); 
  					}
				});*/
				//costruisco pdf
				var doc = new jsPDF('landscape');
				doc.text(50, 20, 'Ordine preventivatore numero: '+d.info.id);
				doc.text(20, 30, 'Dati cliente');
				var h=30;
				jQuery.each(d.info,function(i,e){
					if(i=='id')
						return;
					h+=10;
					doc.text(20, h, i+': '+e);
				});
				var tb=cab.gen_distinta();
				jQuery('#canvastable').append(tb);
				debugger;
				doc.addHTML(jQuery('#canvastable')[0],20,h+10,function(){
					html2canvas(jQuery('#ordercontainer')[0], {

  				onrendered: function(canvas) {
   				 	jQuery('#canvas').html('').append(canvas);
    				var oCanvas = jQuery('canvas')[0];  
					//var png=Canvas2Image.convertToImage(oCanvas); 
					var img=oCanvas.toDataURL();
					
					doc.addPage();
					doc.addImage(img, 'PNG', 10, 20, 280, 130);
					doc.save('Test.pdf');
  					}
				});});
				
				

				});
				/**/
			});
    	});

    	//load table
    	ordersoTable = jQuery(tblid).dataTable({
    		"iDisplayStart": 10,
    		"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, 'All']],
    		"sPaginationType": "bootstrap"
    	});

    });
};

jQuery(document).ready(function($) {
	ordersoTable=null;
	reload_tableorders('#orderstable');

});



</script>