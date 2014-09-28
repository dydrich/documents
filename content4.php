<div class="group_head">Gestione documento</div>
    <form id="doc_form" action="doc.php?upd=1" method="post" enctype="multipart/form-data">
    <fieldset class="doc_fieldset">
    <legend>Dati di base</legend>
    <table>
        <tr>
            <td class="doc_title">Tipo documento</td>
            <td class="doc_field">
                <select name="tipo_documento" class="full_field">
                	<option value="4">DD - Materiale didattico</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="doc_title">Ordine di scuola</td>
            <td class="doc_field">
                <select name="ordine_scuola" id="ordine_scuola" class="full_field">
                	<option value="4">Tutti</option>
				<?php
				while($ordine = $res_ordini->fetch_assoc()){
				?>
					<option <?php if($ordine['id_tipo'] == $current_doc['ordine_scuola']) print("selected='selected'") ?> value="<?php print $ordine['id_tipo'] ?>"><?php print $ordine['tipo'] ?></option>
				<?php } ?>
                </select>
            </td>
        </tr>
        <tr id="r_cat">
            <td class="doc_title">Categoria</td>
            <td class="doc_field">
                <select name="categoria" id="categoria" class="half_field">
                	<option value="0">Tutte</option>
				<?php
				while($categoria = $res_categorie->fetch_assoc()){
				?>
					<option <?php if($categoria['id_categoria'] == $current_doc['categoria']) print("selected='selected'") ?> value="<?php print $categoria['id_categoria'] ?>"><?php print $categoria['nome'] ?></option>
				<?php } ?>
                </select>
                <span class="half_title" id="t_mat">Materia</span>
                <select name="materia" id="materia" class="half_field" <?php if($_i == 0 || $current_doc['categoria'] != 2) print("disabled='disabled'") ?>>
                	<option value="0">Tutte</option>
				<?php
				while($materia = $res_materie->fetch_assoc()){
				?>
					<option <?php if($materia['id_materia'] == $current_doc['materia']) print("selected='selected'") ?> value="<?php print $materia['id_materia'] ?>"><?php print $materia['materia'] ?></option>
				<?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="doc_title">Anno</td>
            <td class="doc_field">
                <select name="anno" id="anno" class="half_field">
                <?php 
                while($anno = $res_anni->fetch_assoc()){
                ?>
                	<option value="<?php print $anno['id_anno'] ?>" <?php if($anno['id_anno'] == $current_doc['anno_scolastico']) print "selected='selected'" ?>><?php print $anno['descrizione']?></option>
                <?php 
                }
                ?>
                </select>
                <span class="half_title" id="cls_span">Classi</span>
                <select name="classe" id="classe" class="half_field">
                	<option value="0">Tutte</option>
                	<option value="1" <?php if($current_doc['classe_rif'] == 1) print("selected='selected'") ?>>Prime</option>
                	<option value="2" <?php if($current_doc['classe_rif'] == 2) print("selected='selected'") ?>>Seconde</option>
                	<option value="3" <?php if($current_doc['classe_rif'] == 3) print("selected='selected'") ?>>Terze</option>
                	<?php 
                	if ($_SESSION['__user__']->getSchoolOrder() != 1):
                	?>
                	<option value="4" <?php if($current_doc['classe_rif'] == 4) print("selected='selected'") ?>>Quarte</option>
                	<option value="5" <?php if($current_doc['classe_rif'] == 5) print("selected='selected'") ?>>Quinte</option>
                	<?php 
                	endif;
                	?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="doc_title">Visibilit&agrave;</td>
            <td class="doc_field">
                <select name="private" id="private" class="full_field" <?php if($_i == 0 || $current_doc['categoria'] != 2) print("disabled='disabled'") ?>>
                	<option value="0" <?php if ($current_doc['privato'] == 0) echo "selected='selected'" ?>>Pubblica (per tutte le classi)</option>
                	<option value="1" <?php if ($current_doc['privato'] == 1) echo "selected='selected'" ?>>Privata (solo le classi che indicherai)</option>
                </select>
            </td>
        </tr>
        <tr id="classes" style="<?php if (!$current_doc || $current_doc['privato'] == 0) echo "display: none" ?>">
            <td class="doc_title">Classi</td>
            <td class="doc_field">
            <?php 
            	$cls = $document->getAllowedClasses();
            	foreach ($_SESSION['__user__']->getClasses() as $k => $cl){
			?>
            	<label><?php echo $cl['classe'] ?></label><input type="checkbox" id="classi[]" name="classi[]" <?php if (in_array($k, $cls)) echo "checked" ?> value="<?php echo $k ?>" />
            <?php 
				}
			
			?>
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
        <tr id="t_file">
            <td class="doc_title mandatory">File</td>
            <td class="doc_field" id="if_container">
            <?php if(isset($current_doc)){ ?>
	            <input class="form_input" type="text" name="fname" id="fname" style="width: 75%" readonly value="<?php print $current_doc['file'] ?>"/>
	            <a href="#" onclick="load_iframe()" style="margin-left: 15px">Modifica file</a>
	            <?php }  else{ ?>
	            <div id="iframe"><iframe src="upload_manager.php?upl_type=document&area=teachers&tipo=4" id="aframe"></iframe></div>
				<a href="#" onclick="del_file()" id="del_upl" style="">Annulla</a>
            <?php } ?>
            </td>
        </tr>
        <tr>
            <td class="doc_title">Tags</td>
            <td class="doc_field">
            	<input type="text" name="tag" id="tag" class="almostfull_field" /><a href="#" id="add_tag" style="margin-left: 20px; margin-bottom: 8px" onclick="addTag(event)">Aggiungi</a>
            	<div id="tags_ct">
            	<?php 
            	if (isset($tags)){
					reset($tags);
					$i = 0;
					foreach ($tags as $t){
            	?>
            		<p id='tag_<?php echo $i ?>' style='height: 16px; margin: 3px 0 0 0'><a href='#' onclick='deleteTag(<?php echo $i ?>)' style='margin-right: 5px'><img src='../../images/list_remove.png' /></a><span style='position: relative; top: -2px'><?php echo $t ?></span></p>
            	<?php 
            		}
            	}
            	?>
            	</div>
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
			    <input type="hidden" name="doc_type" id="doc_type" value="document"/>
			    <input type="hidden" name="tags" id="tags" value="<?php if(isset($current_doc)) print(implode(",", $tags)) ?>" />
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
