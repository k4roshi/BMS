<?php

//$src_dir = '/home/michele/myarchive/sync/biblioteca/formazione&lavoro/lavoro/archivio/vise/eclipse/BMS/';
//$tmp_dir = '/home/michele/.gvfs/program files su user-pc/Apache Software Foundation/Apache2.2/htdocs/';

$src_dir = 'C:/Program Files/Apache Software Foundation/Apache2.2/htdocs/';
$tmp_dir = 'C:/Program Files/Apache Software Foundation/Apache2.2/htdocs/bcompiled/';

mkdir($tmp_dir);

// Compila
$dir = new DirectoryIterator($src_dir);
foreach ($dir as $element) {
	if ((!$element->isDot()) && (!$element->isDir())) {
		$path = $element->getPath();
		$name = $element->getFilename();
		$extension = getExtension($name);
		if (($extension === 'php') && ($name != 'bcompile.php')) {
			echo $name;
			$h = fopen($tmp_dir . $name, 'w');
			bcompiler_write_header($h);
			bcompiler_write_file($h, $src_dir.$name);
			bcompiler_write_footer($h);
			echo " Compiled\n";
			fclose($h);
		}
	}
}

// Sovrascrivi i vecchi file plain text
$dir = new DirectoryIterator($tmp_dir);
foreach ($dir as $element) {
	if ((!$element->isDot()) && (!$element->isDir())) {
		$name = $element->getFilename();
		rename($tmp_dir.$name, $src_dir.$name);
	}
}

rmdir($tmp_dir);


		
function getExtension($filename) {
    $FileExtension = strrpos($filename, ".", 1) + 1;
    if ($FileExtension != false)
        return strtolower(substr($filename, $FileExtension, strlen($filename) - $FileExtension));
    else
        return "";
}
