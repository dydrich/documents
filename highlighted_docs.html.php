<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: area docenti</title>
<link rel="stylesheet" href="../../intranet/teachers/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
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
		Documenti in evidenza
	</div>
	<div class="list_header">
		<div style="width: 45%; float: left; position: relative; top: 30%"><span style="padding-left: 10px">Titolo</span></div>
		<div style="width: 40%; float: left; position: relative; top: 30%">Tipo documento</div>
		<div style="width: 15%; float: left; position: relative; text-align: center; top: 30%">Tipo file</div>
	</div>
	<table style="margin: 20px auto 0 auto; width: 95%">
        <?php 	
		while($row = $res_ev->fetch_assoc()){
			if($row['privato'] == 1){
				if(!($row['permessi']&$_SESSION['__user__']->getPerms())) break;
			}
			$path_parts = pathinfo($row['file']);
        ?>
        <tr class="docs_row">
            <td style="width: 45%"><a href="download_manager.php?doc=document&id=<?php print $row['id'] ?>" style="font-weight: normal; text-decoration: none"><?php print $row['titolo'] ?></a></td>
            <td style="width: 40%"><?php print $row['commento'] ?></td>
            <td style="width: 15%; text-align: center"><?php print strtoupper($path_parts['extension']) ?></td>
        </tr>
        <?php 
	    }
        ?>
        <tr>
            <td colspan="3" style="height: 30px"></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;&nbsp;&nbsp;</td>
        </tr>
    </table>
</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>