<?php
require 'AlizePHP.php';
use alizephp\AlizePHP;
use alizephp\AlizePHPException;
$audio_file = "xaaf.pcm";
$speakerrec = new AlizePHP("person", $audio_file);
try {
	$features_file = $speakerrec->extractFeatures();
	$speakerrec->normaliseEnergy();
} catch (Exception $e) {
	print $e->getMessage();
	print $e->getCode();
}
