<?php
class Compressor{
	private $zip;
	private $overwrite;
	private $newApxl;
	private $folder;
	
	public function __construct($sourceFolder, $overwrite = true){
		$this->zip= new ZipArchive();
		$this->overwrite = $overwrite;
		$this->folder = $sourceFolder;
		
	}
	
	public function makeKey($name){
		if($this->zip->open($name, $this->overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			Utils::log('Cannot open destination file');
			die();
		}	
		$this->rec_zip($this->folder);
	}
	
	public function addNewIndex($newIndex){
		if (file_exists($newIndex)){
		$this->zip->addFile($newIndex, 'index.apxl');
		} else {
			Utils::log('New index not found');
			die();	
		}
	}
	
	public function closeKey(){
		$this->zip->close();
		echo "File Created\n";
	}
	
	private function rec_zip($folder){
		$dir = new DirectoryIterator($folder);
		foreach ($dir as $element) {
			if (!$element->isDot()) {
				if ($element->isDir()) {
					$newdir = $element->getFilename();
					$newfolder = "$folder/$newdir";
					$this->rec_zip($newfolder);
				} else {
					$path = $element->getPath();
					$name = $element->getFilename();
					$fullpath = "$path/$name";
					$exp = explode('/', $fullpath);
					array_shift($exp);
					$fullname = implode('/', $exp);
					//echo "$fullpath - $fullname\n";
					$this->zip->addFile($fullpath, $fullname);
				}
			}
		}
	}
}