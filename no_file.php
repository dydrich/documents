<?php 

require_once "../../lib/start.php";

ini_set("display_errors", DISPLAY_ERRORS);

check_session();

$navigation_label = "Area documenti";

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: file non trovato</title>
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/documents.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/page.js"></script>
</head>
<body>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation.php" ?>
<div id="main">
<div id="right_col">
<?php include "menu.php" ?>
</div>
<div id="left_col">
	<div class="welcome">
		<p id="w_head" style="font-weight: bold">File non trovato</p>
		<p class="w_text">
			Il file <span class="attention"><?php if ($_SESSION['__user__']->getUsername() == 'rbachis') echo $_SESSION['no_file']['file'] ?></span> da te richiesto non &egrave; presente nel server.<br /><br />
			Il problema &egrave; stato segnalato all'amministratore del sito, e sar&agrave; risolto al pi&ugrave; presto.<br /><br />
			Ti preghiamo di riprovare pi&ugrave; tardi e di scusare il disagio.
	 	</p>
	 	<p class="w_text">
	 	<a href="../../<?php echo $_SESSION['no_file']['referer'] ?>">Torna alla pagina precedente</a>
	 	</p>
	</div>
</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
