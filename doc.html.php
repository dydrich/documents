<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: area documenti</title>
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
			var count_subjects = 0;
			var count_classes = 0;
			<?php if ($tipo == 10): ?>
			$('#cls_bs').buttonset();
			$('#subj_bs').buttonset();
			<?php if ($_SESSION['__user__']->getSubject() == 27 || $_SESSION['__user__']->getSubject() == 41): ?>
			$('#std_bs').buttonset();
			$('#cls_bs').on('click', function(e) {
				e.preventDefault();
			});
			$('input.upd_students').on('click', function(event) {
				var idclass = $(this).attr('data-cls');
				$.each($('input.upd_classes[type=checkbox]'), function(idx, value) {
					if ($(this).attr('id') == "cl"+idclass) {
						$(this).prop('checked', true).button('refresh');
					}
					else {
						$(this).prop('checked', false).button('refresh');
					}

				});
				manage_file(10);
				create_fields(10);
			});
			<?php endif; ?>
			$('#tipo_documento').change(function(event) {
				if ($(this).val() != 0) {
					if ($(this).val() > 6) {
						$('#students_row').show(400);
					}
					else {
						$('#students_row').hide(400);
					}
				}

				<?php if (count($subjects) == 1): ?>
				$('#subj_bs').on('click', function(e) {
					e.preventDefault();
				});
				<?php endif; ?>
				create_fields(10);
			});
			$('input[type=checkbox]').change(function(event) {
				create_fields(10);
				manage_file(10);
			});
			<?php endif; ?>
			<?php if ($tipo == 11): ?>
			$('#tipo_documento').on('change', function(event) {
				if ($(this).val() > 4 && $('#classe').val() > 0) {
					load_students($('#classe').val());
					$('#students_row').show(400);
				}
				else {
					$('#students_row').hide(400);
				}
				create_fields(11);
				manage_file(11);
			});
			$('#classe').on('change', function(event) {
				if ($('#tipo_documento').val() > 4) {
					load_students($(this).val());
					$('#students_row').show(400);
				}
				else {
					$('#students_row').hide(400);
				}
				create_fields(11);
				manage_file(11);
			});
			$('#student').on('change', function(event){
				create_fields(11);
			});
			<?php endif; ?>
            <?php if($tipo == 7) : ?>
            if ($('#published')){
                $('#published').datepicker({
                    dateFormat: "dd/mm/yy",
                    minDate: new Date()
                });
            }
            <?php endif; ?>
		});

		var create_fields = function(_type) {
			if ($('#tipo_documento').val() < 2) {
				$('#titolo').val("");
				$('#abstract').val("");
				return false;
			}
			var title = $('#tipo_documento').children(":selected").text();
			var abstract = "Tipo documento: "+$('#tipo_documento').children(":selected").text().toLowerCase();
			abstract += ".\n";

			if (_type == 10) {
				if ($('#tipo_documento').val() < 7) {
					// check subjects
					var sbj = $('input.upd_subjects:checked').length;
					if (sbj > 0) {
						var label_sbj = [];
						$.each($('input.upd_subjects:checked'), function (idx, val) {
							label_sbj.push($('label[for="' + $(this).attr("id") + '"]').text());
						});
						if (sbj == 1) {
							title += " di " + label_sbj[0];
							abstract += "Materia: " + label_sbj[0] + ".\n";
						}
						else {
							var p = label_sbj.join(", ");
							idx = p.lastIndexOf(",");
							before = p.substring(0, idx);
							after = p.substring(idx + 1);
							title += " di " + before + " e" + after;
							abstract += "Materie: " + before + " e" + after + ".\n";
						}
					}
				}
				else if ($('#tipo_documento').val() > 6) {
					// check students
					var count_std = $('input.upd_students:checked').length;
					if (count_std > 0) {
						var label_std = [];
						$.each($('input.upd_students:checked'), function (idx, val) {
							label_std.push($('label[for="' + $(this).attr("id") + '"]').text());
						});
						if (count_std == 1) {
							title += " dell'alunno " + label_std[0];
							abstract += "Alunno: " + label_std[0] + ".\n";
						}
						else {
							var p = label_std.join(", ");
							idx = p.lastIndexOf(",");
							before = p.substring(0, idx);
							after = p.substring(idx + 1);
							title += " degli alunni " + before + " e" + after;
							abstract += "Alunni: " + before + " e" + after + ".\n";
						}
					}
				}

				// check classes
				var cls = $('input.upd_classes:checked').length;
				if (cls > 0) {
					var label_cls = [];
					$.each($('input.upd_classes:checked'), function (idx, val) {
						label_cls.push($('label[for="' + $(this).attr("id") + '"]').text());
					});
					if (cls == 1) {
						if (_type == 10) {
							title += " - classe " + label_cls[0];
							abstract += "Classe: " + label_cls[0];
						}
						else {
							title += " " + label_cls[0];
							abstract += "Classe: " + label_cls[0];
						}
					}
					else {
						title += " - classi " + label_cls.join(", ");
						abstract += "Classi: " + label_cls.join(", ");
					}
				}
			}
			else {
				if ($('#tipo_documento').val() > 4 && $('#student').val()) {
					var label_st = $('#student').children(":selected").text();
					title += " dell'alunno " + label_st;
					abstract += "Alunno: " + label_st + ".\n";
				}
				var label_cls = $('#classe').children(":selected").text();
				title += " " + label_cls;
				abstract += "Classe: " + label_cls;
			}

			$('#titolo').val(title);
			$('#abstract').val(abstract);
		};

		var manage_file = function(_tipo) {
			var cls;
			var t_d = $('#tipo_documento').val();
			if (_tipo == 10) {
				cls = $('input.upd_classes:checked').length;
				var sbj = $('input.upd_subjects:checked').length;
			}
			else {
				cls = $('#classe').val();
			}
			if (cls < 1 || t_d == 0) {
				$('#if_container').hide(400);
			}
			else {
				$('#if_container').show(400);
			}
		};

		var load_students = function(cls) {
			var url = "../../shared/get_students.php";

			$.ajax({
				type: "POST",
				url: url,
				data: {classe: cls},
				dataType: 'json',
				error: function() {
					j_alert("error", "Si è verificato un errore di rete: controlla lo stato della tua connessione e riprova");
					return false;
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
						j_alert("error", json.message);
						console.log(json.dbg_message);
					}
					else if (json.status == "ko") {
						j_alert("error", json.message);
						console.log(json.dbg_message);
					}
					else {
						$('#student').empty().append($("<option value='0'>.</option>"));
						json.data.forEach(function(obj) {
							$('#student').append($("<option value='"+obj.id+"'>"+obj.name+"</option>" ));
						});
					}
				}
			});
		};

		var loading = function(vara){
			background_process("Stiamo caricando il file", vara, false);
		};

		var loading_done = function(r){
			$('#del_upl').show();
			$('#server_file').val(r);
			loaded("Il file è stato caricato");
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
		        if(!confirm("Cancellare il documento?"))
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
				msg += idx+". Il titolo è obbligatorio\n";
				go_ahead = false;
				$('#r_titolo').addClass("has_error");
		    }
		    else {
		        $('#r_titolo').removeClass("has_error");
		    }
		    if($('#abstract').val() == ""){
				idx++;
				msg += idx+". L'abstract è obbligatorio\n";
				go_ahead = false;
				$('#r_abstract').addClass("has_error");
		    }
		    else {
		        $('#r_abstract').removeClass("has_error");
		    }
		    if($('#server_file').val() == ""){
				idx++;
				msg += idx+". Il file è obbligatorio\n";
				go_ahead = false;
				$('#r_file').addClass("has_error");
		    }
		    else {
		        $('#r_file').removeClass("has_error");
		    }
		    if (tipo == 4){
		        if($('#categoria').val() == "0"){
		            idx++;
		            msg += idx+". La categoria è obbligatoria\n";
		            go_ahead = false;
		            $('#r_cat').addClass("has_error");
		        }
		        else {
		            $('#r_cat').removeClass("has_error");
		            if($('#categoria').val() == "2" && $('#materia').val() == "0"){
		                idx++;
		                msg += idx+". La materia è obbligatoria\n";
		                go_ahead = false;
		                $('#r_cat').addClass("has_error");
		            }
		        }
		    }
		    if (tipo == 7){
		        if($('#categoria').val() == "0"){
		            idx++;
		            msg += idx+". La categoria è obbligatoria\n";
		            go_ahead = false;
		            $('#r_cat').addClass("has_error");
		        }
		        else {
		            $('#r_cat').removeClass("has_error");
		        }
		        if($('#scadenza').val() == ""){
		            idx++;
		            msg += idx+". La scadenza è obbligatoria\n";
		            go_ahead = false;
		            $('#r_scad').addClass("has_error");
		        }
		        else {
		            $('#r_scad').removeClass("has_error");
		        }
		    }
			if (tipo == 10) {
				var doc_t = $('#tipo_documento').val();
				if (doc_t < 1) {
					idx++;
					msg += idx+". È obbligatorio indicare il tipo di documento\n";
					go_ahead = false;
					$('#r_tipo').addClass("has_error");
				}
				else {
					$('#r_tipo').removeClass("has_error");
				}
				count_subjects = $('input.upd_subjects:checked').length;
				count_classes = $('input.upd_classes:checked').length;
				if (doc_t < 7 && doc_t > 0) {
					// need one or more classes and at least a subject
					if (count_subjects < 1) {
						idx++;
						msg += idx+". È obbligatorio indicare almeno una materia\n";
						go_ahead = false;
						$('#subject_row').addClass("has_error");
					}
					else {
						$('#subject_row').removeClass("has_error");
					}
				}
				if (count_classes < 1) {
					idx++;
					msg += idx+". È obbligatorio indicare almeno una classe\n";
					go_ahead = false;
					$('#classes_row').addClass("has_error");
				}
				else {
					$('#classes_row').removeClass("has_error");
				}
			}
			if (tipo == 11) {
				var doc_t = $('#tipo_documento').val();
				if (doc_t < 1) {
					idx++;
					msg += idx+". È obbligatorio indicare il tipo di documento\n";
					go_ahead = false;
					$('#r_tipo').addClass("has_error");
				}
				else {
					$('#r_tipo').removeClass("has_error");
				}
				if($('#description').val() == ""){
					idx++;
					msg += idx+". La descrizione è obbligatoria\n";
					go_ahead = false;
					$('#r_cat').addClass("has_error");
				}
				else {
					$('#r_cat').removeClass("has_error");
				}

				if ($('#classe').val() < 1) {
					idx++;
					msg += idx+". È obbligatorio indicare almeno una classe\n";
					go_ahead = false;
					$('#classes_row').addClass("has_error");
				}
				else {
					$('#classes_row').removeClass("has_error");
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
					j_alert("error", "Si è verificato un errore di rete: controlla lo stato della tua connessione e riprova");
					return false;
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
						j_alert("error", json.message);
						console.log(json.dbg_message);
					}
					else if (json.status == "ko") {
						j_alert("error", json.message);
						console.log(json.dbg_message);
					}
					else {
						j_alert("alert", json.message);
						if (par == 2){
							window.setTimeout(function(){
								document.location.href = "docs.php?tipo="+tipo;
							}, 4000);
						}
						else if (par == 1) {
							window.setTimeout(
								function(){
									document.location.href = "doc.php?tipo="+tipo+"&_i=0";
								}, 4000
							);
						}
						else {
							window.setTimeout(function() {
								$('fieldset').animate({
									backgroundColor: '#EEEEEE'
								}, 900);
							}, 3000);
						}
					}
				}
		    });
		};

		var del_file = function(){
			if($('#server_file').val() == ""){
				j_alert("error", "Non hai ancora fatto l'upload di alcun file");
				return false;
			}
			var url = "document_manager.php";

			$.ajax({
				type: "POST",
				url: url,
				data: {server_file: $('#server_file').val(), action: "4", tipo: $('#tipo').val(), doc_type: $('#doc_type').val()},
				dataType: 'json',
				error: function() {
					j_alert("error", "Si è verificato un errore di rete: controlla lo stato della tua connessione e riprova");
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
						j_alert("error", json.message);
						console.log(json.dbg_message);
					}
					else {
						j_alert("alert", json.message);
						reload_iframe();
						$('#server_file').val("");
						$('#del_upl').hide();
					}
				}
		    });


		};

		var reload_iframe = function(){
			<?php if ($tipo == 10) { ?>
			$('#aframe').attr('src', 'upload_manager.php?upl_type=teaching_doc&area=teachers&tipo=<?php echo $tipo; if ($ext != null) echo '&ext='.implode(",", $ext) ?>');
			<?php } else if ($tipo == 11) { ?>
			$('#aframe').attr('src', 'upload_manager.php?upl_type=document_cdc&area=teachers&tipo=<?php echo $tipo; if ($ext != null) echo '&ext='.implode(",", $ext) ?>');
			<?php } else { ?>
			$('#aframe').attr('src', 'upload_manager.php?upl_type=document&area=teachers&tipo=<?php echo $tipo; if ($ext != null) echo '&ext='.implode(",", $ext) ?>');
			<?php } ?>
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

		var load_iframe = function(file){
			$('#server_file').val('');
			$('#if_container').html('<div id="iframe"><iframe src="upload_manager.php?upl_type=document&f='+file+'&area=teachers&tipo=<?php echo $tipo ?>&docID=<?php echo $_REQUEST['_i'] ?>" id="aframe"></iframe></div><a href="#" onclick="del_file()" id="del_upl">Annulla upload</a>');
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
	<div style="top: -10px; margin-left: 35px; margin-bottom: -20px" class="rb_button">
		<a href="docs.php?tipo=<?php echo $tipo ?>">
			<img src="../../images/47bis.png" style="padding: 12px 0 0 12px" />
		</a>
	</div>
<?php
if ($tipo == 4 || $tipo == 7 || $tipo == 10 || $tipo == 11 || $tipo == 2){
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
