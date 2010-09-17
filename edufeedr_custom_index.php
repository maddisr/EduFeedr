<?php
/**
 * Start the Elgg engine
 */
define('externalpage',true);
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

    $login_box = "";
    if (!isloggedin()) {
		$login_box = elgg_view("account/forms/login");
	}

    //Load the front page
    /*translation:Courses*/
	$title = elgg_view_title(elgg_echo('edufeedr:index:educourses'));
	set_context('search');
	$offset = (int)get_input('offset', 0);
	$content .= $title;
	// List all educourses for now
	$entities = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'educourse', 'offset' => $offset, 'limit' => 10, 'order_by_metadata' => array('name' => 'course_starting_date', 'direction' => 'DESC', 'as' => 'integer')));

	$content .= elgg_view_entity_list($entities, sizeof($entities), $offset, 10, FALSE, FALSE);
	set_context('main');
	global $autofeed;
	$autofeed = FALSE;
	$content = elgg_view_layout('two_column_left_sidebar', $login_box . '<div style="border-bottom:1px solid #CCC;margin-bottom:-10px;"></div>', $content);
	page_draw(null, $content);
