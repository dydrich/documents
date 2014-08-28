<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>: area documenti</title>
<link rel="stylesheet" href="../../css/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/jquery/jquery-ui.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
<script>
$(function() {
    if($('#accordion')){
        $( "#accordion" ).accordion({
      		heightStyle: "content"
    	});
    }
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
	<div class="group_head">
		Gestione e visualizzazione documenti
	</div>
	<?php if ($_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM|DOC_PERM)): ?>
		<?php include "w_highlighted_docs.php" ?>
		<?php include "w_last_docs.php" ?>
		<?php include "w_most_downloaded.php" ?>
	<?php endif; ?>
</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
