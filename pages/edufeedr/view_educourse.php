<?php

    $educourse_guid = (int) get_input('educourse');
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
	$filter = get_input('filter');
	if ($filter != 'assignments' && $filter != 'progress' && $filter != 'course' && $filter != 'connections' && $filter != 'courseinfo' && $filter != 'participants')
		$filter = 'course';

	$body .= elgg_view('helpers/educourse_tabs', array('filter' => $filter, 'educourse_guid' => $educourse_guid));

	if ($filter == 'assignments') {
		$body .= elgg_view('edufeedr/educourse_assignments', array(
			'entity' => $entity,
			'entity_owner' => $page_owner
        ));
    } else if ($filter == 'course') {
        $body .= elgg_view('edufeedr/educourse_blog', array(
        'entity' => $entity,
        'entity_owner' => $entity_owner
        ));
	} else if ($filter == 'progress') {
		$body .= elgg_view('edufeedr/educourse_progress', array(
			'entity' => $entity,
			'entity_owner' => $entity_owner
		));
	} else if ($filter == 'connections') {
		$body .= elgg_view('edufeedr/educourse_connections', array(
			'entity' => $entity,
			'entity_owner' => $entity_owner
		));
	} else if ($filter == 'participants') {
		$body .= elgg_view('edufeedr/educourse_participants', array(
			'entity' => $entity,
			'entity_owner' => $entity_owner
		));
	} else {
	    $body .= elgg_view('object/educourse', array(
			'entity' => $entity,
	        'entity_owner' => $page_owner,
	        'comments' => $comments,
	        'full' => true
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
