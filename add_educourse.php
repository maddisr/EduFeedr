<?php

    // Load engine
    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');
    
    // Gatekeeper
    gatekeeper();

    // Get current page_owner
    $page_owner = page_owner_entity();
    if ($page_owner === false || is_null($page_owner)) {
        $page_owner = $_SESSION['user'];
	set_page_owner($SESSION['guid']);
    }

    // Menu
    $menu = '';

    // Content
    $body = '<div class="eduwrapper">';
    // Add title
    /*translation:Add course*/
    $body .= elgg_view_title(elgg_echo('edufeedr:title:add_educourse'));

    // Get form contents
    $body .= elgg_view('edufeedr/forms/edit_educourse');
    $body .= '</div>';

    $content = elgg_view_layout('two_column_left_sidebar', $menu, $body);

    page_draw(null, $content);
?>
