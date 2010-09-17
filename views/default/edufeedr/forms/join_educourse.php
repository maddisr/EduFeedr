<?php

    if (isset($vars['entity']) && $vars['entity']->getSubtype() == 'educourse') {

		if (isset($vars['participant'])) {
			$action = 'edufeedr/edit_participant';
			$participant = $vars['participant'];
			$firstname = $participant->firstname;
			$lastname = $participant->lastname;
			$email = $participant->email;
			$blog = $participant->blog;
			$blogger = $participant->blogger;
		} else {
            $action = 'edufeedr/join_educourse';
		    $firstname = '';
		    $lastname = '';
		    $email = '';
		    $blog = 'http://';
		    $blogger = '';
		}

		if (isset($vars['join_firstname']) && !empty($vars['join_firstname']))
			$firstname = $vars['join_firstname'];
		if (isset($vars['join_lastname']) && !empty($vars['join_lastname']))
			$lastname = $vars['join_lastname'];
		if (isset($vars['join_email']) && !empty($vars['join_email']))
			$email = $vars['join_email'];
		if (isset($vars['join_blog']) && !empty($vars['join_blog']))
			$blog = $vars['join_blog'];
	    if (isset($vars['join_blogger']) && !empty($vars['join_blogger']))
			$blogger = $vars['join_blogger'];

		// Labels and inputs
		/*translation:First name*/
		$firstname_label = elgg_echo('edufeedr:label:firstname');
		$firstname_input = elgg_view('input/text', array('internalname' => 'join_firstname', 'value' => $firstname));

		/*translation:Last name*/
		$lastname_label = elgg_echo('edufeedr:label:lastname');
		$lastname_input = elgg_view('input/text', array('internalname' => 'join_lastname', 'value' => $lastname));

		/*translation:E-mail*/
		$email_label = elgg_echo('edufeedr:label:email');
		$email_input = elgg_view('input/email', array('internalname' => 'join_email', 'value' => $email));

		/*translation:Blog*/
		$blog_label = elgg_echo('edufeedr:label:blog');
		$blog_input = elgg_view('input/url', array('internalname' => 'join_blog', 'value' => $blog));
		$blogger_part = "";
		if (!strcmp($action,'edufeedr/edit_participant')) {
			$blogger_part .= '<p>';
		    /*translation:Blogger (if any)*/
		    $blogger_part .= '<label>' . elgg_echo('edufeedr:label:blogger') . '</label><br />';
			$blogger_part .= elgg_view('input/url', array('internalname' => 'join_blogger', 'value' => $blogger));
			$blogger_part .= '</p>';
		}

		/*translation:Sign up*/
		$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('edufeedr:submit:signup')));
        // Cancel button
        $cancel_href = $vars['url'] . 'pg/edufeedr/view_educourse/' . $vars['entity']->getGUID();
        if (isset($vars['participant'])) {
            $cancel_href = $vars['url'] . 'pg/edufeedr/view_educourse/' . $vars['entity']->getGUID() . '?filter=participants';
        }
        $cancel_input = elgg_view('input/edufeedr_cancel', array('value' => elgg_echo('cancel'), 'href' => $cancel_href));

		$entity_hidden = elgg_view('input/hidden', array('internalname' => 'educourse', 'value' => $vars['entity']->getGUID()));
		if (isset($vars['participant_id']))
			$entity_hidden .= elgg_view('input/hidden', array('internalname' => 'participant_id', 'value' => $vars['participant_id']));
		$field_required = elgg_view('input/edufeedr_required');
		// Captcha
		$captcha_input = "";
		if (!isloggedin() && is_plugin_enabled('captcha')) {
			$captcha_input .= elgg_view('input/captcha');
		}

		$form_body = <<<EOT
		<p>
            <label>$firstname_label</label>$field_required<br />
            $firstname_input
		</p>
        <p>
            <label>$lastname_label</label>$field_required<br />
			$lastname_input
	    </p>
		<p>
            <label>$email_label</label>$field_required<br />
            $email_input
		</p>
        <p>
            <label>$blog_label</label>$field_required<br />
            $blog_input
		</p>
            $blogger_part
            $captcha_input
		<p>
			$entity_hidden
			$submit_input
			$cancel_input
        </p>
EOT;

		echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body));
	}

?>
