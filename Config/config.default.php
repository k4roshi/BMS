<?php

// Nome del file Excel con le configurazioni per breakpoint e blue bar
$GLOBALS['config']['markings'] = 'Config/TABELLA INTERPRETATIVA EUCAST.xlsx';

// Nome dell'eseguibile pdftohtml (eventualmente con percorso assoluto)
$GLOBALS['config']['pdftohtml'] = 'C:/Program Files/pdftohtml/pdftohtml.exe';

// Cartella in cui devono essere caricate le estrazioni SWIN Epidemiology (con slash finale)
$GLOBALS['config']['srcdir'] = 'C:/Program Files/iPadSync/Esportazioni swinEpid (nuove)/';

// Cartella in cui saranno spostate le estrazioni SWIN Epidemiology elaborate (con slash finale)
$GLOBALS['config']['processeddir'] = 'C:/Program Files/iPadSync/Esportazioni swinEpid (già elaborate)/';

// Cartella WebDAV in cui devono essere caricate le presentazioni (con slash finale)
$GLOBALS['config']['outdir'] = 'C:/Program Files/iPadSync/Presentazioni generate per iPad/';

// File di log in cui vengono scritti eventuali messaggi di utilità per l'operatore (è possibile eliminare il file dopo la consultazione) 
$GLOBALS['config']['log'] = 'C:/Program Files/iPadSync/log.txt';

// Testo personalizzato nella prima slide
// e.g. 'Ospedale di Treviso - Ulss 9 - reparto di microbiologia - responsabile Dott. Rigoli Roberto'
$GLOBALS['config']['customtext'] = ' ';

?>