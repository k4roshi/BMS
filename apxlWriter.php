<?php
class apxlWriter {
	private $index;
	private $germ;
	private $dom;
	private $xp;

	public function __construct($template, Data $germ){
		$this->index = $template;
		$this->germ = $germ;
		$this->index= file_get_contents($this->index);
		// Elimina whitespace se presente
		$this->index = preg_replace("/>\s+</", "><", $this->index);

		// Oggetti DOM, impostazioni e creazione Xpath
		$this->dom = new DOMDocument();
		$this->dom->loadXML($this->index);
		$this->dom->formatOutput = true;
		$this->dom->preserveWhiteSpace = false;
		$this->xp = new domxpath($this->dom);
		// liberiamo un po' di memoria
		unset($this->index);
	}

	public function createFirstPage(){
		$firstSlideProperties = array ('Date' => '137', 'Name' => '138', 'DateRange' => '140', 'TestedNr' => '142', 'CustomText' => '143');
		
		$node = $this->getToTextArea($firstSlideProperties['Date']);
		$node->nodeValue = date("j/d/Y");
		$node = $this->getToTextArea($firstSlideProperties['Name']);
		$node->nodeValue = $this->germ->get_name();
		$node = $this->getToTextArea($firstSlideProperties['DateRange']);
		$rangeStart = $this->germ->get_data_range_from(); 
		$rangeEnd = $this->germ->get_data_range_to();
		$node->nodeValue = "Dal $rangeStart al $rangeEnd";
		$node = $this->getToTextArea($firstSlideProperties['TestedNr']);
		$node->nodeValue = $this->germ->get_number_tested();
		$node = $this->getToTextArea($firstSlideProperties['CustomText']);
		$node->nodeValue = $GLOBALS['config']['customtext'];
	}
	
