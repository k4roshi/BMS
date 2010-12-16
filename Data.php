<?php
require_once('Utility/LinkedList.php');
require_once('Utility/PHPExcel/Classes/PHPExcel.php');

class Data {
	public $name;	//TODO private
	public $antimicrobics;
	private $number_tested = 0;
	
	public function __construct($name){
		$this->name = $name;
		$this->antimicrobics = new LinkedList();
	}

	
	// Original data contains cumulative percentage. 
	public function fix_percentage() {
		// Iterate each Antimicrobic
		for ($i = 0; $i < $this->antimicrobics->size; $i++) {
			$value = $this->antimicrobics->get($i)->get_value();

			// Iterate each value and subtract his previous;
			$ticks = array_keys($value);
			end($ticks);
			while ($tick = current($ticks)) {
				
				$prev_tick = prev($ticks);
				if ( (false !== $prev_tick) && ($value[$tick] !== 0) )
					$this->antimicrobics->get($i)->value[$tick] -= $value[$prev_tick];
			}
		}
	}
	
	
	// Some Antimicrobic can be splitted (e.g. 'Cefotaxime' and 'Cefotaxime (meningitis)')
	// merge_duplicates() merge them and recalc percentage. 
	function merge_duplicates() {

		// Iterate each Antimicrobic
		$parent_antimicrobic_idx = 0;
		
		for ($i = 0; $i < $this->antimicrobics->size; $i++) {

			$max_child_nonzero = 0;
			$max_child_idx = 0;
			// If name of the Antimicrobic has char '(' in it, then that Antimicrobic is a child 
			while ( ($i < $this->antimicrobics->size) && (false !== strpos($this->antimicrobics->get($i)->get_name(), '(')) ) {
				if ( $max_child_nonzero < ($child_nonzero = $this->antimicrobics->get($i)->count_nonzero()) ) {
					$max_child_idx = $i;
					$max_child_nonzero = $child_nonzero;
				}
				$i++;
			}
			
			if ( $max_child_idx !== 0 ) {
				// Child found!
			
				// Extracting names
				$child_full_name = $this->antimicrobics->get($max_child_idx)->get_name();
				$start = strpos($child_full_name, '(');
				$end = strpos($child_full_name, ')', $start);
				
				$parent_antimicrobic_name = trim( substr($child_full_name, 0, $start) );
				$child_antimicrobic_name = trim( substr($child_full_name, $start+1, $end-$start-1) );
				
				// Checking coherence
				if ( false === strpos($this->antimicrobics->get($parent_antimicrobic_idx)->get_name(), $parent_antimicrobic_name) )
					die("Errore. Dati non cooerenti per Antimicrobic: " . $parent_antimicrobic_name);
				
				$child_number_tested = $this->antimicrobics->get($max_child_idx)->get_number_tested();
				$parent_number_tested = $this->antimicrobics->get($parent_antimicrobic_idx)->get_number_tested();
				
				// Recalc percentage of child (total amount is now $child_number_tested + $parent_number_tested)
				foreach ($this->antimicrobics->get($max_child_idx)->get_value() as $tick => $tick_val)
					$this->antimicrobics->get($max_child_idx)->value[$tick] = $tick_val * $child_number_tested / ($child_number_tested+$parent_number_tested);
				
				// Recalc percentage of parent AND sum child values
				foreach ($this->antimicrobics->get($parent_antimicrobic_idx)->get_value() as $tick => $tick_val)
					$this->antimicrobics->get($parent_antimicrobic_idx)->value[$tick] = round( $tick_val * $parent_number_tested / ($child_number_tested+$parent_number_tested) + $this->antimicrobics->get($max_child_idx)->value[$tick] );

				// Pruning childs. Now max child is merged with parent so can be safely removed
				for (; $i>$parent_antimicrobic_idx+1; $i--)
					$this->antimicrobics->removeObjectAtIndex($parent_antimicrobic_idx+1);

			}			
			$parent_antimicrobic_idx = $i;
		}
	}
	
	
	public function add_antimicrobic($antimicrobic) {
		// Look up bp/blue markings (based on the pair germ-antimicrobic)
		$germ_name = str_replace(' ', '', $this->get_name());
		$antimicrobic_name = str_replace(' ', '', $antimicrobic->get_name());
		
		if (false !== ($markings = $this->lookup_markings($germ_name, $antimicrobic_name)) ) {
			$antimicrobic->set_bp($markings['bp']);
			$antimicrobic->set_blue($markings['blue']);
		}
		
		// Update $this->number_tested as the MAX of every $antimicrobic->get_number_tested()
		if ( ($number_tested = $antimicrobic->get_number_tested()) > $this->number_tested )
			$this->number_tested = $number_tested;

		$this->antimicrobics->add($antimicrobic);
		
	}
	
	
	public function __toString(){
		$tmp =  "$this->name\n";
		$tmp .= "=====================\n";
		$tmp .= $this->antimicrobics;
		return $tmp;
	}
	
	
	// lookup_markings() can return no values
	function lookup_markings($germ, $antimicrobic) {
	
		$objPHPExcel = PHPExcel_IOFactory::load($GLOBALS['config']['markings']);

		$highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();
		
		$r=2;
		while ( $r <= $highestRow ) {
			$c_germ = $objPHPExcel->getActiveSheet()->getCell('C' . $r)->getValue();
			$c_germ = str_replace(' ', '', $c_germ);

			if (strcasecmp($c_germ, $germ) == 0) {

				$c_antimicrobic = $objPHPExcel->getActiveSheet()->getCell('B' . $r)->getValue();
				$c_antimicrobic =  str_replace(' ', '', $c_antimicrobic);
				
				if (strcasecmp($c_antimicrobic, $antimicrobic) == 0) {
					$out['blue'] = $objPHPExcel->getActiveSheet()->getCell('D' . $r)->getValue();
					$out['blue'] = str_replace('.', ',', $out['blue']);
					$out['bp'] = $objPHPExcel->getActiveSheet()->getCell('E' . $r)->getValue();
					$out['bp'] = str_replace('.', ',', $out['bp']);
					
					if ( ($out['blue'] == '') || ($out['bp'] == '') )
						return;					
					return $out;
				}
			}
			$r++;
		}
	}

	
	public function get_number_tested() {
		return $this->number_tested;
	}
	
