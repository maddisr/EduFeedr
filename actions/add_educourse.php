<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

    // Get input data
    $title = get_input('course_title');
    $description = get_input('course_description');
    $course_tag = get_input('course_tag');
    $course_blog = get_input('course_blog');
    $course_wiki = get_input('course_wiki');
    $signup_deadline = get_input('signup_deadline');
    $course_starting_date = get_input('course_starting_date');
    $course_ending_date = get_input('course_ending_date');

    // Cache variables
    $_SESSION['educourse_title'] = $title;
    $_SESSION['educourse_description'] = $description;
    $_SESSION['educourse_course_tag'] = $course_tag;
    $_SESSION['educourse_course_blog'] = $course_blog;
    $_SESSION['educourse_course_wiki'] = $course_wiki;
    $_SESSION['educourse_signup_deadline'] = $signup_deadline;
    $_SESSION['educourse_course_starting_date'] = $course_starting_date;
    $_SESSION['educourse_course_ending_date'] = $course_ending_date;

    // Make sure all required data is provided
    if (empty($title) || empty($description) || empty($course_tag) || empty($course_blog) || empty($signup_deadline) || empty($course_starting_date) || empty($course_ending_date) || (trim($course_blog) == 'http://')) {
        /*translation:Please fill all required fields.*/
        register_error(elgg_echo('edufeedr:error:blank:fields'));
	forward('pg/edufeedr/add_educourse');
    } else {
        $feeds = edufeedrGetBlogFeeds($course_blog);
			if (!$feeds || !is_array($feeds)) {
				/*translation:Provided url was not a blog or your blog engine is not supported.*/
				register_error(elgg_echo('edufeedr:error:engine:not:supported'));
				forward('pg/edufeedr/add_educourse');
			}
        $educourse = new ElggObject();
	$educourse->subtype = 'educourse';
	$educourse->owner_guid = $_SESSION['user']->getGUID();
	$educourse->access_id = 2; // public
	$educourse->title = $title;
	$educourse->description = $description;
	
	if (!$educourse->save()) {
            /*translation:Error occured, object could not be saved.*/
            register_error(elgg_echo('edufeedr:error:not:saved'));
            forward('pg/edufeedr/add_course');
	}
	$educourse->course_tag = $course_tag;
	$educourse->course_blog = $course_blog;
	// As course wiki is not a required field we should strip the default prefill value from it
	$course_wiki = edufeedrDealWithUrlPlaceholder($course_wiki);
	$educourse->course_wiki = $course_wiki;
	$educourse->signup_deadline = edufeedrDateIntoTimestamp($signup_deadline);
	$educourse->course_starting_date = edufeedrDateIntoTimestamp($course_starting_date);
	$educourse->course_ending_date = edufeedrDateIntoTimestamp($course_ending_date);
	// Aggregation start and end are computed as follows: start - 7 days, end + 14 days
	$educourse->start_aggregate = edufeedrDateIntoTimestamp($course_starting_date) - (86400 * 7);
	$educourse->stop_aggregate = edufeedrDateIntoTimestamp($course_ending_date) + (86400 * 14);
    $es = new EduSuckr;
    $pc = edufeedrGetBlogFeeds($course_blog);
    $es_data = array("course_guid"=>$educourse->getGUID(),
                     "title"=>$title,
                     "description"=>$description,
                     "posts"=>$pc['posts'],
                     "comments"=>$pc['comments'],
                     "course_tag"=>$course_tag,
                     "course_blog"=>$course_blog,
                     "course_wiki"=>$course_wiki,
                     "signup_deadline"=>edufeedrDateIntoTimestamp($signup_deadline),
                     "course_starting_date"=>edufeedrDateIntoTimestamp($course_starting_date),
					 "course_ending_date"=>edufeedrDateIntoTimestamp($course_ending_date),
					 "start_agregate"=>edufeedrDateIntoTimestamp($course_starting_date) - (86400 * 7),
					 "stop_agregate"=>edufeedrDateIntoTimestamp($course_ending_date) + (86400 * 14)
                    );
    if (!$es->setEduCourse($es_data)) {
        /*translation:Error occured, object could not be sent to EduSuckr.*/
        register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
    }
    $teacher = $educourse->getOwnerEntity();
    $participant_id = insert_data("INSERT INTO {$CONFIG->dbprefix}edufeedr_course_participants (course_guid, firstname, lastname, email, blog, blog_base, posts, comments, status) VALUES (".$educourse->getGUID().", 'Course', 'Blog', '".$teacher->email."', '$course_blog', '$course_blog', '".$pc['posts']."', '".$pc['comments']."', 'teacher')");
    $teacher_data = array(
	        'participant_guid' => $participant_id,
	        'course_guid' => $educourse->getGUID(),
	        'firstname' => "Course",
	        'lastname' => "Blog",
	        'email' => $teacher->email,
	        'blog' => $course_blog,
            'blog_base' => $course_blog,
	        'posts' => $pc['posts'],
	        'comments' => $pc['comments'],
	        'status' => 'teacher'
	 );
     $es_result = $es->addParticipant($teacher_data);
     if (!$es_result) {
         /*translation:Error occured, object could not be sent to EduSuckr.*/
         register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
     }

	/*translation:Course added*/
        system_message(elgg_echo('edufeedr:message:educourse:added'));

	// Clear cache
	unset($_SESSION['educourse_title']);
	unset($_SESSION['educourse_description']);
	unset($_SESSION['educourse_course_tag']);
	unset($_SESSION['educourse_course_blog']);
	unset($_SESSION['educourse_course_wiki']);
	unset($_SESSION['educourse_signup_deadline']);
	unset($_SESSION['educourse_course_starting_date']);
	unset($_SESSION['educourse_course_ending_date']);

	forward($educourse->getURL());
    }

    forward('pg/edufeedr');
?>
