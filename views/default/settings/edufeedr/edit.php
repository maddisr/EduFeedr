<?php

    echo '<div>';

    echo '<div>';

	echo '<label>';
	/*translation:Write a full path to cache directory for SimplePie*/
	echo elgg_echo('edufeedr:setting:simplepie:cache:label');
	echo '</label>';

	echo '<input type="text" name="params[edufeedr_simplepie_cache]" size="70" value="' . $vars['entity']->edufeedr_simplepie_cache . '" />';

    echo '</div>';

    echo '<div>';

	echo '<label>';
	/*translation:Write WSDL URL for EduSuckr*/
	echo elgg_echo('edufeedr:setting:wsdl:url:edusuckr');
	echo '</label>';

	echo '<input type="text" name="params[edusuckr_wsdl_url]" size="70" value="' . $vars['entity']->edusuckr_wsdl_url . '" />';

	echo '</div>';
	echo '<div>';

	echo '<label>';
	/*translation:WSDL Credentials for EduSuckr*/
	echo elgg_echo('edufeedr:setting:wsdl:credentials:edusuckr');
	echo '</label>';
    echo '<br />';
    /*translation:Username*/
	echo elgg_echo('edufeedr:setting:wsdl:credentials:nik:edusuckr');
	echo ': <input type="text" name="params[edusuckr_wsdl_nik]" size="20" value="' . $vars['entity']->edusuckr_wsdl_nik . '" />';
	/*translation:Password*/
	echo elgg_echo('edufeedr:setting:wsdl:credentials:pwd:edusuckr');
	echo ': <input type="password" name="params[edusuckr_wsdl_pwd]" size="20" value="' . $vars['entity']->edusuckr_wsdl_pwd . '" />';

	echo '</div>';
	echo '<div>';
	/*translation:EduFeedr FAQ*/
	echo '<label>' . elgg_echo('edufeedr:setting:faq') . '</label><br />';
	echo elgg_view('input/plaintext', array('internalname' => 'params[edufeedr_faq]', 'value' => $vars['entity']->edufeedr_faq));
	echo '</div>';

    echo '</div>';

?>
