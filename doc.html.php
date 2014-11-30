<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: area documenti</title>
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
			$('#save').button();
			if ($('#bdel')){
				$('#bdel').button();
			}
			if ($('#highlighted')){
				$('#highlighted').datepicker({
					dateFormat: "dd/mm/yy"
				});
			}
			if ($('#scadenza')){
				$('#scadenza').datepicker({
					dateFormat: "dd/mm/yy"
				});
			}
			<?php if($tipo == 4): ?>
			if ($('#categoria')){
				$('#categoria').change(function(){
					if($('#categoria').val() == 2){
						$('#materia').attr("disabled", false);
						if ($('#private')){
							$('#private').attr("disabled", false);
						}
					}
					else{
						$('#materia').attr("disabled", true);
						if ($('#private')){
							$('#private').attr("disabled", true);
						}
					}
				});
			}
			if ($('#private')){
				$('#private').change(function(){
					if($('#private').val() == 1){
						toggle_classes('show');
						$('#classe').val(0);
					}
					else{
						toggle_classes('hide');
					}
				});

			}
			if ($('#classe')){
				$('#classe').change(function(){
					if($('#classe').val() > 0){
						$('#private').val(0);
						toggle_classes('hide');
					}
				});
			}
			<?php endif; ?>
			<?php if($tipo != 7): ?>
			$("#tag").autocomplete({
		        source: "get_tags.php",
		        minLength: 2,
		        select: function(event, ui){
					//uid = ui.item.uid;
					//tp = ui.item.type;
					//$('#targetID').val(uid);
					//$('#target_type').val(tp);
		        }
		    });
			<?php endif; ?>
		});

		var loading = function(vara){
			$('#not1').text("Attendere il caricamento del file");
			$('#not1').show(500);
		};

		var loaded = function(r){
			//var json = $.parseJSON(r);
			$('#not1').text("Caricamento completato");
			$('#del_upl').show();
			$('#not1').hide(1500);
			$('#server_file').val(r);
		};

		var show_error = function(text){
			//$('#iframe').show();
			$('#not1').text(text);
			$('#not1').addClass("error");
			$('#not1').show(500);
		};

		var toggle_classes = function(param){
			if (param == "show"){
				$('#classes').show();
			}
			else {
				$('#classes').hide();
			}
		};

		var go = function go(event, par, id, tipo){
			event.preventDefault();
			var __tags = _tags.join(",");
			$('#tags').val(__tags);
			if(par == 2){
		        if(!confirm("Sei sicuro di voler cancellare questo documento?"))
		            return false;
		    }
			$('#_i').val(id);
		    $('#action').val(par);

		    // controllo campi
		    msg = "Ci sono degli errori nel form. Ricontrolla e correggi.\n";
		    idx = 0;
		    go_ahead = true;
		    if($('#titolo').val() == ""){
				idx++;
				msg += idx+". Il titolo e` obbligatorio\n";
				go_ahead = false;
				$('#r_titolo').addClass("has_error");
		    }
		    else {
		        $('#r_titolo').removeClass("has_error");
		    }
		    if($('#abstract').val() == ""){
				idx++;
				msg += idx+". L'abstract e` obbligatorio\n";
				go_ahead = false;
				$('#r_abstract').addClass("has_error");
		    }
		    else {
		        $('#r_abstract').removeClass("has_error");
		    }
		    if($('#server_file').val() == ""){
				idx++;
				msg += idx+". Il file e` obbligatorio\n";
				go_ahead = false;
				$('#r_file').addClass("has_error");
		    }
		    else {
		        $('#r_file').removeClass("has_error");
		    }
		    if (tipo == 4){
		        if($('#categoria').val() == "0"){
		            idx++;
		            msg += idx+". La categoria e` obbligatoria\n";
		            go_ahead = false;
		            $('#r_cat').addClass("has_error");
		        }
		        else {
		            $('#r_cat').removeClass("has_error");
		            if($('#categoria').val() == "2" && $('#materia').val() == "0"){
		                idx++;
		                msg += idx+". La materia e` obbligatoria\n";
		                go_ahead = false;
		                $('#r_cat').addClass("has_error");
		            }
		        }
		    }
		    if (tipo == 7){
		        if($('#categoria').val() == "0"){
		            idx++;
		            msg += idx+". La categoria e` obbligatoria\n";
		            go_ahead = false;
		            $('#r_cat').addClass("has_error");
		        }
		        else {
		            $('#r_cat').removeClass("has_error");
		        }
		        if($('#scadenza').val() == ""){
		            idx++;
		            msg += idx+". La scadenza e` obbligatoria\n";
		            go_ahead = false;
		            $('#r_scad').addClass("has_error");
		        }
		        else {
		            $('#r_scad').removeClass("has_error");
		        }
		    }
			if (!go_ahead){
				alert(msg);
				return false;
			}

		    var url = "document_manager.php";

		    $.ajax({
				type: "POST",
				url: url,
				data: $('#doc_form').serialize(),
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
						$('#not1').text(json.message);
						$('#not1').show(1000);
						$('#not1').hide(3000);
						if (par == 2){
							window.setTimeout(function(){document.location.href = "docs.php?tipo="+tipo}, 4000);
						}
						else if (par == 1) {
							window.setTimeout(
								function(){
									document.location.href = "doc.php?tipo="+tipo+"&_i=0";
								}, 4000
							);
						}
					}
				}
		    });
		};

		var del_file = function(){
			if($('#server_file').val() == ""){
				alert("Non hai ancora fatto l'upload di alcun file");
				return false;
			}
			var url = "document_manager.php";

			$.ajax({
				type: "POST",
				url: url,
				data: {server_file: $('#server_file').val(), action: "4", tipo: $('#tipo').val(), doc_type: $('#doc_type').val()},
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
						$('#not1').removeClass('error');
						$('#not1').text(json.message);
						$('#not1').show(1000);
						$('#not1').hide(3000);
						reload_iframe();
						$('#server_file').val("");
						$('#del_upl').hide();
					}
				}
		    });


		};

		var reload_iframe = function(){
			$('#aframe').attr('src', 'upload_manager.php?upl_type=document&area=teachers&tipo=<?php echo $tipo ?>');
		};

		tid = <?php if (isset($tags)) echo count($tags); else echo "0" ?>;
		_tags = new Array();
		<?php
		$i = 0;
		if (isset($tags)){
			foreach ($tags as $t){
		?>
		_tags[<?php echo $i ?>] = '<?php echo $t ?>';
		<?php
				$i++;
			}
		}
		?>

		var addTag = function(event){
			event.preventDefault();
			tag = $('#tag').val();
			new_p = "<p id='tag_"+tid+"' style='height: 16px; margin: 3px 0 0 0'><a href='#' onclick='deleteTag("+tid+")' style='margin-right: 5px'><img src='../../images/list_remove.png' /></a><span style='position: relative; top: -2px'>"+tag+"</span></p>";
			$('#tags_ct').append(new_p);
			$('#tag').val('');
			$('#tag').focus();
			_tags[tid] = tag;
			tid++;
			return false;
		};

		var deleteTag = function(tag){
			$('#tag_'+tag).hide();
			_tags.splice(tag, 1);
		};

		var load_iframe = function(){
			$('#if_container').html('<div id="iframe"><iframe src="upload_manager.php?upl_type=document&area=teachers&tipo=<?php echo $tipo ?>" id="aframe"></iframe></div><a href="#" onclick="del_file()" id="del_upl">Annulla upload</a>');
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
<div class="notification" id="not1"></div>
<?php 
if ($tipo == 4 || $tipo == 7){
	include "content{$tipo}.php";
}
else {
	include "content.php";
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
