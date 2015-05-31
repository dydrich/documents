<?php

include "../../lib/start.php";

check_session();
check_permission(DSG_PERM);

if($_REQUEST['action'] == 3){
	$sel_cat = "SELECT * FROM rb_categorie_docs WHERE id_categoria = ".$_REQUEST['id'];
	$res_cat = $db->executeQuery($sel_cat);
	$cat = $res_cat->fetch_assoc();
}

$drawer_label = "Gestione categoria di documento";

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?></title>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/documents.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript">
	$(function(){
		load_jalert();
		setOverlayEvent();
		$('#save').button();
		$('#save').click(function(event){
			event.preventDefault();
			save_data();
		});
	});

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
					j_alert("alert", json.message);
					window.setTimeout(function() {
						if ($('#action').val() == 1) {
							document.location.href = "categories.php";
						}
						else {
							$('fieldset').animate({
								backgroundColor: '#EEEEEE'
							}, 900);
						}
					}, 3000);
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
	<div style="top: -10px; margin-left: 35px; margin-bottom: -10px" class="rb_button">
		<a href="categories.php">
			<img src="../../images/47bis.png" style="padding: 12px 0 0 12px" />
		</a>
	</div>
	<form method="post" id="_form" class="no_border" action="category_manager.php">
	<fieldset class="doc_fieldset">
	<table id="cat_table">
		<tr>
			<td class="doc_title">Codice</td>
			<td class="doc_field">
				<input type="text" name="codice" id="codice" value="<?php if(isset($cat)) print $cat['codice'] ?>" class="full_field" />
			</td>
		</tr>
		<tr>
			<td class="doc_title">Nome</td>
			<td class="doc_field">
				<input type="text" name="nome" id="nome" value="<?php if(isset($cat)) echo utf8_decode($cat['nome']) ?>" class="full_field" />
			</td>
		</tr>
		<tr>
			<td class="doc_title">Descrizione</td>
			<td class="doc_field">
				<textarea id="abstract" name="abstract" class="full_field"><?php if(isset($cat) && trim($cat['descrizione']) != "") echo trim(utf8_decode($cat['descrizione'])) ?></textarea>
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
<div id="drawer" class="drawer" style="display: none; position: absolute">
	<div style="width: 100%; height: 430px">
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/index.php"><img src="../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/profile.php"><img src="../../images/33.png" style="margin-right: 10px; position: relative; top: 5%" />Profilo</a></div>
		<div class="drawer_link"><a href="index.php"><img src="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>images/11.png" style="margin-right: 10px; position: relative; top: 5%" />Documenti</a></div>
		<?php if(is_installed("com")){ ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>modules/communication/load_module.php?module=com&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
		<?php } ?>
	</div>
	<?php if (isset($_SESSION['__sudoer__'])): ?>
		<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/admin/sudo_manager.php?action=back"><img src="../../images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
	<?php endif; ?>
	<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>shared/do_logout.php"><img src="../../images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
</body>
</html>
