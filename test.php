<?php
require 'AlizePHP.php';
use alizephp\AlizePHP;
$audio_file = "xaaf.pcm";
$speakerrec = new AlizePHP("person", $audio_file);
$features_file = $speakerrec->extractFeatures();
