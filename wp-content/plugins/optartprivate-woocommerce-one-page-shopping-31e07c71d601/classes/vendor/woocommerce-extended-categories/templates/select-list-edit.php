<?php
/**
 * Template renders an 'edit' view for the category select list setting
 * @param string $identifier
 * @param string $label
 * @param string $description
 * @param string $selected
 * @param array $first_option
 */
?>

<tr class="form-field wc-extended-categories">
    <th><label for="tag-<?php print $identifier ?>"><?php print $label; ?></label></th>
    <td>
        <select name="<?php print $identifier; ?>" id="tag-<?php print $identifier; ?>" class="postform">
            <?php if( sizeof( $first_option ) > 0 ): ?>
                <option value="<?php print $first_option[0]; ?>"><?php print $first_option[1]; ?></option>
            <?php endif; ?>
            <?php foreach( $options as $value => $name ) : ?>
                <option value="<?php print $value; ?>" <?php selected( $selected, $value ); ?>>
                    <?php print $name; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php print $description; ?></p>
    </td>
</tr>