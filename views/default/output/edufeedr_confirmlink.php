<?php
/**
 * Elgg confirmation link
 * A link that displays a confirmation dialog before it executes
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 *
 * @uses $vars['text'] The text of the link
 * @uses $vars['href'] The address
 * @uses $vars['confirm'] The dialog text
 *
 */

$confirm = $vars['confirm'];
if (!$confirm) {
	$confirm = elgg_echo('question:areyousure');
}

// always generate missing action tokens
$link = elgg_add_action_tokens_to_url($vars['href']);

if (isset($vars['class']) && $vars['class']) {
	$class = 'class="' . $vars['class'] . '"';
} else {
	$class = '';
}
?>
<a href="<?php echo $link; ?>" <?php echo $class; ?> <?php if (isset($vars['img'])) { echo 'title="' . htmlentities($vars['text'], ENT_QUOTES, 'UTF-8') . '"'; } ?> onclick="return confirm('<?php echo addslashes($confirm); ?>');"><?php if (isset($vars['img'])) { echo $vars['img']; } else { echo htmlentities($vars['text'], ENT_QUOTES, 'UTF-8'); } ?></a>
