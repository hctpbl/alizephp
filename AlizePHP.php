<?php

namespace alizephp;

class AlizePHP {
	
	private $speaker;
	private $conf;
	
	private function getConfig() {
		$this->conf = require 'config/alizephp.php';
	}
	
	public function getAudioFilePath() {
		return $this->conf['base_dir'] . DIRECTORY_SEPARATOR . $this->conf['audio_dir'] . DIRECTORY_SEPARATOR . $this->speaker;
	}
	
	public function getFeauresFilePath() {
		return $this->conf['base_dir'] . DIRECTORY_SEPARATOR . $this->conf['features_dir'] . $this->speaker;
	}
	
	function __construct($speaker, $audio_file) {
		$this->speaker = $speaker;
		if (!$speaker) die ("Speaker required.");
		file_put_contents("data/pcm/".$this->speaker.".pcm", file_get_contents($audio_file));
	}
	
	function extractFeatures () {
		$command = "sfbcep -m -k 0.97 -p19 -n 24 -r 22 -e -D -A -F PCM16 ".$this->getAudioFilePath()." ".$this->getFeauresPath();
		exec($command);
		return fopen($this->getFeauresFilePath("r"));
	}
	
}