<?php

    // Load engine
    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');

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

	$body .= '<div class="contentWrapper">';
	$body .= '<div id="elgg_horizontal_tabbed_nav">';
	$body .= '<ul>';
    if ($filter == 'course') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
    /*translation:Course feed*/
    $body .= '<li ' . $tabclass. '><a href="?filter=course">' . elgg_echo('edufeedr:tab:course:feed') . '</a></li>';
    if ($filter == 'courseinfo') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
	/*translation:Course info*/
	$body .= '<li ' . $tabclass. '><a href="?filter=courseinfo">' . elgg_echo('edufeedr:tab:courseinfo') . '</a></li>';
	/*translation:Participants*/
	if ($filter == 'participants') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
	/*translation:Participants*/
	$body .= '<li ' . $tabclass. '><a href="?filter=participants">' . elgg_echo('edufeedr:tab:participants') . '</a></li>';
	if ($filter == 'assignments') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
	/*translation:Assignments*/
	$body .= '<li ' . $tabclass. '><a href="?filter=assignments">' . elgg_echo('edufeedr:tab:assignments') . '</a></li>';
	if ($filter == 'progress') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
	/*translation:Progress*/
    $body .= '<li ' . $tabclass. '><a href="?filter=progress">' . elgg_echo('edufeedr:tab:progress') . '</a></li>'; 
	if ($filter == 'connections') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
	/*translation:Social network*/
    $body .= '<li ' . $tabclass. '><a href="?filter=connections">' . elgg_echo('edufeedr:tab:social:network') . '</a></li>';
	$body .= '</ul>';
	$body .= '</div>';
	$body .= '</div>';

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
