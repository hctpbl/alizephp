<?php
require __DIR__.'/../src/AlizePHP.php';

use alizephp\AlizePHP;

/**
 * This class preforms the necessary unit tests in the AlizePHP class.s
 * 
 * @author Héctor Pablos
 *
 */
class AlizePHPTest extends PHPUnit_Framework_TestCase {
	
	protected $nameOne = "usuarioUno";
	protected $nameTwo = "usuarioDos";

	protected $filePathOne = 'tests/xaaf.pcm';
	protected $filePathTwo = 'tests/xaag.pcm';
	
	/**
	 * T-ALI01
	 * Tries to create a user with an empty username
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Speaker must be a nonempty value.
	 */
	public function testCreateUserEmptyUsername() {
		$emptyUsernameUsr = new AlizePHP("", $this->filePathOne);
	}
	
	/**
	 * T-ALI02
	 * Tries to create a username with a string containing only blank
	 * characters
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Speaker must be a nonempty value.
	 */
	public function testCreateUserAllBlanksUsername() {
		$emptyUsernameUsr = new AlizePHP("     ", $this->filePathOne);
	}
	
	/**
	 * T-ALI03
	 * Creates an AlizePHP user with correct values
	 * @return \alizephp\AlizePHP
	 */
	public function testCreateUserSuccessful() {
		$alizeuser = new AlizePHP($this->nameOne);
		$this->assertEquals($alizeuser->getSpeaker(), $this->nameOne);
		//$this->assertEquals($alizeuser->getOriginalAudioFile(), $this->filePathOne);
		
		return $alizeuser;
	}
	
	/**
	 * T-ALI04
	 * Tries to create a user with an empty filename
	 * @depends testCreateUserSuccessful
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Audio file does not exist
	 */
	public function testCreateUserEmptyFile(AlizePHP $alizeuser) {
		$alizeuser->extractFeatures("");
		//$emptyUsernameUsr = new AlizePHP($this->nameOne, "");
	}
	
	/**
	 * T-ALI05
	 * Tries to create a user with passing the name of a file that doesn't
	 * exists
	 * @depends testCreateUserSuccessful
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Audio file does not exist
	 */
	public function testCreateUserNonexistingFile(AlizePHP $alizeuser) {
		$alizeuser->extractFeatures("tests/asd.pcm");
		//$emptyUsernameUsr = new AlizePHP($this->nameOne, "asd.pcm");
	}
	
	/**
	 * T-ALI06
	 * Tries to create a user with the name of a file not readable by the process
	 * @depends testCreateUserSuccessful
	 * @expectedException \alizephp\AlizePHPException
	 * @expectedExceptionMessage Read permission denied for audio file
	 */
	public function testCreateUserUnreadableFile(AlizePHP $alizeuser) {
		$alizeuser->extractFeatures("tests/noPermissions.pcm");
		//$emptyUsernameUsr = new AlizePHP($this->nameOne, "noPermissions.pcm");
	}

	/**
	 * T-ALI07
	 * Tests the success of the extraction of features
	 * @depends testCreateUserSuccessful
	 * @return \alizephp\AlizePHP
	 */
	public function testExtractFeatures(AlizePHP $alizeuser) {
		$alizeuser->extractFeatures($this->filePathOne);
		$this->assertFileExists($alizeuser->getRawFeaturesFileName());
		
		return $alizeuser;
	}
	
	/**
	 * T-ALI08
	 * Test the success of the normalisation of energy method
	 * @depends testExtractFeatures
	 * @return \alizephp\AlizePHP
	 */
	public function testNormaliseEnergy(AlizePHP $alizeuser) {
		$alizeuser->normaliseEnergy();
		$this->assertFileExists($alizeuser->getNormalisedEnergyFileName());
		
		return $alizeuser;
	}
	
	/**
	 * T-ALI09
	 * Tries to normalise energy without having a features file for the
	 * user
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailNormaliseEnergy() {
		$otheruser = new AlizePHP($this->nameTwo);
		$otheruser->normaliseEnergy();
	}
	
	/**
	 * T-ALI10
	 * Tests the success of the detection of energy method
	 * @depends testNormaliseEnergy
	 * @return \alizephp\AlizePHP
	 */
	public function testDetectEnergy(AlizePHP $alizeuser) {
		$alizeuser->detectEnergy();
		$this->assertFileExists($alizeuser->getLabelFileName());
		
		return $alizeuser;
	}
	
	/**
	 * T-ALI11
	 * Tries to detect energy without having a normalised energy file for the
	 * user
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailDetectEnergy() {
		$otheruser = new AlizePHP($this->nameTwo);
		$otheruser->detectEnergy();
	}
	
	/**
	 * T-ALI12
	 * Tests the normalisation of features method
	 * @depends testDetectEnergy
	 * @return \alizephp\AlizePHP
	 */
	public function testNormaliseFeatures(AlizePHP $alizeuser) {
		$alizeuser->normaliseFeatures();
		$this->assertFileExists($alizeuser->getNormalisedFeaturesFileName());
		
		return $alizeuser;
	}
	
	/**
	 * T-ALI13
	 * Tries to normalise features without a label file for the
	 * user
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailNormaliseFeatures() {
		$otheruser = new AlizePHP($this->nameTwo);
		$otheruser->normaliseFeatures();
	}


	/**
	 * T-ALI14
	 * Tests the success of the train target method
	 * @depends testNormaliseFeatures
	 */
	public function testTrainTarget(AlizePHP $alizeuser) {
		$alizeuser->trainTarget();
		$this->assertFileExists($alizeuser->getTrainModelFileName());
		$this->assertTrue(AlizePHP::hasModel($alizeuser->getSpeaker()));
		
		return $alizeuser;
	}
	
	/**
	 * T-ALI15
	 * Tries to generate a model without having a normalised features
	 * file for the user
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailTrainTarget() {
		$otheruser = new AlizePHP($this->nameTwo);
		$otheruser->trainTarget();
	}
	
	/**
	 * Test the compute test method
	 * @depends testTrainTarget
	 */
	public function testComputeTest(AlizePHP $alizeuser) {
		$otheruser = new AlizePHP($this->nameTwo);
		$otheruser->extractFeatures($this->filePathTwo);
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
	 * Tries to use the computeTest method to test a user against
	 * another, with the former not having a normalised features file
	 * generated
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailComputeTestNoFeatures() {
		$otheruser = new AlizePHP($this->nameTwo, $this->filePathTwo);
		$otheruser->trainTarget($this->nameOne);
	}
	
	/**
	 * Tries to use the computeTest method to test a user against
	 * another, with the latter not having a model generated
	 * @depends testComputeTest
	 * @expectedException \alizephp\AlizePHPException
	 */
	public function testFailComputeTestNoModel(AlizePHP $alizeuser) {
		$alizeuser->computeTest($this->nameTwo);
	}

	/**
	 * Tests the removal of a user from the system
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