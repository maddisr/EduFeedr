<?php

    //Gatekeeper
    gatekeeper();
    action_gatekeeper();
    global $CONFIG;

	$id = (int) get_input('post_id');
	$guid = (int) get_input('educourse');

	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $id) {
		$es = new EduSuckr();
		$result = $es->unhidePostById($id);

		if ($result) {
			/*translation:Post restored.*/
			system_message(elgg_echo('edufeedr:message:post_unhidden'));
		} else {
			/*translation:Post could not be restored.*/
			register_error(elgg_echo('edufeedr:error:post_not_unhidden'));
		}
		forward($CONFIG->wwwroot . 'pg/edufeedr/view_hidden/' . $educourse->getGUID() . '/posts');
	}

	forward('pg/edufeedr/');
?>
