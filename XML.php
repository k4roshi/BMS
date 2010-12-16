<?php
require_once 'Data.php';
$result = 'Tmp/index.apxl';
$index= file_get_contents('Resources/index.apxl');
// Elimina whitespace
$index = preg_replace("/>\s+</", "><", $index);


// Oggetti DOM, impostazioni e creazione Xpath
$dom = new DOMDocument();
$dom->loadXML($index);
$dom->formatOutput = true;
$dom->preserveWhiteSpace = false;
$xp = new domxpath($dom);
// liberiamo un po' di memoria
unset($index);

// Proprietˆ univoche delle tabelle $tableProperties[numero della tabella][proprietˆ]
$tableProperties = array (1 => array ('layer' => 'SFDLayerInfo-31', 'normalColumnStyle' => '208', 'blueColumnStyle' => '243'),
						  2 => array ('layer' => 'SFDLayerInfo-33', 'normalColumnStyle' => '222', 'blueColumnStyle' => '244'),
						  3 => array ('layer' => 'SFDLayerInfo-35', 'normalColumnStyle' => '241', 'blueColumnStyle' => '245')
);

// TODO TESTING, Elimina

// importa un po' di dati finti

$germ = new Data('Puzzococco');
$germ->parsetest();



// Calcola di quante tabelle ho bisogno, se > 15 metti n/2 parte alta su 1 e parte bassa su 2, se > 30 metti /3 parte alta parte alta resto
$length = $germ->antimicrobics->size();
$nrTables = $length/15;
$nrTables = ceil($nrTables);

// TODO Debug output, rimuovi
echo 'Items: '.$length;
echo "\nNumero Tabelle: ".$nrTables."\n";

$ElementsTable = array();
$ElementsTable[0] = $ElementsTable[-1]= 0;
if ($length < 15){
	$ElementsTable[1] = $length;
	$startIndex = 0;
} else if ($length < 30){
	$ElementsTable[1] = ceil($length/2);
	$ElementsTable[2] = floor($length/2);
	$start = $ElementsTable[1];
} else if ($length < 45){
	$ElementsTable[1] = $ElementsTable[2] = ceil($length/3);
	$ElementsTable[3] = $length - 2*($ElementsTable[1]);
} else {
	die ("Too many antibiotics tested!");
}

