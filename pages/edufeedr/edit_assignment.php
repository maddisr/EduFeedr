<?php

    // Gatekeeper
    gatekeeper();
    
	$guid = (int) get_input('educourse');
	$assignment_id = (int) get_input('assignment_id');
	$entity = get_entity($guid);

	$assignment = edufeedrGetSingleAssignment($guid, $assignment_id);

	if ($entity->getSubtype() == 'educourse' && edufeedrCanEditEducourse($entity) && $assignment) {

        // Menu
        $menu = '';

        // Content
        $body = '<div class="eduwrapper">';
        // Add title
        /*translation:Edit assignment*/
        $body .= elgg_view_title(elgg_echo('edufeedr:title:edit_assignment'));

		$body .= '<h3 class="edufeedr_action_header">'.$entity->title.'</h3>';
        // Get form contents
        $body .= elgg_view('edufeedr/forms/edit_assignment', array('entity' => $entity, 'assignment' => $assignment));
        $body .= '</div>';

        $content = elgg_view_layout('two_column_left_sidebar', $menu, $body);

	    page_draw(null, $content);
	}
?>
