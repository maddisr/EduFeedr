<?php

    if (isset($vars['entity']) && $vars['entity']->getSubtype() == 'educourse') {

		if (isset($vars['assignment'])) {
			$action = 'edufeedr/edit_assignment';
			$assignment = $vars['assignment'];
			$title = $assignment->title;
			$blog_post_url = $assignment->blog_post_url;
			$deadline = $assignment->deadline;
		} else {
            $action = 'edufeedr/add_assignment';
		    $title = '';
		    $blog_post_url = 'http://';
		    $deadline = '';
		}

		if (isset($vars['assignment_title']) && !empty($vars['assignment_title']))
			$title = $vars['assignment_title'];
		if (isset($vars['assignment_url']) && !empty($vars['assignment_url']))
			$blog_post_url = $vars['assignment_url'];
		if (isset($vars['assignment_deadline']) && !empty($vars['assignment_deadline']))
			$deadline = $vars['assignment_deadline'];

		// Labels and inputs
		$title_label = elgg_echo('title');
		$title_input = elgg_view('input/text', array('internalname' => 'assignment_title', 'value' => $title));

		/*translation:Blog post*/
		$blogposturl_label = elgg_echo('edufeedr:label:blog:post_url');
		$blogposturl_input = elgg_view('input/url', array('internalname' => 'assignment_url', 'value' => $blog_post_url));

		/*translation:Deadline*/
		$deadline_label = elgg_echo('edufeedr:label:deadline');
		$deadline_input = elgg_view('input/edufeedr_calendar', array('internalname' => 'assignment_deadline', 'value' => $deadline));

		$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('save')));
        // Cancel button
        $cancel_href = $vars['url'] . 'pg/edufeedr/view_educourse/' . $vars['entity']->getGUID() . '?filter=assignments';
        $cancel_input = elgg_view('input/edufeedr_cancel', array('value' => elgg_echo('cancel'), 'href' => $cancel_href));

		$entity_hidden = elgg_view('input/hidden', array('internalname' => 'educourse', 'value' => $vars['entity']->getGUID()));
		if (isset($vars['assignment']))
			$entity_hidden .= elgg_view('input/hidden', array('internalname' => 'assignment_id', 'value' => $vars['assignment']->id));
		$field_required = elgg_view('input/edufeedr_required');

		$title_include = "";
		if ($action == 'edufeedr/edit_assignment') {
			$title_include .= '<p>';
			$title_include .= '<label>' . $title_label . '</label><br />';
			$title_include .= $title_input;
			$title_include .= '</p>';
		}

		$form_body = <<<EOT
        $title_include
        <p>
            <label>$blogposturl_label</label>$field_required<br />
			$blogposturl_input
	    </p>
		<p>
            <label>$deadline_label</label>$field_required<br />
            $deadline_input
		</p>
		<p>
			$entity_hidden
            $submit_input
            $cancel_input
        </p>
EOT;

		echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body));
	}

?>
