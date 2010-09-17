<?php
    
    if (isset($vars['entity'])) {
		$body = "";

		$body .= '<div class="educourse">';

		$body .= '<h3>' . $vars['entity']->title . '</h3>';

		$body .= '<div id="edufeedr_educourse_connections">';
		if (get_plugin_setting('edusuckr_wsdl_url', 'edufeedr')){
            $wsdl_url = get_plugin_setting('edusuckr_wsdl_url', 'edufeedr');
            $visualizer_url = str_replace("ws.php?wsdl","visualizer.php?guid=".$vars['entity']->guid,$wsdl_url);
		    $body .= '<div><iframe src ="'.$visualizer_url.'" width="725px" height="420px"><p>Your browser does not support iframes.</p></iframe></div>';
		}

		$body .= '<div id="edufeedr_educourse_sn_downloads">';
		/*translation:Downloads*/
		$body .= '<label>'.elgg_echo('edufeedr:label:course:downloads').'</label>';
		$body .= '<ul>';
		$action_url = $vars['url'] . 'action/edufeedr/download_educourse_sn_tsv?educourse=' . $vars['entity']->getGUID();
		/*translation:Tab separated social network data*/
		$body .= '<li><a href="'.elgg_add_action_tokens_to_url($action_url).'">'.elgg_echo('edufeedr:action:download_sn_tsv_data_file').'</a>';
		/*translation:(can be used in %s)*/
		$body .= ' '.sprintf(elgg_echo('edufeedr:explanation:can_be_used_in'), '<a href="http://manyeyes.alphaworks.ibm.com/" target="_blank">Many Eyes</a>') . '</li>';
		$body .= '</ul>';
		$body .= '</div>';
		$body .= '</div>';

		$body .= '</div>';//educourse ends

		echo $body;
		unset($action_url);
		unset($body);
    }
?>
