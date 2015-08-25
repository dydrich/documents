<div class="welcome">
	<p id="w_head">Ultimi documenti inseriti</p>
    <p class="w_text" style="">
    <?php
    /*
     * estrazione ultimi documenti pubblicati
    */
    $number_docs = 4;
    if(!isset($d_type)){
    	$sel_last_docs = "SELECT id, data_upload AS data, file, doc_type, abstract, titolo, link, '' AS anno, '' AS classe, CONCAT_WS(' ', cognome, nome) AS owner, privato, rb_documents.permessi FROM rb_documents, rb_utenti WHERE owner = uid AND ((doc_type BETWEEN 1 AND 6) || (doc_type = 9)) AND privato <> 1 ORDER BY data DESC LIMIT $number_docs";
    }
    else{
    	$sel_last_docs = "SELECT id, data_upload AS data, file, doc_type, abstract, titolo, link, '' AS anno, '' AS classe, CONCAT_WS(' ', cognome, nome) AS owner, privato, rb_documents.permessi FROM rb_documents, rb_utenti WHERE owner = uid AND doc_type = $d_type AND privato <> 1 ORDER BY data DESC LIMIT $number_docs";
    }
	//echo $sel_last_docs;
    $res_last_docs = $db->execute($sel_last_docs);
	if($res_last_docs->num_rows < 1){
	?>
	<span style="font-weight: bold">Nessun documento ancora inserito</span>
	<?php
	}
	while($doc = $res_last_docs->fetch_assoc()){
		if(($doc['privato'] == 1) && ($_SESSION['__user__']->check_perms(intval($doc['permessi'])) == false)) continue;
		$link = $doc['file'];
		
		if($doc['titolo'] == "") {
			$ab = $doc['abstract'];
		}
		else {
			$ab = $doc['titolo'];
		}
		if($doc['doc_type'] == 1)
			$link = ereg_replace("/", "_", $doc['anno'])."/".$doc['classe']."/".$doc['file'];
	?>
		<a href="download_manager.php?doc=document&id=<?php print $doc['id'] ?>" style="font-weight: normal; text-decoration: none"><?php print $ab ?> </a><span style="margin-left: 3px; font-size: 0.8em">di <?php echo $doc['owner'] ?></span><br />
	<?php 
	} 
	?>
	</p>
</div>
