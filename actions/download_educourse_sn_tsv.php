<?php

    // Gatekeeper
    action_gatekeeper();

    $guid = (int) get_input('educourse');
	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse') {
		$mime = 'text/tab-separated-values';
		$filename = 'educourse_' . $guid . '_sn.tsv';
		header('Content-type:' . $mime);
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		$contents = edufeedrGetCourseConnectionsTSV($educourse);
		$splitString = str_split($contents, 8192);
		foreach ($splitString as $chunk) {
			echo $chunk;
		}
		exit;
	} else {
		/*translation:Download failed.*/
		register_error(elgg_echo('edufeedr:error:download:failed'));
	}

?>
