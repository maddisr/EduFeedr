<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

	// Get input data
	$facilitator = get_input('facilitator');// USERNAME
	$educourse_guid = (int) get_input('educourse');

	$educourse = get_entity($educourse_guid);
	$facilitator = get_user_by_username($facilitator);

	$result = array('success' => 'false', 'facilitator' => '');

	// Check if data is provided
	if ($facilitator && $educourse->getSubtype() == 'educourse' && edufeedrCanManageEducourse($educourse)) {
		// Check if that facilitator has already been added to the course
		$added_check = get_data("SELECT id from {$CONFIG->dbprefix}edufeedr_course_facilitators WHERE course_guid = {$educourse->getGUID()} and user_guid = {$facilitator->getGUID()}");
		if (!$added_check) {
			$facilitator_id = insert_data("INSERT INTO {$CONFIG->dbprefix}edufeedr_course_facilitators (course_guid, user_guid) VALUES ({$educourse->getGUID()},{$facilitator->getGUID()})");

		    if ($facilitator_id) {
			    $result['facilitator'] = elgg_view('edufeedr/singles/educourse_facilitator', array('educourse' => $educourse, 'facilitator' => $facilitator, 'type' => 'edit'));
			    $result['success'] = 'true';
		    }
		}

	}

	echo json_encode($result); exit;

?>
