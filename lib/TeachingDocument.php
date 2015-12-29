<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 11/08/15
 * Time: 10.38
 * documenti relativi all'insegnamento
 */

namespace eschool;

require_once "Document.php";
require_once "../../lib/MimeType.php";
require_once "../../lib/RBUtilities.php";

class TeachingDocument extends \Document {

	private $classes;
	private $student;
	private $subjects;
	private $originalFileName;
	private $teachingDocumentType;

	public function __construct($id, $data, $cls, $sub, \MYSQLDataLoader $dl, $student){
		parent::__construct($id, $data, $dl);
		$this->id = $id;
		if ($cls == null) {
			$this->loadClasses();
		}
		else {
			$this->classes = $cls;
		}

		if ($sub == null) {
			$this->loadSubjects();
		}
		else {
			$this->subjects = $sub;
		}

		$this->teachingDocumentType = $data['categoria'];

		$this->deleteOnDownload = false;
		$this->originalFileName = $this->file;

		$rb = \RBUtilities::getInstance($this->datasource);
		$y = $this->year;
		$this->year = $rb->loadYearFromID($y);

		$ordine_scuola = $this->owner->getSchoolOrder();
		$school_order_directory = "scuola_media";
		if ($ordine_scuola == 2){
			$school_order_directory = "scuola_primaria";
		}

		$user_directory = $this->owner->getFullName();
		$user_directory = preg_replace("/ /", "_", $user_directory);
		$user_directory = strtolower($user_directory);

		$cls = $this->classes[0];

		$this->filePath = "download/registri/{$this->year->get_descrizione()}/{$school_order_directory}/docenti/{$user_directory}/";

		// sostegno
		if ($student != null && ($this->subjects[0] == 27 || $this->subjects[0] == 41) ) {
			$this->student = $rb->loadUserFromUid($student, "student");
		}
		else {
			if ($this->subjects[0] == 27 || $this->subjects[0] == 41) {
				$this->loadStudents();
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getClasses() {
		return $this->classes;
	}

	/**
	 * @param mixed $classes
	 */
	public function setClasses($classes) {
		$this->classes = $classes;
	}

	/**
	 * @return \StudentBean
	 */
	public function getStudent() {
		return $this->student;
	}

	/**
	 * @param \StudentBean $student
	 */
	public function setStudent($student) {
		$this->student = $student;
	}

	/**
	 * @return mixed
	 */
	public function getSubjects() {
		return $this->subjects;
	}

	/**
	 * @param mixed $subjects
	 */
	public function setSubjects($subjects) {
		$this->subjects = $subjects;
	}

	/**
	 * @return mixed
	 */
	public function getOriginalFileName() {
		return $this->originalFileName;
	}

	/**
	 * @param mixed $originalFileName
	 */
	public function setOriginalFileName($originalFileName) {
		$this->originalFileName = $originalFileName;
	}

	/**
	 * @return \DataLoader
	 */
	public function getTeachingDocumentType() {
		return $this->teachingDocumentType;
	}

	/**
	 * @param \DataLoader $teachingDocumentType
	 */
	public function setTeachingDocumentType($teachingDocumentType) {
		$this->teachingDocumentType = $teachingDocumentType;
	}

	public function save(){
		$filename = $this->createFileName(null);
		$file_parts = explode(".", $this->file);
		$extension = $file_parts[count($file_parts) - 1];
		$filename .= ".".$extension;
		rename("../../".$this->getFilePath().$this->file, "../../".$this->getFilePath().$filename);
		$this->file = $filename;
		$this->id = $this->datasource->executeUpdate("INSERT INTO rb_documents (data_upload, file, doc_type, titolo, abstract, anno_scolastico, owner, categoria, ordine_scuola) VALUES (NOW(), '{$this->file}', {$this->documentType}, '{$this->title}', '{$this->abstract}', {$this->year->get_ID()}, {$_SESSION['__user__']->getUid()}, {$this->teachingDocumentType}, {$this->owner->getSchoolOrder()})");
		$this->saveClassAndSubjectRelations();
	}

	public function update(){
		$this->datasource->executeUpdate("UPDATE rb_documents SET titolo = '{$this->title}', abstract = '{$this->abstract}', categoria = {$this->teachingDocumentType} WHERE id = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_relazioni_docente WHERE id_documento = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_allegati_registro_docente WHERE id_documento = {$this->id}");
		$this->checkFileNameForUpdate();
		$this->saveClassAndSubjectRelations();
	}

	public function delete(){
		$this->deleteFile();
		$this->datasource->executeUpdate("DELETE FROM rb_downloads WHERE doc_id = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_relazioni_docente WHERE id_documento = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_documents WHERE id = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_allegati_registro_docente WHERE id_documento = {$this->id}");
	}

	public function download(){
		if (file_exists("../../".$this->getFilePath().$this->file)){
			$this->registerDownload();
			$this->downloadFile();
		}
		else {
			$_SESSION['no_file']['file'] =  $this->getFilePath().$this->file;
			header("Location: no_file.php");
		}
	}

	public function downloadFile(){
		$mime = \MimeType::getMimeContentType($this->file);

		$ext = pathinfo($this->file, PATHINFO_EXTENSION);
		$_filename = $this->title.".".$ext;

		$fp = "../../".$this->getFilePath().$this->file;
		header("Content-Type: ".$mime['ctype']);
		header('Content-Disposition: attachment; filename="'.$_filename.'"');
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");
		readfile($fp);
		//exit;
	}

	private function saveClassAndSubjectRelations() {
		foreach ($this->classes as $cls) {
			foreach ($this->subjects as $subj) {
				// check for existing RecordBook, otherwise insert it
				$alunno = null;
				if ($this->student == null) {
					$sel_ex = "SELECT id FROM rb_registri_personali WHERE anno = {$this->year->get_ID()} AND docente = {$this->owner->getUid()} AND classe = {$cls} AND materia = {$subj}";
				}
				else {
					$alunno = $this->student->getUid();
					$sel_ex = "SELECT id FROM rb_registri_personali WHERE anno = {$this->year->get_ID()} AND docente = {$this->owner->getUid()} AND classe = {$cls} AND alunno = {$alunno}";
				}
				$exists = $this->datasource->executeCount($sel_ex);
				if (!$exists) {
					// insert RecordBook in rb_registri_personali
					$idreg = $this->datasource->executeUpdate("INSERT INTO rb_registri_personali (anno, docente, classe, materia, file, data_creazione, alunno) VALUES ({$this->year->get_ID()}, {$this->owner->getUid()}, {$cls}, {$subj}, null, NOW(), ".field_null($alunno, false).")");
				}
				else {
					if ($alunno == null) {
						$idreg = $this->datasource->executeCount("SELECT id FROM rb_registri_personali WHERE anno = {$this->year->get_ID()} AND docente = {$this->owner->getUid()} AND classe = {$cls} AND materia = {$subj}");
					}
					else {
						$idreg = $this->datasource->executeCount("SELECT id FROM rb_registri_personali WHERE anno = {$this->year->get_ID()} AND docente = {$this->owner->getUid()} AND classe = {$cls} AND alunno = {$alunno}");
					}
				}
				$this->datasource->executeUpdate("INSERT INTO rb_relazioni_docente (id_documento, classe, materia, tipo_documento, alunno) VALUES ({$this->id}, {$cls}, {$subj}, {$this->teachingDocumentType}, ".field_null($alunno, false).")");
				// insert attach in rb_allegati_registro_docente
				$this->datasource->executeUpdate("INSERT INTO rb_allegati_registro_docente (registro, file, tipo_allegato, note, id_documento) VALUES ({$idreg}, '{$this->file}', {$this->teachingDocumentType}, '{$this->title}', {$this->id})");
			}
		}
	}

	private function createFileName($isNew = true) {
		$fn = $this->documentType;
		$fn .= $this->datasource->executeCount("SELECT codice FROM rb_tipologie_relazione_docente WHERE id = ".$this->teachingDocumentType);
		$y = $this->year->get_ID();
		$fn .= str_pad($y, 3, "0", STR_PAD_LEFT);
		if (count($this->classes) > 1) {
			$fn .= "0000";
		}
		else {
			$cls = $this->classes[0];
			$fn .= str_pad($cls, 4, "0", STR_PAD_LEFT);
		}
		$fn .= str_pad($this->owner->getUid(), 7, "0", STR_PAD_LEFT);
		if (count($this->subjects) > 1) {
			$fn .= "000000";
		}
		else {
			$fn .= str_pad($this->subjects[0], 6, "0", STR_PAD_LEFT);
		}

		if ($isNew) {
			$prog = $this->datasource->executeCount("SELECT progressivo FROM rb_progressivo_documenti");
			$fn .= str_pad($prog, 8, "0", STR_PAD_LEFT);
			$this->datasource->executeUpdate("UPDATE rb_progressivo_documenti SET progressivo = progressivo + 1");
		}

		return $fn;
	}

	private function loadClasses() {
		$res_cls = $this->datasource->executeQuery("SELECT DISTINCT(classe) as classe FROM rb_relazioni_docente WHERE id_documento = ".$this->id);
		$this->classes = $res_cls;
	}

	private function loadSubjects() {
		$res_sub = $this->datasource->executeQuery("SELECT DISTINCT(materia) as materia FROM rb_relazioni_docente WHERE id_documento = ".$this->id);
		$this->subjects = $res_sub;
	}

	private function loadStudents() {
		$rb = \RBUtilities::getInstance($this->datasource);
		$res_sts = $this->datasource->executeCount("SELECT alunno FROM rb_relazioni_docente WHERE id_documento = ".$this->id);
		$this->student = $rb->loadUserFromUid($res_sts, "student");
	}

	private function checkFileNameForUpdate() {
		$fn = $this->createFileName(false);
		if ($fn == substr($this->file, 0, 25)) {
			return false;
		}
		else {
			$oldfile = $this->file;
			$this->file = $fn.substr($this->file, 25);
			rename("../../".$this->getFilePath().$oldfile, "../../".$this->getFilePath().$this->file);
			$this->datasource->executeUpdate("UPDATE rb_documents SET file = '{$this->file}' WHERE id = {$this->id}");
		}
	}

	/**
	 * @param $idreg
	 */
	public function deleteAttach($idreg) {
		if (!empty($idreg)) {
			$this->datasource->executeUpdate("DELETE FROM rb_allegati_registro_docente WHERE id_documento = {$this->id} AND registro = {$idreg}");
		}
	}

}
