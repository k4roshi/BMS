<?php
// Crea zip

$zip = new ZipArchive();
$destination = 'test.key';
$overwrite = true;
$newApxl = 'Tmp/index.apxl';

if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
	echo "Cannot open file \n";
}

// comprimi contenuto Resources

rec_zip('Resources', $zip);

// se presente Tmp/index.apxl sostituisci Index.apxl

if (file_exists($newApxl)){
	$zip->addFile($newApxl, 'index.apxl');
}

$zip->close();


// zippa ricorsivamente le cartelle, che fiol.

function rec_zip($folder, ZipArchive $zip){
	$dir = new DirectoryIterator($folder);
	foreach ($dir as $element) {
		if (!$element->isDot()) {
			if ($element->isDir()) {
				$newdir = $element->getFilename();
				$newfolder = "$folder/$newdir";
				rec_zip($newfolder, $zip);
			} else {
				$path = $element->getPath();
				$name = $element->getFilename();
				$fullpath = "$path/$name";
				$exp = explode('/', $fullpath);
				array_shift($exp);
				$fullname = implode('/', $exp);
				//echo "$fullpath - $fullname\n";
				$zip->addFile($fullpath, $fullname);
			}
		}
	}
}

?>