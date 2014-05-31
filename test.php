<?php
require ("src/AlizePHP.php");

use alizephp\AlizePHP;

$audio_file = "xaaf.pcm";

$speakerrec = new AlizePHP("person", $audio_file);
$speakerrec->cleanUserFiles();
$speakerrec2 = new AlizePHP("person2", $audio_file);
$speakerrec2->cleanUserFiles();

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
try {
	$speakerrec2->extractFeatures();
	$speakerrec2->normaliseEnergy();
	$speakerrec2->detectEnergy();
	$speakerrec2->normaliseFeatures();
	$speakerrec2->ivExtractor();
	$speakerrec2->ivTest("person");
} catch (Exception $e) {
	print "<pre>".$e."</pre>";
}
