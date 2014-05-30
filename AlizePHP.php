<?php

namespace alizephp;

require 'AlizePHPException.php';

class AlizePHP {
	
	private $speaker;
	private $original_audio_file;
	private $conf;
	
	/**
	 * Returns speaker name
	 */
	public function getSpeaker() {
		return $this->speaker;
	}
	
	private function getConfig() {
		$this->conf = require 'cfg/alizephp_conf.php';
	}
	
	private function executeCommand($comm) {
		$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin
				1 => array("pipe", "w"),  // stdout
				2 => array("pipe", "w"),  // stderr
		);
		
		$process = proc_open($comm, $descriptorspec, $pipes);
		
		$outvalues = array();
		
		// $outvalues[1] is stdout
		$outvalues[1] = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		
		// $outvalues[2] is stderr
		$outvalues[2] = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		
		// $outvalues[0] is return value
		$outvalues[0] = proc_close($process);
		
		print "<p>$outvalues[0]</p>";
		print "<p>$outvalues[1]</p>";
		print "<p>$outvalues[2]</p>";
		
		if ($outvalues[0] != 0) throw new AlizePHPException($outvalues[2], $outvalues[0]);
		
		return $outvalues;
		
	}
	
	private function getBinPath() {
		return $this->conf['base_bin_dir'];
	}
	
	private function getBaseDataDir() {
		return $this->conf['base_data_dir'];
	}
	
	private function getBaseConfigDir() {
		return $this->conf['base_conf_dir'];
	}
	
	public function getAudioFilePath() {
		return $this->getBaseDataDir() . $this->conf['audio_dir'];
	}
	
	public function getFeauresFilePath() {
		return $this->getBaseDataDir() . $this->conf['features_dir'] ;
	}
	
	public function getLabelsFilePath() {
		return $this->getBaseDataDir() . $this->conf['labels_dir'] ;
	}
	public function getMixtureFilesPath() {
		return $this->conf['mixture_files_path'];
	}
	public function getMatrixFilesPath() {
		return $this->conf['matrix_files_path'];
	}
	public function getVectorFilesPath() {
		return $this->conf['vector_files_path'];
	}
	public function getNdxFilesPath() {
		return $this->conf['ndx_dir'];
	}
	public function getResultFilesPath() {
		return $this->conf['results_dir'];
	}
	public function getIvExtractorFileName() {
		return $this->getNdxFilesPath()."IvExtractor_".$this->getSpeaker().$this->conf['extensions']['ndx_files'];
	}
	public function getAudioFileName() {
		return $this->getAudioFilePath().$this->getSpeaker().$this->conf['extensions']['audio'];
	}
	public function getRawFeaturesFileName() {
		return $this->getFeauresFilePath().$this->getSpeaker().$this->conf['extensions']['raw_features'];
	}
	public function getNormalisedEnergyFileName() {
		return $this->getFeauresFilePath().$this->getSpeaker().$this->conf['extensions']['normalised_energy'];
	}
	public function getNormalisedFeaturesFileName() {
		return $this->getFeauresFilePath().$this->getSpeaker().$this->conf['extensions']['normalised_features'];
	}
	public function getLabelFileName() {
		return $this->getLabelsFilePath().$this->getSpeaker().$this->conf['extensions']['label'];
	}
	public function getVectorFileName(){
		return $this->getVectorFilesPath().$this->getSpeaker().$this->conf['extensions']['vector'];
	}
	public function getTrainModelFileName($speaker = null) {
		if ($speaker === null) {
			$speaker = $this->getSpeaker();
		}
		return $this->getNdxFilesPath()."trainModel_".$speaker.$this->conf['extensions']['ndx_files'];
	}
	public function getNdxFileName() {
		return $this->getNdxFilesPath()."ivTest_plda_target-seg_".$this->getSpeaker().$this->conf['extensions']['ndx_files'];
	}
	public function getResultsFileName() {
		return $this->getResultFilesPath()."scores_".$this->getSpeaker().".txt";
	}
	
	public function __construct($speaker, $audio_file_path) {
		$this->getConfig();
		if (!$speaker) Throw new AlizePHPException("Speaker must be a nonempty value.");
		if (!file_exists($audio_file_path)) throw new AlizePHPException("Audio file not existing.");
		$this->speaker = $speaker;
		$this->original_audio_file = $audio_file_path;
	}
	
	public function extractFeatures ($param_string = null) {
		file_put_contents("data/pcm/".$this->getSpeaker().".pcm", file_get_contents($this->original_audio_file));
		if ($param_string === null) {
			$param_string = "-m -k 0.97 -p19 -n 24 -r 22 -e -D -A -F PCM16";
		}
		$audio_file = $this->getAudioFileName();
		$feaures_file = $this->getRawFeaturesFileName();
		$command = $this->getBinPath() . "sfbcep " . $param_string . " ".$audio_file." ".$feaures_file;
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	public function normaliseEnergy($cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['normalise_energy'];
		}
		$command = $this->getBinPath()."NormFeat --config $cfg_file_path --inputFeatureFilename ".$this->getSpeaker().
					" --featureFilesPath ".$this->getFeauresFilePath().
					" --loadFeatureFileExtension ".$this->conf['extensions']['raw_features'].
					" --saveFeatureFileExtension ".$this->conf['extensions']['normalised_energy'];
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	public function detectEnergy($cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['detect_energy'];
		}
		if ($this->getLabelFileName()) {
			unlink($this->getLabelFileName());
		}
		$command = $this->getBinPath()."EnergyDetector --config $cfg_file_path --inputFeatureFilename ".$this->getSpeaker().
					" --featureFilesPath ".$this->getFeauresFilePath()." --labelFilesPath ".$this->getLabelsFilePath().
					" --loadFeatureFileExtension ".$this->conf['extensions']['normalised_energy'].
					" --saveLabelFileExtension ".$this->conf['extensions']['label'];
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	public function normaliseFeatures($cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['normalise_features'];
		}
		$command = $this->getBinPath()."NormFeat --config $cfg_file_path --inputFeatureFilename ".$this->getSpeaker().
					" --featureFilesPath ".$this->getFeauresFilePath()." --labelFilesPath ".$this->getLabelsFilePath().
					" --loadFeatureFileExtension ".$this->conf['extensions']['raw_features'].
					" --saveFeatureFileExtension ".$this->conf['extensions']['normalised_features'];
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	private function createIvExtractorFile() {
		$ivExtractFile = fopen($this->getIvExtractorFileName(), "w");
		fputs($ivExtractFile, $this->getSpeaker()." ".$this->getSpeaker());
		fclose($ivExtractFile);
	}
	
	private function createTrainModelFile() {
		$trainWorldFile = fopen($this->getTrainModelFileName(), "w");
		fputs($trainWorldFile, "spk01 ".$this->getSpeaker());
		fclose($trainWorldFile);
	}
	
	public function ivExtractor($cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['iv_extractor'];
		}
		if (!file_exists($this->getIvExtractorFileName())) {
			$this->createIvExtractorFile();
		}
		$command = $this->getBinPath()."IvExtractor --config $cfg_file_path --mixtureFilesPath ".$this->getMixtureFilesPath().
					" --matrixFilesPath ".$this->getMatrixFilesPath()." --saveVectorFilesPath ".$this->getVectorFilesPath().
					" --featureFilesPath ".$this->getFeauresFilePath()." --labelFilesPath ".$this->getLabelsFilePath().
					" --targetIdList ".$this->getIvExtractorFileName().
					" --loadFeatureFileExtension ".$this->conf['extensions']['normalised_features'].
					" --loadMixtureFileExtension ".$this->conf['extensions']['mixture'].
					" --saveMixtureFileExtension ".$this->conf['extensions']['mixture'].
					" --loadMatrixFilesExtension ".$this->conf['extensions']['matrix'].
					" --saveMatrixFilesExtension ".$this->conf['extensions']['matrix'].
					" --vectorFilesEtension ".$this->conf['extensions']['vector'].
					" --outputFileName ".$this->getResultsFileName();
		if (!file_exists($this->getTrainModelFileName())) {
			$this->createTrainModelFile();
		}
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	private function createNdxFile() {
		$ndxFile = fopen($this->getNdxFileName(), "w");
		fputs($ndxFile, $this->getSpeaker(). " spk01");
		fclose($ndxFile);
	}
	
	public function ivTest($speaker_to_compare_to, $cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['iv_test'];
		}
		if (!file_exists($this->getNdxFileName())) {
			$this->createNdxFile();
		}
		$command = $this->getBinPath()."IvTest --config $cfg_file_path --loadVectorFilesPath ".$this->getVectorFilesPath().
					" --testVectorFilesPath ".$this->getVectorFilesPath()." --matrixFilesPath ".$this->getMatrixFilesPath().
					" --targetIdList ".$this->getTrainModelFileName($speaker_to_compare_to).
					" --ndxFilename ".$this->getNdxFileName().
					" --loadMatrixFilesExtension ".$this->conf['extensions']['matrix'].
					" --saveMatrixFilesExtension ".$this->conf['extensions']['matrix'].
					" --loadVectorFilesExtension ".$this->conf['extensions']['vector'].
					" --outputFilename ".$this->getResultsFileName();
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	private function deletefeFile($file_path) {
		if (file_exists($file_path)) {
			unlink($file_path);
		}
	}
	
	public function cleanUserFiles() {
		$this->deletefeFile($this->getAudioFileName());
		$this->deletefeFile($this->getRawFeaturesFileName());
		$this->deletefeFile($this->getNormalisedEnergyFileName());
		$this->deletefeFile($this->getNormalisedFeaturesFileName());
		$this->deletefeFile($this->getVectorFileName());
		$this->deletefeFile($this->getIvExtractorFileName());
		$this->deletefeFile($this->getTrainModelFileName());
		$this->deletefeFile($this->getNdxFileName());
		$this->deletefeFile($this->getResultsFileName());
	}
	
	public function hasVector() {
		return file_exists($this->getVectorFileName());
	}
	
}