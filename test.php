<?php
require 'AlizePHP.php';
use alizephp\AlizePHP;
use alizephp\AlizePHPException;
$audio_file = "xaaf.pcm";
$speakerrec = new AlizePHP("person", $audio_file);
try {
	$speakerrec->extractFeatures();
	$speakerrec->normaliseEnergy();
	$speakerrec->detectEnergy();
	$speakerrec->normaliseFeatures();
	$speakerrec->ivExtractor();
} catch (Exception $e) {
	print $e->getMessage();
	print $e->getCode();
}

$speakerrec = new AlizePHP("person2", $audio_file);
try {
	$speakerrec->extractFeatures();
	$speakerrec->normaliseEnergy();
	$speakerrec->detectEnergy();
	$speakerrec->normaliseFeatures();
	$speakerrec->ivExtractor();
	$speakerrec->ivTest("person");
} catch (Exception $e) {
	print $e->getMessage();
	print $e->getCode();
}
