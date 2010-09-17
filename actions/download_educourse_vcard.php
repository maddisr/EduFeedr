<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

	$guid = (int) get_input('educourse');
	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse)) {
		$mime = 'text/directory';
		$filename = 'educourse_' . $guid . '.vcf';
		header('Content-type:' . $mime);
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		$vcard = edufeedrCourseVCARD($educourse);
		$splitString = str_split($vcard, 8192);
		foreach ($splitString as $chunk) {
			echo $chunk;
		}
		exit;
	} else {
		/*translation:Download failed.*/
		register_error(elgg_echo('edufeedr:error:download:failed'));
	}

?>
