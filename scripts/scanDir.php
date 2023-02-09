<?php

$xlsxCatalogPath = __DIR__.'/../files';

//Получение путей всех файлов в папках и подпапках
function get_dir_files( $dir, $recursive = true, $include_folders = false ){
	if( ! is_dir($dir) )
		return array();

	$files = array();

	$dir = rtrim( $dir, '/\\' ); // удалим слэш на конце

	foreach( glob( "$dir/{,.}[!.,!..]*", GLOB_BRACE ) as $file ){

		if( is_dir( $file ) ){
			if( $include_folders )
				$files[] = $file;
			if( $recursive )
				$files = array_merge( $files, call_user_func( __FUNCTION__, $file, $recursive, $include_folders ) );
		}
		else
			$files[] = $file;
	}

	return $files;
}

//Получение только xlsx файлов 
function getExtension($filename) {
    if ("xlsx" == end(explode(".", $filename))) {
        return $filename;
    }
}

$arrayPath = get_dir_files($xlsxCatalogPath);
$xlsxFilesPath = [];

foreach($arrayPath as $filePath) {
    if (getExtension($filePath)) {
         $xlsxFilesPath [] = $filePath;
    }
}
