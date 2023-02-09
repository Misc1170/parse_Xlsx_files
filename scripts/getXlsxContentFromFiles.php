<?php
include(__DIR__.'/scanDir.php');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

// Чтение и добавление в массив xlsx файл
function getSheet($path)
{
    $reader = new Xlsx();
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($path);
    $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
    $data = $sheet->toArray();
    return $data;
}

//Получение уникальных заголовков всех файлов
$headersAllXlsxFiles = [];
$uniqueHeaders = [];
foreach($xlsxFilesPath as $path) {
    $temp = getSheet($path);
    $headersAllXlsxFiles [] = $temp[0];
}

foreach ($headersAllXlsxFiles as $array) foreach ($array as $key =>$value) {
$uniqueHeaders[] = $value;
}
$uniqueHeaders = array_unique($uniqueHeaders);

echo "<pre>"; print_r($uniqueHeaders); echo "</pre>";

//Получение данных из файлов
$dataFromXlsxFiles = [];
$combineXlsxFile =[];
$result = [];
foreach($xlsxFilesPath as $path) {
    $dataWithHeaders = getSheet($path);
        foreach(array_slice($dataWithHeaders,1) as $item) {
            $combineXlsxFile = array_combine(
                $dataWithHeaders[0],
                $item
            );
            $dataFromXlsxFiles [] = $combineXlsxFile;
        }
    }

//Формирую массив с разбивкой по колонкам, например если в одном файле была колонка ТЕСТ а в другом нет, 
//то элементов массива будет по общему кол-ву в 2х файлах, но там где этой колонки не было значение будет пустым

foreach($uniqueHeaders as $header){
    foreach ($dataFromXlsxFiles as $data) {
        $result[$header][] = $data[$header] ?? '';
    }
}

//преобразовываю из стобчатого представления в строковое
$xlsxFile = [];
foreach($result as $header => $items){
    foreach($items as $index => $item) {
        $xlsxFile[$index][$header] = $item;
    }
}

//Запись данных в файл
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestRow();
$allLetters = range('A', 'Z');


$i = 0;
foreach ($uniqueHeaders as $key=>$value) {
        $sheet->setCellValue("$allLetters[$i]" . "$highestRow", "$value");
        $i++;
    }

$highestRow = $sheet->getHighestRow() + 1;

foreach ($xlsxFile as $array){    
echo "<pre>"; print_r($array); echo "</pre>";
$i=0;
    foreach($array as $key => $value){
            $sheet->setCellValue("$allLetters[$i]" . "$highestRow", "$value");
            if($i == count($array)) {
                break;
            }
            $i++;
        }
        $highestRow = $sheet->getHighestRow() + 1;
}
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('new.xlsx');
echo "Данные записаны";

