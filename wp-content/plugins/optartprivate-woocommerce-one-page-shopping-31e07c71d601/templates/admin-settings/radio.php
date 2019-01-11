<?php
/**
 * Displaying the settings radio input
 * @param string $name
 * @param string $options
 * @param boolean $checked
 */
?>

<ul class="jigoshop-radio-vert">
    <?php foreach( $options as $id => $desc ): ?>
        <li>
		<span class="dashicons dashicons-editor-help help_tip" title="<?php print ($tiptips[$id]); ?>"></span>
            <input id="<?php print $name ?>_<?php print $id; ?>"
                   type="radio"
                   value="<?php print $id; ?>"
                   name="<?php print $plugin_identifier ?>[<?php print $name; ?>]"
                <?php checked( $checked, $id ); ?> />
            <label for="<?php print $name ?>_<?php print $id; ?>">
                <?php print $desc; ?>
            </label>
			
        </li>
    <?php endforeach; ?>
</ul>
