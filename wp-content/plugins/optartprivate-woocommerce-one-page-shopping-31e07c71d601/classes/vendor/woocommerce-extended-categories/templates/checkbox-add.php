<?php
/**
 * Template renders an 'add' view for the category checkbox setting
 * @param string $identifier
 * @param string $label
 * @param string $description
 * @param bool $checked
 */
?>

<div class="form-field wc-extended-categories">
    <label for="tag-<?php print $identifier; ?>">
        <?php print $label ?>
    </label>
    <input type="checkbox" id="tag-<?php print $identifier; ?>" name="<?php print $identifier; ?>" <?php checked( $checked ); ?> />
    <p><?php print $description; ?></p>
</div>