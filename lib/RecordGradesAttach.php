<?php

require_once 'Document.php';

class RecordGradesAttach extends Document{

	private $teacher;
	private $cls;
	private $sub;

	public function __construct($file, $y, $cls, $teacher, $sub, DataLoader $dl = null){
		$this->id = 0;
		$this->file = $file;
		$this->year = $y;
		$this->owner = 0;
		$this->cls = $cls;
		$this->teacher = $teacher;
		$this->sub = $sub;
		$this->deleteOnDownload = false;

		$ordine_scuola = $teacher->getSchoolOrder();
		$school_order_directory = "scuola_media";
		if ($ordine_scuola == 2){
			$school_order_directory = "scuola_primaria";
		}

		$user_directory = $teacher->getFullName();
		$user_directory = preg_replace("/ /", "_", $user_directory);
		$user_directory = strtolower($user_directory);

		$this->filePath = "download/registri/{$this->year->get_descrizione()}/{$school_order_directory}/docenti/{$user_directory}/";
		if ($dl != null) $this->datasource = $dl;
	}

	public function setID($id){
		$this->id = $id;
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function download(){
		if (file_exists("../../".$this->getFilePath().$this->file)){
			$this->downloadFile();
		}
		else {
			$_SESSION['no_file']['file'] =  $this->getFilePath().$this->file;
			header("Location: no_file.php");
		}
	}
	
	public function delete(){
		$this->deleteFile();
		$this->registerDelete();
	}
	
	public function registerDelete(){
		$id = $this->getID();
		$this->datasource->executeUpdate("DELETE FROM rb_allegati_registro_docente WHERE id = {$id}");
	}

}