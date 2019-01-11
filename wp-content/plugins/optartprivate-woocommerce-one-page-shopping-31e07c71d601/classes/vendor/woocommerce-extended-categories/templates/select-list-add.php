<?php
/**
 * Template renders an 'add' view for the category select list setting
 * @param string $identifier
 * @param string $label
 * @param string $description
 * @param null $selected
 * @param array $first_option
 */
?>

<div class="form-field wc-extended-categories">
    <label for="tag-<?php print $identifier; ?>">
        <?php print $label ?>
    </label>
    <select name="<?php print $identifier; ?>" id="tag-<?php print $identifier; ?>" class="postform">
        <?php if( sizeof( $first_option ) > 0 ): ?>
            <option value="<?php print $first_option[0]; ?>"><?php print $first_option[1]; ?></option>
        <?php endif; ?>
        <?php foreach( $options as $value => $name ) : ?>
            <option value="<?php print $value; ?>"><?php print $name; ?></option>
        <?php endforeach; ?>
    </select>
    <p><?php print $description; ?></p>
</div>