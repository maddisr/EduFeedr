<?php

    if (isset($vars['educourse']) && isset($vars['facilitator'])) {

		$can_manage = false;
		if($vars['educourse']->canEdit() && edufeedrCanManageEducourse($vars['educourse'])) {
			$can_manage = true;
		}
		$type = 'view';
		if (isset($vars['type']) && ($vars['type'] == 'view' || $vars['type'] == 'edit')) {
			$type = $vars['type'];
		}
		$show_delete = true;
		if ($vars['educourse']->getOwner() == $vars['facilitator']->getGUID()) {
			$show_delete = false;
		}
		$body = '';

		$body .= '<div id="facilitator_'.$vars['facilitator']->getGUID().'">';
		$body .= $vars['facilitator']->name . ' / ';
		$body .= elgg_view('output/email', array('value' => $vars['facilitator']->email));
		if ($type == 'edit' && $show_delete && $can_manage) {
			/*translation:remove*/
			$body .= ' / <a class="edufeedr_action_delete_or_remove" href="#" onclick="edufeedrRemoveFacilitator(\''.$vars['facilitator']->getGUID().'\', \''.$vars['educourse']->getGUID().'\'); return false;">'.elgg_echo('edufeedr:action:remove_facilitator').'</a>';
		}
		$body .= '</div>';

		echo $body;
    }
?>
