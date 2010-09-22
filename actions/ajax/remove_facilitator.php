<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

    // Get input data
    $facilitator_guid = (int) get_input('facilitator');
	$educourse_guid = (int) get_input('educourse');

	$educourse = get_entity($educourse_guid);
	$facilitator = get_entity($facilitator_guid);

	$result = array('success' => 'false');

	if ($facilitator && $educourse->getSubtype() == 'educourse' && edufeedrCanManageEducourse($educourse)) {
		$facilitator_deleted = delete_data("DELETE FROM {$CONFIG->dbprefix}edufeedr_course_facilitators WHERE course_guid = $educourse_guid and user_guid = $facilitator_guid");

		if ($facilitator_deleted) {
			$result['success'] = 'true';
		}
	}

	echo json_encode($result); exit;
?>
