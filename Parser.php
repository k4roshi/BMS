<?php
require_once ('Data.php');
require_once ('Utility/simple_html_dom.php');


class Parser {

	public $content;
	
	public function __construct($sourcefile){
		$dir = dirname($sourcefile);
		$fn = basename($sourcefile);
		
		// Rendering PDF source file to HTML
		$res=exec('"' . $GLOBALS['config']['pdftohtml'] . '" -l 1 -c -i "' . $dir . "/" . $fn . '"');
		$html_fn=$dir . "/" . basename($fn, ".pdf") . '-1.html';
		
		// Parsing all DIV elements and putting text content into $this->content[y_position][x_position]
		$html = file_get_html($html_fn);
		foreach($html->find('div') as $element) {
			$pos = $this->get_position( $element->style );
			$this->content[ $pos['y'] ][ $pos['x'] ] = trim( html_entity_decode( str_replace('&nbsp;', ' ', strip_tags($element)) ) );
		}
		
		// Clean up temporary files
		$mask = $dir . "/" . "*.html";
		array_map( "unlink", glob( $mask ) );
	}

		
	public function parse(){
		if ( false === ($tested_germ = $this->get_tested_germ()) )
			return false;
			
		$germ = new Data($tested_germ);
		
		// Get the coordinates of the origin (DIV with text 'Antimicrobic') 
		$orig = $this->get_coordinates('Antimicrobic');
		if ( false === ($x_axis_coord = $this->get_x_axis_coord()) )
			return false;
			
		// Set the threshold proportional to the distance between two colums
		$x_threshold = ($x_axis_coord['0,004']['x'] - $x_axis_coord['0,002']['x']) * .4;
		$y_threshold = 3;
		
		// OUTER LOOP: For each row of the source being parsed
		foreach ($this->content as $y => $row) {
			
			// Only if the row is aligned with the origin but it is not the origin
			if ( isset($row[ $orig['x'] ]) && $row[ $orig['x'] ] != 'Antimicrobic' ) {

				$antimicrobic = new Antimicrobic($row[ $orig['x'] ]);
				
				// INNER LOOP: For each tick of the x axis
				foreach ($x_axis_coord as $tick => $tick_pos) {
					$parsed_value = $this->get_text($y, $tick_pos['x'], $y_threshold, $x_threshold);
					
					if ($tick == '#Tested')
						$antimicrobic->set_number_tested($parsed_value);
					else
						$antimicrobic->set_value($tick, $parsed_value);
			}

				$germ->add_antimicrobic($antimicrobic);
			}
		}
		return $germ;
	}
	
	

	
	// **** Private functions ****
	function get_tested_germ() {
		if ( false === ($pos = $this->get_coordinates('Organismo:')) )
			return false;
		
		foreach ($this->content[ $pos['y'] ]  as $x => $text)
			if ($x != $pos['x'])
				return $text;  
			
		return false;
	}
		
	
	function get_x_axis_coord() {
		// TODO mettere def intervallo fuori da qui e fuori da Data.php se abbiamo voglia
		$out = array('#Tested' => $this->get_coordinates('#Tested'),
			'0,002' => $this->get_coordinates('0.002'),
			'0,004'=> $this->get_coordinates('0.004'),
			'0,008'=> $this->get_coordinates('0.008'),
			'0,015'=> $this->get_coordinates('0.015'), 
			'0,03'=> $this->get_coordinates('0.03'),
			'0,06'=> $this->get_coordinates('0.06'),
			'0,12'=> $this->get_coordinates('0.12'),
			'0,25'=> $this->get_coordinates('0.25'),
			'0,5'=> $this->get_coordinates('0.5'),
			'1'=> $this->get_coordinates('1'),
			'2'=> $this->get_coordinates('2'), 
			'4'=> $this->get_coordinates('4'),
			'8'=> $this->get_coordinates('8'),
			'16'=> $this->get_coordinates('16'),
			'32'=> $this->get_coordinates('32'),
			'64'=> $this->get_coordinates('64'),
			'128'=> $this->get_coordinates('128'),
			'256'=> $this->get_coordinates('256'),
			'512'=> $this->get_coordinates('512'), 
			'1024'=> $this->get_coordinates('1024'),
			'2048'=> $this->get_coordinates('2048 NOMIC'));
		
		foreach ($out as $coord)
			if ($coord === false)
				return false;
			
		return $out;
	}
	
	
	function get_coordinates($pattern) {
		foreach($this->content as $y => $row)
			if(false !== ($x = array_search($pattern, $row)))
				return array('x' => $x, 'y' => $y);
		
		return false;
  	}
	

	// Return text at specified coordinates. Return false if more than a text box is found.
  	public function get_text($y, $x, $y_threshold, $x_threshold) {

  		$rows = $this->content[$y];
  		while ($y_threshold > 0) {
  			if ( isset($this->content[ $y+$y_threshold ]) )
  				$rows += $this->content[ $y+$y_threshold ];
  			if ( isset($this->content[ $y-$y_threshold ]) )
  				$rows += $this->content[ $y-$y_threshold ];
  			$y_threshold--;
  		}
  		
		foreach ($rows as $c_x => $c_text)
			if (abs($x-$c_x) <= $x_threshold) {
				if (isset($out))
					return false;
				$out = $c_text;
			}
			
			if (isset($out))
				return $out;
			return false;
	}
	
	
	// Input: DIV.style string
	// Output: position as array (top => y, left => x)
	function get_position($style) {
		// Get top:$y
		$from = strrpos($style, 'top:');
		if ($from === false)
			return false;
		$from+=4;
		$to = strpos($style, ';', $from); 
		$y = substr($style, $from, $to-$from);

		// Get left:$x
		$from = strpos($style, 'left:', $to);
		if ($from === false)
			return false;
		$from+=5;
		$to = strpos($style, ';', $from);
		if ($to === false)
			$to = strlen($style);
		$x = substr($style, $from, $to-$from);
		
		return array('x' => $x, 'y' => $y);		
	}

}

?>