<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 04/08/15
 * Time: 9.36
 * relazioni docente
 */
?>
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
		    <tr id="subject_row" style="">
			    <td class="doc_title mandatory">Materie</td>
			    <td class="doc_field" id="subj_bs">
				    <?php
				    foreach ($subjects as $subject) {
					    $checked = "";
					    if (count($subjects) == 1) {
						    $checked = "checked";
					    }
					    else if ($_i != 0 && in_array($subject['id'], $document->getSubjects())) {
						    $checked = "checked";
					    }
					?>
					<input type="checkbox" id="<?php echo "sb".$subject['id'] ?>" name="materie[]" value="<?php echo $subject['id'] ?>" class="upd_subjects" <?php echo $checked; ?> /><label for="sb<?php echo $subject['id'] ?>" style="font-size: 0.85em"><?php echo $subject['materia'] ?></label>
					<?php
				    }
				    ?>
			    </td>
		    </tr>
		    <tr id="classes_row">
			    <td class="doc_title mandatory">Classi</td>
			    <td class="doc_field" id="cls_bs">
				    <?php
				    foreach ($classi as $classe) {
					?>
					<input type="checkbox" id="<?php echo "cl".$classe['id_classe'] ?>" name="classi[]" class="upd_classes" value="<?php echo $classe['id_classe'] ?>" <?php if ($_i != 0 && in_array($classe['id_classe'], $document->getClasses())) echo "checked"; ?> /><label for="cl<?php echo $classe['id_classe'] ?>" style="font-size: 0.85em"><?php echo $classe['classe'] ?></label>
				    <?php
				    }
				    ?>
			    </td>
		    </tr>
		    <?php if ($_SESSION['__user__']->getSubject() == 27 || $_SESSION['__user__']->getSubject() == 41): ?>
		    <tr id="students_row">
			    <td class="doc_title mandatory">Studenti</td>
			    <td class="doc_field" id="std_bs">
				    <?php
				    while ($row = $res_sts->fetch_assoc()) {
					    ?>
					    <input type="radio" id="<?php echo "std".$row['id_alunno'] ?>" name="alunni[]" class="upd_students" value="<?php echo $row['id_alunno'] ?>" data-cls="<?php echo $row['id_classe'] ?>" <?php if ($_i != 0 && $row['id_alunno'] == $document->getStudent()->getUid()) echo "checked"; ?> /><label for="std<?php echo $row['id_alunno'] ?>" style="font-size: 0.85em"><?php echo $row['cognome']." ".$row['nome'] ?></label>
					    <?php
				    }
				    ?>
			    </td>
		    </tr>
		    <?php endif; ?>
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
						<!--<a href="#" onclick="load_iframe('<?php print $current_doc['file'] ?>')" style="margin-left: 15px">Modifica file</a>-->
					<?php }  else{ ?>
						<div id="iframe"><iframe src="upload_manager.php?upl_type=teaching_doc&area=teachers&tipo=10" id="aframe"></iframe></div>
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
					<input type="hidden" name="doc_type" id="doc_type" value="teaching_doc"/>
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
