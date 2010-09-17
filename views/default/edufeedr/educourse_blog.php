<?php
    
    if (isset($vars['entity'])) {
		$ts = time();
		$token = generate_action_token($ts);

		$body = '<div class="educourse">';

		$body .= elgg_view('edufeedr/educourse_de', array('entity'=> $vars['entity']));

        $body .= '<table id="edufeedr_educourse_bc_table">';
        $body .= '<tr><td style="padding-right: 10px;">';
        /*translation:Blog posts*/
		$body .= '<h3>'.elgg_echo('edufeedr:latest:posts'). '</h3>';
        $body .= '</td><td style="padding: 0px 0 15px 0;" class="edufeedr_blogs_table_second_td">';
        /*translation:Comments*/
		$body .= '<h3>'.elgg_echo('edufeedr:latest:comments') . '</h3>';

		$body .= '</td></tr>';
        $body .= '<tr><td style="padding-right: 10px;">';
        // getting from EduSuckr
        $es = new EduSuckr;
        $posts = $es->getCoursePosts(array($vars['entity']->getGUID(),10));
        if ($posts) {
            $phtml = '';
			$post_date = "";
			foreach ($posts as $post) {
				// See if comment belongs to the same date as previous one, if not - add the date header
				if ($post_date != date('d.m.Y', $post['date'])) {
					$phtml .= '<h3>' . elgg_view('output/calendar', array('value' => (int) $post['date'])) . '</h3>';
					$post_date = date('d.m.Y', $post['date']);
				}
                $phtml .= "<h4 style=\"padding-top:15px;font-weight:bold;\"><a href=\"".$vars['url']."pg/edufeedr/view_post/".$vars['entity']->getGUID()."/".$post['id']."\">".$post['title']."</a></h4>";
				//$phtml .= "<div><i>".$post['author']."</i></div>";
				/*translation:%s by %s*/
                $phtml .= "<em>".sprintf(elgg_echo('edufeedr:blog:by'), date("d.m.Y G:i",$post['date']), $post['author'])."</em>";
				$phtml .= "<div>";
				$phtml .= '<div class="edufeedr_post_content">';
				// Getting post content plaintext
				$post_content = strip_tags($post['content']);
                if (strlen($post_content)>250) {
                    $last_space = strrpos(substr($post_content, 0, 250), ' ');
	                $content_piece = substr($post_content, 0, $last_space);
                    $phtml .= $content_piece;
				} else {
                    $phtml .= $post_content;
				}
				$phtml .= '</div>';// post content end
				// Actions
                /*translation:read more*/
				$phtml .= '<a href="'.$vars['url'].'pg/edufeedr/view_post/'.$vars['entity']->getGUID().'/'.$post['id'].'">' . elgg_echo('edufeedr:read:more') . '</a>';
				// Hide post
				if ($vars['entity']->canEdit() && edufeedrCanEditEducourse($vars['entity'])) {
					/*translation:hide*/
					$phtml .= ' | <a href="'.$vars['url'].'action/edufeedr/hide_post?post_id='.$post['id'].'&educourse='.$vars['entity']->getGUID().'&__elgg_ts='.$ts.'&__elgg_token='.$token.'">' . elgg_echo('edufeedr:action:hide') . '</a>';
				}

                $phtml .= "</div>";
            }
            $body .= $phtml;
        }
        $body .= '</td><td style="padding-left: 5px;">';
        $comments = $es->getCourseComments(array($vars['entity']->getGUID(),10));
		if ($comments) {
			$chtml = '';
			$comm_date = "";
			foreach ($comments as $comment) {
				// See if comment belongs to the same date as previous one, if not - add the date header
				if ($comm_date != date('d.m.Y', $comment['date'])) {
					$chtml .= '<h3>' . elgg_view('output/calendar', array('value' => (int) $comment['date'])) . '</h3>';
					$comm_date = date('d.m.Y', $comment['date']);
				}
				$chtml .= elgg_view('edufeedr/singles/educourse_comment', array('educourse' => $vars['entity'], 'comment' => $comment, 'type' => 'coursefeed'));
            }
            $body .= $chtml;
        }
		$body .= '</td></tr></table>';

		// Hidden posts and comments
		if ($vars['entity']->canEdit() && edufeedrCanEditEducourse($vars['entity'])) {
			// Deal with any floats
			$body .= '<div class="clearfloat"></div>';
		    $body .= '<div id="edufeedr_footer_for_facilitator">';
		    /*translation:Hidden posts*/
			$body .= '<a href="'.$vars['url'].'pg/edufeedr/view_hidden/'.$vars['entity']->getGUID().'/posts">'.elgg_echo('edufeedr:link:hidden_posts').'</a>';
			$body .= ' | ';
            /*translation:Hidden comments*/
			$body .= '<a href="'.$vars['url'].'pg/edufeedr/view_hidden/'.$vars['entity']->getGUID().'/comments">'.elgg_echo('edufeedr:link:hidden_comments').'</a>';
		    $body .= '</div>'; // footer ends
		}
		$body .= '</div>';//educourse ends

		echo $body;
    }
?>
