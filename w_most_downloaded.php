<div class="welcome">
	<p id="w_head">Documenti pi&ugrave; scaricati degli ultimi 30 giorni</p>
    <p class="w_text" style="">
    <?php
    /*
     * estrazione documenti
    */
    $number_docs = 4;
    $sel_docs_dw = "SELECT rb_documents.id, file, rb_documents.doc_type, abstract, titolo, link, COUNT(rb_downloads.id) AS counter, privato, permessi FROM rb_documents, rb_downloads WHERE doc_id = rb_documents.id AND rb_documents.doc_type != 7 AND data_dw > DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY rb_documents.id, file, rb_documents.doc_type, abstract, titolo, link ORDER BY counter DESC LIMIT $number_docs";
	$res_last_dw = $db->execute($sel_docs_dw);
	if($res_last_dw->num_rows == 0){
	?>
		<span style="">Nessun documento scaricato negli ultimi 30 giorni</span>
	<?php
	}
	else{
		while($doc_dw = $res_last_dw->fetch_assoc()){
			if(($doc_dw['privato'] == 1) && ($_SESSION['__user__']->check_perms(intval($doc_dw['permessi'])) == false)) continue;
			$link = $doc_dw['file'];
			
			if($doc_dw['titolo'] == ""){
				$ab = $doc_dw['abstract'];
			}
			else{
				$ab = $doc_dw['titolo'];
			}
	?>
		<a href="download_manager.php?doc=document&id=<?php print $doc_dw['id'] ?>" style="font-weight: normal; text-decoration: none"><?php print $ab ?> </a><span style="margin-left: 3px; font-size: 0.8em"> - <?php echo $doc_dw['counter'] ?> download</span><br />
	<?php
		}
	} 
	?>
	</p>
</div>