	public function populateTables(){
		// Proprieta' univoche delle tabelle $tableProperties[numero della tabella][proprieta']
		$tableProperties = array (1 => array ('layer' => 'SFDLayerInfo-31', 'normalColumnStyle' => '208', 'blueColumnStyle' => '243', 'greenColumnStyle' => '246', 'yellowColumnStyle' => '248'),
		2 => array ('layer' => 'SFDLayerInfo-33', 'normalColumnStyle' => '222', 'blueColumnStyle' => '244', 'greenColumnStyle' => '249', 'yellowColumnStyle' => '250'),
		3 => array ('layer' => 'SFDLayerInfo-35', 'normalColumnStyle' => '241', 'blueColumnStyle' => '245', 'greenColumnStyle' => '251', 'yellowColumnStyle' => '252')
		);


		// Calcola di quante tabelle ho bisogno, se > 15 metti n/2 parte alta su 1 e parte bassa su 2, se > 30 metti /3 parte alta parte alta resto
		$length = $this->germ->antimicrobics->size();
		$nrTables = $length/15;
		$nrTables = ceil($nrTables);

		// TODO Debug output, rimuovi
//		echo 'Items: '.$length;
//		echo "\nNumero Tabelle: ".$nrTables."\n";

		$ElementsTable = array();
		$ElementsTable[0] = $ElementsTable[-1]= 0;
		if ($length <= 15){
			$ElementsTable[1] = $length;
			$startIndex = 0;
		} else if ($length <= 30){
			$ElementsTable[1] = ceil($length/2);
			$ElementsTable[2] = floor($length/2);
			$start = $ElementsTable[1];
		} else if ($length <= 45){
			$ElementsTable[1] = $ElementsTable[2] = ceil($length/3);
			$ElementsTable[3] = $length - 2*($ElementsTable[1]);
		} else {
			Utils::log("Too many antibiotics tested!");
			die();
		}

		// TABELLE
		for ($table = 1; $table <= $nrTables; $table++) {
			//*  vai alla slide
			// *  vai alla tabella (1)  /key:page/sf:layers/sf:layer[@sfa:ID="SFDLayerInfo-31"]/sf:drawables[@sfa:ID="NSMutableArray-1644"]/sf:tabular-info
			// *  vai a sf:geometry/sf:position setta sfa:y y = (768 - Dimensione Tabella) /2
			$tableDimension = 48 * $ElementsTable[$table]+1;
			$y = ceil ((768 - $tableDimension)/2);
			$gNode = $this->getToGeometry($table, $tableProperties[$table]['layer']);
			$gNode = $gNode->firstChild;
			$gNode->setAttribute('sfa:h',$tableDimension);
			$gNode = $gNode->nextSibling;
			$gNode->setAttribute('sfa:h',$tableDimension);
			$gNode = $gNode->nextSibling;
			$gNode->setAttribute('sfa:y', $y);
			$grid = $this->getToGrid($table, $tableProperties[$table]['layer']);
			$grid->setAttribute('sf:numrows', $ElementsTable[$table]+1);
			$grid->setAttribute('sf:ocnt', 20*$ElementsTable[$table]+1);
			// impostazione griglia
			$vStyles = $this->getToVStyles($table, $tableProperties[$table]['layer']);
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
				$borderStyle = $this->styledColumnNode($tableProperties[$table]['normalColumnStyle'], 0);
				$Style->appendChild($borderStyle->firstChild->firstChild);
				// stile cella
				for ($i = 0 ; $i < $ElementsTable[$table]; $i++) {
					$tmp = $i+$ElementsTable[$table-1]+$ElementsTable[$table-2]+1;
					$item = $this->germ->antimicrobics->get($i+$ElementsTable[$table-1]+$ElementsTable[$table-2]);
					$bpPos = $item->getBpPosition();
					$bluePosition = $item->getLastBluePosition();
					if (($bpPos === $j-1) && ($bluePosition === $j-1)) {
						// giallo
						$borderStyle = $this->styledColumnNode($tableProperties[$table]['yellowColumnStyle'], $i+1);
					} else if ($bpPos === $j-1){
						// bordo destro verde
						$borderStyle = $this->styledColumnNode($tableProperties[$table]['greenColumnStyle'], $i+1);
					} else if ($bluePosition === $j-1) {
						// blu
						$borderStyle = $this->styledColumnNode($tableProperties[$table]['blueColumnStyle'], $i+1);
					} else {
						$borderStyle = $this->styledColumnNode($tableProperties[$table]['normalColumnStyle'], $i+1);
					}
					$Style->appendChild($borderStyle->firstChild->firstChild);
				}
				$Style = $Style->nextSibling;
			}
			// *	Vai alle righe (sf:rows)
			$rows = $this->getToRows($table, $tableProperties[$table]['layer']);
			$rows->setAttribute('sf:count', $ElementsTable[$table]+1);
			// *  posizionati sulla 3 riga ed elimina quelle inutili
			$singleRow = $rows->firstChild;
			$singleRow = $singleRow->nextSibling->nextSibling;
			for ($j = $ElementsTable[$table]; $j < 14; $j++){
				$singleRow = $this->removeNode($singleRow, true);
			}
			// *  Vai agli stili orizzontali (sf:horizontal-gridline-styles)
			$hStyles = $this->getToHStyles($table, $tableProperties[$table]['layer']);
			// *  modifica sf:array-size in numero antibiotici da inserire in quella tabella
			$hStyles->setAttribute('sf:array-size', $ElementsTable[$table]);
			// *  posizionati sull'ultimo ed elimina tutti quelli non necessari
			$sRow = $this->getToHSingleStyle($table, $tableProperties[$table]['layer'], 15);
			for ($j = 15; $j > $ElementsTable[$table]; $j--){
				$sRowN = $sRow->previousSibling;
				$sRow = $this->removeNode($sRow);
				$sRow = $sRowN;
			}
			// *  Vai ai dati (sf:datasource)
			$dSource = $this->getToDatasource($table, $tableProperties[$table]['layer']);
			for ($i = 0 ; $i < $ElementsTable[$table]; $i++) {
				$item = $this->germ->antimicrobics->get($i + $ElementsTable[$table - 1] + $ElementsTable[$table - 2]);
				$child = $dSource->firstChild;
				$child->setAttribute('sfa:s', $item->get_name());
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
			$dSource = $this->removeNode($dSource, true);

		}

		// elimina tabelle inutili

		for ($i = $nrTables; $i < 3; $i++){
			$node = $this->getToSlideNr($i+1);
			$parent = $node->parentNode;
			$parent->removeChild($node);
		}
	}

