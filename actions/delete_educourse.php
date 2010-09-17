<?php

    // Gatekeeper
    gatekeeper();
    action_gatekeeper();

    $guid = (int) get_input('educourse');

    $educourse = get_entity($guid);

    if ($educourse->getSubtype() == 'educourse' && $educourse->canEdit() && edufeedrCanManageEducourse($educourse)) {
        $rowsaffected = $educourse->delete();
	if ($rowsaffected > 0) {
            /*translation:Course deleted.*/
            system_message(elgg_echo('edufeedr:message:educourse:deleted'));
            delete_data("DELETE FROM {$CONFIG->dbprefix}edufeedr_course_participants WHERE course_guid=".$guid);
            delete_data("DELETE FROM {$CONFIG->dbprefix}edufeedr_course_assignments WHERE course_guid=".$guid);
            $es = new EduSuckr;
            $es->removeEduCourse($guid);
	} else {
            /*translation:Course could not be deleted.*/
            register_error(elgg_echo('edufeedr:error:educourse:not:deleted'));
	}

	forward('pg/edufeedr/');
    }

?>
