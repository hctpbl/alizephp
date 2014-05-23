<?php
use alizephp\AlizePHP;
$audio_file = fopen("xaaf.pcm", "r");
$speakerrec = new AlizePHP("person", $audio_file);
$features_file = $speakerrec->extractFeatures();