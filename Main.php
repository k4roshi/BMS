<?php
require_once 'Config/config.php';
require_once 'Data.php';
require_once 'Parser.php';
require_once 'apxlWriter.php';
require_once 'Compressor.php';

$templateDir = 'Resources';
$templateFile = "$templateDir/index.apxl";
$outputIndex = 'Tmp/index.apxl';


// Looking for PDFs in $GLOBALS['config']['srcdir']
$files = scandir($GLOBALS['config']['srcdir']);

foreach($files as $fn){
	if ( ($fn[0] != '.') && (substr($fn, strlen($fn)-4, 4) == ".pdf") ) {
		
		// PDF found, parsing it
		$parser = new Parser($GLOBALS['config']['srcdir'] . $fn);
		$data = $parser->parse();
		
		// Output filename
		$out_fn = $data->get_name() . ' (' . date('d-m-Y') . ').key';
		
		// Preprocessing
		$data->fix_percentage();
		$data->merge_duplicates();
		
		// Generating presentation
		$apxlWriter = new apxlWriter($templateFile, $data);
		$apxlWriter->populateTables();
		$apxlWriter->populateCharts();
		$apxlWriter->saveResult($outputIndex);
		
		// Compressing it
		$zip = new Compressor($templateDir);
		$zip->makeKey($GLOBALS['config']['outdir'] . $out_fn);
		$zip->addNewIndex($outputIndex);
		$zip->closeKey();
		
		rename($GLOBALS['config']['srcdir'] . $fn, $GLOBALS['config']['completeddir'] . $fn);
	}
}

?>