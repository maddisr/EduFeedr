<?php
    
    if (isset($vars['entity']) && isset($vars['hidden_type']) && $vars['entity']->canEdit() && edufeedrCanEditEducourse($vars['entity'])) {
		$ts = time();
		$token = generate_action_token($ts);

		$body = '<div class="educourse">';

		$body .= '<h3>' . $vars['entity']->title . '</h3>';

        // getting from EduSuckr
		$es = new EduSuckr;

		if ($vars['hidden_type'] == 'posts') {
			$posts = $es->getHiddenPostsByCourse($vars['entity']->getGUID());

			if ($posts && is_array($posts)) {
				foreach ($posts as $post) {
				    $body .= '<h4 style="padding-top: 15px;font-weight:bold;"><a href="' . $post['link'] . '" target="_blank">' . $post['title'] . '</a></h4>';
				    /*translation:%s by %s*/
				    $body .= '<em>' . sprintf(elgg_echo('edufeedr:blog:by'), date('d.m.Y G:i', $post['date']), $post['author']) . '</em>';
				    /*translation:restore*/
				    $body .= '<a href="'.$vars['url'].'action/edufeedr/unhide_post?post_id='.$post['id'].'&educourse='.$vars['entity']->getGUID().'&__elgg_ts='.$ts.'&__elgg_token='.$token.'">' . elgg_echo('edufeedr:action:unhide') . '</a>';
				}
			}

		} else {
			$comments = $es->getHiddenCommentsByCourse($vars['entity']->getGUID());
			if ($comments && is_array($comments)) {
				foreach($comments as $comment) {
				    $body .= '<div class="edufeedr_post_comment">';
				    $body .= '<h4 style="padding-top:15px;font-weight:bold;"><a href="'.$comment['link'].'" target="_blank">'.$comment['title'].'</a></h4>';
				    $body .= '<em>' . date('d.m.Y G:i', $comment['date']) . '</em>';
				    /*translation:restore*/
				    $body .= '<a href="'.$vars['url'].'action/edufeedr/unhide_comment?comment_id='.$comment['id'].'&educourse='.$vars['entity']->getGUID().'&__elgg_ts='.$ts.'&__elgg_token='.$token.'">' . elgg_echo('edufeedr:action:unhide') . '</a>';
				    $body .= '</div>';
				}

			}
		}

		$body .= '</div>';//educourse ends

		echo $body;
    }
?>
