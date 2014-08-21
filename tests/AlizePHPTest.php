<?php
require __DIR__.'/../src/AlizePHP.php';

use alizephp\AlizePHP;

class AlizePHPTest extends PHPUnit_Framework_TestCase {
	
	protected $nameOne = "usuarioUno";
	protected $nameTwo = "usuarioDos";

	protected $filePathOne = "xaaf.pcm";
	protected $filePathTwo = "xaad.pcm";
	
	protected $alizeusrOne;
	protected $alizeusrTwo;
	
	protected function setUp() {
		$this->$alizeusrOne = new AlizePHP($this->$nameOne, $this->$filePathOne);
		$this->$alizeusrTwo = new AlizePHP($this->$nameTwo, $this->$filePathTwo);
	}
	
	protected function tearDown() {
		$this->$alizeusrOne->cleanUserFiles();
		$this->$alizeusrTwo->cleanUserFiles();
	}
	
	public function testCreateUserEmptyUsername() {
		$emptyUsernameUsr = new AlizePHP("", $this->filePathOne);
	}
}