<?php
require_once 'Data.php';
require_once 'Parser.php';
require_once 'apxlWriter.php';
require_once 'Compressor.php';

$sourceData = '';
$templateDir = 'Resources';
$templateFile = "$templateDir/index.apxl";
$outputIndex = 'Tmp/index.apxl';
$outputKey = 'Pisellone.key';
$parser = new Parser($sourceData);

$testData = $parser->parse();

$apxlWriter = new apxlWriter($templateFile, $testData);
$apxlWriter->populateTables();
$apxlWriter->populateCharts();
$apxlWriter->saveResult($outputIndex);

$zip = new Compressor($templateDir);
$zip->makeKey($outputKey);
$zip->addNewIndex($outputIndex);
$zip->closeKey();

