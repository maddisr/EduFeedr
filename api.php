<?php

    function edufeedrDateIntoTimestamp($date) {
        if ($date > 86400)
            return $date;
	$replacables = array('.', ' ', '/', ',');
	$date = str_replace($replacables, '-', $date);
	return strtotime($date);
	}

    function edufeedrIsEducourseOpen($educourse) {
        $now = time();
		if ($educourse->getSubtype() == 'educourse' && $educourse->signup_deadline > $now)
			return true;

		return false;
	}

	function edufeedrCanEditEducourse($educourse) {
		if (isadminloggedin())
			return true;
		else if (isloggedin() && $educourse->getSubtype() == 'educourse' && ($educourse->getOwner() == get_loggedin_userid() || edufeedrIsFacilitator($educourse)))
			return true;

		return false;
	}

	function edufeedrCanManageEducourse($educourse) {
		if (isadminloggedin()) {
			return true;
		} else if (isloggedin() && $educourse->getSubtype() == 'educourse' && $educourse->getOwner()== get_loggedin_userid()) {
			return true;
		}
		return false;
	}

	function edufeedrCourseVCARD($educourse) {
		$participants = edufeedrGetCourseParticipants($educourse->getGUID());

		uasort($participants, '__edufeedr_users_sort_cmp');

		$vcard = "";
		$separator = "\r\n";

		foreach ($participants as $key => $participant) {
			$vcard .= 'BEGIN:VCARD' . $separator;
			$vcard .= 'VERSION:3.0' . $separator;
			$vcard .= 'N:' . $participant->lastname . ';' . $participant->firstname . ';;;' . $separator;
			$vcard .= 'FN:' . $participant->firstname . ' ' . $participant->lastname . $separator;
			$vcard .= 'EMAIL;TYPE=PREF,INTERNET:' . $participant->email . $separator;
			$vcard .= 'item1.URL;TYPE=PREF:' . $participant->blog . $separator;
			$vcard .= 'item1.X-ABLabel:blog' . $separator;
			$vcard .= 'CATEGORIES:' . $educourse->course_tag . $separator;
			$vcard .= 'END:VCARD';

			$vcard .= $separator . $separator;
		}

		return $vcard;
	}

	function edufeedrCourseOPML($educourse, $type = 'posts') {
		$participants = edufeedrGetCourseParticipants($educourse->getGUID());

		uasort($participants, '__edufeedr_users_sort_cmp');

		$owner = $educourse->getOwnerEntity();

		$opml = '<?xml version="1.0" encoding="UTF-8"' . '?' . '>';
		$opml .= '<opml version="2.0">';

		$opml .= '<head>';
		$opml .= '<title>' . $educourse->title . '</title>';
		$opml .= '<dateCreated>' . date(DATE_RSS, $educourse->time_created) . '</dateCreated>';
		$opml .= '<ownerName>' . $owner->name . '</ownerName>';
		$opml .= '<ownerEmail>' . $owner->email . '</ownerEmail>';
		$opml .= '<ownerId>' . $owner->getURL() . '</ownerId>';
		$opml .= '</head>';

		$opml .= '<body>';

		foreach ($participants as $participant) {
			$opml .= '<outline type="rss" text="' . $participant->firstname . ' ' . $participant->lastname . '" description="" xmlUrl="' . $participant->$type . '" htmlUrl="' . $participant->blog . '"/>';
		}

		$opml .= '</body>';

		$opml .= '</opml>';

		return $opml;
	}

	function edufeedrGetSuitableGenerators() {
		return array(
			'blogger' => array('posts' => 'feeds/posts/default', 'comments' => 'feeds/comments/default'),
			'wordpress' => array('posts' => 'wp-atom.php', 'comments' => 'wp-commentsrss2.php') // Using ugly defaults to be on the safe side
			);
	}

	function edufeedrGetBlogFeeds($url) {

		$add_url_end = strcmp("/", substr($url, -1));
		if ($add_url_end) {
			$url = $url . "/";
		}
		$tags = get_meta_tags($url);

		if (array_key_exists('generator', $tags)) {
		    $generator = $tags['generator'];
		    $generator = strtolower($generator);
		} else {
			if (__edufeedrGetUrlExists($url)) {
			    if (strrpos($url, 'edublogs.org/') === strlen($url) - strlen('edublogs.org/'))
				    $generator = 'wordpress';
			    else
				    return false;
			} else {
				return false;
			}
		}

		$suitable_generators = edufeedrGetSuitableGenerators();

		foreach ($suitable_generators as $key => $sg) {
			if (substr_count($generator, $key) > 0) {
				return array('posts' => $url . $sg['posts'], 'comments' => $url . $sg['comments']);
			}
		}

		return false;
	}

	// Feed parsing methods and helpers
    // XXX UNUSED
	function edufeedrSingleParticipantProgress($assignments, $posts_url, $course_starting_date) {
		$simplepie_cache = get_plugin_setting('edufeedr_simplepie_cache', 'edufeedr');
		$feed = new SimplePie();
		$feed->set_feed_url($posts_url);
		if (empty($simplepie_cache)) {
			$feed->enable_cache(false);
		} else {
			$feed->enable_cache(true);
			$feed->set_cache_duration(1800);
			$feed->set_cache_location($simplepie_cache);
		}
		$feed->set_output_encoding('UTF-8');
		$feed->init();
		$feed->handle_content_type();

		//We do not handle errors here, as invalid XML is processed fine, but might raise errors
		//TODO Think about preprocessing Feed before giving it to SimplePie parser
		if ($feed->error())
			error_log('Problems with RSS feed: ' . $feed->error());

		$returned = array();

		$items = $feed->get_items(0, 0);

		$i == 0;
		$prev_a_start = 0;
		foreach ($assignments as $key => $assignment) {
			// States: 0 - no link or blog post, 1 - blog post in certain time frame, 2 - link to assignment
			$returned[$key] = array('state' => 0);
			$timeframe_empty = true;
			$timeframe_result = array('state' => 1);

			if ($i == 0)
				$frame_start_ts = $course_starting_date;
			else
				$frame_start_ts = $prev_a_start;

			$prev_a_start = (int) $assignment->deadline;
			// Add one day, so that people would have time till the end of the day
			$frame_end_ts = (int) $assignment->deadline + 86400;
			$i++;

			foreach ($items as $item) {
				if (__edufeedrItemIsAssignmentResponse($item->get_content(), $assignment->blog_post_url))
					$returned[$key] = array('state' => 2, 'link' => $item->get_permalink(), 'title' => $item->get_title());
				else if ($timeframe_empty && __edufeedrIsAssignmentTimeFrame($frame_start_ts, $frame_end_ts, $item->get_date('U'))) {
					$timeframe_empty = false;
					$timeframe_result = array('state' => 1, 'link' => $item->get_permalink(), 'title' => $item->get_title());
				}
			}

			if (($returned[$key]['state'] == 0) && (!$timeframe_empty))
				$returned[$key] = $timeframe_result;
		}

		// destroy feed
		$feed->__destruct();
		unset($feed);

		return $returned;
	}

	// Axpects to get output of get_content of SimplePie_Item and string to search for. Returns true if string is found in content at least once, othervice returns false
	function __edufeedrItemIsAssignmentResponse($content, $assignment_url) {
		// Get rid of ending slash
		if (strrpos($assignment_url, "/") === strlen($assignment_url) - 1)
			$assignment_url = substr($assignment_url, 0 , -1);

		if (substr_count($content, $assignment_url) > 0)
			return true;

		return false;
	}

	function __edufeedrIsAssignmentTimeframe($frame_start_ts, $frame_end_ts, $post_ts) {

		if (empty($frame_start_ts) || empty($frame_end_ts) || empty($post_ts))
			return false;

		if (($frame_start_ts <= $post_ts) && ($frame_end_ts >= $post_ts))
			return true;

		return false;
	}

	// Check if the link is live and responding
	function __edufeedrGetUrlExists($url=NULL) {
		if ($url == NULL) return false;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($httpcode>=200 && $httpcode<300) return true;
		
		else return false;
	}

	function edufeedrShortenText($text, $max_length = 150) {
		$text = strip_tags($text);

		if (strlen($text)<=$max_length)
			return $text;

		$text = $text . "";
		$text = substr($text, 0, $max_length);
		$text = substr($text, 0, strrpos($text, ' '));
		$text = $text . "...";

		return $text;
	}

	function edufeedrGetAssignmentData($url) {

		$returned = array('state' => false, 'message'=> '');

		if (!__edufeedrGetUrlExists($url)) {
			/*translation:Entered URL does not exist.*/
			$message = elgg_echo('edufeedr:error:url:does:not:exist');
			$returned['message'] = $message;
			return $returned;
		}

        $simplepie_cache = get_plugin_setting('edufeedr_simplepie_cache', 'edufeedr');
		$feed = new SimplePie();
		$feed->set_feed_url($url);
        if (empty($simplepie_cache)) {
			$feed->enable_cache(false);
		} else {
			$feed->enable_cache(true);
			$feed->set_cache_duration(1800);
			$feed->set_cache_location($simplepie_cache);
		}
		$feed->set_output_encoding('UTF-8');
		$feed->init();
		$feed->handle_content_type();

		if ($feed->error()) {
			/*translation:Entered URL has no feed.*/
			$message = elgg_echo('edufeedr:error:link:has:no:feed');
			$returned['message'] = $message;
			return $returned;
		}

		$returned_data = array('title' => '', 'description' => '');

		$items = $feed->get_items(0, 0);

		foreach ($items as $item) {
			if ($item->get_permalink() == $url) {
				$returned_data['title'] = $item->get_title();
				$returned_data['description'] = edufeedrShortenText($item->get_content(), 200);
				$returned['state'] = true;
				$returned['data'] = $returned_data;
				return $returned;
				// Destroy feed
				$feed->__destruct();
				unset($feed);
				exit;
			}
		}

		/*translation:Post not found in blog RSS.*/
		$message = elgg_echo('edufeedr:error:post:not:found:in:feed');
		$returned['message'] = $message;

		// Destroy feed
		$feed->__destruct();
		unset($feed);

		return $returned;
	}

	function __edufeedr_users_sort_cmp($a, $b) {
		$a_name = strtolower($a->lastname);
		$b_name = strtolower($b->lastname);
		if ($a_name == $b_name)
			return 0;
		return ($a_name < $b_name) ? -1 : 1;
	}

	function __edufeedr_assignments_sort_cmp($a, $b) {
		$a_deadline = $a->deadline;
		$b_deadline = $b->deadline;

		if ($a_deadline == $b_deadline)
			return 0;
		return ($a_deadline < $b_deadline) ? -1 : 1;
	}

	function edufeedrGetCourseConnectionsTSV($educourse) {
	    $es = new EduSuckr;
	    //var_dump($es->getCourseLinkingConnections($educourse->getGUID())); exit;
        if (!$connections = unserialize($es->getCourseLinkingConnections($educourse->getGUID()))) {
            /*translation:Error occured, object could not be sent to EduSuckr.*/
            register_error(elgg_echo('edufeedr:error:not:sent:edusuckr'));
        }
		$returned = "";
		$tab_separator = "\t";
		$line_separator = "\r\n";

		$returned .= 'Person' . $tab_separator . 'Links or comments' . $line_separator;

		foreach ($connections as $connection) {
			$returned .= $connection['person'] . $tab_separator . $connection['links'] . $line_separator;
		}

		return $returned;
	}

	function edufeedrGetCourseParticipants($guid) {
		global $CONFIG;
		return get_data("SELECT * FROM {$CONFIG->dbprefix}edufeedr_course_participants WHERE course_guid = $guid AND status='active'");
	}

	function edufeedrGetCourseAssignments($guid) {
		global $CONFIG;
		return get_data("SELECT * from {$CONFIG->dbprefix}edufeedr_course_assignments WHERE course_guid = $guid");
	}

	function edufeedrGetSingleParticipant($course_guid, $participant_id) {
		global $CONFIG;
		if (!$course_guid || !$participant_id)
			return false;
		return get_data_row("SELECT * FROM {$CONFIG->dbprefix}edufeedr_course_participants WHERE course_guid = $course_guid and id = $participant_id");
	}

	function edufeedrGetSingleAssignment($course_guid, $assignment_id) {
		global $CONFIG;
		if (!$course_guid || !$assignment_id)
			return false;
		return get_data_row("SELECT * FROM {$CONFIG->dbprefix}edufeedr_course_assignments WHERE course_guid = $course_guid and id = $assignment_id");
	}

	function edufeedrCourseFacilitators($educourse) {
		global $CONFIG;
		if ($educourse && $educourse->getSubtype() == 'educourse') {
			return get_data("SELECT * FROM {$CONFIG->dbprefix}edufeedr_course_facilitators WHERE course_guid = {$educourse->getGUID()}");
		}
		return false;
	}

	function edufeedrIsFacilitator($educourse) {
		global $CONFIG;
		if ($educourse && $educourse->getSubtype() == 'educourse') {
			$current_user_guid = get_loggedin_userid();
			$is_facilitator = get_data_row("SELECT id FROM {$CONFIG->dbprefix}edufeedr_course_facilitators WHERE course_guid = {$educourse->getGUID()} and user_guid=$current_user_guid");
			if ($is_facilitator) {
				return true;
			}
		}
		return false;
	}

	// Returns empty string in case only a placeholder is provided
	function edufeedrDealWithUrlPlaceholder($url) {
		if (trim($url) == 'http://') {
			return '';
		}
		return $url;
	}
?>
