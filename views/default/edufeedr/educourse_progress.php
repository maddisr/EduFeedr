<?php
    
    if (isset($vars['entity'])) {
		$ts = time();
		$token = generate_action_token($ts);
		$body = "";
		$body .= '<div class="educourse">';
		$body .= '<h3>' . $vars['entity']->title . '</h3>';		
        $es = new EduSuckr;
        $body_data = unserialize($es->getProgressTable($vars['entity']->getGUID()));   
        $body .= '<table id="educourse_progress_table">';
	    $body .= '<tbody>';
        foreach ($body_data as $participant_data) {
                $participant = $participant_data['participant'];
                $assignments_results = $participant_data['assignment'];
				$body .= '<tr>';
				$body .= '<td id="participant">';
				$body .= $participant->firstname . ' ' . $participant->lastname;
				$body .= '</td>';
				$body .= '<td>';
				if ($assignments_results) {
					foreach ($assignments_results as $assignment_result) {
						if ($assignment_result['state'] == 0)
							$body .= '<img src="' . $vars['url'] . 'mod/edufeedr/views/default/graphics/assignment_not_done.png" alt="' . $assignment_result['state'] . '" />';
						else if ($assignment_result['state'] == 1)
							$body .= '<a href="' . $vars['url'] . 'pg/edufeedr/view_post/'. $vars['entity']->getGUID() . '/' . $assignment_result['id'] . '" title="' . $assignment_result['title'] . '"><img src="' . $vars['url'] . 'mod/edufeedr/views/default/graphics/assignment_time_frame.png" alt="' . $assignment_result['state']  . '" /></a>';
						else if ($assignment_result['state'] == 2)
						$body .= '<a href="' . $vars['url'] . 'pg/edufeedr/view_post/' . $vars['entity']->getGUID() . '/' . $assignment_result['id'] . '" title="' . $assignment_result['title'] . '"><img src="' . $vars['url'] . 'mod/edufeedr/views/default/graphics/assignment_done.png" alt="' . $assignment_result['state']  . '" /></a>';
				}
			}
			$body .= '</td>';
	        $body .= '</tr>';
	    }
			$body .= '</tbody>';
			$body .= '</table>';

		$body .= '</div>';//educourse ends

		echo $body;
    }
?>
