<?php
require_once  'vendor/autoload.php';

use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfReader\PdfReader;
$path = 'file.pdf';
function getPageCountOfPdf($path) 
{
    $stream = StreamReader::createByFile($path);
    $parser = new PdfParser($stream);

    $pdfReader = new PdfReader($parser);

    return $pdfReader->getPageCount();
}
getPageCountOfPdf($path);

?>