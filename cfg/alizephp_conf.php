<?php
	return array (
		'base_bin_dir' => "bin" . DIRECTORY_SEPARATOR,
		'base_data_dir' => 'data' . DIRECTORY_SEPARATOR,
		'base_conf_dir' => 'cfg' . DIRECTORY_SEPARATOR,
		'audio_dir' => 'pcm' . DIRECTORY_SEPARATOR,
		'features_dir' => 'prm' . DIRECTORY_SEPARATOR,
		'cfg_files' => array (
				'normalise_energy' => 'NormFeat_energy_SPro.cfg'
			),
		'extensions' => array(
				'audio' => '.pcm',
				'raw_features' => '.tmp.prm',
				'normalised_features' => '.enr.tmp.prm'
			)
	);