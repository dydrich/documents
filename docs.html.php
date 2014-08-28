<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?></title>
<link rel="stylesheet" href="../../css/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/jquery/jquery-ui.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
$(function(){
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
	<div class="group_head">
		Gestione <?php echo strtolower($doc_type) ?>
	</div>
	<div class="list_header">
		<div class="list_title" id="tit1"><span style="padding-left: 10px">Titolo</span></div>
		<div class="list_title" id="tit2"><?php if ($_REQUEST['tipo'] == 4): ?>Tipo documento<?php elseif ($_REQUEST['tipo'] == 7): ?>Progressivo<?php else : ?>Data upload<?php endif; ?></div>
		<div class="list_title list_f" id="tit">Tipo file</div>
		<div class="list_title list_f" >AS</div>
		<div class="list_title list_f" >DW</div>
	</div>
	<table class="list">
        <?php 
        $x = 1; // contatore grafico
		if($res_docs->num_rows > $limit)
		   	$max = $limit;
		else
		   	$max = $res_docs->num_rows;
		
		if($res_docs->num_rows == 0){
		?>	
			<tr>
		    	<td colspan="5" class="nodocs">Nessun documento presente</td> 
		    </tr>
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
        <tr class="<?php echo $row_class ?>">
            <td class="list_f1"><a href="doc.php?_i=<?php print $row['id'] ?>"><?php print stripslashes($row['titolo']) ?></a></td>
            <td class="list_f2"><?php if ($_REQUEST['tipo'] == 4) echo $row['categ']; else if ($_REQUEST['tipo'] == 7) echo $row['progressivo_atto']; else echo format_date($d, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." ".substr($t, 0, 5) ?></td>
            <td class="_center list_f"><?php print strtoupper($path_parts['extension']) ?></td>
            <td class="_center list_f"><?php print $row['descrizione'] ?></td>
            <td class="_center list_f"><?php echo $dw ?></td>
        </tr>
        <?php 
	            $x++;
	        }
	        include "../../shared/navigate.php";
		}
        ?>
        <tr>
            <td colspan="5" class="void"></td>
        </tr>
        <tr>
            <td colspan="5" class="_right">
               	<button id="button">Nuovo documento</button>
            </td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;&nbsp;&nbsp;</td>
        </tr>
    </table>
</div>
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
