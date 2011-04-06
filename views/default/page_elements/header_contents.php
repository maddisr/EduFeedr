<?php
/**
 * Elgg header contents
 * This file holds the header output that a user will see
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 **/

?>

<div id="page_container">
<div id="page_wrapper">

<div id="layout_header">
<div id="wrapper_header">
	<!-- display the page title -->
	<div id="edufeedr_site_logo"><a href="<?php echo $vars['url']; ?>" title="<?php echo $vars['config']->sitename; ?>"><img src="<?php echo $vars['url']; ?>mod/edufeedr/views/default/graphics/logo.png" alt="logo" /> <span class="edufeedr_header_name"><!--<?php echo $vars['config']->sitename; ?>--></span></a>
		<div id="beta_link">Beta</div>
	</div>
</div><!-- /#wrapper_header -->
</div><!-- /#layout_header -->
