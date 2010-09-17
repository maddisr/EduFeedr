<?php
    
    if (isset($vars['entity'])) {
		$ts = time();
		$token = generate_action_token($ts);

		$body = '<div class="educourse">';

		$body .= '<h3 class="edufeedr_similar_width">' . $vars['entity']->title . '</h3>';
		if ($vars['entity']->canEdit() && edufeedrCanEditEducourse($vars['entity'])) {
			$body .= '<div class="edufeedr_educourse_assignment_controls edufeedr_action_header">';
			/*translation:+ Add assignment*/
			$body .= '<a href="' . $vars['url'] . 'pg/edufeedr/add_assignment/' . $vars['entity']->getGUID() . '" class="edufeedr_action_add">' . elgg_echo('edufeedr:action:control:add:assignment') . '</a>';
			$body .= '</div>';
		}

		$assignments = edufeedrGetCourseAssignments($vars['entity']->getGUID());

		if ($assignments && is_array($assignments)) {

			uasort($assignments, '__edufeedr_assignments_sort_cmp');

			$i = 0;
			$max = sizeof($assignments);
			foreach ($assignments as $key => $assignment) {
				$body .= '<div class="edufeedr_assignment_body">';
				$i++;

				$assignment_controls = "";
                if ($vars['entity']->canEdit() && edufeedrCanEditEducourse($vars['entity'])) {
					$assignment_controls .= '<div class="assignment_action_controls">';
					/*translation:Edit*/
					$assignment_controls .= '<a href="' . $vars['url'] . 'pg/edufeedr/edit_assignment/' . $vars['entity']->getGUID() . '/' . $assignment->id . '">' . elgg_echo('edufeedr:action:control:edit:assignment') . '</a>';
					$assignment_controls .= ' | ';
					$assignment_controls .= elgg_view('output/confirmlink', array(
						'href' => $vars['url'] . 'action/edufeedr/remove_assignment?educourse=' . $vars['entity']->getGUID() . '&assignment_number=' . $assignment->id . '&__elgg_ts=' . $ts . '&__elgg_token=' . $token,
						/*translation:Remove*/
						'text' => elgg_echo('edufeedr:action:control:remove:assignment'),
						/*translation:Are you sure you want to remove this assignment?*/
						'confirm' => elgg_echo('edufeedr:action:control:remove:remove:assignment:confirmation'),
						'class' => 'edufeedr_action_delete_or_remove'
						));
					$assignment_controls .= '</div>';
				}


				/*translation:Assignment %s*/
				$body .= '<div class="edufeedr_assignment_label">' . sprintf(elgg_echo('edufeedr:assignment:label:numbered'), $i) . '</div>';
				$body .= '<div class="edufeedr_assignment_title"><a href="' . $assignment->blog_post_url . '" title="' . $assignment->title . '" target="_blank">' . $assignment->title . '</a></div>';
				$body .= '<div class="edufeedr_assignment_deadline">' . elgg_view('output/calendar', array('value' => (int) $assignment->deadline)). '</div>';
				$body .= '<div class="edufeedr_assignment_description">' . $assignment->description . '</div>';
				$body .= $assignment_controls;

				$body .= '</div>';//assignment body ends
			}
		}

		$body .= '</div>';//educourse ends

		echo $body;
    }
?>
