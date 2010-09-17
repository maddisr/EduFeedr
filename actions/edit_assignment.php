<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

    // Get input data
	$guid = (int) get_input('educourse');
	$assignment_id = (int) get_input('assignment_id');
	$title = mysql_real_escape_string(get_input('assignment_title'));
	$blog_post_url = get_input('assignment_url');
	$deadline = get_input('assignment_deadline');

	$educourse = get_entity($guid);

	$current_assignment = edufeedrGetSingleAssignment($guid, $assignment_id);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $current_assignment) {

		// Cache variables
		$_SESSION['assignment_title'] = $title;
		$_SESSION['assignment_url'] = $blog_post_url;
		$_SESSION['assignment_deadline'] = $deadline;

		// Make sure all required data is provided
		if (empty($title) || empty($blog_post_url) || empty($deadline)) {
			/*translation:Please fill all required fields.*/
			register_error(elgg_echo('edufeedr:error:blank:fields'));
			forward('pg/edufeedr/edit_assignment/' . $guid . '/' . $assignment_id );
		} else {
			$blog_data = edufeedrGetAssignmentData($blog_post_url);
			if (!$blog_data || !$blog_data['state']) {
				register_error($blog_data['message']);
				forward('pg/edufeedr/edit_assignment/' . $guid . '/' . $assignment_id );
			}

			$title = $title;
			$description = mysql_real_escape_string($blog_data['data']['description']);
			$deadline = edufeedrDateIntoTimestamp($deadline);

			$assignment_update = update_data("UPDATE {$CONFIG->dbprefix}edufeedr_course_assignments SET title = '$title', description = '$description', blog_post_url = '$blog_post_url', deadline = '$deadline', modified = NOW() WHERE course_guid = $guid and id = $assignment_id");

			if ($assignment_update) {
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
				/*translation:Assignment changed.*/
				system_message(elgg_echo('edufeedr:message:assignment:changed'));
			} else {
				/*translation:Assignment could nto be changed.*/
				register_error(elgg_echo('edufeedr:error:assignment:could:not:be:changed'));
			}

			// Clear cache
			unset($_SESSION['assignment_title']);
			unset($_SESSION['assignment_url']);
			unset($_SESSION['assignment_deadline']);

			forward($vars['url'] . 'pg/edufeedr/view_educourse/' . $educourse->getGUID() . '?filter=assignments');
		}
		forward('pg/edufeedr');
	}

?>