// TABELLE
for ($table = 1; $table <= $nrTables; $table++) {
//*  vai alla slide
// *  vai alla tabella (1)  /key:page/sf:layers/sf:layer[@sfa:ID="SFDLayerInfo-31"]/sf:drawables[@sfa:ID="NSMutableArray-1644"]/sf:tabular-info
// *  vai a sf:geometry/sf:position setta sfa:y y = (1024 - Dimensione Tabella) /2
	$tableDimension = 46 * $ElementsTable[$table]+1;
	$y = ceil ((768 - $tableDimension)/2);
	$gNode = getToGeometry($table, $tableProperties[$table]['layer']);
	$gNode = $gNode->firstChild;
	$gNode->setAttribute('sfa:h',$tableDimension);
	$gNode = $gNode->nextSibling;
	$gNode->setAttribute('sfa:h',$tableDimension);
	$gNode = $gNode->nextSibling;
	$gNode->setAttribute('sfa:y', $y);
	$grid = getToGrid($table, $tableProperties[$table]['layer']);
	$grid->setAttribute('sf:numrows', $ElementsTable[$table]+1);
	$grid->setAttribute('sf:ocnt', 20*$ElementsTable[$table]+1);
	// impostazione griglia
	$vStyles = getToVStyles($table, $tableProperties[$table]['layer']);
	$Style = $vStyles->firstChild;
	// migliorabile se ho voglia (stili colonna)
	for ($j=0; $j < 19; $j++){
		$Style->setAttribute('sf:count', $ElementsTable[$table]+1);
		// rimuovi figli inutili
		while ($Style->hasChildNodes()){
			$deadNode = $Style->firstChild;
			$Style->removeChild($deadNode);
		}
		// aggiungi stile intestazione
		$borderStyle = styledColumnNode($tableProperties[$table]['normalColumnStyle'], 0);
		$Style->appendChild($borderStyle->firstChild->firstChild);
		// stile cella
		for ($i = 0 ; $i < $ElementsTable[$table]; $i++) {
			$tmp = $i+$ElementsTable[$table-1]+$ElementsTable[$table-2]+1;
			$item = $germ->antimicrobics->get($i+$ElementsTable[$table-1]+$ElementsTable[$table-2]);
			if ($item->getBpPosition() != $j-1) {
				// normale
				$borderStyle = styledColumnNode($tableProperties[$table]['normalColumnStyle'], $i+1);
			} else {
				// bordo destro blu
				$borderStyle = styledColumnNode($tableProperties[$table]['blueColumnStyle'], $i+1);
			}
			$Style->appendChild($borderStyle->firstChild->firstChild);
		}
		$Style = $Style->nextSibling;
	}
// *	Vai alle righe (sf:rows)
	$rows = getToRows($table, $tableProperties[$table]['layer']);
	$rows->setAttribute('sf:count', $ElementsTable[$table]+1);
// *  posizionati sulla 3 riga ed elimina quelle inutili
	$singleRow = $rows->firstChild;
	$singleRow = $singleRow->nextSibling->nextSibling;
	for ($j = $ElementsTable[$table]; $j < 14; $j++){
		$singleRow = removeNode($singleRow, true);
	}
// *  Vai agli stili orizzontali (sf:horizontal-gridline-styles)
	$hStyles = getToHStyles($table, $tableProperties[$table]['layer']);
// *  modifica sf:array-size in numero antibiotici da inserire in quella tabella
	$hStyles->setAttribute('sf:array-size', $ElementsTable[$table]);
// *  posizionati sull'ultimo ed elimina tutti quelli non necessari
	$sRow = getToHSingleStyle($table, $tableProperties[$table]['layer'], 15);
	for ($j = 15; $j > $ElementsTable[$table]; $j--){
		$sRowN = $sRow->previousSibling;
		$sRow = removeNode($sRow);
		$sRow = $sRowN;
	}
// *  Vai ai dati (sf:datasource)
	$dSource = getToDatasource($table, $tableProperties[$table]['layer']);
	for ($i = 0 ; $i < $ElementsTable[$table]; $i++) {
		$item = $germ->antimicrobics->get($i + $ElementsTable[$table - 1] + $ElementsTable[$table - 2]);
		$child = $dSource->firstChild;
		$child->setAttribute('sfa:s', $item->name);
		$dSource = $dSource->nextSibling;
		foreach ($item->value as $value){
			$insert = $value;
			if ($value == '0')
				$insert = '';
			$child = $dSource->firstChild;
			$child->setAttribute('sfa:s', $insert);
			$dSource = $dSource->nextSibling;
		}
	}
	// elimina inutili
	while ($dSource != null)
		$dSource = removeNode($dSource, true);
	
}

// elimina tabelle inutili

for ($i = $nrTables; $i < 3; $i++){
	$node = getToSlideNr($i+1);
	$parent = $node->parentNode;
	$parent->removeChild($node);
}	


// GRAFICI
for ($i = 0; $i < $germ->antimicrobics->size; $i++){
	$chartIndex = 4 + $i;
	$item = $germ->antimicrobics->get($i);
	// setta nome
	$name = getToGraphName($chartIndex);
	$aName = $item->name;
	$name->nodeValue = $aName;
	$bluePoint = $item->getLastBluePosition();
	$breakPoint = $item->getBpPosition();
	// setta barra BP
	$BPNode = getToBPBar($chartIndex);
	$xPos = 81 + (49.5 * $breakPoint);
	$BPNode->setAttribute('sfa:x', $xPos);
	// riempi dati in serie corrette
	$gData = getToGraphData($chartIndex);
	$counter = 0;
	foreach ($item->value as $value){
		if ($counter <= $bluePoint){
			// BLU - Prima Serie
			$choice = $gData->firstChild;
		} else if ($counter <= $breakPoint){
			// verde - seconda serie
			$choice = $gData->firstChild->nextSibling;
		} else {
			// rosso - terza serie
			$choice = $gData->firstChild->nextSibling->nextSibling;
		}
		$choice->setAttribute('sfa:number', $value);
		$counter++;
		$gData = $gData->nextSibling;
	}
}

