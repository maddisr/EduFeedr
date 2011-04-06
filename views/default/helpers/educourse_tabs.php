<?php

    if (isset($vars['filter']) && isset($vars['educourse_guid'])) {

		$body = '';
        $filter = $vars['filter'];
        $educourse_guid = $vars['educourse_guid'];
        
        // Define course view link
        $course_view_link = $CONFIG->wwwroot . 'pg/edufeedr/view_educourse/' . $educourse_guid;
        $body .= '<div class="contentWrapper">';
        $body .= '<div id="elgg_horizontal_tabbed_nav">';
        $body .= '<ul>';
        if ($filter == 'course') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
        /*translation:Course feed*/
        $body .= '<li ' . $tabclass. '><a href="'.$course_view_link.'?filter=course">' . elgg_echo('edufeedr:tab:course:feed') . '</a></li>';
        if ($filter == 'courseinfo') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
        /*translation:Course info*/
        $body .= '<li ' . $tabclass. '><a href="'.$course_view_link.'?filter=courseinfo">' . elgg_echo('edufeedr:tab:courseinfo') . '</a></li>';
        /*translation:Participants*/
        if ($filter == 'participants') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
        /*translation:Participants*/
        $body .= '<li ' . $tabclass. '><a href="'.$course_view_link.'?filter=participants">' . elgg_echo('edufeedr:tab:participants') . '</a></li>';
        if ($filter == 'assignments') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
        /*translation:Assignments*/
        $body .= '<li ' . $tabclass. '><a href="'.$course_view_link.'?filter=assignments">' . elgg_echo('edufeedr:tab:assignments') . '</a></li>';
        if ($filter == 'progress') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
        /*translation:Progress*/
        $body .= '<li ' . $tabclass. '><a href="'.$course_view_link.'?filter=progress">' . elgg_echo('edufeedr:tab:progress') . '</a></li>';
        if ($filter == 'connections') { $tabclass = ' class="selected"'; } else { $tabclass = ''; }
        /*translation:Social network*/
        $body .= '<li ' . $tabclass. '><a href="'.$course_view_link.'?filter=connections">' . elgg_echo('edufeedr:tab:social:network') . '</a></li>';
        $body .= '</ul>';
        $body .= '</div>';
        $body .= '</div>';

		echo $body;
    }
?>
