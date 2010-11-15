<?php

    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');
    global $CONFIG;

    // No gatekeeper, this page is public

    /*translation:All courses*/
    $title = elgg_view_title(elgg_echo('edufeedr:title:all:courses'));

    $content = "";

	$offset = get_input('offset', 0);
	$limit = 10;

	$entities_count = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'educourse', 'offset' => $offset, 'order_by_metadata' => array('name' => 'course_starting_date', 'direction' => 'DESC', 'as' => 'integer'), 'count' => 'true'));
	$entities = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'educourse', 'offset' => $offset, 'order_by_metadata' => array('name' => 'course_starting_date', 'direction' => 'DESC', 'as' => 'integer')));

	$content .= elgg_view_entity_list($entities, $entities_count, $offset, $limit, false, false);

	global $autofeed;
	$autofeed = FALSE;
    $content = elgg_view_layout('two_column_left_sidebar', '', $title . $content);
    page_draw(null, $content);
?>
