<form action="document_manager.php" method="post" enctype="multipart/form-data" id="doc_form" class="no_border">
    <fieldset class="doc_fieldset">
    <legend>Dati file</legend>
    <table>
    	<tr>
        	<td class="doc_title">Anno scolastico</td>
            <td class="doc_field">
                <select class="full_field" name="anno" id="anno">
            <?php
			while($year = $res_anni->fetch_assoc()){
            ?>
            		<option <?php if($year['id_anno'] == $current_doc['anno_scolastico']) print("selected='selected'") ?> value="<?php print $year['id_anno'] ?>"><?php print $year['descrizione'] ?></option>
            <?php 
			} 
			?>
                </select>
            </td>
        </tr>   
        <tr id="r_titolo">
            <td id="t_tit" class="doc_title mandatory">Titolo</td>
            <td class="doc_field">
                <input type="text" id="titolo" name="titolo" class="full_field" value="<?php if(isset($current_doc)) print(stripslashes($current_doc['titolo'])) ?>" />
            </td>
        </tr>
        <tr id="r_abstract">
            <td class="doc_title mandatory">Abstract</td>
            <td class="doc_field">
                <textarea name="abstract" id="abstract" class="full_field"><?php if(isset($current_doc)) print($current_doc['abstract']) ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="doc_title">In evidenza sino al</td>
            <td class="doc_field">
            	<input type="text" name="highlighted" id="highlighted" class="full_field" readonly="readonly" value="<?php if(isset($current_doc)) print format_date(substr($current_doc['evidenziato'], 0, 10), SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?>"  />
            </td>
	        </tr>
        <tr id="r_file">
            <td class="doc_title mandatory">File</td>
            <td class="doc_field" id="if_container">
            <?php if(isset($current_doc)){ ?>
                <input class="form_input" type="text" name="fname" id="fname" style="width: 75%" readonly value="<?php print $current_doc['file'] ?>"/>
                <a href="#" onclick="load_iframe()" style="margin-left: 15px">Modifica file</a>
	            <?php }  else{ ?>
                <div id="iframe"><iframe src="upload_manager.php?upl_type=document&area=teachers&tipo=<?php echo $tipo ?>" id="aframe"></iframe></div>
				<a href="#" onclick="del_file()" id="del_upl">Annulla upload</a>
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
			    <input type="hidden" name="doc_type" id="doc_type" value="document"/>
			    <input type="hidden" name="referer" value="<?php print $referer ?>" />
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
