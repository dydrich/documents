<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>: documento</title>
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/documents.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
$(function(){
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
	<div class="group_head">
		Dettagli documento
	</div>
	<fieldset class="doc_fieldset_det">
		<legend>Documento</legend>
		<p class="doc_det_title"><?php echo $doc->getTitle() ?></p>
		<p class="doc_data">Utente: <?php echo $doc->getOwner()->getFullName() ?></p>
		<p class="doc_data">Data caricamento: <?php echo format_date($d_upl, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." ".substr($t, 0, 5) ?></p>
		<p class="doc_data">Download: <?php echo $mydoc['dw_counter'] ?></p>
	</fieldset>
	<fieldset class="doc_fieldset_det">
		<legend>File</legend>
		<p class="doc_data">Nome: <?php echo $doc->getFile() ?></p>
		<p class="doc_data">Tipo: <?php echo $filedata['tipo'] ?></p>
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
	<fieldset class="doc_fieldset_det">
		<legend>Dati aggiuntivi</legend>
		<p class="doc_data">Categoria: <?php echo $categoria ?></p>
		<p class="doc_data">Materia: <?php echo $materia ?></p>
		<p class="doc_data">Classi: <?php echo $classi ?> <?php echo strtolower($school) ?></p>
	</fieldset>
	<?php 
	}
	else if ($doc->getDocumentType() == 7){
	?>
	<fieldset class="doc_fieldset_det">
		<legend>Dati aggiuntivi</legend>
		<p class="doc_data">Protocollo: <?php echo $doc->getProtocol() ?></p>
		<p class="doc_data">Atto: <?php echo $doc->getActNumber() ?></p>
		<p class="doc_data">Progressivo: <?php echo $doc->getProgressive() ?></p>
		<p class="doc_data">Pubblicazione: <?php if ($doc->isExpired()) echo format_date($d_upl, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." - ".format_date($doc->getDueDate(), SQL_DATE_STYLE, IT_DATE_STYLE, "/"); else echo "In corso sino al ".format_date($doc->getDueDate(), SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?></p>
	</fieldset>
	<?php
	}
	?>
	<div id="dwl_button">
		<button id="button">Scarica documento</button>
	</div>
</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