	public function set_number_tested($number_tested) {
		$this->number_tested = $number_tested;
	}
		
	public function get_name() {
		return $this->name;
	}
	
}


class Antimicrobic {
	
	private $name;
	
	// inizializziamo tutto a 0, piu' comodo.
	public $value = array('0,002' => 0, '0,004'=> 0, '0,008'=> 0, '0,015'=> 0, 
	'0,03'=> 0, '0,06'=> 0, '0,12'=> 0, '0,25'=> 0, '0,5'=> 0, '1'=> 0, '2'=> 0, 
	'4'=> 0, '8'=> 0, '16'=> 0, '32'=> 0, '64'=> 0, '128'=> 0, '256'=> 0, '512'=> 0);
	private $number_tested = 0;
	private $bp; // break point
	private $blue; // ultima casella blu
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	public function count_nonzero() {
		$count = 0;
		foreach ($this->value as $val)
			if ($val !== 0)
				$count++;
				
		return $count;
	}

	
	public function getBpPosition(){
		$array = array_keys($this->value);
		while ($i = current($array)) {
			if ($i == $this->bp) {
				return key($array);
			}
			next($array);
		}
	}
	
	
	public function getLastBluePosition(){
		$array = array_keys($this->value);
		while ($i = current($array)) {
			if ($i == $this->blue) {
				return key($array);
			}
			next($array);
		}
	}
	
	
	public function get_name(){
		return $this->name;
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
	
	
	public function get_number_tested() {
		return $this->number_tested;
	}
	
	public function set_number_tested($number_tested) {
		$this->number_tested = $number_tested;
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

?>
