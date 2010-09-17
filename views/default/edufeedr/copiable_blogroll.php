<?php

    if (isset($vars['participants'])) {
		echo '<ul>';
		foreach ($vars['participants'] as $participant) {
			echo '<li><a href="' . $participant->blog . '">' . $participant->firstname . ' ' . $participant->lastname . '</a></li>';
		}
		echo '</ul>';
	}

?>
