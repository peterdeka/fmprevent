<?php

if ( !current_user_can( 'manage_options' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
?>	
<div class="container" style="margin:0">  	
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
				<li><a href="#orders" data-toggle="tab">Oridni</a></li>

			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane active" id="editdb">
					<div class="row"><div class="col-md-8">
					<h4>Da qui puoi visualizzare i modelli di cavo inseriti ed aggiungerne nuovi.</h4>
			<?php
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				$error = false;
				global $wpdb;
				$table_name = $wpdb->prefix . "fmprev_cable_types";

				if($wpdb->insert( $table_name, array( 'sigla' => $_POST['sigla'] ) )>0){
					echo '<p style="background-color:white;color:green;font-weight:Bold">Nuovo elemento con sigla '.$_POST['sigla'].' inserito con successo.</p>';
				}
				else{
					echo '<p style="background-color:white;color:red;font-weight:Bold">Errore durante l\'inserimento del nuovo elemento.</p>';
				}
			}
			?>
		</div></div>

			<?php

			/*LISTA cavi inseriti*/
			echo '<div class="row"><div class="col-md-9"><h3>Cavi inseriti</h3><table id="cabletable" class="table data-table"><thead><tr><th>ID</th><th>Sigla</th></tr></thead><tbody>';
			global $wpdb;
			$table_name = $wpdb->prefix . "fmprev_cable_types";
			$result = $wpdb->get_results("select * from ".$wpdb->prefix . "fmprev_cable_types");

			foreach ( $result as $r ) 
			{
				echo '<tr><td>'.$r->id.'</td>';
				echo '<td>'.$r->sigla.'</td></tr>';
			}	
			echo '</tbody></table></div>';
			echo '<div class="col-md-3"><h3>Aggiungi cavo</h3>';
			$email_form = '<form method="post" action="' . get_permalink() . '">
			<div>
			<label for="sigla">Sigla nuovo cavo:</label>
			<input type="text" name="sigla" id="sigla" size="50" maxlength="50" />
			</div>
			<div>
			<input type="submit" value="Aggiungi" name="send" id="new_send" />
			</div>
			</form>';

			echo $email_form;
			echo '</div></div>';

			?>
			</div>
			<div class="tab-pane" id="orders"></div>
				
			</div>
		</div>
		</div>
		</div>
