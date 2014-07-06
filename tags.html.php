<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>: documenti</title>
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

function show_div(div){
	$('#'+div).toggle(1500);
}
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
	<div class="page_title_left">
		Documenti con tag: <?php print $doc_type['tag'] ?>
	</div>
<?php 
$id_anno = 0;
if($res_docs->num_rows < 1){
?>
	<div class="nodocs">Nessun documento trovato</div>
<?php
}
else{
	$d_type = $_REQUEST['tipo'];
?>
<div id="accordion">
<?php
	while($doc = $res_docs->fetch_assoc()){
		if(($doc['privato'] == 1) && (isset($gid) && $gid != "") && (!($gid&$doc['permessi']))){
			continue;	
		}
		$link = $doc['file'];
		$ab = "<span class='bold_'>".$doc['titolo']."</span>";

		if ($doc['doc_type'] == 7){
			$ab = "<span class='bold_'>".$doc['progressivo_atto']."</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$ab;
		}
		else if ($doc['doc_type'] == 4 && $doc['materia'] != ""){
			try{
				$materia = $db->executeCount("SELECT materia FROM rb_materie WHERE id_materia = {$doc['materia']}");
			} catch (MySQLException $ex){
				$ex->redirect();
			}
			//$ab .= " (di ".$doc['cognome']." ".$doc['nome']." - {$materia}";
			$ab .= " ({$materia}";
			if ($doc['classe_rif'] != "" && $doc['classe_rif'] != 0){
				$desc_cls = "";
				switch ($doc['classe_rif']){
					case 1:
						$desc_cls = "classi prime";
						break;
					case 2:
						$desc_cls = "classi seconde";
						break;
					case 3:
						$desc_cls = "classi terze";
						break;
					case 4:
						$desc_cls = "classi quarte";
						break;
					case 5:
						$desc_cls = "classi quinte";
						break;
				}
				$ab .= " - {$desc_cls}";
			}
		}
		else {
			$ab .= " (di ".$doc['cognome']." ".$doc['nome'];
		}
		if ($doc['doc_type'] == 4 && $doc['ordine_scuola'] != 4){
			try{
				$school = $db->executeCount("SELECT tipo FROM rb_tipologia_scuola WHERE id_tipo = {$doc['ordine_scuola']}");
			} catch (MySQLException $ex){
				$ex->redirect();
			}
			$ab .= " ".strtolower($school);
		}
		if ($doc['doc_type'] != 7){
			$ab .= ")";
		}
		if($ab == "") $ab = "Nessuna descrizione presente";
		list($y, $m, $d) = explode("-", $doc['data_upload']);
		$check_y = $doc['anno_scolastico'];
		$y_label = "Anno scolastico ".$doc['descrizione'];
		if ($doc['doc_type'] == 7){
			$check_y = $y;
			$y_label = "Anno ".$y;
		}
		if($id_anno != $check_y){
			if($id_anno != 0){
?>
		</div> <!-- chiusura anno -->
<?php 
			} 
?>
	<h3><?php echo $y_label ?></h3>
	<div id="tipo<?php print $doc['doc_type'] ?>_anno<?php print $doc['anno_scolastico'] ?>">
<?php
		}
		if ($doc['doc_type'] != 7){
			$id_anno = $doc['anno_scolastico'];
		}
		else {
			$id_anno = $y;
		}
?>
	<p class="doc">
		<a href="document.php?id=<?php print $doc['id'] ?>"><?php echo truncateString(stripslashes($ab), 140) ?></a>
	</p>
<?php
	}
	print("</div> <!-- chiusura anno -->");
}
if($res_docs->num_rows > 0){
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
