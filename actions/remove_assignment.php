<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

	// Get input data
	$guid = (int) get_input('educourse');
	$assignment_number = (int) get_input('assignment_number');

	$educourse = get_entity($guid);

	$current_assignment = edufeedrGetSingleAssignment($guid, $assignment_number);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $current_assignment) {

		$assignment_deleted = delete_data("DELETE FROM {$CONFIG->dbprefix}edufeedr_course_assignments where id = $assignment_number");

		// Remove element from array
		if ($assignment_deleted) {
		     $es = new EduSuckr;
             if (!$es->removeAssignment($assignment_number)) {
                /*translation:Error occured, object could not be sent to EduSuckr.*/
                register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
            }
			/*translation:Assignment removed.*/
			system_message(elgg_echo('edufeedr:message:assignment:removed'));
			forward($vars['url'] . 'pg/edufeedr/view_educourse/' . $educourse->getGUID() . '?filter=assignments');
		} else {
			/*translation:Assignment not found.*/
			register_error(elgg_echo('edufeedr:error:assignment:not:found'));
			forward($vars['url'] . 'pg/edufeedr/view_educourse/' . $educourse->getGUID() . '?filter=assignments');
		}
	}

	forward('pg/edufeedr');
?>
