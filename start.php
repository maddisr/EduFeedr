<?php

    // Includes
    require_once('api.php');
    require_once('thirdparty/SimplePie/simplepie.inc');
    require_once('thirdparty/SimplePie/idn/idna_convert.class.php');
    require_once('thirdparty/nusoap/nusoap.php');
    require_once('edusuckr.php');

    // Initializer
    function edufeedr_init() {
	global $CONFIG;

	// Add to main menu
	/*translation:EduFeedr*/
	add_menu(elgg_echo('edufeedr:menu:edufeedr'), $CONFIG->wwwroot . 'pg/edufeedr/index');

	// Register page_handler
	register_page_handler('edufeedr', 'edufeedr_page_handler');

	// Add custom css
	elgg_extend_view('css', 'edufeedr/css');

	// Register types
	register_entity_type('object', 'educourse');

	// Custom urls for entities
	register_entity_url_handler('educourse_url', 'object', 'educourse');

	// Custom permission hook
	register_plugin_hook('permissions_check', 'object', 'educourse_permission_hook');

	// Init custom tables installation
	// XXX DISABLE THIS FUNCTIONALITY FOR PRODUCTION INSTANCE
	edufeedr_install_custom_tables();

	// Replace the default index page
	register_plugin_hook('index', 'system', 'edufeedr_custom_index');
    }

    // Page setup
    function edufeedr_pagesetup() {
        global $CONFIG;

	    if (get_context() == 'edufeedr' || (get_context() == 'main' && isloggedin())) {
	        /*translation:All courses*/
		    add_submenu_item(elgg_echo('edufeedr:submenu:all:courses'), $CONFIG->wwwroot . 'pg/edufeedr/index');
		    /*translation:Open courses*/
		    add_submenu_item(elgg_echo('edufeedr:submenu:open:courses'), $CONFIG->wwwroot . 'pg/edufeedr/open');
		    /*translation:Ongoing courses*/
		    add_submenu_item(elgg_echo('edufeedr:submenu:ongoing:courses'), $CONFIG->wwwroot . 'pg/edufeedr/ongoing');
		    /*translation:Ended courses*/
		    add_submenu_item(elgg_echo('edufeedr:submenu:ended:courses'), $CONFIG->wwwroot . 'pg/edufeedr/ended');
	        if (isloggedin()) {
                /*translation:Add course*/
                add_submenu_item(elgg_echo('edufeedr:submenu:add:educourse'), $CONFIG->wwwroot . 'pg/edufeedr/add_educourse');
		    }
		}
		if (get_context() == 'edufeedr' || get_context() == 'main') {
            if (get_plugin_setting('edufeedr_faq')) {
		        /*translation:EduFeedr FAQ*/
				add_submenu_item(elgg_echo('edufeedr:submenu:faq'), $CONFIG->wwwroot . 'pg/edufeedr/faq', 'edufeedrother');
			}
		}
    }

    // Custom url for educourse
    function educourse_url($educourse) {
        global $CONFIG;
	return $CONFIG->wwwroot . 'pg/edufeedr/view_educourse/' . $educourse->getGUID();
    }

    // Page handler
    function edufeedr_page_handler($page) {
        global $CONFIG;

	if($page[0]) {
	    switch ($page[0]) {
		case "index":
			include($CONFIG->pluginspath . 'edufeedr/index.php');
			break;
		case "open":
			include($CONFIG->pluginspath . 'edufeedr/open_courses.php');
			break;
		case "ongoing":
			include($CONFIG->pluginspath . 'edufeedr/ongoing_courses.php');
			break;
		case "ended":
			include($CONFIG->pluginspath . 'edufeedr/ended_courses.php');
			break;
		case "add_educourse":
			include($CONFIG->pluginspath . 'edufeedr/add_educourse.php');
			break;
		case "edit_educourse":
			set_input('educourse', $page[1]);
			include($CONFIG->pluginspath . 'edufeedr/edit_educourse.php');
			break;
		case "view_educourse":
			set_input('educourse', $page[1]);
			include($CONFIG->pluginspath . 'edufeedr/view_educourse.php');
			break;
		case "view_post":
			set_input('educourse', $page[1]);
			set_input('post_id', $page[2]);
			include($CONFIG->pluginspath . 'edufeedr/view_post.php');
			break;
		case"view_hidden":
			set_input('educourse', $page[1]);
			set_input('hidden_type', $page[2]);
			include($CONFIG->pluginspath . 'edufeedr/view_hidden.php');
			break;
		case "join":
			set_input('educourse', $page[1]);
			include($CONFIG->pluginspath . 'edufeedr/join_educourse.php');
			break;
		case "edit_participant":
			set_input('educourse', $page[1]);
			set_input('participant_id', $page[2]);
			include($CONFIG->pluginspath . 'edufeedr/edit_participant.php');
			break;
		case "add_assignment":
			set_input('educourse', $page[1]);
			include($CONFIG->pluginspath . 'edufeedr/add_assignment.php');
			break;
		case "edit_assignment":
			set_input('educourse', $page[1]);
			set_input('assignment_id', $page[2]);
			include($CONFIG->pluginspath . 'edufeedr/edit_assignment.php');
			break;
		case "faq":
			include($CONFIG->pluginspath . 'edufeedr/edufeedr_faq.php');
			break;
		default:
			include($CONFIG->pluginspath . 'edufeedr/index.php');
			break;
		}
	} else {
		include($CONFIG->pluginspath . 'edufeedr/index.php');
	}
	}

	// Permission override for course sign-up by anonymous
	function educourse_permission_hook($hook_name, $entity_type, $return_value, $parameters) {
		$entity = $parameters['entity'];

		// XXX Not sure if this is needed any more, as now participants are situated in standalone table that is unaffected by Elgg permission system
		if ($entity->getSubtype() == 'educourse')
			return true;
	}

	// Create custom database tables
	function edufeedr_install_custom_tables() {
		run_sql_script(dirname(dirname(dirname(__FILE__))) . '/mod/edufeedr/edufeedr_tables.sql');
	}

	function edufeedr_custom_index() {
		if (!include_once(dirname(__FILE__) . '/edufeedr_custom_index.php')) return false;
		return true;
	}

    // Initialize
    register_elgg_event_handler('init', 'system', 'edufeedr_init');
    // Register page_setup
    register_elgg_event_handler('pagesetup', 'system', 'edufeedr_pagesetup');
    // Actions
    register_action('login', true, $CONFIG->pluginspath . 'edufeedr/actions/login.php');
    register_action('edufeedr/add_educourse', false, $CONFIG->pluginspath . 'edufeedr/actions/add_educourse.php');
    register_action('edufeedr/edit_educourse', false, $CONFIG->pluginspath . 'edufeedr/actions/edit_educourse.php');
	register_action('edufeedr/delete_educourse', false, $CONFIG->pluginspath . 'edufeedr/actions/delete_educourse.php');
	register_action('edufeedr/join_educourse', true, $CONFIG->pluginspath . 'edufeedr/actions/join_educourse.php');
	register_action('edufeedr/remove_participant', false, $CONFIG->pluginspath . 'edufeedr/actions/remove_participant.php');
	register_action('edufeedr/download_educourse_vcard', false, $CONFIG->pluginspath . 'edufeedr/actions/download_educourse_vcard.php');
	register_action('edufeedr/download_educourse_opml', true, $CONFIG->pluginspath . 'edufeedr/actions/download_educourse_opml.php');
	register_action('edufeedr/download_educourse_sn_tsv', true, $CONFIG->pluginspath . 'edufeedr/actions/download_educourse_sn_tsv.php');
	register_action('edufeedr/edit_participant', false, $CONFIG->pluginspath . 'edufeedr/actions/edit_participant.php');
	register_action('edufeedr/add_assignment', false, $CONFIG->pluginspath . 'edufeedr/actions/add_assignment.php');
	register_action('edufeedr/edit_assignment', false, $CONFIG->pluginspath . 'edufeedr/actions/edit_assignment.php');
	register_action('edufeedr/remove_assignment', false, $CONFIG->pluginspath . 'edufeedr/actions/remove_assignment.php');
	register_action('edufeedr/hide_post', false, $CONFIG->pluginspath . 'edufeedr/actions/hide_post.php');
	register_action('edufeedr/hide_comment', false, $CONFIG->pluginspath . 'edufeedr/actions/hide_comment.php');
	register_action('edufeedr/unhide_post', false, $CONFIG->pluginspath . 'edufeedr/actions/unhide_post.php');
	register_action('edufeedr/unhide_comment', false, $CONFIG->pluginspath . 'edufeedr/actions/unhide_comment.php');
	register_action('edufeedr/add_facilitator', false, $CONFIG->pluginspath . 'edufeedr/actions/ajax/add_facilitator.php');
	register_action('edufeedr/remove_facilitator', false, $CONFIG->pluginspath . 'edufeedr/actions/ajax/remove_facilitator.php');
?>
