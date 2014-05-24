<?php

namespace alizephp;

class AlizePHP {
	
	private $speaker;
	private $conf;
	
	private function getConfig() {
		$this->conf = require 'config/alizephp.php';
		print_r($this->conf);
	}
	
	public function getAudioFilePath() {
		return $this->conf['base_dir'] . DIRECTORY_SEPARATOR . $this->conf['audio_dir'] . DIRECTORY_SEPARATOR . $this->speaker . ".pcm";
	}
	
	public function getFeauresFilePath() {
		return $this->conf['base_dir'] . DIRECTORY_SEPARATOR . $this->conf['features_dir'] . DIRECTORY_SEPARATOR . $this->speaker . ".prm";
	}
	
	function __construct($speaker, $audio_file) {
		$this->speaker = $speaker;
		if (!$speaker) die ("Speaker required.");
		$this->getConfig();
		echo $this->getAudioFilePath();
		//echo $this->getFeaturesFilePath();
		file_put_contents("data/pcm/".$this->speaker.".pcm", file_get_contents($audio_file));
	}
	
	function extractFeatures () {
		$command = "sfbcep -masas -k 0.97 -p19 -n 24 -r 22 -e -D -A -F PCM16 ".$this->getAudioFilePath()." ".$this->getFeauresFilePath();
		echo $command;
		$out = exec($command, $out2);
		echo $out;
		print_r($out2);
		return fopen($this->getFeauresFilePath(), "r");
	}
	
}
