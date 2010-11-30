<?php

    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');
    global $CONFIG;

    // No gatekeeper, this page is public

    /*translation:All courses*/
    $title = elgg_view_title(elgg_echo('edufeedr:title:all:courses'));

    $content = "";

	$offset = (int) get_input('offset', 0);
	$limit = 10;

    // Database timestamps are 00.00.00, compensate asif the time was 23.59.59
    $modified_time = time() - 86399;
    $entities_count = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'educourse', 'metadata_name_value_pairs' => array(
        array('name' => 'course_starting_date', 'value' => time(), 'operand' => '<', 'case_sensitive' => 'false'),
        array('name' => 'course_ending_date', 'value' => $modified_time, 'operand' => '>', 'case_sensitive' => 'false')
	), 'count' => 'true',
	'wheres' => array("(SELECT COUNT(*) FROM {$CONFIG->dbprefix}edufeedr_course_participants pts WHERE e.guid = pts.course_guid) > 0", "(SELECT COUNT(*) FROM {$CONFIG->dbprefix}edufeedr_course_assignments cas WHERE e.guid = cas.course_guid) > 0")
    ));
    $entities = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'educourse', 'offset' => $offset, 'metadata_name_value_pairs' => array(
        array('name' => 'course_starting_date', 'value' => time(), 'operand' => '<', 'case_sensitive' => 'false'),
        array('name' => 'course_ending_date', 'value' => $modified_time, 'operand' => '>', 'case_sensitive' => 'false')
	), 'order_by_metadata' => array('name' => 'course_starting_date', 'direction' => 'DESC', 'as' => 'integer'),
		'wheres' => array("(SELECT COUNT(*) FROM {$CONFIG->dbprefix}edufeedr_course_participants pts WHERE e.guid = pts.course_guid) > 0", "(SELECT COUNT(*) FROM {$CONFIG->dbprefix}edufeedr_course_assignments cas WHERE e.guid = cas.course_guid) > 0")
		));
    unset($modified_time);

	$content .= elgg_view_entity_list($entities, $entities_count, $offset, $limit, false, false);

	global $autofeed;
	$autofeed = FALSE;
    $content = elgg_view_layout('two_column_left_sidebar', '', $title . $content);
    page_draw(null, $content);
?>
