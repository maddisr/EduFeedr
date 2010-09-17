<?php

    // Load engine
    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');

    // Gatekeeper
    gatekeeper();

    $guid = (int) get_input('educourse');
    $entity = get_entity($guid);
	if ($entity->getSubtype() == 'educourse' && edufeedrCanEditEducourse($entity)) {

        // Menu
        $menu = '';

        // Content
        $body = '<div class="eduwrapper">';
        // Add title
        /*translation:Add assignment*/
        $body .= elgg_view_title(elgg_echo('edufeedr:title:add_assignment'));

	    $body .= '<h3 class="edufeedr_action_header">'.$entity->title.'</h3>';
        // Get form contents
        $body .= elgg_view('edufeedr/forms/edit_assignment', array('entity' => $entity));
        $body .= '</div>';

        $content = elgg_view_layout('two_column_left_sidebar', $menu, $body);

	    page_draw(null, $content);
	}
?>
