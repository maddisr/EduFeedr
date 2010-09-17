<?php

    // Load engine
    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');

    // Gatekeeper
    gatekeeper();
    
	$guid = (int) get_input('educourse');
	$participant_id = (int) get_input('participant_id');
	$entity = get_entity($guid);

	$participant = edufeedrGetSingleParticipant($guid, $participant_id);

	if ($entity->getSubtype() == 'educourse' && edufeedrCanEditEducourse($entity) && $participant) {

        // Menu
        $menu = '';

        // Content
        $body = '<div class="eduwrapper">';
        // Add title
		/*translation:Edit participant*/
		$body .= elgg_view_title(elgg_echo('edufeedr:title:edit:participant'));

		$body .= '<h3 class="edufeedr_action_header">'.$entity->title.'</h3>';
        // Get form contents
        $body .= elgg_view('edufeedr/forms/join_educourse', array('entity' => $entity, 'participant' => $participant, 'participant_id' => $participant_id));
        $body .= '</div>';

        $content = elgg_view_layout('two_column_left_sidebar', $menu, $body);

	    page_draw(null, $content);
	}
?>
