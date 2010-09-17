<?php

    // Gatekeeper
    gatekeeper();

	// Get input data
	$guid = (int) get_input('educourse');
	$participant_number = (int) get_input('participant_number');

	$educourse = get_entity($guid);

	$current_participant = edufeedrGetSingleParticipant($guid, $participant_number);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $current_participant) {

		$participant_deleted = delete_data("DELETE FROM {$CONFIG->dbprefix}edufeedr_course_participants WHERE id = $participant_number");

		// Remove element from array
		if ($participant_deleted) {
			/*translation:Participant removed.*/
			system_message(elgg_echo('edufeedr:message:participant:removed'));
			$es = new EduSuckr;
			$es_result = $es->removeParticipant($participant_number);
            if (!$es_result) {
                /*translation:Error occured, object could not be sent to EduSuckr.*/
                register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
            }
			forward($educourse->getURL());
		} else {
			/*translation:Participant not found.*/
			register_error(elgg_echo('edufeedr:error:participant:not:found'));
			forward($educourse->getURL());
		}
	}

	forward('pg/edufeedr');
?>
