<?php
    
    if (isset($vars['entity']) && isset($vars['participant'])) {

		$body = '<div class="educourse">';

		$body .= '<h3>' . $vars['entity']->title . '</h3>';

		$body .= '<div class="profile">';
        
		$avatar_href = "http://gravatar.com/avatar/".md5(trim($vars['participant']->email))."?s=128";
		if ($avatar_data = file_get_contents($avatar_href)) {
			$body .= '<img class="profile_avatar" src="data:image/jpeg;base64,'.base64_encode($avatar_data).'" alt="avatar" />';
		}

		$body .= '<h3>'.$vars['participant']->firstname.' '.$vars['participant']->lastname.'</h3>';

		$body .= '<table id="profile_data"><tbody>';
		if (edufeedrCanEditEducourse($vars['entity'])) {
			$body .= '<tr>';
		    /*translation:E-mail*/
			$body .= '<td>'.elgg_echo('edufeedr:profile:text:email').':</td>';
			$body .= '<td>'.elgg_view('output/email', array('value' => $vars['participant']->email)).'</td>';
			$body .= '</tr>';
		}
		$body .= '<tr>';
		/*translation:Blog*/
		$body .= '<td>'.elgg_echo('edufeedr:profile:text:blog').':</td>';
		$body .= '<td>'.elgg_view('output/url', array('value' => $vars['participant']->blog, 'target' => '_blank')).'</td>';
		$body .= '</tr>';
		$body .= '</tbody></table>';

        // getting from EduSuckr
		$es = new EduSuckr;

		$posts = $es->getParticipantPosts(array($vars['entity']->guid, $vars['participant']->blog_base));
		if (!($posts && is_array($posts))) {
			$posts = array();
		}
		/*translation:Blog posts*/
		$body .= '<h3>'.elgg_echo('edufeedr:latest:posts').' ('.sizeof($posts).'):</h3>';
		if (is_array($posts) && sizeof($posts)>0) {
			$body .= '<table id="profile_posts"><tbody>';
			foreach ($posts as $post) {
                $body .= "<tr>";
				$body .= '<td>'.date('d.m.Y', $post['date']).'</td>';
				$body .= '<td>'.elgg_view('output/url', array('value' => $vars['url'].'pg/edufeedr/view_post/'.$vars['entity']->getGUID().'/'.$post['id'], 'text' => $post['title'])).'</td>';
                $body .= "</tr>";
			}
			$body .= '</tbody></table>';
		}

		$es = new EduSuckr;
		
		$comments = $es->getParticipantComments($vars['entity']->guid, $vars['participant']->blog_base);
		if (!($comments && is_array($comments))) {
			$comments = array();
		}
		/*translation:Comments*/
		$body .= '<h3>'.elgg_echo('edufeedr:latest:comments').' ('.sizeof($comments).'):</h3>';
        if (is_array($comments) && sizeof($comments)>0) {
			$body .= '<table id="profile_comments"><tbody>';
			foreach ($comments as $comment) {
				$body .= "<tr>";
				$body .= '<td>'.date('d.m.Y', $comment['date']).'</td>';
				$body .= '<td>'.elgg_view('output/url', array('value' => $vars['url'].'pg/edufeedr/view_post/'.$vars['entity']->getGUID().'/'.$comment['post_id'], 'text' => $comment['post_author'])).'</td>';
				$body .= "</tr>";
			}
			$body .= '</tbody></table>';
		}

		$body .= '</div>';//profile ends
		$body .= '</div>';//educourse ends

		echo $body;
    }
?>
