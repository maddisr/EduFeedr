<?php
    
    if (isset($vars['entity']) && isset($vars['post_id'])) {
		$ts = time();
		$token = generate_action_token($ts);

		$body = '<div class="educourse">';

		$body .= '<h3>' . $vars['entity']->title . '</h3>';

        // getting from EduSuckr
		$es = new EduSuckr;
        $data = $es->getCoursePostById(array($vars['post_id'], $vars['entity']->guid));
        $is_post_hidden = false;
        if ($data && isset($data['post']['hidden']) && $data['post']['hidden']) {
            $is_post_hidden = true;
        }
        $can_edit_educourse = false;
        if ($vars['entity']->canEdit() && edufeedrCanEditEducourse($vars['entity'])) {
            $can_edit_educourse = true;
        }
        $post_shown = true;
        if (!$can_edit_educourse) {
            if ($is_post_hidden) {
                $post_shown = false;
            }
        }
		if ($data && $post_shown) {
			$body .= '<h4 style="padding-top: 15px;font-weight:bold;"><a href="' . $data['post']['link'] . '" target="_blank">' . $data['post']['title'] . '</a></h4>';

			/*translation:%s by %s*/
			$body .= '<em>' . sprintf(elgg_echo('edufeedr:blog:by'), date('d.m.Y G:i', $data['post']['date']), $data['post']['author']) . '</em>';
			$body .= '<div id="educourse_post_content">' . nl2br($data['post']['content']) . '</div>';
			
			// Hide post	
			if ($can_edit_educourse) {
                if ($is_post_hidden) {
                    /*translation:This post has been hidden from the course.*/
                    $body .= '<span class="post_hidden_text">' . elgg_echo('edufeedr:text:post_has_been_hidden') . '</span>';
                    $restore_url = $vars['url'].'action/edufeedr/unhide_post?post_id='.$data['post']['id'].'&educourse='.$vars['entity']->getGUID();
                    $restore_url = elgg_add_action_tokens_to_url($restore_url);
                    /*translation:restore*/
                    $body .= " <a href=\"{$restore_url}\">".ucfirst( elgg_echo('edufeedr:action:unhide') )."</a>";
                } else {
				    /*translation:hide*/
				    $body .= '<a href="'.$vars['url'].'action/edufeedr/hide_post?post_id='.$data['post']['id'].'&educourse='.$vars['entity']->getGUID().'&__elgg_ts='.$ts.'&__elgg_token='.$token.'">' . elgg_echo('edufeedr:action:hide') . '</a>';
                }
			}
			
			$body .= '<div id="educourse_post_link"><a href="' . $data['post']['link'] . '" target="_blank">' . $data['post']['link'] . '</a></a>';

			// Comments
			/*translation:%s comments*/
			$body .= '<h3 style="margin-top:20px;margin-bottom:10px;">' . sprintf(elgg_echo('edufeedr:blog:comments'), sizeof($data['comments'])) . ':</h3>';
			if (is_array($data['comments']) && sizeof($data['comments']) > 0) {
				foreach ($data['comments'] as $comment) {
					$body .= elgg_view('edufeedr/singles/educourse_comment', array('educourse' => $vars['entity'], 'comment' => $comment, 'type' => 'viewpost'));
				}
			}

			$body .= '<div id="edufeedr_blog_post_a_comment">';
			/*translation:Post a comment*/
			$body .= '<a href="'.$data['post']['link'].'" target="_blank">' . elgg_echo('edufeedr:post:write:a:comment') . '</a>';
			$body .= '</div>';
        }
		$body .= '</div>';//educourse ends

		echo $body;
    }
?>
