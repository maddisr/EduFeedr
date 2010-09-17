<?php
    
    if (isset($vars['entity'])) {

		$body = "";
		$body .= '<table id="edufeedr_educourse_de_table">';
		$body .= '<tr></td>';
		$body .= '<td><div id="edufeedr_educourse_description">';
		$body .= '<h3>' . $vars['entity']->title . '</h3>';
	    $body .= elgg_view('output/longtext', array('value' => $vars['entity']->description));
		$body .= '</div>'; // edufeedr_educourse_description
		$body .= '</td><td class="edufeedr_blogs_table_second_td">';
		$body .= '<div id="edufeedr_educourse_enrollment">';
		if (edufeedrIsEducourseOpen($vars['entity']) || edufeedrCanEditEducourse($vars['entity'])) {
			/*translation:Enrollment for this course is open until %s*/
			$body .= '<div>' . sprintf(elgg_echo('edufeedr:educourse:enrollment:active:text'), elgg_view('output/calendar', array('value'=> $vars['entity']->signup_deadline))) . '</div>';
			/*translation:Enroll to the course!*/
			$body .= '<a href="' . $vars['url'] . 'pg/edufeedr/join/' . $vars['entity']->getGUID() . '" class="edufeedr_enrollment_link">' . elgg_echo('edufeedr:submit:enroll:to:the:course') . '</a>';
		} else {
			/*translation:the facilitator*/
			$contact_facilitator_link = '<a href="mailto:' . $vars['entity']->getOwnerEntity()->email . '">' . elgg_echo('edufeedr:enroll:contact:facilitator') . '</a>';
			/*translation:Enrollment for this course ended on %s.<br />In case of questions, please contact %s.*/
			$body .= sprintf(elgg_echo('edufeedr:educourse:enrollment:inactive:text'), elgg_view('output/calendar', array('value' => $vars['entity']->signup_deadline)), $contact_facilitator_link);
		}
		$body .= '</div>'; // edufeedr_educourse_enrollment
		$body .= '</td></tr></table>'; // edufeedr_educourse_de_table

		echo $body;
    }
?>
