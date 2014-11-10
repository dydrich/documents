<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?></title>
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
			document.location.href = "doc.php?tipo=<?php echo $_REQUEST['tipo'] ?>&_i=0";
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
	<div style="top: 75px; margin-left: 625px; margin-bottom: 10px; position: absolute" class="rb_button">
		<a href="doc.php?tipo=<?php echo $_REQUEST['tipo'] ?>&_i=0">
			<img src="../../images/39.png" style="padding: 12px 0 0 12px" />
		</a>
	</div>
	<div class="card_container" style="margin-top: 10px">
        <?php 
        $x = 1; // contatore grafico
		if($res_docs->num_rows > $limit)
		   	$max = $limit;
		else
		   	$max = $res_docs->num_rows;
		
		if($res_docs->num_rows == 0){
		?>	
			<div style="width: 90%; margin: auto; font-size: 1.1em; font-weight: bold; text-align: center">Nessun documento presente</div>
		<?php 	
		}
		else{	
	        while($row = $res_docs->fetch_assoc()){
	           	if($x > $limit) break;
	           	// tipo file
				$path_parts = pathinfo($row['file']);
				if ($_REQUEST['tipo'] != 4){
					list($d, $t) = explode(" ", $row['data_upload']);
				}
				$sel_dw = "SELECT COUNT(id) FROM rb_downloads WHERE doc_id = {$row['id']}";
				$dw = $db->executeCount($sel_dw);
        ?>
		<a href="doc.php?_i=<?php print $row['id'] ?>">
			<div class="card">
				<div class="card_title">
					<?php echo truncateString(stripslashes($row['titolo']), 80) ?>
					<div style="float: right; width: 90px; margin-right: 30px" class="normal">
						A. S. <?php print $row['descrizione'] ?>
					</div>
				</div>
				<div class="card_varcontent" style="overflow: hidden">
					<div class="card_row">
						<?php if ($_REQUEST['tipo'] == 4): ?>Tipo documento<?php elseif ($_REQUEST['tipo'] == 7): ?>Progressivo<?php else : ?>Data upload<?php endif; ?>: <?php if ($_REQUEST['tipo'] == 4) echo $row['categ']; else if ($_REQUEST['tipo'] == 7) echo $row['progressivo_atto']; else echo format_date($d, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." ".substr($t, 0, 5) ?>
					</div>
					<div class="minicard">
						Tipo file: <?php print strtoupper($path_parts['extension']) ?>
					</div>
					<div class="minicard" style="margin-left: 7.5%">
						Download: <?php echo $dw ?>
					</div>
				</div>
			</div>
		</a>
        <?php 
	            $x++;
	        }
	        include "../../shared/navigate.php";
		}
        ?>
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
