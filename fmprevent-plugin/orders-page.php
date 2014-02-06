		<div class="row"><div class="col-md-8">
			<h4>Da qui puoi visualizzare gli ordini effettuati dai clienti, clicca su un ordine per visualizzare il cavo.</h4>
			<div id="msgarea" style="font-size:17px"></div>

		</div>
	</div>


	<div class="row">
		<div class="col-md-9">
			<h3>Ordini ricevuti</h3>
			<table id="orderstable" class="table data-table">
				<thead><tr><th>ID</th><th>Nome</th><th>azioni</th></tr></thead>
				<tbody>
				</tbody></table></div>
			
			</div>

<script type="text/javascript">
reload_table=function(tblid){
		jQuery.post(ajaxurl,{action:'get_orders'}).done(function(data){
		if(ordersoTable != null){ordersoTable.fnDestroy();ordersotable=null;}
		var tb=jQuery(tblid+" tbody");
		tb.html('');
		var d=data.replace(/\\/g, '');
		d=d.substring(1,d.length-1);
		var d = JSON.parse(d);
		jQuery.each(d.orders,function(i,el){

			tb.append('<tr><td>'+el.id+'</td><td>'+el.name+'</td><td><button type="button" class="btn btn-danger btn-xs delrow">Elimina</button></td</tr>');

		});
    	
    	jQuery(tblid+' .delrow').click(function(ev){
    		var id=jQuery(ev.target).parent('td').siblings().first().html();
			var r=jQuery.post(ajaxurl,{action:'del_orders',id:id});
			r.done(function(data){
				if(data=='OK'){
					jQuery('#msgarea').html('<span class="label label-success">'+id+' ELIMINATO con successo.</span>');
				reload_table();
				}
				else
					jQuery('#msgarea').html('<span class="label label-danger">Errore durante l\'eliminazione dell\'ordine.</span>');
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
	reload_table('#orderstable');

});



</script>