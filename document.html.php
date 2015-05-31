<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>: documento</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
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
			$('#button').button();

			$('#button').click(function(){
				download_file();
			});
		});

		var download_file = function(){
			document.location.href = "download_manager.php?id=<?php echo $doc->getID() ?>&doc=document";
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
	<fieldset style="position: relative; top: -15px; width: 75%; padding: 10px 0 10px 10px; margin-left: auto; margin-right: auto; margin-top: 20px; border-radius: 10px">
		<legend>Documento</legend>
		<p style="width: 90%; padding-left: 10px; font-weight: bold"><?php echo $doc->getTitle() ?></p>
		<p class="doc_data">Utente: <?php echo $doc->getOwner()->getFullName() ?></p>
		<p class="doc_data">Data caricamento: <?php echo format_date($d_upl, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." ".substr($t, 0, 5) ?></p>
		<p class="doc_data">Download: <?php echo $mydoc['dw_counter'] ?></p>
	</fieldset>
	<fieldset style="width: 75%; padding: 10px 0 10px 10px; margin-left: auto; margin-right: auto; margin-top: 20px; border-radius: 10px">
		<legend>File</legend>
		<p class="doc_data">Nome: <?php echo $doc->getFile() ?></p>
		<p class="doc_data">Tipo: <?php if (isset($filedata)) echo $filedata['tipo']; else echo "non disponibile" ?></p>
		<p class="doc_data">Dimensione: <?php echo $size ?></p>
	</fieldset>
	<?php 
	if ($doc->getDocumentType() == 4){
		$categoria = $db->executeCount("SELECT nome FROM rb_categorie_docs WHERE id_categoria = {$doc->getCategory()}");
		if ($doc->getSubject() != "") $materia = $db->executeCount("SELECT materia FROM rb_materie WHERE id_materia = {$doc->getSubject()}");
		$classi = "--";
		if ($doc->getClasses() != ""){
			if ($doc->getClasses() == 1){
				$classi = "prime";
			}
			else if ($doc->getClasses() == 2){
				$classi = "seconde";
			}
			else if ($doc->getClasses() == 3){
				$classi = "terze";
			}
			else if ($doc->getClasses() == 4){
				$classi = "quarte";
			}
			else if ($doc->getClasses() == 5){
				$classi = "quinte";
			}
		}
	?>
	<fieldset style="width: 75%; padding: 10px 0 10px 10px; margin-left: auto; margin-right: auto; margin-top: 20px; border-radius: 10px">
		<legend>Dati aggiuntivi</legend>
		<p class="doc_data">Categoria: <?php echo $categoria ?></p>
		<p class="doc_data">Materia: <?php echo $materia ?></p>
		<p class="doc_data">Classi: <?php echo $classi ?> <?php echo strtolower($school) ?></p>
	</fieldset>
	<?php 
	}
	else if ($doc->getDocumentType() == 7){
	?>
	<fieldset style="width: 75%; padding: 10px 0 10px 10px; margin-left: auto; margin-right: auto; margin-top: 20px; border-radius: 10px">
		<legend>Dati aggiuntivi</legend>
		<p class="doc_data">Protocollo: <?php echo $doc->getProtocol() ?></p>
		<p class="doc_data">Atto: <?php echo $doc->getActNumber() ?></p>
		<p class="doc_data">Progressivo: <?php echo $doc->getProgressive() ?></p>
		<p class="doc_data">Pubblicazione: <?php if ($doc->isExpired()) echo format_date($d_upl, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." - ".format_date($doc->getDueDate(), SQL_DATE_STYLE, IT_DATE_STYLE, "/"); else echo "In corso sino al ".format_date($doc->getDueDate(), SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?></p>
	</fieldset>
	<?php
	}
	?>
	<div id="dwl_button" style="text-align: right; margin-right: 0; width: 30%; margin-left: 58%; margin-top: 20px">
		<button id="button">Scarica documento</button>
	</div>
</div>
<p class="spacer"></p>
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
		<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>admin/sudo_manager.php?action=back"><img src="../../images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
	<?php endif; ?>
	<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['docs']['path_to_root'] ?>shared/do_logout.php"><img src="../../images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
</body>
</html>
