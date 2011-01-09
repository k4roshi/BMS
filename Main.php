<?php
require_once 'Config/config.php';
require_once 'Utility/Utils.php';
require_once 'Data.php';
require_once 'Parser.php';
require_once 'apxlWriter.php';
require_once 'Compressor.php';

$templateDir = 'Resources';
$templateFile = "$templateDir/index.apxl";
$outputIndex = 'Tmp/index.apxl';


while (1) {
	// Looking for PDFs in $GLOBALS['config']['srcdir']
	$files = scandir($GLOBALS['config']['srcdir']);
	
	foreach($files as $fn){
		if ( ($fn[0] != '.') && (pathinfo($fn, PATHINFO_EXTENSION) == "pdf") ) {

			// PDF found, parsing it
			$parser = new Parser($GLOBALS['config']['srcdir'] . $fn);
			$data = $parser->parse();
			if ($data !== false) {			
				// Output filename
				$out_fn = Utils::overwrite_avoider($GLOBALS['config']['outdir'] . $data->get_name() . ' (' . date('d-m-Y') . ').key');
				
				// Preprocessing
				$data->fix_percentage();
				$data->merge_duplicates();
				
				// Generating presentation
				$apxlWriter = new apxlWriter($templateFile, $data);
				$apxlWriter->createFirstPage();
				$apxlWriter->populateTables();
				$apxlWriter->populateCharts();
				$apxlWriter->saveResult($outputIndex);
				
				// Compressing it
				$zip = new Compressor($templateDir);
				$zip->makeKey($out_fn);
				$zip->addNewIndex($outputIndex);
				$zip->closeKey();
				
				// Move source file to $GLOBALS['config']['processeddir']
				$processed_fn = Utils::overwrite_avoider($GLOBALS['config']['processeddir'] . $fn);
				rename($GLOBALS['config']['srcdir'] . $fn, $processed_fn);
			}
		}
	}
	// Polling until File System Notifier is implemented on Windows
	sleep(10);
}
?>
