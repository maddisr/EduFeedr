<?php

    if (isset($vars['participant']) && isset($vars['entity'])) {
	    echo '<li>';
		echo $vars['participant']->firstname . ' ' . $vars['participant']->lastname . ' / ';
		if (edufeedrCanEditEducourse($vars['entity']))
			echo elgg_view('output/email', array('value' => $vars['participant']->email)) . ' / ';
		echo elgg_view('output/url', array('href' => $vars['participant']->blog, 'target' => '_blank'));

		if (edufeedrCanEditEducourse($vars['entity'])) {
			echo ' / ';
			/*translation:Edit*/
			echo '<a href="' . $vars['url'] . 'pg/edufeedr/edit_participant/' . $vars['entity']->getGUID() . '/' . $vars['participant']->id . '">' . elgg_echo('edufeedr:course:edit:participant') . '</a>';
			echo '&nbsp;&nbsp;';
			echo elgg_view('output/confirmlink', array(
				'href' => $vars['url'] . 'action/edufeedr/remove_participant?educourse=' . $vars['entity']->getGUID() . '&participant_number=' . $vars['participant']->id,
				/*translation:Remove*/
				'text' => elgg_echo('edufeedr:course:remove:participant'),
				/*translation:Are you sure you want to remove participant %s?*/
				'confirm' => sprintf(elgg_echo('edufeedr:course:remove:participant:confirmation'), $vars['participant']->firstname . ' ' . $vars['participant']->lastname),
				'class' => 'edufeedr_action_delete_or_remove'
				)
			);
		}
		echo '</li>';
	}
?>
