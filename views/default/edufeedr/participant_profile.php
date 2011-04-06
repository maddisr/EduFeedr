<?php
    
    if (isset($vars['entity']) && isset($vars['participant'])) {

		$body = '<div class="educourse">';

		$body .= '<h3>' . $vars['entity']->title . '</h3>';

		$body .= '<div class="profile">';
		$body .= '<h3>'.$vars['participant']->firstname.' '.$vars['participant']->lastname.'</h3>';

		$body .= '<table id="profile_data"><tbody>';
		$avatar_href = "http://gravatar.com/avatar/".md5(trim($vars['participant']->email))."?s=54";
		if ($avatar_data = file_get_contents($avatar_href)) {
			$body .= '<tr>';
			$body .= '<td colspan="2"><img class="profile_avatar" src="data:image/jpeg;base64,'.base64_encode($avatar_data).'" alt="avatar" /></td>';
			$body .= '</tr>';
		}
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

		$posts = array();// TODO Populate me
		/*translation:Blog posts*/
		$body .= '<h3>'.elgg_echo('edufeedr:latest:posts').' ('.sizeof($posts).')</h3>';
		if (is_array($posts) && sizeof($posts)>0) {
			$body .= '<table id="profile_posts"><tbody>';
			foreach ($posts as $post) {
				$body .= '<td>'.date('d.m.Y', $post['date']).'</td>';
				$body .= '<td>'.elgg_view('output/url', array('value' => $vars['url'].'pg/edufeedr/view_post/'.$vars['entity']->getGUID().'/'.$post['id'], 'text' => $post['title'])).'</td>';
			}
			$body .= '</tbody></table>';
		}

        $comments = array();// TODO Populate me
		/*translation:Comments*/
		$body .= '<h3>'.elgg_echo('edufeedr:latest:comments').' ('.sizeof($comments).')</h3>';
        if (is_array($comments) && sizeof($comments)>0) {
			$body .= '<table id="profile_comments"><tbody>';
			foreach ($comments as $comment) {
				$body .= '<td>'.date('d.m.Y', $comment['date']).'</td>';
				$body .= '<td>'.elgg_view('output/url', array('value' => $vars['url'].'pg/edufeedr/view_post/'.$vars['entity']->getGUID().'/'.$comment['post_id'], 'text' => $comment['post_author'])).'</td>';
			}
			$body .= '</tbody></table>';
		}

		$body .= '</div>';//profile ends
		$body .= '</div>';//educourse ends

		echo $body;
    }
?>
