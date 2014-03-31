<?php

    //Gatekeeper
    gatekeeper();
    action_gatekeeper();

	$course_guid = (int) get_input('course_guid');
	$post_id = (int) get_input('post_id');
	$assignment_id = (int) get_input('assignment_id');

	$educourse = get_entity($course_guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $post_id && $assignment_id) {
		$es = new EduSuckr();
		$result = $es->connectPostWithAssignment($course_guid, $post_id, $assignment_id);

		if ($result) {
			/*translation:Post connected.*/
			system_message(elgg_echo('edufeedr:message:post_connected'));
		} else {
			/*translation:Post could not be connected.*/
			register_error(elgg_echo('edufeedr:error:post_not_connected'));
		}
		// XXX WRONG URL, SHOULD BE POST
		forward("pg/edufeedr/view_post/$course_guid/$post_id");
	}

	forward('pg/edufeedr/');
?>
