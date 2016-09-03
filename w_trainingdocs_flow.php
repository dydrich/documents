<?php
/*
 * estrazione ultimi documenti pubblicati
*/
$number_docs = 24;
$sel_ev_docs = "SELECT id, evidenziato AS data, file, doc_type, abstract, titolo, link, permessi, privato FROM rb_documents WHERE evidenziato IS NOT NULL AND evidenziato > NOW() AND rb_documents.doc_type = 13 ORDER BY data_upload DESC";
$res_ev = $db->execute($sel_ev_docs);
$count_docs = $res_ev->num_rows;
if ($count_docs < 4) {
    $ticker_height = 5 + (25 * $count_docs);
}
else {
    $ticker_height = 100;
}

?>
<script>
    function tr_ticker(){
        $('#tr_ticker li:first').slideUp(
            function () {
                $(this).appendTo($('#tr_ticker')).slideDown(); }
        );
    }
    setInterval(function(){ tr_ticker () }, 3000);
</script>
<div class="welcome">
    <p id="w_head" style="margin-bottom: 0; background-image: none">
        <i class="fa fa-book" style="position: relative; left: -30px; font-size: 1.4em"></i>
        <span style="position: relative; left: -20px">Formazione docenti</span>
    </p>
    <div id="ticker_container" style="height: <?php echo $ticker_height ?>px">
        <ul id="tr_ticker" class="ticker">
            <?php
            if($res_ev->num_rows > 0){
                $x = 0;
                while($doc_ev = $res_ev->fetch_assoc()){
                    if($x >= $number_docs) break;

                    if($doc_ev['titolo'] == "") {
                        $ab = $doc_ev['abstract'];
                    }
                    else {
                        $ab = $doc_ev['titolo'];
                    }

                    $ab = truncateString($ab, 80);
                    ?>
                    <li><a href="<?php echo $_SESSION['__path_to_root__'] ?>/modules/documents/load_module.php?area=teachers&page=documents&value=13" class="attention"><?php print $ab ?></a></li>
                    <?php
                    $x++;
                }
            }
            else {
                echo "<span>Nessun documento</span>";
                $more_space = true;
            }
            ?>
        </ul>
    </div>
</div>
