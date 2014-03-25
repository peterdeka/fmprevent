<?php

if ( !current_user_can( 'manage_options' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
?>	
<div class="container" style="margin:0;background-color:#FDFDFD">  	
	<div class="row">
		<div class="col-md-7">
			<h2>Preventivatore cavi FMGroup</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<!-- Nav tabs -->
			<ul class="nav nav-tabs">
				<li class="active"><a href="#editdb" data-toggle="tab">Gestione cavi</a></li>
				<li><a href="#connectors" data-toggle="tab">Connettori</a></li>
				<li><a href="#orders" data-toggle="tab">Ordini</a></li>
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane active" id="editdb">
					<div class="row"><div class="col-md-8">
						<h4>Da qui puoi visualizzare i modelli di cavo inseriti ed aggiungerne nuovi.</h4>
						<div id="msgarea" style="font-size:17px"></div>
					
					</div></div>

			
					<div class="row"><div class="col-md-9"><h3>Cavi inseriti</h3><table id="cabletable" class="table data-table"><thead><tr><th>ID</th><th>Sigla</th><th>Prezzo al metro</th><th>azioni</th></tr></thead><tbody>
					</tbody></table></div>
					<div class="col-md-3"><h3>Aggiungi cavo</h3>
					
					<form>
					<label for="sigla">Sigla nuovo cavo:</label>
					<input type="text" name="sigla" id="newsigla" size="30" maxlength="30" />
					<label for="sigla">Prezzo al metro:</label>
					<input type="text" name="prezzo" id="newprezzo" size="30" maxlength="6" />
					
					<button  type="button" class="btn btn-primary" id="new_send">Aggiungi</button>
					</form>
					

					
					</div></div>
					
				</div>
				<div class="tab-pane" id="connectors">
					<?php include 'connectors-page.php'; ?>
					
				</div>
				<div class="tab-pane" id="orders">
					
					<?php include 'orders-page.php'; ?>
				</div>
				
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
reload_table=function(tblid){
		jQuery.post(ajaxurl,{action:'get_cabletypes'}).done(function(data){
		if(oTable != null)oTable.fnDestroy();
		var tb=jQuery(tblid+" tbody");
		tb.html('<tr><td>Loading...</td><td>Loading...</td><td>Loading...</td><td>Loading...</td></tr>');
		var d=data.replace(/\\/g, '');
		d=d.substring(1,d.length-1);
		var d = JSON.parse(d);
		tb.html('');
		jQuery.each(d.cabletypes,function(i,el){

			tb.append('<tr><td>'+el.id+'</td><td>'+el.sigla+'</td><td>'+el.prezzo+'</td><td><button type="button" class="btn btn-danger btn-xs delrow">Elimina</button></td</tr>');

		});
    	
    	jQuery(tblid+' .delrow').click(function(ev){
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
    	});

    	//load table
    	oTable = jQuery(tblid).dataTable({
    		"iDisplayStart": 10,
    		"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, 'All']],
    		"sPaginationType": "bootstrap"
    	});

    });
};

jQuery(document).ready(function($) {
	oTable=null;
	reload_table('#cabletable');

	$('#new_send').click(function(e){
		e.preventDefault();
		var r=$.post(ajaxurl,{action:'add_cabletypes',newtype:$('#newsigla').val(),newprice:$('#newprezzo').val()});
		r.done(function(data){
			if(data=='OK'){
				$('#msgarea').html('<span class="label label-success">Elemento inserito con successo.</span>');
				reload_table('#cabletable');
			}
			else if(data=='AX')
				$('#msgarea').html('<span class="label label-warning">Attenzione l\'elemento è già esistente.</span>');
			else
				$('#msgarea').html('<span class="label label-danger">Errore durante l\'inserimento del nuovo elemento.</span>');
		});
		return false;
	});


});



jQuery(document).ready(function($) {
  jQuery.post(ajaxurl
,{action:'get_connectors'}).done(function(data){
  conn_type=JSON.parse(JSON.parse(data));


});});
</script>