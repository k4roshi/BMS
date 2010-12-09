<?php
$result = 'Tmp/index.apxl';
$index= file_get_contents('Resources/index.apxl');

// Oggetti DOM, impostazioni e creazione Xpath
$dom = new DOMDocument();
$dom->loadXML($index);
$dom->formatOutput = true;
$dom->preserveWhiteSpace = false;
$xp = new domxpath($dom);
// liberiamo un po' di memoria
unset($index);

// Nodo padre di tutte le slide
$slideList = $xp->query('/key:presentation/key:slide-list');
$slideTree = $slideList->item(0);

// Stringhe di xpath per navigazione
/*
$slide1 = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-1"]';
$tabella1 = $slide1.'/key:page/sf:layers/sf:layer[@sfa:ID="SFDLayerInfo-31"]/sf:drawables[@sfa:ID="NSMutableArray-357"]/sf:tabular-info';
$posizioneT1= $tabella1.'/sf:geometry/sf:position';
$righeT1 = $tabella1.'/sf:tabular-model/sf:grid/sf:rows';
$stylesT1 = $tabella1.'/sf:tabular-model/sf:horizontal-gridline-styles';
$datasource1 = $tabella1.'/sf:tabular-model/sf:datasource';
*/
/*
$geometry = $xp->query($posizioneT1);
$geometry = $geometry->item(0);
$geometry->setAttribute('sfa:x', '13');
$geometry->setAttribute('sfa:y', '400');

$rows = $xp->query($righeT1);
$rowsParent = $rows->item(0);
$rowsParent->setAttribute('sf:count', '11');
echo $rowsParent->getAttribute('sf:count');

$row = $rowsParent->firstChild->nextSibling;
appendSibling($row->cloneNode(), $row);
*/

// test rimozione
$slideTree->removeChild(getSlideNr(2));

$node = getSlideNr(3);
$parent = $node->parentNode;
$parent->removeChild($node);

$dom->save($result);

/* pseudocodice 
// Calcola di quante tabelle ho bisogno, se > 15 metti n/2 parte alta su 1 e parte bassa su 2, se > 30 metti /3 parte alta parte alta resto

// for  numero di tabelle do
 * 	Vai agli stili verticali (sf:vertical-gridline-styles)
 * 	 for colonne
 * 		copia stile prima riga -> normale
 * 		copia stile seconda riga -> blu (volendo li metto fissi)
 * 		for antibiotici da inserire in quella tabella
 * 			if antibiotico.bpposition+2 = numero colonna
 * 				sostituisci nodo con blu, cambia start-index in numero antibiotico e stop index in numero antibiotico +1
 * 			else
 * 				sostituisci nodo con normale e fai stesse robe
 *	Vai alle righe (sf:rows)
 *	modifica sf:count con il numero delle righe effettive
 *  posizionati sulla 3 riga ed elimina quelle inutili
 *  Vai agli stili orizzontali (sf:horizontal-gridline-styles)
 *  modifica sf:array-size in numero antibiotici da inserire in quella tabella-1
 *  posizionati sul secondo
 *  elimina quelli non necessari
 *  Vai ai dati (sf:datasource)
 *	sf:t rappresenta inizio riga. salta la prima, vai alla seconda
 *
 *	for antibiotici da inserire in questa tabella


*/
// funzioni utili

function appendSibling(DOMNode $newnode, DOMNode $ref) 
{ 
  if ($ref->nextSibling) { 
    // $ref has an immediate brother : insert newnode before this one 
    return $ref->parentNode->insertBefore($newnode, $ref->nextSibling); 
  } else { 
    // $ref has no brother next to him : insert newnode as last child of his parent 
    return $ref->parentNode->appendChild($newnode); 
  } 
} 

function getSlideNr($nr){
	global $xp;
	$slidePath = '/key:presentation/key:slide-list/key:slide[@sfa:ID="BGSlide-'.$nr.'"]';
	$slide = $xp->query($slidePath);
	$slide = $slide->item(0);
	return $slide;
}

//echo $dom->saveXML();