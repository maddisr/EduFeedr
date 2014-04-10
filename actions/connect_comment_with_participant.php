<?php

    //Gatekeeper
    gatekeeper();
    action_gatekeeper();
	
	$course_guid = (int) get_input('course_guid');
	$comment_id = (int) get_input('comment_id');
	$participant_id = (int) get_input('participant_id');
	$post_id = (int) get_input('post_id');
	
	$educourse = get_entity($course_guid);
	
	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $comment_id && $participant_id) {
		$es = new EduSuckr();
		$result = $es->connectCommentWithParticipant($course_guid, $comment_id, $participant_id);

		if ($result) {
			/*translation:Comment connected.*/
			system_message(elgg_echo('edufeedr:message:comment_connected'));
		} else {
			/*translation:Comment could not be connected.*/
			register_error(elgg_echo('edufeedr:error:post_not_connected'));
		}
		// XXX WRONG URL, SHOULD BE POST
		forward("pg/edufeedr/view_post/$course_guid/$post_id");
		//DOTO POST_ID
	}

	forward('pg/edufeedr/');
?>