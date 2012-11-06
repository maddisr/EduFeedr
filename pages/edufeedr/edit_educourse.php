<?php

    // Gatekeeper
    gatekeeper();

    // Get current page_owner
    $page_owner = page_owner_entity();
    if($page_owner === false || is_null($page_owner)) {
        $page_owner = $_SESSION['user'];
	set_page_owner($_SESSION['guid']);
    }

    // Get educourse if it exists
    $educourse = (int) get_input('educourse');

    if ($educourse = get_entity($educourse)) {
        
	if ($educourse->canEdit() && edufeedrCanEditEducourse($educourse)) {
            $body = '<div class="eduwrapper">';
            /*translation:Edit course info*/
			$body .= elgg_view_title(elgg_echo('edufeedr:title:edit_educourse'));
			$body .= '<h3 class="edufeedr_action_header">'.$educourse->title.'</h3>';
	    $body .= elgg_view('edufeedr/forms/edit_educourse', array('entity' => $educourse));
	    $body .= '</div>';
	    // Menu
	    $menu = "";
	    $content = elgg_view_layout('two_column_left_sidebar', $menu, $body);
	}
    }

    page_draw(null, $content);

?>
