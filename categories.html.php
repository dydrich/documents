<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>::categorie albo</title>
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
	<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
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
			$('#new_link').click(function(){
				document.location.href = "category.php?id=0&action=1";
			});
			$('.del_x').click(function(event){
				event.preventDefault();
				cat = $(this).attr("data-cat");
				del_cat(cat, 2);
			})
		});

		var del_cat = function(id_cat, action){
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
							show_error("Si è verificato un errore di rete: controlla lo stato della tua connessione e riprova");
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
								$('#cat'+id_cat).fadeOut(2000);
								j_alert("alert", json.message);
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
		<div style="position: absolute; top: 74px; margin-left: 655px; margin-bottom: 10px" class="rb_button">
			<a href="#" id="new_link">
				<img src="../../images/39.png" style="padding: 12px 0 0 12px" />
			</a>
		</div>
		<div class="card_container" style="margin-top: 20px">
 	    <?php
 	    while($cat = $res_type->fetch_assoc()){
 	    ?>
	        <a href="category.php?id=<?php echo $cat['id_categoria']?>&action=3">
		        <div class="card" id="cat<?php echo $cat['id_categoria'] ?>">
			        <div class="card_title accent_color">
				        <?php echo $cat['nome'] ?>
				        <div style="float: right; margin-right: 20px">
					        <a href="../../shared/no_js.php" class="del_x" data-cat="<?php echo $cat['id_categoria'] ?>">
						        <img src="../../images/51.png" style="position: relative; bottom: 2px" />
					        </a>
				        </div>
			        </div>
			        <div class="card_minicontent">
				        <?php print $cat['descrizione'] ?>
			        </div>
		        </div>
		    </a>
 	    <?php 
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
