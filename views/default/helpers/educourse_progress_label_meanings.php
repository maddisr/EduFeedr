<?php
$meanings = array(
    array(
        'image' => 'assignment_done.png',
        'alt' => 'linked',
        /*translation:blog post linked to the assignment*/
        'text' => elgg_echo('edufeedr:progress:meaning:text:linked')
    ),
    array(
        'image' => 'assignment_time_frame.png',
        'alt' => 'during_period',
        /*translation:blog post during the assignment period*/
        'text' => elgg_echo('edufeedr:progress:meaning:text:during_period')
    )
);
?>
<div class="progress_meanings">
<?php foreach ($meanings as $single): ?>
    <div>
        <img src="<?php echo $vars['url']; ?>mod/edufeedr/views/default/graphics/<?php echo $single['image']; ?>" alt="<?php echo $single['alt']; ?>" />
        <?php echo $single['text']; ?>
    </div>
<?php endforeach; ?>
</div>
