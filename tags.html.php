<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>: documenti</title>
	<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/documents.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript">
		$(function() {
			load_jalert();
			setOverlayEvent();
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
	<div>
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
