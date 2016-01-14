<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 15/08/15
 * Time: 22.48
 * documenti del cdc
 */?>
<form id="doc_form" action="doc.php?upd=1" method="post" enctype="multipart/form-data" class="no_border">
	<fieldset class="doc_fieldset">
		<legend>Dati di base</legend>
		<table>
			<tr id="r_tipo">
				<td class="doc_title mandatory">Tipo documento</td>
				<td class="doc_field">
					<select id="tipo_documento" name="tipo_documento" class="full_field">
						<option value="0">Scegli un tipo documento</option>
						<?php
						while($tipologia = $res_tipologie->fetch_assoc()){
							?>
							<option <?php if($tipologia['id'] == $current_doc['categoria']) print("selected='selected'") ?> value="<?php print $tipologia['id'] ?>"><?php print $tipologia['tipo'] ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="doc_title">Anno</td>
				<td class="doc_field">
					<select name="anno" id="anno" class="full_field">
						<?php
						while($anno = $res_anni->fetch_assoc()){
							if ($anno['id_anno'] != $_SESSION['__current_year__']->get_ID()) {
								continue;
							}
							?>
							<option value="<?php print $anno['id_anno'] ?>" <?php if($anno['id_anno'] == $current_doc['anno_scolastico']) print "selected='selected'" ?>><?php print $anno['descrizione']?></option>
							<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr id="classes_row">
				<td class="doc_title mandatory">Classe</td>
				<td class="doc_field" id="cls_bs">
					<select name="classe" id="classe" class="full_field">
						<option value="0">Scegli una classe </option>
					<?php
					foreach ($classi as $classe) {
						?>
						<option value="<?php echo $classe['id_classe'] ?>" <?php if (isset($document) && $classe['id_classe'] == $document->getClasse()) echo "selected" ?> ><?php echo $classe['classe'] ?></option>
						<?php
					}
					?>
					</select>
				</td>
			</tr>
			<tr id="students_row" style="display: none">
				<td class="doc_title mandatory">Studente</td>
				<td class="doc_field" id="std_bs">
					<select name="student" id="student" class="full_field">

					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="doc_fieldset">
		<legend>Dati file</legend>
		<table>
			<tr id="r_titolo">
				<td class="doc_title mandatory">Titolo</td>
				<td class="doc_field">
					<input type="text" id="titolo" name="titolo" class="full_field" value="<?php if(isset($current_doc)) print($current_doc['titolo']) ?>" />
				</td>
			</tr>
			<tr id="r_abstract" >
				<td class="doc_title mandatory">Abstract</td>
				<td class="doc_field">
					<textarea name="abstract" id="abstract" class="full_field"><?php if(isset($current_doc)) print($current_doc['abstract']) ?></textarea>
				</td>
			</tr>
			<tr id="r_file">
				<td class="doc_title mandatory">File</td>
				<td class="doc_field" id="if_container" style="<?php if($_i == 0) echo "display: none;" ?>">
					<?php if(isset($current_doc)){ ?>
						<input class="form_input full_field" type="text" name="fname" id="fname" readonly value="<?php print $current_doc['file'] ?>"/>
						<!--<a href="#" onclick="load_iframe('<?php print $current_doc['file'] ?>')" style="margin-left: 15px">Modifica file</a> -->
					<?php }  else{ ?>
						<div id="iframe"><iframe src="upload_manager.php?upl_type=document_cdc&area=teachers&tipo=11<?php if($ext != null) echo '&ext='.implode(",", $ext) ?>" id="aframe"></iframe></div>
						<a href="#" onclick="del_file()" id="del_upl" style="">Annulla</a>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;&nbsp;&nbsp;
					<input type="hidden" name="action" id="action" />
					<input type="hidden" name="_i" id="_i" />
					<input type="hidden" name="tipo" id="tipo" value="<?php print $tipo ?>" />
					<input type="hidden" name="server_file" value="<?php if(isset($current_doc)) echo $current_doc['file'] ?>" id="server_file" />
					<input type="hidden" name="path" value="<?php if(isset($current_doc) && (isset($path))) print $path ?>"/>
					<input type="hidden" name="oldfile" value="<?php if(isset($current_doc)) print $current_doc['file'] ?>"/>
					<input type="hidden" name="referer" value="<?php print $referer ?>" />
					<input type="hidden" name="doc_type" id="doc_type" value="document_cdc"/>
				</td>
			</tr>
		</table>
	</fieldset>
	<div class="button_group">
		<button id="save" onclick="go(event, <?php if(isset($_GET['_i']) && $_GET['_i'] != 0) print("3, ".$_REQUEST['_i'].", ".$tipo); else print("1, 0, ".$tipo); ?>)">Registra</button>
		<?php if(isset($_GET['_i']) && $_GET['_i'] != 0){
			?>
			<button id="bdel" onclick="go(event, 2, <?php print $_i ?>, <?php print $tipo ?>)">Cancella</button>
			<?php
		}
		?>
	</div>
</form>
