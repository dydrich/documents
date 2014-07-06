<?php

require_once 'Document.php';

class DidacticDocument extends Document{
	
	private $category;
	private $subject;
	private $_classes;
	private $schoolOrder;
	private $isPrivate;
	private $allowedClasses;
	
	public function __construct($id, $data, $dl){
		parent::__construct($id, $data, $dl);
		$this->setCategory($data['categoria']);
		$this->isPrivate = $data['privato'];
		$this->allowedClasses = array();
		if ($this->isPrivate == ""){
			$this->isPrivate = 0;
		}
		if ($data['categoria'] == 2){
			$this->subject = $data['materia'];
		}
		if ($data['classe_rif'] != "" && $data['classe_rif'] != 0){
			$this->_classes = $data['classe_rif'];
		}
		else if ($data['privato'] == 1){
			if (isset($data['classi'])) {
				if(is_array($data['classi']) && count($data['classi']) > 0){
					foreach ($data['classi'] as $c){
						$this->allowedClasses[] = $c;
					}
				}
				else {
					$this->allowedClasses[] = $data['classi'];
				}
			}
		}
		$this->schoolOrder = $data['ordine_scuola'];
	}
	
	public function setCategory($c){
		$this->category = $c;
	}
	
	public function getCategory(){
		return $this->category;
	}
	
	public function setSchoolOrder($c){
		$this->schoolOrder = $c;
	}
	
	public function getSchoolOrder(){
		return $this->schoolOrder;
	}
	
	public function setSubject($s){
		$this->subject = $s;
	}
	
	public function getSubject(){
		return $this->subject;
	}
	
	public function setClasses($cl){
		$this->_classes = $cl;
	}
	
	public function getClasses(){
		return $this->_classes;
	}
	
	public function setAllowedClasses($classes){
		$this->allowedClasses = $classes;
	}
	
	public function getAllowedClasses(){
		return $this->allowedClasses;
	}
	
	public function save(){		
		$this->id = $this->datasource->executeUpdate("INSERT INTO rb_documents (data_upload, file, doc_type, owner, titolo, abstract, anno_scolastico, categoria, materia, classe_rif, ordine_scuola, privato) VALUES (NOW(), '{$this->file}', {$this->documentType}, {$_SESSION['__user__']->getUid()}, '{$this->title}', '{$this->abstract}', {$this->year}, {$this->category}, ".field_null($this->subject, false).", ".field_null($this->_classes, false).", {$this->schoolOrder}, {$this->isPrivate})");
		if (count($this->allowedClasses) > 0){
			foreach ($this->allowedClasses as $cl){
				$this->datasource->executeUpdate("INSERT INTO rb_documents_shared (anno, id_documento, classe) VALUES ({$this->year}, {$this->id}, $cl)");
			}
		}
		$this->insertTags();
	}
	
	public function update(){
		$this->datasource->executeUpdate("UPDATE rb_documents SET file = '{$this->file}', titolo = '{$this->title}', abstract = '{$this->abstract}', anno_scolastico = {$this->year}, categoria = {$this->category}, classe_rif = ".field_null($this->_classes, false).", materia = ".field_null($this->subject, false).", ordine_scuola = {$this->schoolOrder}, privato = {$this->isPrivate} WHERE id = {$this->id}");
		if (count($this->allowedClasses) > 0){
			$this->datasource->executeUpdate("DELETE FROM rb_documents_shared WHERE id_documento = {$this->id}");
			foreach ($this->allowedClasses as $cl){
				$this->datasource->executeUpdate("INSERT INTO rb_documents_shared (anno, id_documento, classe) VALUES ({$this->year}, {$this->id}, $cl)");
			}
		}
		$this->insertTags();
	}
}