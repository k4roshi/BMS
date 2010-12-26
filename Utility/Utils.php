<?php
require_once 'Config/config.php';

class Utils {
	
	public static function log($text) {
		$fh = fopen($GLOBALS['config']['log'], 'a+');
		fwrite($fh, date('[d-m-Y  H:i] ') .  $text . "\r\n");
		fclose($fh);
	}
	
	
	// Check if $filename exists and eventualy append an increasing counter between file name and file extension
	public static function overwrite_avoider($filename) {
		if (!file_exists($filename))
			return $filename;
		
		$dir = pathinfo($filename, PATHINFO_DIRNAME);
		$fn = pathinfo($filename, PATHINFO_FILENAME);
		$ext = pathinfo($filename, PATHINFO_EXTENSION);		
		
		$i = 1;
		while ( file_exists( ($new_filename = $dir . '/' . $fn . ' - ' . sprintf("%02d", $i) . '.' . $ext) ) )
			$i++;
			 
		return $new_filename;
	}
}

?>