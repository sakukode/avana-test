<?php

namespace Avana\ExcelValidation;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelValidation
{
    protected $rows;    
    protected $columns;
    protected $pathFile;
    protected $validFile = false;
    protected $inputFileType;

    public function __construct(string $pathFile)
    {        
        $this->pathFile = $pathFile;
        $this->loadData();
    }

    /**
     * Read Data from Spreadsheet File using Library PHPSpreadsheet
     * https://github.com/PHPOffice/phpspreadsheet
     * @return void
     */
    public function loadData()
    {                
        //search file by valid extension
        $this->checkFile();
        
        if($this->isValid() === true) {
            $reader = IOFactory::createReader($this->inputFileType);
            $reader->setReadEmptyCells(false);
            $spreadsheet = $reader->load($this->pathFile);        
            $worksheet   = $spreadsheet->getActiveSheet();        
            $data        = $worksheet->toArray();

            $this->setColumnAndRows($data);
        }
    }

    public function isValid()
    {
        return $this->validFile;
    }

    /**
     * Find file by supported extension lists, if not found set it not valid
     * @return void
     */
    public function checkFile()
    {
        $fileTypes = ['xls', 'xlsx'];

        foreach($fileTypes as $fileType) {
            if (file_exists($this->pathFile.".".$fileType)) {
                $this->validFile = true;
                $this->inputFileType = ucfirst($fileType);
                $this->pathFile = $this->pathFile.".".$fileType;
                break;
            }
        }
    }

    /**
     * Return Result Validate
     * @return array
     */
    public function output()
    {
        return $this->validate();
    }

    /**
     * Validate each cell from each row with column rule
     * @return array
     */
    protected function validate()
    {
        if($this->validFile === false) {
            return "File Format Not Supported Or File not found.";
        }

        $rows = $this->rows; 

        $results = [];

        foreach($rows as $i => $row) {
            $errors = $this->validateRow($row);

            if(count($errors) > 0) {
                $results[] = [
                    'row' => $i + 2,
                    'error' => implode(",", $errors)
                ];
            }
        }

        return $results;
    }

    /**
     * Validate row
     * @param  array $row
     * @return array
     */
    protected function validateRow($row)
    {
        $columns = $this->columns;
        $errors = [];
        foreach($row as $index => $value) {
            $column = $columns[$index];
            
            $columnName = $column['name'];
            $columnRule = $column['rule'];            

            switch ($columnRule) {
                case 'required':
                    if($value == '')
                        $errors[] = "Missing value in ".$columnName;
                    break;
                case 'not_contain_space':                                    
                    if(preg_match('/\s/',$value))
                        $errors[] = $columnName." should not contain any space";
                    break;
                default:
                    # code...
                    break;
            }
        }

        return $errors;
    }

    /**
     * Set column and rows
     * @param array $data
     * @return void
     */
    protected function setColumnAndRows($data)
    {
        $headers = array_shift($data);
        $rows    = $data;

        //find rule each column
        $columns = array_map(function ($header) {
            $res = $this->getColumn($header);
            return $res;
        }, $headers);

        $this->columns = $columns;
        $this->rows = $rows;    
    }

    /**
     * Get column with rule
     * @param  array $header
     * @return array
     */
    protected function getColumn($header)
    {                
        if($header[0] === '#') {            
            $column = [
                'name' => ltrim($header, '#'),
                'rule' => 'not_contain_space'
            ];
        } else if($header[strlen($header)-1] === '*') {            
            $column = [
                'name' => rtrim($header, '*'),
                'rule' => 'required'
            ];
        } else {
            $column = [
                'name' => $header,
                'rule' => null
            ];
        }

        return $column;
    }
   
}
