<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>::categorie albo</title>
<link rel="stylesheet" href="../../css/reg.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="theme/style.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/jquery/jquery-ui.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
$(function(){
	$('#new_link').button();
	$('#new_link').click(function(){
		document.location.href = "category.php?id=0&action=1";
	});
});

var del_cat = function(id_cat, action, row){
	if(action == 2){
		if(!confirm("Confermi l'eliminazione di questa categoria?")){
			return false;
		}
		else{
			$.ajax({
				type: "POST",
				url: "category_manager.php",
				data: {id: id_cat, action: action},
				dataType: 'json',
				error: function() {
					show_error("Errore di trasmissione dei dati");
				},
				succes: function() {
					
				},
				complete: function(data){
					r = data.responseText;
					if(r == "null"){
						return false;
					}
					var json = $.parseJSON(r);
					if (json.status == "kosql"){
						show_error(json.message);
						console.log(json.dbg_message);
					}
					else {
						$('#tr'+row).hide();
						$('#not1').text(json.message);
						$('#not1').show(1000);
						$('#not1').hide(3000);
					}
				}
		    });
		}
	}
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
		Gestione categorie di documento: albo pretorio
	</div>
	<div class="list_header">
		<div class="list_title ctit1"><span style="padding-left: 10px">Codice</span></div>
		<div class="list_title ctit2">Nome</div>
		<div class="list_title ctit3">Descrizione</div>
		<div class="list_title ctit4"></div>
	</div>
 	    <table class="list" id="ctable">
 	    <thead>
 	    <tbody id="tbody">
 	    <?php
 	    $row = 0;
 	    while($cat = $res_type->fetch_assoc()){
 	    ?>
 	    	<tr id="tr<?php print $row ?>">
 	    		<td id="row<?php print $row ?>_1" class="ctit1"><?php print utf8_decode($cat['codice']) ?></td>
 	    		<td id="row<?php print $row ?>_2" class="ctit2"><a href="category.php?id=<?php echo $cat['id_categoria']?>&action=3" style="text-decoration: none"><?php echo utf8_decode($cat['nome']) ?></a></td>
 	    		<td id="row<?php print $row ?>_3" class="ctit3"><?php print utf8_decode($cat['descrizione']) ?></td>
 	    		<td class="ctit4"><a href="#" class="del_x" onclick="del_cat(<?php echo $cat['id_categoria'] ?>, 2, <?php echo $row ?>)">x</a></td>
 	    	</tr>
 	    <?php 
 	    	$row++;
 	    } 
 	    ?>
 	    </tbody>
 	    </table>
 	    <div class="button_group">
 	    	<button id="new_link">Nuova categoria</button>
 	    </div>
	</div>	
<p class="spacer"></p>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
</body>
</html>
