<?php

    class EduSuckr {
        
        public $error = NULL;
        public $result = NULL;
        public $client = NULL;
        public $wsdl_url = NULL;
        
        public function __construct() {
            $wsdl_url = get_plugin_setting('edusuckr_wsdl_url', 'edufeedr');
            $wsdl_nik = get_plugin_setting('edusuckr_wsdl_nik', 'edufeedr');
            $wsdl_pwd = get_plugin_setting('edusuckr_wsdl_pwd', 'edufeedr');
            if ($wsdl_url){
                $this->client = new nusoap_client($wsdl_url, TRUE);
                $this->client->setCredentials($wsdl_nik, $wsdl_pwd, "basic");
                $err = $this->client->getError();
                if ($err) {
	                $this->error = '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	                return 0;
                }
            } else {
                $this->error = '<h2>Connection error</h2><pre>Cannot connect, WSDL url not set</pre>';
            }
        }
  
        function setEduCourse($param){
            return $this->client->call('setEduCourse', array("param"=>$param));
        }
        
        function removeEduCourse($param){
            return $this->client->call('removeEduCourse', array($param));
        }
        
        function addParticipant($param) {
            return $this->client->call('addParticipant', array($param));
        }
        
        function removeParticipant($param) {
            return $this->client->call('removeParticipant', array($param));
        }
        
        function setAssignment($param) {
            return $this->client->call('setAssignment', array($param));
        }
        
        function removeAssignment($param) {
            return $this->client->call('removeAssignment', array($param));
        }
        
        /*hides blog post called by numeric id*/
        function hidePostById($param) {
            return $this->client->call('hidePostById', array($param));
        }
        
        /*hides blog comment called by numeric id*/
        function hideCommentById($param) {
            return $this->client->call('hideCommentById', array($param));
        }
        
        /*unhides blog post called by numeric id*/
        function unhidePostById($param) {
            return $this->client->call('unhidePostById', array($param));
        }
        
        /*unhides blog comment called by numeric id*/
        function unhideCommentById($param) {
            return $this->client->call('unhideCommentById', array($param));
        }
        
        function getProgressTable($param) {
            return $this->client->call('getProgressTable', array($param));
        }
        
        function getCourseLinkingConnections($param) {
            return $this->client->call('getCourseLinkingConnections', array($param));
        }
        
        /*gets posts for course*/
        function getCoursePosts($param) {
            return unserialize($this->client->call('getCoursePosts', array($param)));
        }
        function getCourseComments($param) {
            return unserialize($this->client->call('getCourseComments', array($param)));
		}
		function getCoursePostById($param) {
			return unserialize($this->client->call('getCoursePostById', array($param)));
		}
		function getHiddenPostsByCourse($course_guid) {
			$posts = $this->client->call('getHiddenPostsByCourse', array($course_guid));
			if ($posts) {
			    return unserialize($posts);
			}
			return $posts;
		}
		function getHiddenCommentsByCourse($course_guid) {
            $comments = $this->client->call('getHiddenCommentsByCourse', array($course_guid));
			if ($comments) {
			    return unserialize($comments);
			}
			return $comments;
        }

		function getParticipantPosts($param) {
			return unserialize($this->client->call('getParticipantPosts', array($param)));
		}
		
		/**
		 * TODO add doctstring
		 */
        function connectPostWithAssignment($coure_guid, $post_id, $assignment_id) {
            return $this->client->call('connectPostWithAssignment', array($coure_guid, $post_id, $assignment_id));
        }
		function disconnectPostWithAssignment($coure_guid, $post_id) {
            return $this->client->call('disconnectPostWithAssignment', array($coure_guid, $post_id));
        }
		function connectCommentWithParticipant($course_guid, $post_id, $participant_id) {
            return $this->client->call('connectCommentWithParticipant', array($course_guid, $post_id, $participant_id));
		}
	
	}

?>
