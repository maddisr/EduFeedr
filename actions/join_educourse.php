<?php

    // Gatekeeper
    action_gatekeeper();

    // Get input data
    $guid = (int) get_input('educourse');
	$firstname = get_input('join_firstname');
	$lastname = get_input('join_lastname');
	$email = get_input('join_email');
	$blog = get_input('join_blog');

	$educourse = get_entity($guid);

	if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && (edufeedrIsEducourseOpen($educourse) || edufeedrCanEditEducourse($educourse))) {

		// Cache variables
		$_SESSION['join_firstname'] = $firstname;
		$_SESSION['join_lastname'] = $lastname;
		$_SESSION['join_email'] = $email;
		$_SESSION['join_blog'] = $blog;

        //Captcha stuff
		if (is_plugin_enabled('captcha')) {
			if (!isloggedin()) {
                $captcha_token = get_input('captcha_token');
                $captcha_input = get_input('captcha_input');

                if (!(($captcha_token) && (captcha_verify_captcha($captcha_input, $captcha_token)))) {
                    register_error(elgg_echo("captcha:captchafail"));
			        forward('pg/edufeedr/join/' . $guid);
                }
			}
		}

		// Make sure all required data is provided
		if (empty($firstname) || empty($lastname) || empty($email) || empty($blog) || (trim($blog) == 'http://')) {
			/*translation:Please fill all required fields.*/
			register_error(elgg_echo('edufeedr:error:blank:fields'));
			forward('pg/edufeedr/join/' . $guid);
		} else {
			// See if provided blog is acceptable
			$feeds = edufeedrGetBlogFeeds($blog);
			if (!$feeds || !is_array($feeds)) {
				/*translation:Provided url was not a blog or your blog engine is not supported.*/
				register_error(elgg_echo('edufeedr:error:engine:not:supported'));
				forward('pg/edufeedr/join/' . $guid);
			}
			// Same blog can not be used twice
			$blog_allowed = edufeedrCanRegisterWithBlog($guid, $blog);
			if (!$blog_allowed) {
				/*translation:You can not register to the course with the same blog twice.*/
				register_error(elgg_echo('edufeedr:error:blog_added_second_time'));
			    forward('pg/edufeedr/join/' . $guid);
			}

			// Add participant into table
			$posts = $feeds['posts'];
			$comments = $feeds['comments'];
			$participant_id = insert_data("INSERT INTO {$CONFIG->dbprefix}edufeedr_course_participants (course_guid, firstname, lastname, email, blog, posts, comments) VALUES ($guid, '$firstname', '$lastname', '$email', '$blog', '$posts', '$comments')");

			if ($participant_id) {
				/*translation:You have joined the course %s*/
				system_message(sprintf(elgg_echo('edufeedr:message:joined:educourse'), $educourse->title));
				// sendig to FC
                $es = new EduSuckr;
                $participant_data = array(
	                'participant_guid' => $participant_id,
		            'course_guid' => $guid,
		            'firstname' => $firstname,
		            'lastname' => $lastname,
		            'email' => $email,
		 	        'blog' => $blog,
		 	        'posts' => $posts,
		 	        'comments' => $comments,
		 	        'status' => 'active'
	            );
                $es_result = $es->addParticipant($participant_data);
                if (!$es_result) {
                    /*translation:Error occured, object could not be sent to EduSuckr.*/
                    register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
                }
			} else {
				/*translation:Participant could not be added to course %s*/
				register_error(sprintf(elgg_echo('edufeedr:error:could:not:join:educourse'), $educourse->title));
			}


			// Clear cache
			unset($_SESSION['join_firstname']);
			unset($_SESSION['join_lastname']);
			unset($_SESSION['join_email']);
			unset($_SESSION['join_blog']);

			forward($educourse->getURL());
		}
		forward('pg/edufeedr');
	}

?>
