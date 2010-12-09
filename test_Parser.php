<?php
require_once ('Parser.php');


//$test_parser = new Parser('./prova3.pdf');
//$test_parser = new Parser('/home/michele/tmp/vise ipad/prove esportazioni swinEpid/aureo 2009 tutti materiali.pdf');
$test_parser = new Parser('/home/michele/tmp/vise ipad/prove esportazioni swinEpid/e coli.pdf');

$test_data = $test_parser->parse();

echo ($test_data);
?>