<?php
require __DIR__.'/../src/AlizePHP.php';

use alizephp\AlizePHP;

class AlizePHPTest extends PHPUnit_Framework_TestCase {
	
	protected $nameOne = "usuarioUno";
	protected $nameTwo = "usuarioDos";

	protected $filePathOne = 'tests/xaaf.pcm';
	protected $filePathTwo = 'tests/xaad.pcm';
	
	protected $alizeusrOne;
	protected $alizeusrTwo;
	
	protected function setUp() {
		$this->alizeusrOne = new AlizePHP($this->nameOne, $this->filePathOne);
		//$this->alizeusrTwo = new AlizePHP($this->nameTwo, $this->filePathTwo);
	}
	
	protected function tearDown() {
		if ($this->alizeusrOne) {
			$this->alizeusrOne->cleanUserFiles();
		}
		if ($this->alizeusrTwo) {
			$this->alizeusrTwo->cleanUserFiles();
		}
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Speaker must be a nonempty value.
	 */
	public function testCreateUserEmptyUsername() {
		$emptyUsernameUsr = new AlizePHP("", $this->filePathOne);
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Original audio file missing or unreadable.
	 */
	public function testCreateUserEmptyFile() {
		$emptyUsernameUsr = new AlizePHP($this->nameOne, "");
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Original audio file missing or unreadable.
	 */
	public function testCreateUserNonexistingFile() {
		$emptyUsernameUsr = new AlizePHP($this->nameOne, "asd.pcm");
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Original audio file missing or unreadable.
	 */
	public function testCreateUserUnreadableFile() {
		$emptyUsernameUsr = new AlizePHP($this->nameOne, "noPermissions.pcm");
	}
}