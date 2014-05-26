<?php
	return array (
		'base_bin_dir' => "bin" . DIRECTORY_SEPARATOR,
		'base_data_dir' => 'data' . DIRECTORY_SEPARATOR,
		'base_conf_dir' => 'cfg' . DIRECTORY_SEPARATOR,
		'audio_dir' => 'pcm' . DIRECTORY_SEPARATOR,
		'features_dir' => 'prm' . DIRECTORY_SEPARATOR,
		'labels_dir' => 'lbl' . DIRECTORY_SEPARATOR,
		'mixture_files_path' => 'gmm' . DIRECTORY_SEPARATOR,
		'matrix_files_path' => 'mat' . DIRECTORY_SEPARATOR,
		'vector_files_path' => 'iv' . DIRECTORY_SEPARATOR,
		'ndx_dir' => 'ndx' . DIRECTORY_SEPARATOR,
		'cfg_files' => array (
				'normalise_energy' => 'NormFeat_energy_SPro.cfg',
				'detect_energy' => 'EnergyDetector_SPro.cfg',
				'normalised_features' => 'NormFeat_SPro.cfg',
				'iv_extractor' => 'ivExtractor_fast.cfg',
				'iv_test' => 'ivTest_EFR_Mahalanobis.cfg'
			),
		'extensions' => array(
				'audio' => '.pcm',
				'raw_features' => '.tmp.prm',
				'normalised_energy' => '.enr.tmp.prm',
				'normalised_feaures' => '.norm.prm',
				'label' => '.lbl',
				'ndx_files' => '.ndx',
				'mixture' => '.gmm',
				'matrix' => '.matx',
				'vector' => '.y'
			)
	);