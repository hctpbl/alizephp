<?php
	$installation_path = __DIR__.'/..';
	return array (
		'base_bin_dir' => "$installation_path/bin/",
		'base_data_dir' => "$installation_path/data/",
		'base_conf_dir' => "$installation_path/cfg/",
		'audio_dir' => "pcm/",
		'features_dir' => "prm/",
		'labels_dir' => "lbl/",
		'mixture_files_path' => "$installation_path/gmm/",
		'matrix_files_path' => "$installation_path/mat/",
		'vector_files_path' => "$installation_path/iv/",
		'ndx_dir' => "$installation_path/ndx/",
		'results_dir' => "$installation_path/res/",
		'cfg_files' => array (
				'normalise_energy' => 'NormFeat_energy_SPro.cfg',
				'detect_energy' => 'EnergyDetector_SPro.cfg',
				'normalise_features' => 'NormFeat_SPro.cfg',
				'iv_extractor' => 'ivExtractor_fast.cfg',
				'iv_test' => 'ivTest_EFR_Mahalanobis.cfg'
			),
		'extensions' => array(
				'audio' => '.pcm',
				'raw_features' => '.tmp.prm',
				'normalised_energy' => '.enr.tmp.prm',
				'normalised_features' => '.norm.prm',
				'label' => '.lbl',
				'ndx_files' => '.ndx',
				'mixture' => '.gmm',
				'matrix' => '.matx',
				'vector' => '.y'
			)
	);