<?php

require_once "../../lib/start.php";
require_once "../../lib/SchoolPDF.php";

check_session();
check_permission(DIR_PERM|DSG_PERM);

$_SESSION['__path_to_root__'] = "../../";
$_SESSION['__path_to_mod_home__'] = "./";

$month = $_REQUEST['m'];
$year  = $_REQUEST['y'];

/*
 * estremi della ricerca
 */
$start = "{$year}-{$month}-01";
$end = "";
switch($month){
	case "01":
	case "03":
	case "05":
	case "07":
	case "08":
	case "10":
	case "12":
		$end = "{$year}-{$month}-31";
		break;
	case "04":
	case "06":
	case "09":
	case "11":
		$end = "{$year}-{$month}-30";
		break;
	case "02":
		/*
		 * TODO: gestire bisestile
		 */
		$end = "{$year}-{$month}-28";
		break;
}


$sel_docs = "SELECT rb_documents.*, rb_categorie_docs.nome, CONCAT_WS(' ', rb_utenti.nome, rb_utenti.cognome) AS owner FROM rb_documents, rb_categorie_docs, rb_utenti WHERE owner = uid AND rb_documents.categoria = rb_categorie_docs.id_categoria AND doc_type = 7  ";
$sel_docs .= "AND (data_upload BETWEEN '{$start}' AND '{$end}') ORDER BY rb_documents.data_upload DESC ";
//print $sel_docs;
$res_docs = $db->executeQuery($sel_docs);

setlocale(LC_ALL, "it_IT");

class MYPDF extends SchoolPDF {

	private $y_position = 0.0;

	function pageHeader($month, $year){
		$mesi = array("", "gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre");
		$this->SetY(25.0);
		$this->SetFont('', 'B', 9);
		//$this->SetFillColor(232, 234, 236);
		$this->SetTextColor(0);
		$this->Cell(180, 4, "Riepilogo albo pretorio del mese di {$mesi[intval($month)]} {$year}", 0, 0, "C");
		$this->setCellPaddings(0, 0, 0, 3);
		$this->SetLineStyle(array('width' => .1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(128, 128, 128)));
		$this->SetTextColor(0);
	}

	public function pageBody($month, $year, $res_docs) {
		

		$idx = 0;
		$this->y_position = 30.0;
		$this->SetY($this->y_position);
		$this->pageHeader($month, $year);
		
		while($doc = $res_docs->fetch_assoc()){
			$abs = $atto = $prot = "";
			$abs = ($doc['abstract'] != "" && $doc['abstract'] != $doc['titolo']) ? $doc['titolo']." - ".$doc['abstract'] :
				$doc['titolo'];
			$abs = $doc['progressivo_atto']."  -  ".$abs;
			$atto =  ($doc['numero_atto'] != "") ? $doc['numero_atto'] : "non indicato";
			$prot =  ($doc['protocollo'] != "") ? $doc['protocollo'] : "non indicato";
			 
			$this->y_position += 10.0;
			if($this->y_position > 250){
				$this->endPage();
				$this->startPage();
				$this->pageHeader($month, $year);
				$this->y_position = 40;
			}
			$this->SetY($this->y_position);
			$this->SetFont('', 'B', 8);
			$this->MultiCell(180, 4, $abs, 0, "L");
			$this->SetFont('', '', 8);
			$this->y_position += 6.0;
			$this->SetY($this->y_position);
			$this->Cell(20, 5, "Numero atto: {$atto}", 0, 0);
			$this->y_position += 4.0;
			$this->SetY($this->y_position);
			$this->Cell(20, 5, "Protocollo: {$prot}", 0, 0);
			$this->y_position += 4.0;
			$this->SetY($this->y_position);
			$this->Cell(20, 5, "Pubblicato il ".format_date(substr($doc['data_upload'], 0, 10), SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ore ".substr($doc['data_upload'], 11, 5)." da {$doc['owner']}", 0, 0);
			$this->y_position += 4.0;
			$this->SetY($this->y_position);
			$this->Cell(20, 5, "Categoria: ".utf8_decode($doc['nome']), 0, 0);
		}
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("");
$pdf->SetTitle("Riepilogo mensile albo pretorio");

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Scuola Media Statale Arborea - Lamarmora, Iglesias", $_SESSION['__current_year__']->to_string()."  - Dettaglio delle assenze di  ".$alunno['cognome']." ".$alunno['nome']." (".mysql_num_rows($res_assenze).")");
$pdf->SetHeaderData("", 0, "Istituto Comprensivo \"C. Nivola\"", "Via Pacinotti - Iglesias (CI)");

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8.0));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 8.0));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
$pdf->SetFont('helvetica', '', 10);

$pdf->SetLineWidth(0.1);

// add a page
$pdf->AddPage("P");

$pdf->pageBody($month, $year, $res_docs);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('riepilogo_albo.pdf', 'D');
