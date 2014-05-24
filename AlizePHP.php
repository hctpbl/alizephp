<?php

namespace alizephp;

class AlizePHP {
	
	private $speaker;
	private $conf require 'config/alizephp_conf.php';
	
	private function getConfig() {
		//$this->conf = require 'config/alizephp.php';
	}
	
	public function getAudioFilePath() {
		return $this->conf['base_dir'] . DIRECTORY_SEPARATOR . $this->conf['audio_dir'] . DIRECTORY_SEPARATOR . $this->speaker . ".pcm";
	}
	
	public function getFeauresFilePath() {
		return $this->conf['base_dir'] . DIRECTORY_SEPARATOR . $this->conf['features_dir'] . DIRECTORY_SEPARATOR . $this->speaker . ".prm";
	}
	
	function __construct($speaker, $audio_file_path) {
		$this->speaker = $speaker;
		if (!$speaker) Throw new AlizePHPException("Speaker must be a nonempty value.");
		file_put_contents("data/pcm/".$this->speaker.".pcm", file_get_contents($audio_file_path));
	}
	
	function extractFeatures ($param_string = NULL) {
		if ($param_string == NULL) $param_string = "-m -k 0.97 -p19 -n 24 -r 22 -e -D -A -F PCM16";
		$command = "sfbcep " . $param_string . " ".$this->getAudioFilePath()." ".$this->getFeauresFilePath();
		exec($command);
		return fopen($this->getFeauresFilePath("r"));
	}
	
}