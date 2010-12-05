<?php
require_once ('Data.php');

// non necessaria credo

class Parser {
	
	public $text;
	public $testedGerm;
	
	public function __construct($text, $testedGerm){
		$this->text = $text;
		$this->testedGerm = $testedGerm;
	}
	
	public function parse(){
		
	}

}
?>