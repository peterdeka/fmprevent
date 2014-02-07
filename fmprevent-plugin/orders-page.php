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


<div id="ordercontainer" class="panel panel-default">
<div class="panel-body">
<div id='fmprevent' ></div>
 </div>
</div><div id="canvas"></div>
<script type="text/javascript">
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
				thecable = new FMPrevent.Views.Cable({model:cab});
				jQuery('#fmprevent').before(FMPrevent.Templates.OrderInfo(d.info));
				jQuery('#fmprevent').after('<button id="printbtn" class="btn btn-primary">Stampa</button>');
				jQuery('#printbtn').click(function(){
				html2canvas(jQuery('#fmprevent')[0], {
  				onrendered: function(canvas) {
   				 jQuery('#canvas').html('').append(canvas);
    			var oCanvas = jQuery('canvas')[0];  
					Canvas2Image.saveAsPNG(oCanvas); 
  				}
			});

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