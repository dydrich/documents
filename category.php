<?php

include "../../lib/start.php";

check_session();
check_permission(DSG_PERM);

if($_REQUEST['action'] == 3){
	$sel_cat = "SELECT * FROM rb_categorie_docs WHERE id_categoria = ".$_REQUEST['id'];
	$res_cat = $db->executeQuery($sel_cat);
	$cat = $res_cat->fetch_assoc();
}

$navigation_label = "Albo pretorio - categorie di documento";

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?></title>
<link rel="stylesheet" href="../../css/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
$(function(){
	$('#save').button();
	$('#save').click(function(event){
		event.preventDefault();
		save_data();
	});
});

var show_error = function(text){
	//$('#iframe').show();
	$('#not1').text(text);
	$('#not1').addClass("error");
	$('#not1').show(500);
};

var save_data = function(){
	var url = "category_manager.php";
	$.ajax({
		type: "POST",
		url: url,
		data: $('#_form').serialize(),
		dataType: 'json',
		error: function() {
			show_error("Errore di trasmissione dei dati");
		},
		succes: function() {
			
		},
		complete: function(data){
			r = data.responseText;
			if(r == "null"){
				return false;
			}
			var json = $.parseJSON(r);
			if (json.status == "kosql"){
				show_error(json.message);
				console.log(json.dbg_message);
			}
			else {
				$('#not1').text(json.message);
				$('#not1').show(1000);
				$('#not1').hide(3000);
			}
		}
    });
};
</script>
</head>
<body>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation.php" ?>
<div id="main">
<div id="right_col">
<?php include "menu.php" ?>
</div>
<div id="left_col">
<div class="notification" id="not1"></div>
	<div class="group_head">
		Dettaglio categoria
	</div>
	<form method="post" id="_form" action="category_manager.php">
	<fieldset class="doc_fieldset">
	<table id="cat_table">
		<tr>
			<td class="doc_title">Codice</td>
			<td class="doc_field">
				<input type="text" name="codice" id="codice" value="<?php if($cat) print $cat['codice'] ?>" class="full_field" />
			</td>
		</tr>
		<tr>
			<td class="doc_title">Nome</td>
			<td class="doc_field">
				<input type="text" name="nome" id="nome" value="<?php if($cat) echo utf8_decode($cat['nome']) ?>" class="full_field" />
			</td>
		</tr>
		<tr>
			<td class="doc_title">Descrizione</td>
			<td class="doc_field">
				<textarea id="abstract" name="abstract" class="full_field"><?php if($cat && trim($cat['descrizione']) != "") echo trim(utf8_decode($cat['descrizione'])) ?></textarea>
			</td>
		</tr>
	</table>
	</fieldset>
	<div class="button_group">
		<input type="hidden" name="id" id="id" value="<?php print $_REQUEST['id'] ?>" />
		<input type="hidden" name="action" id="action" value="<?php print $_REQUEST['action'] ?>" />
		<button id="save">Salva le modifiche</button>		
	</div>
	</form>
<p class="spacer"></p>
</div>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
