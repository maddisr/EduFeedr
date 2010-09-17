<?php

    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');
    global $CONFIG;

	// No gatekeeper, this page is public

    /*translation:Ended courses*/
    $title = elgg_view_title(elgg_echo('edufeedr:title:ended:courses'));

    $content = "";

	$offset = get_input('offset', 0);
	$entities_count = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'educourse', 'metadata_name_value_pairs' => array(array('name' => 'course_ending_date', 'value' => time(), 'operand' => '<', 'case_sensitive' => 'false')), 'count' => 'true'));
	$entities = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'educourse', 'offset' => $offset, 'metadata_name_value_pairs' => array(array('name' => 'course_ending_date', 'value' => time(), 'operand' => '<', 'case_sensitive' => 'false')), 'order_by_metadata' => array('name' => 'course_starting_date', 'direction' => 'DESC', 'as' => 'integer')));
    $content .= elgg_view_entity_list($entities, $entities_count, $offset, false, false, true);

	global $autofeed;
	$autofeed = FALSE;
    $content = elgg_view_layout('two_column_left_sidebar', '', $title . $content);
    page_draw(null, $content);
?>
