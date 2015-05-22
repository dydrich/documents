<form action="dettaglio_documento.php?upd=1" method="post" enctype="multipart/form-data" id="doc_form" class="no_border">
    	<fieldset class="doc_fieldset">
	    <legend>Dati di base</legend>
	    <table>
	    	<tr>
	            <td class="doc_title">Numero Atto</td>
	            <td class="doc_field">
	            	<input type="text" name="act" id="act" class="full_field" value="<?php if(isset($current_doc)) echo $document->getActNumber() ?>"  />
	            </td>
	        </tr>
	        <tr>
	            <td class="doc_title">Protocollo</td>
	            <td class="doc_field">
	            	<input type="text" name="protocol" id="protocol" class="full_field" value="<?php if(isset($current_doc)) echo $document->getProtocol() ?>"  />
	            </td>
	        </tr>
	        <tr id="r_cat">
	        	<td class="doc_title mandatory">Categoria</td>
	            <td class="doc_field">
	                <select class="full_field" name="categoria" id="categoria">
	                	<option value="0">Seleziona una categoria</option>
	            <?php
	            while($cat = $res_categorie->fetch_assoc()){
	            ?>
	            		<option <?php if($cat['id_categoria'] == $document->getCategory()) print("selected='selected'") ?> value="<?php print $cat['id_categoria'] ?>"><?php print utf8_decode($cat['nome']) ?></option>
	            <?php } ?>
	                </select>
	            </td>
	        </tr>
	        <tr>
	            <td class="doc_title">In evidenza sino al <?php echo $document->getHighlighted() ?></td>
	            <td class="doc_field">
	            	<input type="text" name="highlighted" id="highlighted" class="full_field" readonly="readonly" value="<?php if(isset($current_doc) && $document->getHighlighted() != null) print format_date(substr($document->getHighlighted(), 0, 10), SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?>"  />

	            </td>
	        </tr>
	        <tr id="r_scad">
	            <td class="doc_title mandatory">Scade il</td>
	            <td class="doc_field">
	            	<input type="text" name="scadenza" id="scadenza" class="full_field" readonly="readonly" value="<?php if(isset($current_doc)) print format_date($document->getDueDate(), SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?>"  />

	            </td>
	        </tr>	        
	        <tr>
	        	<td class="doc_title">Anno scolastico</td>
	            <td class="doc_field">
	                <select class="full_field" name="anno" id="anno">
	            <?php
				while($year = $res_anni->fetch_assoc()){
	            ?>
	            		<option <?php if($year['id_anno'] == $document->getYear()) print("selected='selected'") ?> value="<?php print $year['id_anno'] ?>"><?php print $year['descrizione'] ?></option>
	            <?php 
				} 
				?>
	                </select>
	            </td>
	        </tr>   
	    </table>
        </fieldset>
    <fieldset class="doc_fieldset">
    <legend>Dati file</legend>
    <table>
        <tr id="r_titolo">
            <td id="t_tit" class="doc_title mandatory">Titolo</td>
            <td class="doc_field">
                <input type="text" id="titolo" name="titolo" class="full_field" value="<?php if(isset($current_doc)) print($document->getTitle()) ?>" />
            </td>
        </tr>
        <tr id="r_abstract">
            <td class="doc_title mandatory">Abstract</td>
            <td class="doc_field">
                <textarea name="abstract" id="abstract" class="full_field"><?php if(isset($current_doc)) print($document->getAbstract()) ?></textarea>
            </td>
        </tr>
        <tr id="r_file">
            <td class="doc_title mandatory">File</td>
            <td class="doc_field" id="if_container">
            <?php if(isset($current_doc)){ ?>
	            	<input class="form_input" type="text" name="fname" id="fname" style="width: 75%" readonly value="<?php print $document->getFile() ?>"/>
	                <a href="#" onclick="load_iframe()" style="margin-left: 15px">Modifica file</a>
	            <?php }  else{ ?>
	            	<div id="iframe"><iframe src="upload_manager.php?upl_type=document&area=teachers&tipo=7" id="aframe"></iframe></div>
					<a href="#" onclick="del_file()" id="del_upl">Annulla upload</a>
            <?php } ?>
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
	        <input type="hidden" name="action" id="action" />
		    <input type="hidden" name="_i" id="_i" />
		    <input type="hidden" name="tipo" id="tipo" value="<?php print $tipo ?>" />
		    <input type="hidden" name="server_file" value="<?php if(isset($current_doc)) echo $current_doc['file'] ?>" id="server_file" />
		    <input type="hidden" name="oldfile" value="<?php if(isset($doc)) print $doc['file'] ?>"/>
		    <input type="hidden" name="progressivo_atto" id="progressivo_atto" value="<?php if(isset($current_doc)) print $document->getProgressive() ?>"/>
		    <input type="hidden" name="doc_type" id="doc_type" value="document"/>
	    </div>
    </form>
