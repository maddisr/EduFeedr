<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

	$guid = (int) get_input('educourse');
	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse)) {
		$line_separator = "\r\n";
		$mime = 'text/csv';
		$filename = 'educourse_' . $guid . '_participants_and_assignments.csv';
		header('Content-type:' . $mime);
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		$assignments = edufeedrGetCourseAssignments($guid);
		uasort($assignments, '__edufeedr_assignments_sort_cmp');
		$participants = edufeedrGetCourseParticipants($guid);
		uasort($participants, '__edufeedr_users_sort_cmp');

		$content = "\"First name\";\"Last name\";\"E-mail\";\"Blog\"";
		$assignments_part = "";
		if (is_array($assignments) && sizeof($assignments)>0) {
			foreach ($assignments as $assignment) {
				$content .= ";".addslashes($assignment->title);
				$assignemnts_part .= ";";
			}
		}
		$content .= $line_separator;

		if (is_array($participants) && sizeof($participants)>0) {
			foreach ($participants as $participant) {
				$content .= "\"".addslashes($participant->firstname)."\"";
				$content .= ";\"".addslashes($participant->lastname)."\"";
				$content .= ";\"".addslashes($participant->email)."\"";
				$content .= ";\"".addslashes($participant->blog)."\"";
				$content .= $assignemnts_part;
				$content .= $line_separator;
			}
		}

		$splitString = str_split($content, 8192);
		foreach ($splitString as $chunk) {
			echo $chunk;
		}
		exit;
	} else {
		/*translation:Download failed.*/
		register_error(elgg_echo('edufeedr:error:download:failed'));
	}

?>
