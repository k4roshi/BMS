<?php
require_once('Utility/LinkedList.php');
require_once('Library/PHPExcel/Classes/PHPExcel.php');

class Data {
	public $name;
	public $antimicrobics;
	
	public function __construct($name){
		$this->name = $name;
		$this->antimicrobics = new LinkedList();
	}
	
	
	public function add_antimicrobic($antimicrobic) {
		// Look up bp/blue markings (based on the pair germ-antimicrobic)
		if (false !== ($markings = lookup_markings($name, $antimicrobic)) ) {
			$antimicrobic->set_bp($markings['bp']);
			$antimicrobic->set_blue($markings['blue']);
		}
		$this->antimicrobics->add($antimicrobic);
	}
	
	
	public function __toString(){
		$tmp =  "$this->name\n";
		$tmp .= '=====================';
		$tmp .= $this->antimicrobics;
		return $tmp;
	}
	
	
	// lookup_markings() can return no values
	function lookup_markings($germ, $antimicrobic) {
		$objPHPExcel = PHPExcel_IOFactory::load("EUCAST.xlsx");	//TODO porta fuori

		$r=2;
		while ( "" != ($c_germ = $objPHPExcel->getActiveSheet()->getCell('B'.$r)->getValue()) ) {
			
			if (strcasecmp(trim($c_germ), trim($germ))) {
				$c_antimicrobic = $objPHPExcel->getActiveSheet()->getCell('C'.$r)->getValue();
				
				if (strcasecmp(trim($c_antimicrobic), trim($antimicrobic))) {
					$out['blue'] = $objPHPExcel->getActiveSheet()->getCell('D'.$r)->getValue();
					$out['bp'] = $objPHPExcel->getActiveSheet()->getCell('E'.$r)->getValue();
					return $out;
				}
			}
		}
	}
}


class Antimicrobic {
	
	public $name;
	
	// inizializziamo tutto a 0, pi� comodo.
	private $value = array('0,002' => 0, '0,004'=> 0, '0,008'=> 0, '0,015'=> 0, 
	'0,03'=> 0, '0,06'=> 0, '0,12'=> 0, '0,25'=> 0, '0,5'=> 0, '1'=> 0, '2'=> 0, 
	'4'=> 0, '8'=> 0, '16'=> 0, '32'=> 0, '64'=> 0, '128'=> 0, '256'=> 0, '512'=> 0, 
	'1024'=> 0, '2048'=> 0);
	private $bp; // break point
	private $blue; // ultima casella blu
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	
	public function get_value() {
		return $this->value;
	}
	
	public function set_value($tick, $value) {
		// Replace asterisk (used as a marker in the source document)
		$value = str_replace('*', '', $value);
		
		if (!isset($this->value[$tick]) || !is_numeric($value))
			return false;
		
		$this->value[$tick] = $value;
		return true;
	}
	
	
	public function get_bp() {
		return $this->bp;	// can be not set
	}
	
	public function set_bp($bp) {
		$this->bp = $bp;
	}
	
	
	public function get_blue() {
		return $this->blue;	// can be not set
	}
	
	public function set_blue($blue) {
		$this->blue = $blue;
	}
	
	
	public function sanity_check() {
		return true;	//TODO se i valori sono a blocchi contigui. Poi prova con sogli più basse.
	}
	
	
	public function __toString(){
		$tmp = "$this->name\n";
		$tmp .= "Ultima casella blu: $this->blue\n";
		$tmp .= "Break Point: $this->bp\n";
		$tmp .= '=====================';
		foreach ($this->value as $level => $entry){
			$tmp .= "$level = $entry\n"; 
		}
		return $tmp;
	}

}

?>