	public function populateCharts(){
		for ($i = 0; $i < $this->germ->antimicrobics->size; $i++){
			$chartIndex = 4 + $i;
			$item = $this->germ->antimicrobics->get($i);
			// setta nome
			$name = $this->getToGraphName($chartIndex);
			$aName = $item->get_name();
			$name->nodeValue = $aName;
			$bluePoint = $item->getLastBluePosition();
			$breakPoint = $item->getBpPosition();
			// setta barra BP
			if ($breakPoint === null){
				// non settato, rimuovo
				$BPNode = $this->getToBP($chartIndex);
				$this->removeNode($BPNode);
			} else {
				// settato, aggiorno posizione
				$BPNode = $this->getToBPBar($chartIndex);
				$xPos = 81 + (49.5 * $breakPoint);
				$BPNode->setAttribute('sfa:x', $xPos);
			}
			// riempi dati in serie corrette
			$gData = $this->getToGraphData($chartIndex);
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
		for ($i = $this->germ->antimicrobics->size+4; $i <= 48; $i++){
			$node = $this->getToSlideNr($i);
			$this->removeNode($node);
		}

	}

	public function saveResult($outputFile){
		$this->dom->save($outputFile);
		echo "Saved!\n";
	}

	// Funzioni private

	private function getToSlideNr($nr){
		$slidePath = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$nr.'"]';
		$slide = $this->xp->query($slidePath);
		$slide = $slide->item(0);
		return $slide;
	}

	private function getToTextArea($number){
		$path = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-0"]/key:page/sf:layers/sf:layer[@sfa:ID="SFDLayerInfo-29"]/sf:drawables/sf:shape[@sfa:ID="BGShapeInfo-'.$number.'"]/sf:text/sf:text-storage/sf:text-body/sf:p';
		$geometry = $this->xp->query($path);
		return $geometry->item(0);
	}
	
	private function getToGeometry($slide, $layerInfo){
		$path = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:geometry';
		$geometry = $this->xp->query($path);
		return $geometry->item(0);
	}

	private function getToGrid($slide, $layerInfo){
		$style = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid';
		$style = $this->xp->query($style);
		return $style->item(0);
	}

	private function getToVStyles($slide, $layerInfo){
		$style = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:vertical-gridline-styles';
		$style = $this->xp->query($style);
		return $style->item(0);
	}

	private function getToHStyles($slide, $layerInfo){
		$style = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:horizontal-gridline-styles';
		$style = $this->xp->query($style);
		return $style->item(0);
	}

	private function getToHSingleStyle($slide, $layerInfo, $number){
		$row = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:horizontal-gridline-styles/sf:style-run[@sf:gridline-index="'.$number.'"]';
		$row = $this->xp->query($row);
		return $row->item(0);
	}

	private function getToRows($slide, $layerInfo){
		$rows = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:rows';
		$rows = $this->xp->query($rows);
		return $rows->item(0);
	}

	private function getToDatasource($slide, $layerInfo){
		$rows = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[@sfa:ID="'.$layerInfo.'"]/sf:drawables/sf:tabular-info/sf:tabular-model/sf:grid/sf:datasource/sf:t[2]';
		$rows = $this->xp->query($rows);
		return $rows->item(0);
	}

	private function getToGraphData($slide){
		$gData = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[2]/sf:drawables/sf:chart-info/sf:chart-model/sf:chart-data/sf:mutable-array';
		$gData = $this->xp->query($gData);
		return $gData->item(0);
	}

	private function getToGraphName($slide){
		$gData = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[2]/sf:drawables/sf:shape/sf:text/sf:text-storage/sf:text-body/sf:p';
		$gData = $this->xp->query($gData);
		return $gData->item(0);
	}

	private function getToBPBar($slide){
		$BPData = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[2]/sf:drawables/sf:group/sf:geometry/sf:position';
		$BPData = $this->xp->query($BPData);
		return $BPData->item(0);
	}
	
	private function getToBP($slide){
		$BPData = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$slide.'"]/key:page/sf:layers/sf:layer[2]/sf:drawables/sf:group';
		$BPData = $this->xp->query($BPData);
		return $BPData->item(0);
	}

	private function removeNode(DOMNode $node, $getSibling = false){
		// returns nextSibling if getSibling is set
		$nodeN = null;
		if ($getSibling)
			$nodeN = $node->nextSibling;
		$parent = $node->parentNode;
		$parent->removeChild($node);
		return $nodeN;
	}

	private function styledColumnNode($style, $startN){
		$fragment = $this->dom->createDocumentFragment();
		$stopN = $startN + 1;
		$cStyleNormal = '<key:presentation xmlns:sfa="http://developer.apple.com/namespaces/sfa" xmlns:sf="http://developer.apple.com/namespaces/sf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:key="http://developer.apple.com/namespaces/keynote2" key:version="92008102400" sfa:ID="BGShow-0" key:play-mode="interactive" key:kiosk-slide-delay="5" key:kiosk-build-delay="2" key:mode="once"><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-'.$style.'" sf:start-index="'.$startN.'" sf:stop-index="'.$stopN.'"/></key:presentation>';
		$fragment->appendXML($cStyleNormal);
		return $fragment;
	}
}