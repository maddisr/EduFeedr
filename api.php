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
	    // Make deadline be the end of the day
		if ($educourse->getSubtype() == 'educourse' && ((int) $educourse->signup_deadline + 86399) >= $now)
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

    /**
     * Returns array with feed location data for base blog case.
     * Using atom feeds for blogger.
     * Using non-permalink feeds for wordpress (work all the time).
     *
     * @return array
     */
	function edufeedrGetSuitableGenerators() {
		return array(
            'blogger' => array(
                'posts' => 'feeds/posts/default',
                'comments' => 'feeds/comments/default'
            ),
            'wordpress' => array(
                'posts' => '?feed=atom',
                'comments' => '?feed=comments-rss2'
            )
		);
	}

    /**
     * Determines blog generator. Uses meta tags as much as possible.
     *
     * @param string $url URL of the blog
     * 
     * @return mixed Genrator string or false
     */
    function edufeedrGetBlogGenerator($url) {
        $generator = "";
        $tags = get_meta_tags($url);

		if (array_key_exists('generator', $tags)) {
		    $generator = $tags['generator'];
		    $generator = strtolower($generator);
		} else {
			if (__edufeedrGetUrlExists($url)) {
                if (strrpos($url, 'edublogs.org/') === strlen($url) - strlen('edublogs.org/')) {
                    $generator = 'wordpress';
                }
			}
		}

        if ($generator) {
            return $generator;
        }
        return false;
    }

    /**
     * Checks for category or label case and returns the URLs for feeds and blog_base.
     *
     * @param string $blog_url  URL provided (might be base or category/label)
     * @param string $generator The generator of the blog
     *
     * @return mixed Array with feed locations and blog base or false
     */
    function edufeedrGetCategoryOrLabelInformation($blog_url, $generator) {
        $generators_data = edufeedrGetSuitableGenerators();
        if (substr_count($generator, 'blogger')) {
            if (strpos($blog_url, '/search/label/') !== false) {
                $returned = array();
                $split_arr = preg_split('@/search/label/@', $blog_url, 2);
                $returned['blog_base'] = edufeedrFixEndSlashOnURL($split_arr[0]);
                // Using data on generator feed locations
                $returned['posts_feed_url'] = sprintf("%s%s/-/%s", $returned['blog_base'], $generators_data['blogger']['posts'], trim($split_arr[1], " /"));
                $returned['comments_feed_url'] = sprintf("%s%s", $returned['blog_base'], $generators_data['blogger']['comments']);
                return $returned;
            }
        } elseif (substr_count($generator, 'wordpress')) {
            if (strpos($blog_url, '/category/') !== false) {
                $returned = array();
                $split_arr = preg_split('@/category/@', $blog_url, 2);
                $returned['blog_base'] = edufeedrFixEndSlashOnURL($split_arr[0]);
                // Can not use generator data, using hard-coded feed location logic (the permalink case)
                $returned['posts_feed_url'] = sprintf("%scategory/%s/feed/atom/", $returned['blog_base'], trim($split_arr[1], " /"));
                $returned['comments_feed_url'] = sprintf("%scomments/feed/", $returned['blog_base']);
                return $returned;
            }
            $parts = parse_url($blog_url);
            if ($parts && isset($parts['query'])) {
                $query_arr = array();
                parse_str($parts['query'], $query_arr);
                if (isset($query_arr['cat'])) {
                    $returned = array();
                    $split_arr = preg_split('@\?@', $blog_url, 2);
                    $returned['blog_base'] = edufeedrFixEndSlashOnURL($split_arr[0]);
                    // Does not use generator data, using hard-coded feed location logic (the non-permalink case)
                    $returned['posts_feed_url'] = sprintf("%s?cat=%d&feed=atom", $returned['blog_base'], $query_arr['cat']);
                    $returned['comments_feed_url'] = sprintf("%s?feed=comments-rss2", $returned['blog_base']);
                    return $returned;
                }
            }
        }
        return false;
    }

    /**
     * Tries to determine blog posts and comments feeds.
     *
     * @param string $url URL of the blog
     *
     * @return mixed Array with feed locations and blog base or false
     */
	function edufeedrGetBlogFeeds($url) {

        $url = edufeedrFixEndSlashOnURL($url);

        $generator = edufeedrGetBlogGenerator($url);
        if (!$generator) {
            return false;
        }

        $col_information = edufeedrGetCategoryOrLabelInformation($url, $generator);
        if ($col_information) {
            return array(
                'posts' => $col_information['posts_feed_url'],
                'comments' => $col_information['comments_feed_url'],
                'blog_base' => $col_information['blog_base']
            ); 
        }
        
        $suitable_generators = edufeedrGetSuitableGenerators();

        foreach ($suitable_generators as $key => $sg) {
            if (substr_count($generator, $key) > 0) {
                return array(
                    'posts' => $url . $sg['posts'],
                    'comments' => $url . $sg['comments'],
                    'blog_base' => $url
                );
            }
        }

        return false;
    }

    /**
     * Builds and returns a URL from parts provided.
     * The parts should mostly come from parse_url usage.
     *
     * @param array $parts Parts of the URL to be used
     *
     * @return string
     */
    function edufeedrHttpBuildUrl(array $parts) {
        $scheme = isset($parts['scheme']) ? "{$parts['scheme']}://" : '';
        $host = isset($parts['host']) ? "{$parts['host']}" : '';
        $port = isset($parts['port']) ? ":{$parts['port']}" : '';
        $path = isset($parts['path']) ? "{$parts['path']}" : '';
        $query = isset($parts['query']) ? "?{$parts['query']}" : '';

        return $scheme.$host.$port.$path.$query;
    }

    /**
     * Determines if provided blog URL should be fixed.
     * Fixes as needed and returns the URL.
     *
     * @param string $blog_url URL of the blog
     *
     * @return string
     */
    function edufeedrFixIncorrectBlogAddress($blog_url) {
        $parts = parse_url($blog_url);
        if ( $parts && is_array($parts) && (sizeof($parts) > 0) ) {
            $generator = edufeedrGetBlogGenerator($blog_url);
            if ($generator) {
                if ($generator == 'wordpress.com') {
                    if ( isset($parts['scheme']) && ($parts['scheme'] == 'https') ) {
                        $parts['scheme'] = 'http';
                        $blog_url = edufeedrHttpBuildUrl($parts);
                    }
                } elseif ($generator == 'blogger') {
                    if (strpos($parts['host'], 'www.') === 0) {
                        $parts['host'] = substr($parts['host'], 4);
                        $blog_url = edufeedrHttpBuildUrl($parts);
                    }
                }

            }
        }

        return $blog_url;
    }

    /**
     * Appends a slash at the end of the URL if needed and returns it.
     *
     * @param string $url URL to be checked
     * 
     * @return string
     */
    function edufeedrFixEndSlashOnURL($url) {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            $add_url_end = strcmp("/", substr($url, -1));
            if ($add_url_end !== 0) {
                $url = $url . "/";
            }
        }

        return $url;
    }

	// Feed parsing methods and helpers

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
	
	/**
	 * TODO Need to write docstring
	 */
	function edufeedrGetCourseAssignmentsCount($guid) {
		global $CONFIG;
		$data = get_data_row("SELECT COUNT(*) AS count from {$CONFIG->dbprefix}edufeedr_course_assignments WHERE course_guid = $guid");
		return (int) $data->count;
	}
	
	function edufeedrGetCourseParticipantsCount($guid) {
		global $CONFIG;
		$data = get_data_row("SELECT COUNT(*) AS count from {$CONFIG->dbprefix}edufeedr_course_participants WHERE course_guid = $guid AND status='active'");
		return (int) $data->count;
	}

	function edufeedrGetSingleParticipant($course_guid, $participant_id) {
		global $CONFIG;
		if (!$course_guid || !$participant_id)
			return false;
		return get_data_row("SELECT * FROM {$CONFIG->dbprefix}edufeedr_course_participants WHERE course_guid = $course_guid and id = $participant_id");
	}

    /**
     * Checks if participant with certain blog base has already been
     * registered to the course. Using blog_base for the check.
     * This means that only one unique base is allowed per course.
     *
     * @param int    $course_guid   Course unique identifier
     * @param string $blog_base_url Blog base URL
     *
     * @return bool
     */
	function edufeedrCanRegisterWithBlogBase($course_guid, $blog_base_url) {
		global $CONFIG;
		if (!$course_guid || !$blog_base_url) {
			return false;
		}
		$blog_base_url_alternate = $blog_base_url;
		// Deal with cases of url having a trailing slash or not
		if (strrpos($blog_base_url, "/") === strlen($blog_base_url) - 1) {
			$blog_base_url_alternate = substr($blog_base_url, 0 , -1);
		} else {
			$blog_base_url_alternate = $blog_base_url_alternate . '/';
		}

		if (!get_data_row("SELECT id FROM {$CONFIG->dbprefix}edufeedr_course_participants WHERE course_guid = $course_guid AND (blog_base = '$blog_base_url' OR blog_base = '$blog_base_url_alternate')")) {
			return true;
		}
		return false;
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
