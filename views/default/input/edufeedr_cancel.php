<?php

    $href = $vars['href'];
    $value = $vars['value'];

	$script = 'onClick="window.location = \'' . $vars['href'] . '\';return false;"';

	echo elgg_view('input/submit', array('internalname' => 'edufeedr_cancel_button', 'js' => $script, 'value' => $vars['value'], 'class' => 'submit_button edufeedr_cancel_button'));
?>
