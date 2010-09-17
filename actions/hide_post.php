<?php

    //Gatekeeper
    gatekeeper();
    action_gatekeeper();

	$id = (int) get_input('post_id');
	$guid = (int) get_input('educourse');

	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $id) {
		$es = new EduSuckr();
		$result = $es->hidePostById($id);

		if ($result) {
			/*translation:Post hidden.*/
			system_message(elgg_echo('edufeedr:message:post_hidden'));
		} else {
			/*translation:Post could not be hidden.*/
			register_error(elgg_echo('edufeedr:error:post_not_hidden'));
		}
		forward($educourse->getURL());
	}

	forward('pg/edufeedr/');
?>
