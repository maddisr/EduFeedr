<?php

    if ($vars['full']) {
        echo elgg_view('export/entity', $vars);
    } else {
        $icon = elgg_view(
	    'graphics/icon', array(
                'entity' => $vars['entity'],
		'size' => 'small'
            )
		);
		// Icon to empty
		$icon = "";
        
	$title = $vars['entity']->title;

	$content = '<div class="edufeedr_educourse_listing">';

	$content .= '<div class="edufeedr_educourse_listing_title">';
	$content .= '<strong><a href="' . $vars['entity']->getURL() . '" title="' . $vars['entity']->title . '">' . $vars['entity']->title . '</a></strong>';
	$content .= '</div>';

	$content .= '<div class="edufeedr_date">';
	$content .= elgg_view('output/calendar', array('value' => $vars['entity']->course_starting_date));
	$content .= ' - ';
	$content .= elgg_view('output/calendar', array('value' => $vars['entity']->course_ending_date));
	$content .= '</div>';

	$content .= '<div class="edufeedr_educourse_listing_description">';
	$content .= elgg_view('output/longtext', array('value' => $vars['entity']->description));
	$content .= '</div>';

	/*
	$owner = $vars['entity']->getOwnerEntity();
	$content .= '<div class="edufeedr_educourse_listing_owner">';
	$content .= '<a href="' . $owner->getURL() . '" title="' . $owner->name . '">' . $owner->name . '</a>';
	$content .= '</div>';
    */

	$content .= '</div>';

	echo elgg_view_listing($icon, $content);
    }
?>
