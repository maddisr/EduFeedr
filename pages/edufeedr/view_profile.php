<?php

    $educourse_guid = (int) get_input('educourse');
    $participant_id = (int) get_input('participant_id');
    $menu = "";
    $content = "";

	if (($entity = get_entity($educourse_guid)) && ($participant = edufeedrGetSingleParticipant($educourse_guid, $participant_id))) {

		$title = $entity->title;
		$menu = "";

		$body = '<div class="eduwrapper">';

		// Tabs
		$filter = 'participants';// Only one thing is defaulted
		$body .= elgg_view('helpers/educourse_tabs', array('filter' => $filter, 'educourse_guid' => $educourse_guid));

		if ($filter == 'participants') {
			$body .= elgg_view('edufeedr/participant_profile', array(
				'entity' => $entity,
				'participant' => $participant
			));
		}
		$body .= '</div>';
		$content = elgg_view_layout('two_column_left_sidebar', $menu, $body);
	} else {
		/*translation:Course not found*/
		$cotent = elgg_view_layout('two_column_left_sidebar', $menu, elgg_view_title(elgg_echo('edufeedr:error:educourse:not:found')));
	}

	page_draw(null, $content);
?>
