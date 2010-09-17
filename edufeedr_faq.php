<?php

    // Load engine
    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');

    $body .= '';
    /*translation:EduFeedr FAQ*/
	$body .= elgg_view_title(elgg_echo('edufeedr:title:faq'));
	$faq = get_plugin_setting('edufeedr_faq', 'edufeedr');
	if ($faq) {
		$body .= elgg_view('output/longtext', array('value' => $faq));
	}
	unset($faq);
	$content = elgg_view_layout('two_column_left_sidebar', '', $body);
	unset($body);

    page_draw(null, $content);
?>
