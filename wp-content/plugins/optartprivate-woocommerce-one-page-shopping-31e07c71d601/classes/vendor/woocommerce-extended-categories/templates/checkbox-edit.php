<?php
/**
 * Template displays a checkbox which you can find on category edit page
 * @param string $identifier
 * @param string $label
 * @param bool $checked
 * @param string $description
 */
?>

<tr class="form-field wc-extended-categories">
    <th><label for="tag-<?php print $identifier ?>"><?php print $label; ?></label></th>
    <td>
        <input type="checkbox" id="tag-<?php print $identifier ?>" name="<?php print $identifier ?>" <?php checked( $checked ) ?>/>
        <p class="description"><?php print $description; ?></p>
    </td>
</tr>