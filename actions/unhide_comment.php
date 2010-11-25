<?php

    //Gatekeeper
    gatekeeper();
    action_gatekeeper();
    global $CONFIG;

	$id = (int) get_input('comment_id');
	$guid = (int) get_input('educourse');

	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $id) {
		$es = new EduSuckr();
		$result = $es->unhideCommentById(array($id, $guid));

		if ($result) {
			/*translation:Comment restored.*/
			system_message(elgg_echo('edufeedr:message:comment_unhidden'));
		} else {
			/*translation:Comment could not be restored.*/
			register_error(elgg_echo('edufeedr:error:comment_not_unhidden'));
		}
		forward($CONFIG->wwwroot . 'pg/edufeedr/view_hidden/' . $educourse->getGUID() . '/comments');
	}

	forward('pg/edufeedr/');
?>
