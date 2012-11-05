<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

    // Get input data
    $guid = (int) get_input('educourse');
    $title = get_input('course_title');
    $description = get_input('course_description');
    $course_tag = get_input('course_tag');
    $course_wiki = get_input('course_wiki');
    $signup_deadline = get_input('signup_deadline');
    $course_starting_date = get_input('course_starting_date');
	$course_ending_date = get_input('course_ending_date');
	$start_aggregate = get_input('start_aggregate');
	$stop_aggregate = get_input('stop_aggregate');

    $educourse = get_entity($guid);
    if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse)) {

        // Cache variables
        $_SESSION['educourse_title'] = $title;
        $_SESSION['educourse_description'] = $description;
        $_SESSION['educourse_course_tag'] = $course_tag;
        $_SESSION['educourse_course_wiki'] = $course_wiki;
        $_SESSION['educourse_signup_deadline'] = $signup_deadline;
        $_SESSION['educourse_course_starting_date'] = $course_starting_date;
		$_SESSION['educourse_course_ending_date'] = $course_ending_date;
		$_SESSION['start_aggregate'] = $start_aggregate;
		$_SESSION['stop_aggregate'] = $stop_aggregate;

        // Make sure all required data is provided
        if (empty($title) || empty($description) || empty($course_tag) || empty($signup_deadline) || empty($course_starting_date) || empty($course_ending_date) || empty($start_aggregate) || empty($stop_aggregate)) {
            /*translation:Please fill all required fields.*/
            register_error(elgg_echo('edufeedr:error:blank:fields'));
	    forward('pg/edufeedr/edit_educourse/' . $guid);
        } else {
			// Additional condition to check aggregating start and end
			if ((edufeedrDateIntoTimestamp($course_starting_date) < edufeedrDateIntoTimestamp($start_aggregate)) || (edufeedrDateIntoTimestamp($course_ending_date) > edufeedrDateIntoTimestamp($stop_aggregate))) {
				/*translation: Please check aggregation start and end dates. Aggregation can not start after the course starts and it can not end before the course ends.*/
				register_error(elgg_echo('edufeedr:error:check:aggregation_dates'));	
				forward('pg/edufeedr/edit_educourse/' . $guid);
			}
        $owner = $educourse->getOwnerEntity();
	    $educourse->access_id = 2; // public
	    $educourse->title = $title;
	    $educourse->description = $description;
	    if (!$educourse->save()) {
                /*translation:Error occured, object could not be saved.*/
                register_error(elgg_echo('edufeedr:error:not:saved'));
                forward('pg/edufeedr/edit_educourse/' . $guid);
	    }
	    $educourse->clearMetadata('course_tag');
	    $educourse->course_tag = $course_tag;
	    $educourse->clearMetadata('course_wiki');
	    $educourse->course_wiki = $course_wiki;
	    $educourse->clearMetadata('signup_deadline');
	    $educourse->signup_deadline = edufeedrDateIntoTimestamp($signup_deadline);
	    $educourse->clearMetadata('course_starting_date');
	    $educourse->course_starting_date = edufeedrDateIntoTimestamp($course_starting_date);
	    $educourse->clearMetadata('course_ending_date');
		$educourse->course_ending_date = edufeedrDateIntoTimestamp($course_ending_date);
		$educourse->clearMetadata('start_aggregate');
		$educourse->start_aggregate = edufeedrDateIntoTimestamp($start_aggregate);
		$educourse->clearMetadata('stop_aggregate');
		$educourse->stop_aggregate = edufeedrDateIntoTimestamp($stop_aggregate);

        $es = new EduSuckr;
        // Please note that course_blog address remains the same
        // Probably should also change the EduSuckr component in the future
        $pc = edufeedrGetBlogFeeds($educourse->course_blog);
        $es_data = array("course_guid"=>$educourse->getGUID(),
                         "title"=>$title,
                         "description"=>$description,
                         "posts"=>$pc['posts'],
                         "comments"=>$pc['comments'],
                         "course_tag"=>$course_tag,
                         "course_blog"=>$educourse->course_blog,
                         "course_wiki"=>$course_wiki,
                         "signup_deadline"=>edufeedrDateIntoTimestamp($signup_deadline),
                         "course_starting_date"=>edufeedrDateIntoTimestamp($course_starting_date),
						 "course_ending_date"=>edufeedrDateIntoTimestamp($course_ending_date),
						 "start_agregate"=>edufeedrDateIntoTimestamp($start_aggregate),
						 "stop_agregate"=>edufeedrDateIntoTimestamp($stop_aggregate)
                     );
        if (!$es->setEduCourse($es_data)) {
            /*translation:Error occured, object could not be sent to EduSuckr.*/
            register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
        }

	/*translation:Course modified*/
        system_message(elgg_echo('edufeedr:message:educourse:modified'));

	// Clear cache
	unset($_SESSION['educourse_title']);
	unset($_SESSION['educourse_description']);
	unset($_SESSION['educourse_course_tag']);
	unset($_SESSION['educourse_course_wiki']);
	unset($_SESSION['educourse_signup_deadline']);
	unset($_SESSION['educourse_course_starting_date']);
	unset($_SESSION['educourse_course_ending_date']);
	unset($_SESSION['start_aggregate']);
	unset($_SESSION['stop_aggregate']);

	forward($educourse->getURL());
    }

    forward('pg/edufeedr');
    }
?>
