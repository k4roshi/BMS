<?
require_once ('Parser.php');


//$test_parser = new Parser('./prova3.pdf');
//$test_parser = new Parser('/home/michele/tmp/vise ipad/prove esportazioni swinEpid/aureo 2009 tutti materiali.pdf');
//$test_parser = new Parser('/home/michele/tmp/vise ipad/prove esportazioni swinEpid/e coli.pdf');

$test_data = new Data('Pippo');
$test_data->parsetest();

print_r($test_data->antimicrobics->get(2)->value);
print_r($test_data->antimicrobics->get(3)->value);

$keys = array_keys($test_data->antimicrobics->get(3)->value);
foreach ($keys as $k)
	$test_data->antimicrobics->get(2)->value[$k] += $test_data->antimicrobics->get(3)->value[$k];

print_r($test_data->antimicrobics->get(2)->value);

//$test_data->fix_percentage();

?>
