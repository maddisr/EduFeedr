<?php

    // Load engine
    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');
    
    $guid = (int) get_input('educourse');
    $entity = get_entity($guid);
	if ($entity->getSubtype() == 'educourse')
		$educourse = $entity;
	else
		$educourse = null;

    // Menu
    $menu = '';

    // Content
    $body = '<div class="eduwrapper">';
    // Add title
    /*translation:Enroll to the course*/
    $body .= elgg_view_title(elgg_echo('edufeedr:title:educourse_enroll'));

	$body .= '<h3 class="edufeedr_action_header">'.$educourse->title.'</h3>';
    // Get form contents
    $body .= elgg_view('edufeedr/forms/join_educourse', array('entity' => $educourse));
    $body .= '</div>';

    $content = elgg_view_layout('two_column_left_sidebar', $menu, $body);

    page_draw(null, $content);
?>
