<?
require_once('Library/PHPExcel/Classes/PHPExcel.php');

$objPHPExcel = PHPExcel_IOFactory::load("EUCAST.xlsx");

//	print_r ($objPHPExcel);

$r=2;
while ( "" != ($c_germ = $objPHPExcel->getActiveSheet()->getCell('B'.$r)->getValue()) ) {
	
	if (strcasecmp($c_germ, $germ)) {
		$c_antimicrobic = $objPHPExcel->getActiveSheet()->getCell('C'.$r)->getValue();
		if (strcasecmp($c_antimicrobic, $antimicrobic)) {
			$out['blue'] = $objPHPExcel->getActiveSheet()->getCell('D'.$r)->getValue();
			$out['bp'] = $objPHPExcel->getActiveSheet()->getCell('E'.$r)->getValue();
			return $out;
		}
	}
}

if (isset($a))
	echo "T";
else
	echo "F";

?>
