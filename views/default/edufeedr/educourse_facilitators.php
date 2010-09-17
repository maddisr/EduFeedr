<?php

    if (isset($vars['entity'])) {
        $ts = time();
		$token = generate_action_token($ts);
		$owner = $vars['entity']->getOwnerEntity();
		if (!isset($vars['type']) || ($vars['type'] != 'view' && $vars['type'] != 'edit')) {
			$type = 'view';
		} else {
			$type = $vars['type'];
		}
		$can_manage = false;
		if ($vars['entity']->canEdit() && edufeedrCanManageEducourse($vars['entity'])) {
			$can_manage = true;
		}

		$body = "";
		$body .= '<div class="edufeedr_course_teachers" id="edufeedr_course_facilitators">';
		/*translation:Facilitators*/
		$body .= '<label>' . elgg_echo('edufeedr:label:course_facilitators') . '</label>';

		$body .= elgg_view('edufeedr/singles/educourse_facilitator', array('educourse' => $vars['entity'], 'facilitator' => $owner, 'type' => $type));

		$facilitators = edufeedrCourseFacilitators($vars['entity']);

		if ($facilitators && is_array($facilitators) && sizeof($facilitators) > 0) {
			foreach ($facilitators as $facilitator) {
				$facilitator_entity = get_entity($facilitator->user_guid);
				$body .= elgg_view('edufeedr/singles/educourse_facilitator', array('educourse' => $vars['entity'], 'facilitator' => $facilitator_entity, 'type' => $type));
			}
		}

		if ($can_manage && $type == 'edit') {
			// This AJAX can be also used by other subviews
			$body .= <<<EOT
			<script type="text/javascript">
			function edufeedrAddFacilitator(educourse) {
				 var facilitator_name = jQuery("#facilitator_name").attr('value');
                 var data = "facilitator=" + facilitator_name +"&educourse=" + educourse + "&__elgg_ts=$ts&__elgg_token=$token";
                  
				 jQuery.ajax({
					 url: "{$CONFIG->wwwroot}action/edufeedr/add_facilitator",
					 type: "POST",
                     dataType: "json",
                     data: data,
					 success: function(data) {
						 if (data['success'] == 'true') {
                              jQuery('#edufeedr_course_facilitators').append(data['facilitator']);
                         }
                     },
                     error: function(data) {}
                 });
			}
            
			function edufeedrRemoveFacilitator(facilitator, educourse) {
				var data = "facilitator=" + facilitator + "&educourse=" + educourse + "&__elgg_ts=$ts&__elgg_token=$token";
				jQuery.ajax({
					url: "{$CONFIG->wwwroot}action/edufeedr/remove_facilitator",
                    type: "POST",
					data: data,
                    dataType: "json",
					success: function(data) {
						if (data['success'] == 'true') {
                        jQuery('#facilitator_' + facilitator).remove();
						}
                    },
                    error: function(data) {}
                });
            }
            </script>
EOT;
			/*translation:Type the username of another user to add an additional facilitator to this course.*/
			$body .= '</div>'; // END MAIN FACILITATORS
			/*translation:Type the username of another user to add and additional facilitator to this course.*/
			$body .= '<br /><div>' . elgg_echo('edufeedr:text:add_facilitator') . '</div>';
			$body .= elgg_view('input/text', array('value' => '', 'internalname' => 'facilitator_name', 'internalid' => 'facilitator_name', 'class' => 'input_text_small'));
			/*translation:Add*/
			$body .= ' <a href="#" onclick="edufeedrAddFacilitator(\''.$vars['entity']->getGUID().'\'); return false;">'.elgg_echo('edufeedr:href:button:add').'</a>';
		}

		echo $body;
	}
?>
