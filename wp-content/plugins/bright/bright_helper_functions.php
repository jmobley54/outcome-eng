<?php

/* print out an internationalized message for the bright plugin */
function bright_message($message) {
  return __($message, 'bright-plugin');
}

/* echo out an internationalized message for the bright plugin */
function bright_echo_message($message) {
  echo(bright_message($message));
}

/* convert [bright:somethin] into somethin */
function bright_extract_embed_code($embedCode) {
  return substr($embedCode,7,strlen($embedCode) - 8);
}

/* function bright_return_as_javascript($code) { */
/*   return "  <script type='text/javascript'>\n		" . $code . "\n    </script>"; */
/* } */

function bright_render_as_javascript($code) {
  echo(bright_return_as_javascript($code));
}

