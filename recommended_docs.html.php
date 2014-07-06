<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: area alunni</title>
<link rel="stylesheet" href="../../intranet/teachers/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
$(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content"
    });
  });
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
	<div class="page_title">
		Documenti consigliati per la tua classe
	</div>
	<div id="accordion">
<?php
$mat = 0;
foreach ($materie as $k => $document){
	if (count($document['docs']) < 1){
		continue;
	}
?>
	<h3><?php echo $document['materia'] ?></h3>
	<div>
<?php
	foreach ($document['docs'] as $d){
?>
	<p class="doc">
		<a class="doc" href="download_manager.php?doc=document&&id=<?php print $d['id'] ?>"><?php print utf8_decode($d['titolo']) ?></a>
		<span class="didascalia">(di <?php echo $d['prof'] ?>, a. s. <?php echo $d['descrizione'] ?>)</span>
	</p>
<?php
	}
?>
	</div>
<?php 
}
?>
</div>
</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>