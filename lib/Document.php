<?php

require_once "../../lib/users_classes.php";
require_once "../../lib/MimeType.php";
require_once "../../lib/RBUtilities.php";

class Document{
	
	/** 
	 * document ID 
	 * rb_documents.id
	 */
	protected $id;
	protected $dataUpload;
	protected $file;
	protected $documentType;
	protected $abstract;
	protected $year;
	protected $owner;
	protected $lastUpdate;
	protected $title;	
	protected $groups = null;
	protected $protected = false;
	protected $highlited = null;	
	protected $permission = null;
	protected $filePath = null;
	protected $deleteOnDownload = null;
	protected $tags = array();
	
	protected $datasource;
	/**
	 * 
	 * @var area di chiamata del documento: admin, intranet o public
	 */
	protected $area;
	
	/**
	 * 
	 * upload const
	 */
	const FILE_EXISTS = 1;
	const UPL_ERROR = 2;
	const UPL_OK = 3;
	
	public function __construct($id, $data, DataLoader $dl){
		$this->id = $id;
		$this->datasource = $dl;
		$this->highlited = '';
		//echo $data['owner'];
		if ($data != null){
			$this->dataUpload = $data['data_upload'];
			$this->file = $data['file'];
			$this->abstract = $data['abstract'];
			$this->year = $data['anno_scolastico'];
			$this->documentType = $data['doc_type'];
			$rb = RBUtilities::getInstance($dl);
			$this->owner = $rb->loadUserFromUid($data['owner'], 'school');
			$this->title = $data['titolo'];
			$this->filePath = "download/{$data['doc_type']}/";
			if (isset($data['evidenziato']) && $data['evidenziato'] != "" && $data['evidenziato'] != null){
				$this->highlited = substr($data['evidenziato'], 0, 10)." 23:59:59";
			}
			if (isset($data['tags']) && $data['tags'] != ""){
				$tags = explode(",", $data['tags']);
				$right_tags = array();
				foreach ($tags as $t){
					if ($t != ""){
						$right_tags[] = $t;
					}
				}
				if (count($right_tags) > 0){
					$this->tags = $right_tags;
				}
			}
		}
		$this->deleteOnDownload = false;
		$this->area = "admin";
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function getDataUpload(){
		return $this->dataUpload;
	}
	
	public function getFile(){
		return $this->file;
	}
	
	public function setFile($f){
		$this->file = $f;
	}
	
	public function getTags(){
		return $this->tags;
	}
	
	public function setTags($tags){
		$this->tags = $tags;
	}
	
	public function getDocumentType(){
		return $this->documentType;
	}
	
	public function setDocumentType($t){
		$this->documentType = $t;
	}
	
	public function getAbstract(){
		return $this->abstract;
	}
	
	public function setAbstract($ab){
		$this->abstract = $ab;
	}
	
	public function getYear(){
		return $this->year;
	}
	
	public function getOwner(){
		return $this->owner;
	}
	
	public function setOwner(SchoolUserBean $o){
		$this->owner = $o;
	}
	
	public function getLastUpdate(){
		return $this->lastUpdate;
	}
	
	public function setLastUpdate($lu){
		$this->lastUpdate = $lu;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function setTitle($t){
		$this->title = $t;
	}
	
	public function getGroups(){
		return $this->groups;
	}
	
	public function setGroups($g){
		$this->groups = $g;
	}
	
	public function setprotected($bool){
		$this->protected = $bool;
	}
	
	public function isprotected(){
		return $this->protected;
	}
	
	public function getHighlighted(){
		return $this->highlited;
	}
	
	public function setHighlighted($h){
		$this->highlited = $h;
	}
	
	public function getPermission(){
		return $this->permission;
	}
	
	public function setPermission($s){
		$this->permission = $s;
	}
	
	public function getFilePath(){
		return $this->filePath;
	}
	
	public function setFilePath($fp){
		$this->filePath = $fp;
	}
	
	public function deleteOnDownload(){
		return $this->deleteOnDownload;
	}
	
	public function setDeleteOnDownload($dod){
		$this->deleteOnDownload = $dod;
	}
	
	public function getArea(){
		$this->area;
	}
	
	public function setArea($ar){
		$this->area = $ar;
	}
	
	protected function insertTags(){
		$this->datasource->executeUpdate("DELETE FROM rb_documents_tags WHERE id_documento = {$this->id}");
		foreach ($this->tags as $tag){
			$sel_tag = "SELECT tid FROM rb_tags WHERE tag = '{$tag}'";
			$tid = $this->datasource->executeCount($sel_tag);
			if ($tid == null){
				// insert new tag
				$tid = $this->datasource->executeUpdate("INSERT INTO rb_tags (tag) VALUES ('{$tag}')");
			}
			$this->datasource->executeUpdate("INSERT INTO rb_documents_tags (tid, id_documento) VALUES ({$tid}, {$this->id})");
		}
	}
	
	public function downloadFile(){
		$mime = MimeType::getMimeContentType($this->file);
	
		$fp = fopen("../../".$this->getFilePath().$this->file, "r");
		header("Content-Type: ".$mime['ctype']);
		header("Content-Disposition: attachment; filename=".$this->file);
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");
		fpassthru($fp);
		@fclose($fp);
		//exit;
	}
	
	public function deleteFile(){
		if (file_exists("../../".$this->getFilePath().$this->file)){
			unlink("../../".$this->getFilePath().$this->file);
		}
	}
	
	public function registerDownload(){
		$id_type = $this->getDocumentType();
		$id = $this->getID();
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($_SESSION['__user__']){
			$user = $_SESSION['__user__']->getUId();
		}
		else {
			$user = 0;
		}	 
		$ins = "INSERT INTO rb_downloads (doc_id, doc_type, ip_address, data_dw, user) VALUES ({$id}, {$id_type}, '{$ip}', NOW(), {$user})";
		$this->datasource->executeUpdate($ins);
		$table = "";
		if($id_type == 1){
			$table = "rb_stud_works";
			$field = "id_work";
		}
		else {
			$table = "rb_documents";
			$field = "id";
		}
		$upd = "UPDATE $table SET dw_counter = (dw_counter + 1) WHERE $field = $id";
		$rs_upd = $this->datasource->executeUpdate($upd);
	}
	
	public function save(){
		$this->id = $this->datasource->executeUpdate("INSERT INTO rb_documents (data_upload, file, doc_type, titolo, abstract, anno_scolastico, owner, evidenziato) VALUES (NOW(), '{$this->file}', {$this->documentType}, '{$this->title}', '{$this->abstract}', {$this->year}, {$_SESSION['__user__']->getUid()}, ".field_null($this->highlited, true).")");
		$this->insertTags();
	}
	
	public function update(){
		$this->datasource->executeUpdate("UPDATE rb_documents SET file = '{$this->file}', titolo = '{$this->title}', abstract = '{$this->abstract}', anno_scolastico = {$this->year}, evidenziato =  ".field_null($this->highlited, true)." WHERE id = {$this->id}");
		$this->insertTags();
	}
	
	public function delete(){
		$this->deleteFile();
		$this->datasource->executeUpdate("DELETE FROM rb_documents_tags WHERE id_documento = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_downloads WHERE doc_id = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_documents WHERE id = {$this->id}");
	}
	
	public function download(){
		if (file_exists("../../".$this->getFilePath().$this->file)){
			$this->registerDownload();
			$this->downloadFile();
		}
		else {
			$_SESSION['no_file']['file'] =  $this->getFilePath().$this->file;
			if ($_SESSION['no_file']['referer'] == "albo/index.php"){
				$_SESSION['no_file']['id'] = $this->getID();
				$_SESSION['no_file']['fn'] = $this->getTitle();
				header("Location: ../../../albo-pretorio/no_file.php");
			}
			else {
				header("Location: no_file.php");
			}
		}
	}
}

