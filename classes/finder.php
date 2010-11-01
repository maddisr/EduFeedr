<?php

    /*
	 * BlogFeedFinder
	 *
	 * Parses posts and comments feeds from blog URL provided
	 *
	 * @author Pjotr Savitski
	 * @notes Supports Blogger and Wordpress
	 * @__version__ 0.1
	 *
	 * @methods List of methods that raturn information
	 * @get_posts_feed Returns URI of the posts feed (either RSS or Atom)
	 * @get_comments_feed Returns URI of the comments feed (either RSS or Atom)
	 * @get_blog_base Returns blog base URI, usable in case when provided url was a category
	 * @get_blog_url Returns blog url
	 * @get_engine_type Returns engine type
	 *
	 * @exceptions
	 * @no_url_provided URL has not been provided for class initialization
	 * @url_has_no_contents Provided URL has no contents.
	 *
	 */

    class BlogFeedFinder {
        public $blog_url;
		public $data;
		public $feeds;
		public $engine_type;
		public $blog_base;

		function __construct($blog_url) {
			// Throw exception if no URL is provided
			if (!$blog_url) {
				throw new Exception('no_url_provided');
			}
		    $data = @file_get_contents($blog_url);
			if (!$data) {
				// Throw exception if provided URL has no contents.
				throw new Exception('url_has_no_contents');
			}
		    $this->set_blog_url($blog_url);
			$this->data = $data;
			// Free some memory
			unset($data);
			$this->set_engine_type();
			$this->set_blog_base();
			$this->set_blog_feeds();
			// Data is not needed any more, free some memory
			unset($this->data);
		}

		public function get_posts_feed() {
			return $this->feeds['posts'];
		}

        public function get_comments_feed() {
			return $this->feeds['comments'];
		}

		public function get_blog_base() {
			return $this->blog_base;
		}

		public function get_blog_url() {
			return $this->blog_url;
		}

		public function get_engine_type() {
			return $this->engine_type;
		}

		// Normalizes feed url discovered from the header
		protected function normalize_feed_url($url) {
			return str_replace(array('\'', '"'), "", $url);
		}

		protected function add_trailing_slash($url) {
			$url = trim($url);
			if (substr($url, -1) != '/') {
				$url .= '/';
			}
			return $url;
		}

		protected function extract_real_base($url) {
			$url = trim($url);
			if ((substr_count($url, '/') == 3 && substr($url, -1) != '/') || substr_count($url, '/') > 3) {
				$position = strpos($url, '/', 8);
				$url = substr($url, 0, $position);
			}
			return $url;
		}

		// Sets blog_url parameter to the provided URL, adds trailing slash if needed
		protected function set_blog_url($blog_url) {
			$this->blog_url = $this->add_trailing_slash($blog_url);
		}

		// Sets blog base, first try to extract that from the header, makes a fallback onto provided url
		protected function set_blog_base() {
			$blog_base = $this->blog_url;
			if (substr_count($this->engine_type, 'blogger')) {
				if (preg_match_all('#<link[^>]+rel=[\'|"]canonical[^>]*>#is', $this->data, $rawIndex)) {
					if (preg_match('#href=\s*(?:"|)([^"\s>]+)#i', $rawIndex[0][0], $rawHref)) {
						// Canonical will give a path to current page, not index, deal with it
						$blog_base = $this->extract_real_base($this->normalize_feed_url($rawHref[1]));
					}
				}
			} else if (substr_count($this->engine_type, 'wordpress')) {
				if (preg_match_all('#<link[^>]+rel=["|\']index[^>]*>#is', $this->data, $rawIndex)) {
					if (preg_match('#href=\s*(?:"|)([^"\s>]+)#i', $rawIndex[0][0], $rawHref)) {
						$blog_base = $this->normalize_feed_url($rawHref[1]);
					}
				}
			}
			$this->blog_base = $this->add_trailing_slash($blog_base);
		}

		// Try to set engine type with single word
		protected function set_engine_type() {
			$engine_type = NULL;
			$tags = get_meta_tags($this->blog_url);

			if (array_key_exists('generator', $tags)) {
				$generator = strtolower($tags['generator']);
				if (substr_count($generator, 'blogger')) {
					$engine_type = 'blogger';
				} else if (substr_count($generator, 'wordpress')) {
					$engine_type = 'wordpress';
				} else {
					$engine_type = $generator;
				}
				unset($generator);
			} else {
				// Handle special cases
				if (strpos($this->blog_url, 'edublogs.org/')) {
					$engine_type = 'wordpress';
				}
			}
			$this->engine_type = $engine_type;
		}

		// Get all possible elements thta look like feeds from header
        protected function get_possible_feeds() {
            $feeds = array();
            if (preg_match_all('#<link[^>]+type=\s*(?:"|)application/(rss|atom)\+xml[^>]*>#is', $this->data, $rawMatches)) {
                foreach ($rawMatches[0] as $rawMatch) {
                    $feed = array();
                    // type case
                    if (preg_match('#type=\s*(?:"|)([^"\s>]+)#i', $rawMatch, $rawType)) {
                        $feed['type'] = $rawType[1];
                    }
                    // href case
                    if (preg_match('#href=\s*(?:"|)([^"\s>]+)#i', $rawMatch, $rawUrl)) {
                        $feed['href'] = $this->normalize_feed_url($rawUrl[1]);
                    }
                    $feeds[] = $feed;
                }
            }
            return $feeds;
		}

        protected function set_blog_feeds() {
			$feeds = array('posts' => '', 'comments' => '');
			$possible_feeds = $this->get_possible_feeds();
            if (substr_count($this->engine_type, 'blogger')) {// blogger case
			    foreach ($possible_feeds as $single_feed) {
				    if ($single_feed['type'] == 'application/atom+xml' && strpos($single_feed['href'], 'feeds/posts')) {
					    $feeds['posts'] = $single_feed['href'];
				    } else if ($single_feed['type'] == 'application/atom+xml' && strpos($single_feed['href'], 'feeds/comments')) {
					    $feeds['comments'] = $single_feed['href'];
				    }
				    // As of the moment comments are not really provided
				    if ($feeds['comments'] == '') {
				        $feeds['comments'] = $this->blog_base . 'feeds/comments/default';
				    }
			    }
			} else if (substr_count($this->engine_type, 'wordpress')) {// wordpress case
			    foreach ($possible_feeds as $single_feed) {
				    if (($single_feed['type'] == 'application/rss+xml' || $single_feed['type'] == 'application/atom+xml') && !strpos($single_feed['href'], '/comments/feed/')) {
					    $feeds['posts'] = $single_feed['href'];
				    } else if (($single_feed['type'] == 'application/rss+xml' || $single_feed['type'] == 'application/atom+xml') && strpos($single_feed['href'],'/comments/feed/')) {
					    $feeds['comments'] = $single_feed['href'];
					}
					// In case the comments feed is nowhere to be found, just construct it manually
					if ($feeds['comments'] == '') {
						$feeds['comments'] = $this->blog_base . 'comments/feed/atom/';
					}
			    }
		    }

		    $this->feeds = $feeds;
		}

	} // BlogFeedFinder end
?>
