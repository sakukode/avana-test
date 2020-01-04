<?php

use PHPUnit\Framework\TestCase;
use Avana\ExcelValidation\ExcelValidation;

class ExcelValidationTest extends TestCase
{
	private $validFile;
	private $notValidFile;

	public function setUp(): void
	{
		$this->validFile = new ExcelValidation(__DIR__.'/data/sample');
		$this->notValidFile = new ExcelValidation(__DIR__.'/data/foo');
	}

    public function testCheckValidFile()
    {
        $file = $this->validFile;

        $this->assertTrue($file->isValid());
    }

    public function testCheckNotValidFile()
    {
        $file = $this->notValidFile;

        $this->assertFalse($file->isValid());
    }

    public function testOutputWithValidFile()
    {
    	$file = $this->validFile;
    	$output = $file->output();

    	$testOutput = [
    		[
    			"row" => 3,
    			"error" => "Missing value in Field_A,Field_B should not contain any space,Missing value in Field_D"
    		],
    		[
    			"row" => 4,
    			"error" => "Missing value in Field_A,Missing value in Field_E"
    		]
    	];

    	$this->assertEquals($testOutput, $output);
    }

    public function testOutputWithNotValidFile()
    {
    	$file = $this->notValidFile;
    	$output = $file->output();

    	$this->assertEquals('File Format Not Supported Or File not found.', $output);
    }    
}