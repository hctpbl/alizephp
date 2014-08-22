<?php
require __DIR__.'/../src/AlizePHP.php';

use alizephp\AlizePHP;

class AlizePHPTest extends PHPUnit_Framework_TestCase {
	
	protected $nameOne = "usuarioUno";
	protected $nameTwo = "usuarioDos";

	protected $filePathOne = 'tests/xaaf.pcm';
	protected $filePathTwo = 'tests/xaag.pcm';
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Speaker must be a nonempty value.
	 */
	public function testCreateUserEmptyUsername() {
		$emptyUsernameUsr = new AlizePHP("", $this->filePathOne);
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Speaker must be a nonempty value.
	 */
	public function testCreateUserAllBlanksUsername() {
		$emptyUsernameUsr = new AlizePHP("     ", $this->filePathOne);
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
	
	public function testCreateUserSuccessful() {
		$alizeuser = new AlizePHP($this->nameOne, $this->filePathOne);
		$this->assertEquals($alizeuser->getSpeaker(), $this->nameOne);
		$this->assertEquals($alizeuser->getOriginalAudioFile(), $this->filePathOne);
		
		return $alizeuser;
	}

	/**
	 * @depends testCreateUserSuccessful
	 */
	public function testExtractFeatures(AlizePHP $alizeuser) {
		$alizeuser->extractFeatures();
		$this->assertFileExists($alizeuser->getRawFeaturesFileName());
		
		return $alizeuser;
	}
	
	/**
	 * @depends testExtractFeatures
	 */
	public function testNormaliseEnergy(AlizePHP $alizeuser) {
		$alizeuser->normaliseEnergy();
		$this->assertFileExists($alizeuser->getNormalisedEnergyFileName());
		
		return $alizeuser;
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailNormaliseEnergy() {
		$otheruser = new AlizePHP($this->nameTwo, $this->filePathTwo);
		$otheruser->normaliseEnergy();
	}
	
	/**
	 * @depends testNormaliseEnergy
	 */
	public function testDetectEnergy(AlizePHP $alizeuser) {
		$alizeuser->detectEnergy();
		$this->assertFileExists($alizeuser->getLabelFileName());
		
		return $alizeuser;
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailDetectEnergy() {
		$otheruser = new AlizePHP($this->nameTwo, $this->filePathTwo);
		$otheruser->detectEnergy();
	}
	
	/**
	 * @depends testDetectEnergy
	 */
	public function testNormaliseFeatures(AlizePHP $alizeuser) {
		$alizeuser->normaliseFeatures();
		$this->assertFileExists($alizeuser->getNormalisedFeaturesFileName());
		
		return $alizeuser;
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailNormaliseFeatures() {
		$otheruser = new AlizePHP($this->nameTwo, $this->filePathTwo);
		$otheruser->normaliseFeatures();
	}


	/**
	 * @depends testNormaliseFeatures
	 */
	public function testTrainTarget(AlizePHP $alizeuser) {
		$alizeuser->trainTarget();
		$this->assertFileExists($alizeuser->getTrainModelFileName());
		$this->assertTrue(AlizePHP::hasModel($alizeuser->getSpeaker()));
		
		return $alizeuser;
	}
	
	/**
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailTrainTarget() {
		$otheruser = new AlizePHP($this->nameTwo, $this->filePathTwo);
		$otheruser->trainTarget();
	}
	
	/**
	 * @depends testTrainTarget
	 */
	public function testComputeTest(AlizePHP $alizeuser) {
		$otheruser = new AlizePHP($this->nameTwo, $this->filePathTwo);
		$otheruser->extractFeatures();
		$otheruser->normaliseEnergy();
		$otheruser->detectEnergy();
		$otheruser->normaliseFeatures();
		$result = $otheruser->computeTest($alizeuser->getSpeaker());
		
		$this->assertFileExists($otheruser->getNdxFileName());
		$this->assertFileExists($otheruser->getResultsFileName());
		$this->assertInternalType('float',$result);
		
		$otheruser->cleanUserFiles();
		
		return $alizeuser;
	}


	/**
	 * @depends testComputeTest
	 */
	public function testCleanUserFiles(AlizePHP $alizeuser) {
		$alizeuser->cleanUserFiles();
		$this->assertTrue(!file_exists($alizeuser->getRawFeaturesFileName()));
		$this->assertTrue(!file_exists($alizeuser->getNormalisedEnergyFileName()));
		$this->assertTrue(!file_exists($alizeuser->getLabelFileName()));
		$this->assertTrue(!file_exists($alizeuser->getNormalisedFeaturesFileName()));
		$this->assertTrue(!file_exists($alizeuser->getTrainModelFileName()));
		$this->assertTrue(!file_exists($alizeuser->getModelFileName()));
		$this->assertTrue(!file_exists($alizeuser->getResultsFileName()));
	}
}