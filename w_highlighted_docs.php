<div class="welcome">
	<p id="w_head" style="margin-bottom: 0; background-image: none">
		<i class="fa fa-file-text" style="position: relative; left: -30px; font-size: 1.4em"></i>
		<span style="position: relative; left: -20px">Documenti in evidenza</span>
	</p>
    <p class="w_text" id="hd">
    <?php
    /*
     * estrazione ultimi documenti pubblicati
    */
    $number_docs = 4;
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
				$link = ereg_replace("/", "_", $doc_ev['anno'])."/".$doc_ev['classe']."/".$doc_ev['file'];
			}
	?>
		<a href="download_manager.php?doc=document&id=<?php print $doc_ev['id'] ?>" class="attention"><?php print $ab ?></a><br />
	<?php
			$x++;
		}
		if($x >= $number_docs){ 
	?>
		<a href="highlighted_docs.php" style="text-decoration: none">[Vedi tutti...]</a>
	<?php
		}
	}
	else {
		echo "<span>Nessun documento in evidenza</span>";
		$more_space = true;
	}
	?>
	</p>
</div>