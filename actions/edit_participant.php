<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

    // Get input data
	$guid = (int) get_input('educourse');
	$participant_id = (int) get_input('participant_id');
	$firstname = get_input('join_firstname');
	$lastname = get_input('join_lastname');
	$email = get_input('join_email');
	$blog = get_input('join_blog');
	$blogger = get_input('join_blogger');
	$blogger_id = 0;

	$educourse = get_entity($guid);

	$current_participant = edufeedrGetSingleParticipant($guid, $participant_id);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanEditEducourse($educourse) && $current_participant) {

		// Cache variables
		$_SESSION['join_firstname'] = $firstname;
		$_SESSION['join_lastname'] = $lastname;
		$_SESSION['join_email'] = $email;
		$_SESSION['join_blog'] = $blog;
		$_SESSION['join_blogger'] = $blogger;

		// Make sure all required data is provided
		if (empty($firstname) || empty($lastname) || empty($email) || empty($blog)) {
			/*translation:Please fill all required fields.*/
			register_error(elgg_echo('edufeedr:error:blank:fields'));
			forward('pg/edufeedr/edit_participant/' . $guid . '/' . $participant_id );
		} else {
            // See if blog needs fixing
            $fixed_blog = edufeedrFixIncorrectBlogAddress($blog);
            if ($blog != $fixed_blog) {
                $blog = $fixed_blog;
                /*translation:Blog address was corrected.*/
                system_message(elgg_echo('edufeedr:message:blog_address_corrected'));
            }
			$feeds = edufeedrGetBlogFeeds($blog);
			if (!($feeds || is_array($feeds))) {
				/*translation:Provided url was not a blog or your blog engine is not supported.*/
				register_error(elgg_echo('edufeedr:error:engine:not:supported'));
				forward('pg/edufeedr/edit_participant/' . $guid . '/' . $participant_id );
			}
			if ($blogger) {
			    preg_match('@^(?:http://www.blogger.com/profile/)?([^/]+)@i', $blogger, $matches);
			    if (count($matches)>1 && is_numeric($matches[1]) && strlen($matches[1])==20) {
			        $blogger_id = $matches[1];
			        if (strlen($blogger)<=20)
			            $blogger = 'http://www.blogger.com/profile/'.$blogger_id;
			    } else {
				    /*translation:This url was not a blogger profile or you made mistakes when typing.*/
				    register_error(elgg_echo('edufeedr:error:url:not:blogger:profile'));
				    forward('pg/edufeedr/edit_participant/' . $guid . '/' . $participant_id );
			    }
			}

			// Update participant
			$posts = $feeds['posts'];
			$comments = $feeds['comments'];
            $blog_base = $feeds['blog_base'];
			$participant_update = update_data("UPDATE {$CONFIG->dbprefix}edufeedr_course_participants SET firstname = '$firstname', lastname = '$lastname', email = '$email', blog = '$blog', blog_base = '$blog_base', posts = '$posts', comments = '$comments', blogger = '$blogger', modified = NOW() WHERE course_guid = $guid and id = $participant_id");

			if ($participant_update) {
				/*translation:Participant information changed.*/
				system_message(elgg_echo('edufeedr:message:participant:changed'));
				$es = new EduSuckr;
                $participant_data = array(
	                'participant_guid' => $participant_id,
		            'course_guid' => $guid,
		            'firstname' => $firstname,
		            'lastname' => $lastname,
		            'email' => $email,
		 	        'blog' => $blog,
                    'blog_base' => $blog_base,
		 	        'posts' => $posts,
		 	        'comments' => $comments,
		 	        'blogger_id' => $blogger_id,
		 	        'status' => 'active'
	            );
                $es_result = $es->addParticipant($participant_data);
                if (!$es_result) {
                    /*translation:Error occured, object could not be sent to EduSuckr.*/
                    register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
                }
			} else {
				/*translation:Participant information could not be changed.*/
				register_error(elgg_echo('edufeedr:error:participant:could:not:be:changed'));
			}

			// Clear cache
			unset($_SESSION['join_firstname']);
			unset($_SESSION['join_lastname']);
			unset($_SESSION['join_email']);
			unset($_SESSION['join_blog']);
			unset($_SESSION['join_blogger']);

			forward($educourse->getURL());
		}
		forward('pg/edufeedr');
	}

?>
