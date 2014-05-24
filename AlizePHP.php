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
	
	function __construct($speaker, $audio_file_path) {
		$this->getConfig();
		$this->speaker = $speaker;
		if (!$speaker) Throw new AlizePHPException("Speaker must be a nonempty value.");
		file_put_contents("data/pcm/".$this->speaker.".pcm", file_get_contents($audio_file_path));
	}
	
	function extractFeatures ($param_string = null) {
		if ($param_string === null) {
			$param_string = "-m -k 0.97 -p19 -n 24 -r 22 -e -D -A -F PCM16";
		}
		$audio_file = $this->getAudioFilePath().$this->speaker.$this->conf['extensions']['audio'];
		$feaures_file = $this->getFeauresFilePath().$this->speaker.$this->conf['extensions']['raw_features'];
		$command = $this->getBinPath() . "sfbcep " . $param_string . " ".$audio_file." ".$feaures_file;
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		if ($outvalues[0] != 0) throw new AlizePHPException($outvalues[2], $outvalues[0]);
		return true;
	}
	
	function normaliseEnergy($cfg_file_path = null) {
		if ($cfg_file_path === null) {
			$cfg_file_path = $this->getBaseConfigDir() . $this->conf['cfg_files']['normalise_energy'];
		}
		$input_feaures_file = $this->getFeauresFilePath().$this->speaker.$this->conf['extensions']['raw_features'];
		$command = $this->getBinPath()."NormFeat --config $cfg_file_path --inputFeatureFilename ".$input_feaures_file." --featureFilesPath ".$this->getFeauresFilePath();
		print "<p>$command</p>";
		$outvalues = $this->executeCommand($command);
		if ($outvalues[0] != 0) throw new AlizePHPException($outvalues[2], $outvalues[0]);
		return true;
	}
	
}