<?php
require_once ('Data.php');

// non necessaria credo

class Parser {
/*	
	public $text;
	public $testedGerm;

	public function __construct($text, $testedGerm){
		$this->text = $text;
		$this->testedGerm = $testedGerm;
	}
*/
	public $sourcecontent;
	public $size;

	public function __construct($sourcefile){
		$dir = dirname($sourcefile);
		$fn = basename($sourcefile);
		
		$res=exec('pdftohtml -l 1 -c -i ' . $dir . "/" . $fn);
		$html_fn=$dir . "/" . basename($fn, ".pdf") . '-1.html';
		$this->sourcecontent = file_get_contents($html_fn);
		$this->size = strlen($this->sourcecontent); 
				
		$mask = $dir . "/" . "*.html";
		array_map( "unlink", glob( $mask ) );
	}
	
	
	function extract($y, $x, $y_threshold, $x_threshold) {
		$pos = strpos($this->sourcecontent, 'top:'.$y.';left:'.$x);
		$from = strpos($this->sourcecontent, '<', $pos);
		$to = strpos($this->sourcecontent, "\n", $pos);
		return strip_tags( substr($this->sourcecontent, $from, $to-$from+1) );
	}
	
	// Input: pointer of "top:11;left:22"
	// Output: Array(11, 22)
	function untok() {
		
	}
	
	function get_coordinates($pattern) {
		$pos = strpos($this->sourcecontent, $pattern);
		if ($pos === false)
			return false;
		
		// Get top:$y
		$from = strrpos($this->sourcecontent, 'top:', -($this->size-$pos)) + 4;
		$to = strpos($this->sourcecontent, ';', $from); 
		$y = substr($this->sourcecontent, $from, $to-$from);

		// Get left:$x
		$from = $to+6;
		$to = strpos($this->sourcecontent, '"', $from);	
		$x = substr($this->sourcecontent, $from, $to-$from);
		
		return array('x' => $x, 'y' => $y);		
	}
	
	
	public function parse(){
		if (strlen($this->sourcecontent) == 0)
			return false;
		
		$testedGerm = $this->extract(67, 134, 0);		//echo $testedGerm;
		if ($testedGerm === false)
			return false;
			
		$out= new Data($testedGerm);
		
		$orig = $this->get_coordinates('Antimicrobic');
		
		//return $out;
	}
}


$test_parser = new Parser('./prova3.pdf');
$test_data = $test_parser->parse();
//echo $test_data;
?>