<?php

    if (isset($vars['entity'])) {

        if (!isset($vars['full']) || $vars['full'] == false)
            echo elgg_view('object/educourse_listing', array('entity' => $vars['entity']));

	if (isset($vars['full']) && $vars['full'] == true) {
		$ts = time();
		$token = generate_action_token($ts);
		$body .= "";
		$body .= '<div class="educourse">';

		$body .= elgg_view('edufeedr/educourse_de', array('entity' => $vars['entity']));
	    
        /*translation:Course tag*/
        $body .= '<label>'.elgg_echo('edufeedr:educourse:course:tag').':</label>';
	    $body .= '<div id="edufeedr_educourse_tag">';
	    $body .= elgg_view('output/text', array('value' => $vars['entity']->course_tag));
	    $body .= '</div>';
        /*translation:Course blog*/
        $body .= '<label>'.elgg_echo('edufeedr:educourse:course:blog_url').':</label>';
	    $body .= '<div class="edufeedr_educourse_url">';
	    $body .= elgg_view('output/url', array('href' => $vars['entity']->course_blog, 'target' => '_blank'));
		$body .= '</div>';
		if ($vars['entity']->course_wiki) {
            /*translation:Course website or wiki*/
            $body .= '<label>'.elgg_echo('edufeedr:educourse:course:wiki_url').':</label>';
	        $body .= '<div class="edufeedr_educourse_url">';
	        $body .= elgg_view('output/url', array('href' => $vars['entity']->course_wiki, 'target' => '_blank'));
		    $body .= '</div>';
		}
        /*translation:Enrollment deadline*/
        $body .= '<label>'.elgg_echo('edufeedr:educourse:enrollment:deadline').':</label>';
	    $body .= '<div class="edufeedr_date">';
	    $body .= elgg_view('output/calendar', array('value' => $vars['entity']->signup_deadline));
	    $body .= '</div>';
        /*translation:Starting date*/
        $body .= '<label>'.elgg_echo('edufeedr:educourse:starting:date').':</label>';
	    $body .= '<div class="edufeedr_date">';
	    $body .= elgg_view('output/calendar', array('value' => $vars['entity']->course_starting_date));
	    $body .= '</div';
        /*translation:Ending date*/
        $body .= '<label>'.elgg_echo('edufeedr:educourse:ending:date').':</label>';
	    $body .= '<div class="edufeedr_date">';
	    $body .= elgg_view('output/calendar', array('value' => $vars['entity']->course_ending_date));
	    $body .= '</div>';

		$body .= '<div class="clearfloat" style="height:20px;"></div>';
		// Display facilitators
		$body .= elgg_view('edufeedr/educourse_facilitators', array('entity' => $vars['entity'], 'type' => 'view'));

		// Action footer
		if ($vars['entity']->canEdit() && edufeedrCanEditEducourse($vars['entity'])) {
			// Deal with any floats
			$body .= '<div class="clearfloat"></div>';
			$body .= '<div id="edufeedr_footer_for_facilitator">';
			/*translation:Edit course info*/
			$educourse_edit_echo = elgg_echo('edufeedr:action:edit_course_info');
			$body .= '<a href="' . $vars['url'] . 'pg/edufeedr/edit_educourse/' . $vars['entity']->getGUID() . '" title="' . $educourse_edit_echo . '">' . $educourse_edit_echo . '</a>';
			if (edufeedrCanManageEducourse($vars['entity'])) {
				$body .= ' | ';
				$body .= elgg_view('output/confirmlink', array(
					'href' => $vars['url'] . 'action/edufeedr/delete_educourse?educourse=' . $vars['entity']->getGUID(),
					/*translation:Delete course*/
					'text' => elgg_echo('edufeedr:action:delete_course'),
					'confirm' => elgg_echo('deleteconfirm'),
					'class' => 'edufeedr_action_delete_or_remove'
                    )
				);
			}
		    $body .= '</div>';
		}
		$body .= '</div>';

		echo $body;

	}
    }
?>