// elimina grafici inutili
for ($i = $germ->antimicrobics->size+4; $i <= 48; $i++){
	$node = getToSlideNr($i);
	removeNode($node);
}

// salva
$dom->save($result);
echo "Saved!\n";

// funzioni utili

function getToSlideNr($nr){
	global $xp;
	$slidePath = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$nr.'"]';
	$slide = $xp->query($slidePath);
	$slide = $slide->item(0);
	return $slide;
}

function getToGeometry($slide, $layerInfo){
	global $xp;
	$path = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:geometry';
	$geometry = $xp->query($path);
	return $geometry->item(0);
}

function getToGrid($slide, $layerInfo){
	global $xp;
	$style = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid';
	$style = $xp->query($style);
	return $style->item(0);
}

function getToVStyles($slide, $layerInfo){
	global $xp;
	$style = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:vertical-gridline-styles';
	$style = $xp->query($style);
	return $style->item(0);
}

function getToHStyles($slide, $layerInfo){
	global $xp;
	$style = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:horizontal-gridline-styles';
	$style = $xp->query($style);
	return $style->item(0);
}

function getToHSingleStyle($slide, $layerInfo, $number){
	global $xp;
	$row = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:horizontal-gridline-styles/sf:style-run[@sf:gridline-index="'.$number.'"]';
	$row = $xp->query($row);
	return $row->item(0);
}

function getToRows($slide, $layerInfo){
	global $xp;
	$rows = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:rows';
	$rows = $xp->query($rows);
	return $rows->item(0);
}

function getToDatasource($slide, $layerInfo){
	global $xp;
	$rows = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:datasource/sf:t[2]';
	$rows = $xp->query($rows);
	return $rows->item(0);
}

function getToGraphData($slide){
	global $xp;
	$gData = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[2]/sf:drawables/sf:chart-info/sf:chart-model/sf:chart-data/sf:mutable-array';
	$gData = $xp->query($gData);
	return $gData->item(0);
}

function getToGraphName($slide){
	global $xp;
	$gData = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[2]/sf:drawables/sf:shape/sf:text/sf:text-storage/sf:text-body/sf:p';
	$gData = $xp->query($gData);
	return $gData->item(0);
}

function getToBPBar($slide){
	global $xp;
	$BPData = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[2]/sf:drawables/sf:group/sf:geometry/sf:position';
	$BPData = $xp->query($BPData);
	return $BPData->item(0);
}

function removeNode(DOMNode $node, $getSibling = false){
	// returns nextSibling if getSibling is set
	$nodeN = null;
	if ($getSibling) 
		$nodeN = $node->nextSibling;
	$parent = $node->parentNode;
	$parent->removeChild($node);
	return $nodeN;
}

function styledColumnNode($style, $startN){
	global $dom;
	$fragment = $dom->createDocumentFragment();
	$stopN = $startN + 1;
	$cStyleNormal = '<key:presentation xmlns:sfa="http://developer.apple.com/namespaces/sfa" xmlns:sf="http://developer.apple.com/namespaces/sf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:key="http://developer.apple.com/namespaces/keynote2" key:version="92008102400" sfa:ID="BGShow-0" key:play-mode="interactive" key:kiosk-slide-delay="5" key:kiosk-build-delay="2" key:mode="once"><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-'.$style.'" sf:start-index="'.$startN.'" sf:stop-index="'.$stopN.'"/></key:presentation>';
	$fragment->appendXML($cStyleNormal);
	return $fragment;
}