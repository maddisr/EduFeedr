<?php

    if (isset($vars['educourse']) && isset($vars['comment'])) {

		$body = '';
		$ts = time();
		$token = generate_action_token($ts);
		
		// See what type of view do we need
		$type = 'coursefeed';
		if (isset($vars['type']) && ($vars['type'] == 'coursefeed' || $vars['type'] == 'viewpost')) {
			$type = $vars['type'];
		}

		$can_edit = false;
		if ($vars['educourse']->canEdit() && edufeedrCanEditEducourse($vars['educourse'])) {
			$can_edit = true;
		}

		$body .= '<div class="edufeedr_post_comment">';

		// Title
		if (in_array($type, array('coursefeed', 'viewpost'))) {
			if ($vars['comment']['author'] == $vars['comment']['post_author']) {
				$body .= '<h4 style="padding-top:15px;">'.$vars['comment']['author'].'</h4>';
				
			} else {
			/*translation:%s to %s*/
				$body .= '<h4 style="padding-top:15px;">'.sprintf(elgg_echo('edufeedr:post_comment_commenter_to_poster'), $vars['comment']['author'], $vars['comment']['post_author']).'</h4>';
			
			
			}
		}

		// Date
		$body .= '<em>' . date('d.m.Y G:i', $vars['comment']['date']) . '</em>';
		// Content
		$body .= '<div class="edufeedr_comment_content">';
		// Getting comment content plaintext
		$comment_content = strip_tags($vars['comment']['content']);
		
		if ($type == 'coursefeed') {
			
			
			if (strlen($comment_content)>200) {
				$last_space = strrpos(substr($comment_content, 0, 200), ' ');
				$body .= substr($comment_content, 0, $last_space);
			} else {
				$body .= $comment_content;
			}
		} else if ($type == 'viewpost'){
			$body .= nl2br($vars['comment']['content']);
		}
		$body .= '</div>';
		// Actions
		if ($type == 'coursefeed') {
			if ($vars['comment']['post_id']) {
				/*translation:read more*/
				$body .= '<a href="'.$vars['url'].'pg/edufeedr/view_post/'.$vars['educourse']->getGUID().'/'.$vars['comment']['post_id'].'">' . elgg_echo('edufeedr:read:more') . '</a>';
				if ($can_edit) {
					$body .= ' | ';
				}
			}
		}
		// Hide action should be available in any view
        if ($can_edit) {
		    /*translation:hide*/
			$body .= '<a href="'.$vars['url'].'action/edufeedr/hide_comment?comment_id='.$vars['comment']['id'].'&educourse='.$vars['educourse']->getGUID().'&__elgg_ts='.$ts.'&__elgg_token='.$token.'">' . elgg_echo('edufeedr:action:hide'). '</a>';
		}
		$body.='<br/>';
			if ($type == 'viewpost'){
				$body.= '<div id="educourse_post_link">'.elgg_echo('Connect this comment with participant:  ').'';
				$body.='<br/>';
				if (edufeedrGetCourseParticipantsCount($vars['educourse']->getGUID()) > 0) {
					if (empty($vars['comment']['participant_id'])) {
						$form_body .= '<div>';
						$form_body .= '<input type="hidden" name="course_guid" value="' . $vars['educourse']->getGUID() . '" />';
						$form_body .= '<input type="hidden" name="comment_id" value="' . $vars['comment']['id'] . '" />';
						$form_body .= '<input type="hidden" name="post_id" value="' . $vars['comment']['post_id'] . '" />';
						$participants = edufeedrGetCourseParticipants($vars['educourse']->getGUID());
					
						$options_values = array();
						foreach ($participants as $participant) {
						$options_values[$participant->id] = $participant->firstname.' '. $participant->lastname;
						}
				    
						$form_body .= elgg_view('input/pulldown',  array(
							'internalname' => 'participant_id',
							'value' => '',
							'options_values' => $options_values,
						));
						/*translation:connect_comment*/
						$form_body .= '<input type="submit" value="' . elgg_echo('edufeedr:connect_comment'). '"/>';
						$form_body .= '</div>';
						$body .= elgg_view('input/form', array('action' => "{$vars['url']}action/edufeedr/connect_comment_with_participant", 'body' => $form_body));
					} else {
						$participant = edufeedrGetSingleParticipant($vars['educourse']->getGUID(), $vars['comment']['participant_id']);
						$form_body .= '<div>';
						$form_body .= '<input type="hidden" name="course_guid" value="' . $vars['educourse']->getGUID() . '" />';
						$form_body .= '<input type="hidden" name="comment_id" value="' . $vars['comment']['id'] . '" />';
						$form_body .= '<input type="hidden" name="post_id" value="' . $vars['comment']['post_id'] . '" />';	    
						
						/*translation:disconnect_comment*/	
						$form_body .= '<input type="submit" value="' . elgg_echo('edufeedr:disconnect_comment'). '"/>';
						$form_body .= '</div>';
						$body .= elgg_view('input/form', array('action' => "{$vars['url']}action/edufeedr/disconnect_comment_with_participant", 'body' => $form_body));
					}
				}
			}	
		
		$body .= '</div>';// end of comment

		echo $body;
    }
?>
