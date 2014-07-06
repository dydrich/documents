<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: documenti condivisi</title>
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
		Documenti condivisi per la tua classe
	</div>
<?php
if ($res_ev->num_rows < 1){
?>	
<div class="welcome">
	<p id="w_head">Documenti in evidenza</p>
    <p class="w_text" id="hd">
    	Nessun documento condiviso
    </p>
</div>
<?php 
}	
if($res_ev->num_rows > 0){
?>
<div id="accordion">
<?php
	$shared_docs = array();
   	$x = 0;
	while($doc_ev = $res_ev->fetch_assoc()){
		if (!$shared_docs[$doc_ev['materia']]){
			$shared_docs[$doc_ev['materia']] = array();
		}
		$shared_docs[$doc_ev['materia']][] = $doc_ev;
	}
	foreach ($shared_docs as $mt => $sd){
?>
	<h3><?php print $mt ?></h3>
	<div>
<?php 
		foreach ($sd as $dc){
			if($dc['titolo'] == "") {
				$ab = $dc['abstract'];
			}
			else {
				$ab = $dc['titolo'];
			}
			$ab .= " ({$dc['materia']}, di {$dc['cognome']} {$dc['nome']})";
?>
		<a href="download_manager.php?doc=document&id=<?php print $dc['id'] ?>" class="attention"><?php print $ab ?></a><br />
<?php
		}
?>
	</div>
<?php 	
	}
?>
</div>
<?php
}
?>
</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>