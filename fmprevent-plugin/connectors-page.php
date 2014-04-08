<div class="row"><div class="col-md-8">
						<h4>Da qui puoi visualizzare i modelli di cavo inseriti ed aggiungerne nuovi.</h4>
						<div id="msgarea" style="font-size:17px"></div>
					
					</div></div>

			
					<div class="row"><div class="col-md-9"><h3>Connettori inseriti</h3><table id="connstable" class="table data-table">
						<thead><tr><th>ID</th><th>Codice</th><th>tipo</th><th>dimesione</th><th>prezzo</th><th>azioni</th></tr></thead><tbody>
					</tbody></table></div>
					<div class="col-md-3"><h3>Aggiungi connettore</h3>
					
					<form role="form">
					<div class="form-group">
						<label for="codice">Codice connettore:</label>
						<input type="text" name="codice" id="codice" size="20" maxlength="6"/>
					</div>
					<div class="form-group">
						<label for="tipo">Tipo connettore:</label>
						<select name="tipo" id="tipo"></select>
					</div>
					<div class="form-group">
						<label for="misura">Misura:</label>
						<input type="text" name="misura" id="misura" size="20" maxlength="6" />
					</div>
					<div class="form-group">
						<label for="prezzo">Prezzo:</label>
						<input type="text" name="prezzo" id="newprezzo" size="20" maxlength="6" />
					</div>
					<button  type="button" class="btn btn-primary" id="newc_send">Aggiungi</button>
					</form>
					

					
					</div></div>
					
<script type="text/javascript">
reload_tablec=function(tblid){
		jQuery.post(ajaxurl,{action:'get_connectors'}).done(function(data){
		if(coTable != null)coTable.fnDestroy();
		var tb=jQuery(tblid+" tbody");
		tb.html('<tr><td>Loading...</td><td>Loading...</td><td>Loading...</td><td>Loading...</td><td>Loading...</td><td>Loading...</td></tr>');
		var d=data.replace(/\\/g, '');
		d=d.substring(1,d.length-1);
		var d = JSON.parse(d);
		tb.html('');
		jQuery.each(d.conntypes,function(i,el){
			
			jQuery.each(el.sizes,function(ii,eel){
				console.log(el);
				tb.append('<tr><td>'+eel.id+'</td><td>'+eel.codice+'<td>'+el.nome+'</td><td>'+eel.size+'</td><td>'+eel.prezzo+'</td><td><button type="button" class="btn btn-danger btn-xs delrow">Elimina</button></td</tr>');
			});
		});
    	
    /*	jQuery(tblid+' .delrow').click(function(ev){
    		var sig=jQuery(ev.target).parent('td').siblings().last().html();
			var r=jQuery.post(ajaxurl,{action:'del_cabletypes',type:sig});
			r.done(function(data){
				if(data=='OK'){
					jQuery('#msgarea').html('<span class="label label-success">'+sig+' ELIMINATO con successo.</span>');
				reload_table('#cabletable');
				}
				else
					jQuery('#msgarea').html('<span class="label label-danger">Errore durante l\'eliminazione dell\'elemento.</span>');
			});
    	});*/

    	//load table
    	coTable = jQuery(tblid).dataTable({
    		"iDisplayStart": 10,
    		"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, 'All']],
    		"sPaginationType": "bootstrap"
    	});

    });
};

jQuery(document).ready(function($) {
	coTable=null;
	reload_tablec('#connstable');
	jQuery.post(ajaxurl,{action:'get_connectors'}).done(function(data){
		
		var connsel=jQuery('select#tipo');
		
		var data=JSON.parse(JSON.parse(data));
		jQuery(data.conntypes).each(function(i,el){
			connsel.append('<option value="'+el.id+'">'+el.nome+'</option>');
		});


	});

	$('#newc_send').click(function(e){
		e.preventDefault();
		var f=$(this).parent('form');
		var r=$.post(ajaxurl,{action:'add_connector_sz',newtype:$(f).find('#tipo').val(),newsz:$(f).find('#misura').val(),newprice:$(f).find('#newprezzo').val()});
		r.done(function(data){
			if(data=='OK'){
				$('#msgarea').html('<span class="label label-success">Elemento inserito con successo.</span>');
				reload_tablec('#connstable');
			}
			else if(data=='AX')
				$('#msgarea').html('<span class="label label-warning">Attenzione l\'elemento è già esistente.</span>');
			else
				$('#msgarea').html('<span class="label label-danger">Errore durante l\'inserimento del nuovo elemento.</span>');
		});
		return false;
	});


});



</script>					