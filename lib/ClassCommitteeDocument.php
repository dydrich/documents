<?php

/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 19/08/15
 * Time: 8.45
 */

namespace eschool;

require_once "Document.php";
require_once "../../lib/RBUtilities.php";

class ClassCommitteeDocument extends \Document{

	private $classe;
	private $student;
	private $originalFileName;
	private $classCommitteeDocumentType;

	public function __construct($id, $data, $cls, \MYSQLDataLoader $dl, $student){
		parent::__construct($id, $data, $dl);
		$this->id = $id;
		if ($cls == null) {
			$this->loadClasse();
		}
		else {
			$this->classe = $cls;
		}

		$this->classCommitteeDocumentType = $data['categoria'];

		$this->deleteOnDownload = false;
		$this->originalFileName = $this->file;

		$rb = \RBUtilities::getInstance($this->datasource);
		$y = $this->year;
		$this->year = $rb->loadYearFromID($y);

		$this->filePath = "download/{$this->documentType}/";

		// sostegno
		if ($student != null && $student != "") {
			$this->student = $rb->loadUserFromUid($student, "student");
		}
		else if ($this->classCommitteeDocumentType > 4){
			$this->loadStudent();
		}
		else {
			$this->student = null;
		}
	}

	/**
	 * @return mixed
	 */
	public function getClasse() {
		return $this->classe;
	}

	/**
	 * @param mixed $classes
	 */
	public function setClasse($classe) {
		$this->classe = $classe;
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
	public function getClassCommitteeDocumentType() {
		return $this->classCommitteeDocumentType;
	}

	/**
	 * @param \DataLoader $teachingDocumentType
	 */
	public function setClassCommitteeDocumentType($ccdt) {
		$this->classCommitteeDocumentType = $ccdt;
	}

	public function save(){
		$filename = $this->createFileName(true);
		$file_parts = explode(".", $this->file);
		$extension = $file_parts[count($file_parts) - 1];
		$filename .= ".".$extension;
		rename("../../".$this->getFilePath().$this->file, "../../".$this->getFilePath().$filename);
		$this->file = $filename;
		$this->id = $this->datasource->executeUpdate("INSERT INTO rb_documents (data_upload, file, doc_type, titolo, abstract, anno_scolastico, owner, categoria, ordine_scuola) VALUES (NOW(), '{$this->file}', {$this->documentType}, '{$this->title}', '{$this->abstract}', {$this->year->get_ID()}, {$_SESSION['__user__']->getUid()}, {$this->classCommitteeDocumentType}, {$this->owner->getSchoolOrder()})");
		$this->saveClassAndSubjectRelations(true);
	}

	public function update(){
		$this->datasource->executeUpdate("UPDATE rb_documents SET titolo = '{$this->title}', abstract = '{$this->abstract}', categoria = {$this->classCommitteeDocumentType} WHERE id = {$this->id}");
		$this->checkFileNameForUpdate();
		$this->saveClassAndSubjectRelations(false);
	}

	public function delete(){
		$this->deleteFile();
		$this->datasource->executeUpdate("DELETE FROM rb_downloads WHERE doc_id = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_documenti_cdc WHERE id_documento = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_documents WHERE id = {$this->id}");
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

	private function saveClassAndSubjectRelations($isNew) {
		$uid = "";
		if ($this->student != null) {
			$uid = $this->student->getUid();
		}
		if ($isNew) {
			$this->datasource->executeUpdate("INSERT INTO rb_documenti_cdc (id_documento, classe, tipo_documento, alunno) VALUES ({$this->id}, {$this->classe}, {$this->classCommitteeDocumentType}, " . field_null($uid, false) . ")");
		}
		else {
			$this->datasource->executeUpdate("UPDATE rb_documenti_cdc SET classe = {$this->classe}, tipo_documento = {$this->classCommitteeDocumentType}, alunno = " . field_null($uid, false) . " WHERE id_documento = {$this->id}");
		}
	}

	private function createFileName($isNew = true) {
		$fn = $this->documentType;
		$fn .= $this->datasource->executeCount("SELECT codice FROM rb_tipologie_documento_cdc WHERE id = ".$this->classCommitteeDocumentType);
		$y = $this->year->get_ID();
		$fn .= str_pad($y, 3, "0", STR_PAD_LEFT);
		$fn .= str_pad($this->classe, 4, "0", STR_PAD_LEFT);

		$fn .= str_pad($this->owner->getUid(), 7, "0", STR_PAD_LEFT);

		if ($isNew) {
			$prog = $this->datasource->executeCount("SELECT progressivo FROM rb_progressivo_documenti");
			$fn .= str_pad($prog, 8, "0", STR_PAD_LEFT);
			$this->datasource->executeUpdate("UPDATE rb_progressivo_documenti SET progressivo = progressivo + 1");
		}

		return $fn;
	}

	private function loadClasse() {
		$res_cls = $this->datasource->executeCount("SELECT classe FROM rb_documenti_cdc WHERE id_documento = ".$this->id);
		$this->classe = $res_cls;
	}

	private function loadStudent() {
		$rb = \RBUtilities::getInstance($this->datasource);
		$res_sts = $this->datasource->executeCount("SELECT alunno FROM rb_documenti_cdc WHERE id_documento = ".$this->id);
		$this->student = $rb->loadUserFromUid($res_sts, "student");
	}

	private function checkFileNameForUpdate() {
		$fn = $this->createFileName(false);
		if ($fn == substr($this->file, 0, 19)) {
			return false;
		}
		else {
			$oldfile = $this->file;
			$this->file = $fn.substr($this->file, 19);
			rename("../../".$this->getFilePath().$oldfile, "../../".$this->getFilePath().$this->file);
			$this->datasource->executeUpdate("UPDATE rb_documents SET file = '{$this->file}' WHERE id = {$this->id}");
		}
	}
}
