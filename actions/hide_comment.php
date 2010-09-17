<?php

    //Gatekeeper
    gatekeeper();
    action_gatekeeper();

	$id = (int) get_input('comment_id');
	$guid = (int) get_input('educourse');

	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $id) {
		$es = new EduSuckr();
		$result = $es->hideCommentById($id);

		if ($result) {
			/*translation:Comment hidden.*/
			system_message(elgg_echo('edufeedr:message:comment_hidden'));
		} else {
			/*translation:Comment could not be hidden.*/
			register_error(elgg_echo('edufeedr:error:comment_not_hidden'));
		}
		forward($educourse->getURL());
	}

	forward('pg/edufeedr/');
?>
