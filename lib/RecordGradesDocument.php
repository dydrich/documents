<?php

require_once 'Document.php';

class RecordGradesDocument extends Document{
	
	private $teacher;
	private $cls;
	private $hasAttachments;
	
	public function __construct($file, $y, $cls, $teacher, DataLoader $dl = null){
		$this->id = 0;
		$this->file = $file;
		$this->year = $y;
		$this->owner = 0;
		$this->cls = $cls;
		$this->teacher = $teacher;
		$this->deleteOnDownload = false;
		$this->hasAttachments = false;

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
	
	public function setHasAttach($has){
		$this->hasAttachments = $has;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function setFile($f){
		$this->file = $f;
	}
	
	public function hasAttach(){
		return $this->hasAttachments;
	}
	
	public function download(){
		if (file_exists("../../".$this->getFilePath().$this->file)){
			if (!$this->hasAttach()){
				$this->downloadRecordBook();
			}
			else {
				$this->downloadRecordBookWithAttach();
			}
		}
		else {
			$_SESSION['no_file']['file'] =  $this->getFilePath().$this->file;
			header("Location: no_file.php");
		}
	}
	
	private function downloadRecordBook(){
		$this->downloadFile();
	}
	
	private function downloadRecordBookWithAttach(){
		list($f, $ext) = explode(".", $this->getFile());
		$file = $f.".zip";
		$this->createRecordBookZip();
		$this->setFile($file);
		$this->downloadFile();
		$this->deleteFile();
	}
	
	private function createRecordBookZip(){
		$id = $this->getId();
		$attach = $this->datasource->executeQuery("SELECT file FROM rb_allegati_registro_docente WHERE registro = {$id}");
		$old_dir = getcwd();
		chdir("../../".$this->getFilePath());
		$zip = new ZipArchive();
		list($f, $ext) = explode(".", $this->getFile());
		$filename = "./".$f.".zip";
		if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$zip->addFile($this->getFile());
		if (isset($attach) && $attach && $attach != null && count($attach) > 0) {
			foreach ($attach as $att){
				$att = preg_replace("/ /", "_", $att);
				$zip->addFile($att);
			}
		}
		$zip->close();
		chdir($old_dir);
	}
}
