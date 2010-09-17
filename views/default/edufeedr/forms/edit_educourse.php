<?php

    if (isset($vars['entity'])) {
        $action = 'edufeedr/edit_educourse';
	$title = $vars['entity']->title;
	$description = $vars['entity']->description;
	$course_tag = $vars['entity']->course_tag;
	$course_blog = $vars['entity']->course_blog;
	$course_wiki = $vars['entity']->course_wiki;
	$signup_deadline = $vars['entity']->signup_deadline;
	$course_starting_date = $vars['entity']->course_starting_date;
	$course_ending_date = $vars['entity']->course_ending_date;
	$start_aggregate = $vars['entity']->start_aggregate;
	$stop_aggregate = $vars['entity']->stop_aggregate;
    } else {
        $action = 'edufeedr/add_educourse';
	$title = '';
	$description = '';
	$course_tag = '';
	$course_blog = 'http://'; // prefill for URL
	$course_wiki = 'http://'; // prefill for URL
	$signup_deadline = '';
	$course_starting_date = '';
	$course_ending_date = '';
    }

    // Get cached values, if there are any
    if (isset($vars['educourse_title']) && !empty($vars['educourse_title']))
        $title = $vars['educourse_title'];
    if (isset($vars['educourse_description']) && !empty($vars['educourse_description']))
        $description = $vars['educourse_description'];
    if (isset($vars['educourse_course_tag']) && !empty($vars['educourse_course_tag']))
        $course_tag = $vars['educourse_course_tag'];
    if (isset($vars['educourse_course_blog']) && !empty($vars['educourse_course_blog']))
        $course_blog = $vars['educourse_course_blog'];
    if (isset($vars['educourse_course_wiki']) && !empty($vars['educourse_course_wiki']))
        $course_wiki = $vars['educourse_course_wiki'];
    if (isset($vars['educourse_signup_deadline']) && !empty($vars['educourse_signup_deadline']))
        $signup_deadline = $vars['educourse_signup_deadline'];
    if (isset($vars['educourse_course_starting_date']) && !empty($vars['educourse_course_starting_date']))
        $course_starting_date = $vars['educourse_course_starting_date'];
    if (isset($vars['educourse_course_ending_date']) && !empty($vars['educourse_course_ending_date']))
		$course_ending_date = $vars['educourse_course_ending_date'];
	// Aggregation only available for already created courses
	if (isset($vars['entity'])) {
	    if (isset($vars['start_aggregate']) && !empty($vars['start_aggregate']))
		    $start_aggregate = $vars['start_aggregate'];
	    if (isset($vars['stop_aggregate']) && !empty($vars['stop_aggregate']))
		    $stop_aggregate = $vars['stop_aggregate'];
	}


	$field_required = elgg_view('input/edufeedr_required');
    // Labels and inputs
    $title_label = elgg_echo('title');
    $title_input = elgg_view('input/text', array('internalname' => 'course_title', 'value' => $title));

    $description_label = elgg_echo('description');
    $description_input = elgg_view('input/plaintext', array('internalname' => 'course_description', 'value' => $description));

    /*translation:Course tag*/
    $course_tag_label = elgg_echo('edufeedr:label:course_tag');
    $course_tag_input = elgg_view('input/text', array('internalname' => 'course_tag', 'value' => $course_tag));

    /*translation:Course blog*/
    $course_blog_label = elgg_echo('edufeedr:label:course:blog_url');
    $course_blog_input = elgg_view('input/url', array('internalname' => 'course_blog', 'value' => $course_blog));

    /*translation:Course website or wiki*/
    $course_wiki_label = elgg_echo('edufeedr:label:course:wiki_url');
    $course_wiki_input = elgg_view('input/url', array('internalname' => 'course_wiki', 'value' => $course_wiki));

    /*translation:Enrollment deadline*/
    $signup_deadline_label = elgg_echo('edufeedr:label:enrollment_deadline');
    $signup_deadline_input = elgg_view('input/edufeedr_calendar', array('internalname' => 'signup_deadline', 'value' => $signup_deadline));

    /*translation:Starting date*/
    $course_starting_date_label = elgg_echo('edufeedr:label:educourse_starting_date');
    $course_starting_date_input = elgg_view('input/edufeedr_calendar', array('internalname' => 'course_starting_date', 'value' => $course_starting_date));

    /*translation:Ending date*/
    $course_ending_date_label = elgg_echo('edufeedr:label:educourse_ending_date');
	$course_ending_date_input = elgg_view('input/edufeedr_calendar', array('internalname' => 'course_ending_date', 'value' => $course_ending_date));

	if (isset($vars['entity'])) {
		/*translation:Start aggregating blog posts from*/
		$course_start_aggregate_label = elgg_echo('edufeedr:label:educourse_start_aggregate');
		$course_start_aggregate_input = elgg_view('input/edufeedr_calendar', array('internalname' => 'start_aggregate', 'value' => $start_aggregate));

		/*translation:End aggeregating blog posts on*/
		$course_stop_aggregate_label = elgg_echo('edufeedr:label:educourse_stop_aggregate');
		$course_stop_aggregate_input = elgg_view('input/edufeedr_calendar', array('internalname' => 'stop_aggregate', 'value' => $stop_aggregate));

		$course_facilitators_input = elgg_view('edufeedr/educourse_facilitators', array('entity' => $vars['entity'], 'type' => 'edit'));

		$form_aggregate_edit_addition = <<<EOT
		<p>
			<label>$course_start_aggregate_label</label>$field_required<br />
            $course_start_aggregate_input
		</p>
        <p>
			<label>$course_stop_aggregate_label</label>$field_required<br />
            $course_stop_aggregate_input
		</p>
            $course_facilitators_input
        
EOT;
	} else {
		$form_aggregate_edit_addition = "";
	}


	$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('save')));
	// Cancel button
	$cancel_href = $vars['url'] . 'pg/edufeedr/index';
	if (isset($vars['entity'])) {
		$cancel_href = $vars['url'] . 'pg/edufeedr/view_educourse/' . $vars['entity']->getGUID();
	}
	$cancel_input = elgg_view('input/edufeedr_cancel', array('value' => elgg_echo('cancel'), 'href' => $cancel_href));

    if(isset($vars['entity'])) {
        $entity_hidden = elgg_view('input/hidden', array('internalname' => 'educourse', 'value' => $vars['entity']->getGUID()));
    } else {
        $entity_hidden = '';
	}

    $form_body = <<<EOT
    <p>
        <label>$title_label</label>$field_required<br />
        $title_input
    </p>
    <p>
        <label>$description_label</label>$field_required<br />
        $description_input
    </p>
    <p>
        <label>$course_tag_label</label>$field_required<br />
        $course_tag_input
    </p>
    <p>
	<label>$course_blog_label</label>$field_required<br />
        $course_blog_input
    </p>
    <p>
        <label>$course_wiki_label</label><br />
        $course_wiki_input
    </p>
    <p>
	<label>$signup_deadline_label</label>$field_required<br />
        $signup_deadline_input
    </p>
    <p>
        <label>$course_starting_date_label</label>$field_required<br />
        $course_starting_date_input
    </p>
    <p>
        <label>$course_ending_date_label</label>$field_required<br />
        $course_ending_date_input
	</p>
    $form_aggregate_edit_addition
    <p>
        $entity_hidden
		$submit_input
		$cancel_input
    </p>
EOT;

    echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body));
?>
