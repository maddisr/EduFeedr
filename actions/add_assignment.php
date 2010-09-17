<?php

    // Gatekeeper
    action_gatekeeper();

    // Get input data
    $guid = (int) get_input('educourse');
	//$title = get_input('assignment_title');
	$blog_post_url = get_input('assignment_url');
	$deadline = get_input('assignment_deadline');

	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse)) {

		// Cache variables
		//$_SESSION['assignment_title'] = $title;
		$_SESSION['assignment_url'] = $blog_post_url;
		$_SESSION['assignment_deadline'] = $deadline;

		// Make sure all required data is provided
		if (empty($blog_post_url) || empty($deadline) || (trim($blog_post_url) == 'http://')) {
			/*translation:Please fill all required fields.*/
			register_error(elgg_echo('edufeedr:error:blank:fields'));
			forward('pg/edufeedr/add_assignment/' . $guid);
		} else {
			// See if provided blog is acceptable
			$blog_data = edufeedrGetAssignmentData($blog_post_url);
			if (!$blog_data || !$blog_data['state']) {
				register_error($blog_data['message']);
				forward('pg/edufeedr/add_assignment/' . $guid);
			}

			$title = mysql_real_escape_string($blog_data['data']['title']);
			$description = mysql_real_escape_string($blog_data['data']['description']);
			$deadline = edufeedrDateIntoTimestamp($deadline);

			$assignment_id = insert_data("INSERT INTO {$CONFIG->dbprefix}edufeedr_course_assignments (course_guid, title, description, blog_post_url, deadline) VALUES ($guid, '$title', '$description', '$blog_post_url', '$deadline')");

			if ($assignment_id) {
			    $es = new EduSuckr;
                $es_data = array(
                     "assignment_id"=>$assignment_id,
                     "course_guid"=>$guid,
                     "title"=>$title,
                     "description"=>$description,
                     "blog_post_url"=>$blog_post_url,
                     "deadline"=>$deadline
                );
                if (!$es->setAssignment($es_data)) {
                    /*translation:Error occured, object could not be sent to EduSuckr.*/
                    register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
                }
				/*translation:Assignment added to course %s*/
				system_message(sprintf(elgg_echo('edufeedr:message:assignment:added'), $educourse->title));
			} else {
				/*translation:Assignment could not be added to course %s*/
				register_error(sprintf(elgg_echo('edufeedr:error:assignment:could:not:be:added'), $educourse->title));
			}

			// Clear cache
			//unset($_SESSION['assignment_title']);
			unset($_SESSION['assignment_url']);
			unset($_SESSION['assignment_deadline']);

			forward($vars['url'] . 'pg/edufeedr/view_educourse/' . $educourse->getGUID() . '?filter=assignments');
		}
		forward('pg/edufeedr');
	}

?>
