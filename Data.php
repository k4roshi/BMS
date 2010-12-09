<?php
require_once('Utility/LinkedList.php');
class Data {
	public $name;
	public $antimicrobics;
	
	public function __construct($name){
		$this->name = $name;
		$this->antimicrobics = new LinkedList();
	}
	
	
	public function add_antimicrobic($antimicrobic) {
		$this->antimicrobics->add($antimicrobic);
	}
	
	
	public function __toString(){
		$tmp =  "$this->name\n";
		$tmp .= "=====================\n";
		$tmp .= $this->antimicrobics;
		return $tmp;
	}
/*	
	public function parse($text){
		
	}
	
	// testing stuff
	public function parsetest($text){
		$this->antimicrobics->add(new Antimicrobic('Levofloxacin'));
		$this->antimicrobics->get(0)->value['0,004'] = 33;
		$this->antimicrobics->get(0)->bp = '0,5';
		$this->antimicrobics->get(0)->blue = "0,12";
		$this->antimicrobics->add(new Antimicrobic('Pippo'));
		$this->antimicrobics->get(1)->value['0,015'] = 55;
*/
}


class Antimicrobic {
	
	//TODO sarebbe meglio farle private e fare i metodi set get, ma non ho voglia.
	
	public $name;
	
	// inizializziamo tutto a 0, pi� comodo.
	public $value = array('0,002' => 0, '0,004'=> 0, '0,008'=> 0, '0,015'=> 0, 
	'0,03'=> 0, '0,06'=> 0, '0,12'=> 0, '0,25'=> 0, '0,5'=> 0, '1'=> 0, '2'=> 0, 
	'4'=> 0, '8'=> 0, '16'=> 0, '32'=> 0, '64'=> 0, '128'=> 0, '256'=> 0, '512'=> 0, 
	'1024'=> 0, '2048'=> 0);
	public $bp; // break point
	public $blue; // ultima casella blu
	
	public function __construct($name) {
		$this->name = $name;
	}

	public function getBpPosition(){
		$array = array_keys($this->value);
		while ($i = current($array)) {
			if ($i== $this->bp) {
				return key($array);
			}
			next($array);
		}
	}
	
	
	public function set_value($tick, $value) {
		// Replace asterisk (used as a marker in the source document)
		$value = str_replace('*', '', $value);
		
		if (!isset($this->value[$tick]) || !is_numeric($value))
			return false;
		
		$this->value[$tick] = $value;
		return true;
	}
	
	
	public function sanity_check() {
		return true;	//TODO se i valori sono a blocchi contigui. Poi prova con sogli più basse.
	}
	

	public function __toString(){
		$tmp = "$this->name\n";
		$tmp .= "Ultima casella blu: $this->blue\n";
		$tmp .= "Break Point: $this->bp\n";
		$tmp .= "-----------------------\n";
		foreach ($this->value as $level => $entry){
			$tmp .= "$level = $entry\n"; 
		}
		return $tmp;
	}

}

/*
//echo $test;

echo $test->antimicrobics->get(0)->getBpPosition();
*/

?>
