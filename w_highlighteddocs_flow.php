<script>
	function ticker(){
		$('#ticker li:first').slideUp(
			function () {
				$(this).appendTo($('#ticker')).slideDown(); }
		);
	}
	setInterval(function(){ ticker () }, 3000);
</script>
<div class="welcome">
	<p id="w_head" style="margin-bottom: 0">Documenti in evidenza</p>
		<ul id="ticker" class="ticker">
    <?php
    /*
     * estrazione ultimi documenti pubblicati
    */
    $number_docs = 24;
    $sel_ev_docs = "SELECT id, evidenziato AS data, file, doc_type, abstract, titolo, link, permessi, privato FROM rb_documents WHERE evidenziato IS NOT NULL AND evidenziato > NOW() ORDER BY data_upload DESC";
	$res_ev = $db->execute($sel_ev_docs);
	if($res_ev->num_rows > 0){
		$x = 0;
		while($doc_ev = $res_ev->fetch_assoc()){
			if($x >= $number_docs) break;
			if(($doc_ev['privato'] == 1) && ($_SESSION['__user__']->check_perms(intval($doc_ev['permessi'])) == false)) continue;

			if($doc_ev['titolo'] == "") {
				$ab = $doc_ev['abstract'];
			}
			else {
				$ab = $doc_ev['titolo'];
			}
			if($doc_ev['doc_type'] == 1) {
				$link = preg_replace("/\//", "/_/", $doc_ev['anno'])."/".$doc_ev['classe']."/".$doc_ev['file'];
			}
			$ab = truncateString($ab, 80);
			?>
			<li><a href="<?php echo $_SESSION['__path_to_root__'] ?>/modules/documents/load_module.php?area=teachers&page=highlighted_docs" class="attention"><?php print $ab ?></a></li>
			<?php
			$x++;
		}
	}
	else {
		echo "<span>Nessun documento in evidenza</span>";
		$more_space = true;
	}
	?>
		</ul>
</div>
