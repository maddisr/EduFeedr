<?php
    
    if (isset($vars['entity'])) {
		$body = "";
		$ts = time();
		$token = generate_action_token($ts);

		$body .= '<div class="educourse">';

		$body .= elgg_view('edufeedr/educourse_de', array('entity' => $vars['entity']));

		// Course patricipants
		$participants = edufeedrGetCourseParticipants($vars['entity']->getGUID());

        if (is_array($participants)) {

            uasort($participants, '__edufeedr_users_sort_cmp');
            $body .= '<div class="edufeedr_course_participants">';
            /*translation:Course participants*/
            $body .= '<label>' . elgg_echo('edufeedr:label:course:participants') . ':</label>';
            $body .= '<ol>';            foreach ($participants as $participant) {                $body .= elgg_view('edufeedr/singles/educourse_participant', array('participant' => $participant, 'entity' => $vars['entity']));
            }
            $body .= '</ol>';
            $body .= '</div>';
			$blogroll = elgg_view('edufeedr/copiable_blogroll', array('participants' => $participants));

			$body .= '<div id="edufeedr_educourse_secondary_actioncontrols">';
			/*translation:Downloads*/
			$body .= '<label>' . elgg_echo('edufeedr:label:course:downloads') . '</label>';
			$body .= '<ul>';
			// vCard is only available for course owner and facilitators
			if ($vars['entity']->canEdit() && edufeedrCanEditEducourse($vars['entity'])) {
            /*translation:vCard file with Address Book contacts*/
				$body .= '<li><a href="' . $vars['url'] . 'action/edufeedr/download_educourse_vcard?educourse=' . $vars['entity']->getGUID() . '&__elgg_ts='. $ts . '&__elgg_token=' . $token .'">' . elgg_echo('edufeedr:action:download:vcard:file') . '</a></li>';
			}
            /*translation:OPML file with RSS feeds for blog posts*/
            $body .= '<li><a href="' . $vars['url'] . 'action/edufeedr/download_educourse_opml?educourse=' . $vars['entity']->getGUID() . '&type=posts&__elgg_ts=' . $ts . '&__elgg_token=' . $token . '">' . elgg_echo('edufeedr:action:download:posts:opml:file') . '</a></li>';
        /*translation:OPML file with RSS feeds for blog comments*/
            $body .= '<li><a href="' . $vars['url'] . 'action/edufeedr/download_educourse_opml?educourse=' . $vars['entity']->getGUID() . '&type=comments&__elgg_ts=' . $ts . '&__elgg_token=' . $token . '">' . elgg_echo('edufeedr:action:download:comments:opml:file') . '</a></li>';
			$body .= '</ul>';
            $body .= '</div>';

            $body .= '<div class="educourse_course_blogroll">';
            /*translation:Blogroll*/
            $body .= '<label>' . elgg_echo('edufeedr:label:blogroll') . '</label><br />';
            $body .= '<input type="text" class="input-text" name="educourse_blogroll" id="educourse_blogroll" value="' . htmlentities($blogroll, ENT_QUOTES, 'UTF-8') . '" onclick="$(\'#educourse_blogroll\').focus().select()" />';
            $body .= '</div>';
		}

		// Display facilitators
		$body .= elgg_view('edufeedr/educourse_facilitators', array('entity' => $vars['entity'], 'type' => 'view'));

		$body .= '</div>';//educourse ends

		echo $body;
    }
?>
