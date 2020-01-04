<?php
require_once __DIR__ .'/vendor/autoload.php';
use Avana\ExcelValidation\ExcelValidation;

$file = new ExcelValidation('samples/Type_A');
$output = $file->output();

echo json_encode($output);
