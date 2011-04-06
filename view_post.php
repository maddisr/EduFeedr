<?php

    // Load engine
    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');

    $educourse_guid = (int) get_input('educourse');
    $post_id = (int) get_input('post_id');
    $menu = "";
    $content = "";

    if ($entity = get_entity($educourse_guid)) {

	$comments = $entity->getAnnotations('comments');
        
        set_page_owner($entity->getOwner());
        $page_owner = $entity->getOwnerEntity();

	$title = $entity->title;
	$menu = "";

	$body = '<div class="eduwrapper">';

	// Tabs
	$filter = 'course';// Only one thing is defaulted
    $body .= elgg_view('helpers/educourse_tabs', array('filter' => $filter, 'educourse_guid' => $educourse_guid));

    if ($filter == 'course') {
        $body .= elgg_view('edufeedr/educourse_post', array(
	    'entity' => $entity,
		'post_id' => $post_id
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
