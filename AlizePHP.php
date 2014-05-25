<?php

namespace alizephp;

require 'AlizePHPException.php';

class AlizePHP {
	
	private $speaker;
	private $conf;
	
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
	public function getIvExtractorFileName() {
		return $this->conf['ndx_dir']."IvExtractor_".$this->speaker.".ndx";
	}
	
	public function __construct($speaker, $audio_file_path) {
		$this->getConfig();
		$this->speaker = $speaker;
		if (!$speaker) Throw new AlizePHPException("Speaker must be a nonempty value.");
		file_put_contents("data/pcm/".$this->speaker.".pcm", file_get_contents($audio_file_path));
	}
	
	public function extractFeatures ($param_string = null) {
		if ($param_string === null) {
			$param_string = "-m -k 0.97 -p19 -n 24 -r 22 -e -D -A -F PCM16";
		}
		$audio_file = $this->getAudioFilePath().$this->speaker.$this->conf['extensions']['audio'];
		$feaures_file = $this->getFeauresFilePath().$this->speaker.$this->conf['extensions']['raw_features'];
		$command = $this->getBinPath() . "sfbcep " . $param_string . " ".$audio_file." ".$feaures_file;
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	public function normaliseEnergy($cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['normalise_energy'];
		}
		$command = $this->getBinPath()."NormFeat --config $cfg_file_path --inputFeatureFilename ".$this->speaker.
					" --featureFilesPath ".$this->getFeauresFilePath();
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	public function detectEnergy($cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['detect_energy'];
		}
		$command = $this->getBinPath()."EnergyDetector --config $cfg_file_path --inputFeatureFilename ".$this->speaker.
					" --featureFilesPath ".$this->getFeauresFilePath()." --labelFilesPath ".$this->getLabelsFilePath();
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	public function normaliseFeatures($cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['normalise_features'];
		}
		$command = $this->getBinPath()."NormFeat --config $cfg_file_path --inputFeatureFilename ".$this->speaker.
					" --featureFilesPath ".$this->getFeauresFilePath()." --labelFilesPath ".$this->getLabelsFilePath();
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
	private function createIvExtractorFile() {
		$ivExtractFile = fopen($this->getIvExtractorFileName(), "w");
		fputs($ivExtractFile, $this->speaker." ".$this->speaker);
		fclose($ivExtractFile);
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
					" --targetIdList ".$this->getIvExtractorFileName();
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		return true;
	}
	
}