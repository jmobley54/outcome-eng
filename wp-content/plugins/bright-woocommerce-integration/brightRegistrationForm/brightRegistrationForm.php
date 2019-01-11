<?php
/**
 *
 */

/**
 *
 */
class brightRegistrationForm
{
  private $redirect_to;
  private $fields;
  private $submittedData = array();
  
  /**
   *
   */
  function __construct()
  {
    $this->fields = array(['name' => "reg_name",
                           'type' => "text",
                           'placeholder' => 'Username',
                           'class' => 'fui-user',
                           'validation' => function($username) {
                              if (strlen($username) < 4) 
                                return new WP_Error('username_length', 'Username too short. At least 4 characters is required');
                              
                              if ( username_exists( $username ) )
                                return new WP_Error('username_exists', "Username {$username} already exists.");
                              
                              return false;
                            },
                           'userData' => 'user_login',
                           'required' => true,
                           'id' => 'reg-name'
                           ],
                          array('name' => 'reg_fname',
                                'type' => 'text',
                                'placeholder' => 'First Name',
                                'class' => 'fui-user',
                                'userData' => 'first_name',
                                'id' => 'reg-fname'),
                          array('name' => 'reg_lname',
                                'type' => 'text',
                                'placeholder' => 'Last Name',
                                'userData' => 'last_name',
                                'class' => 'fui-user',
                                'id' => 'reg-lname'),
                          array('name' => 'reg_email',
                                'type' => 'email',
                                'placeholder' => 'Email',
                                'required' => true,
                                'userData' => 'user_email',
                                'validation' => function($email) {
                                  if (!is_email($email)) 
                                    return new WP_Error('email_invalid', "Email {$email} is not valid");
                                  
                                  if (email_exists($email)) 
                                    return new WP_Error('email', 'Email {$email} Already in use.  Please login below.');
                                  
                                  return false;
                                },
                                'class' => 'fui-mail',
                                'id' => 'reg-email'),
                          array('name' => 'reg_password',
                                'type' => 'password',
                                'placeholder' => 'Password',
                                'userData' => 'user_pass',
                                'required' => true,
                                'validation' => function($password,$brightRegistrationForm) {
                                  $errors = array();
                                  $passwordRepeat = $brightRegistrationForm->submittedData['reg_password_confirm'];
                                  if ( $password != $passwordRepeat ) 
                                    array_push($errors, new WP_Error('password', 'Passwords do not match!'));
                                  
                                  
                                  if (strlen($password) < 6) 
                                    array_push($errors, new WP_Error('password length', "Password is too short (" . strlen($password) . "), please use 6 characters or more."));
                                  
                                  //#### Test password has uppercase and lowercase letters
                                  if (preg_match("/^(?=.*[a-z])(?=.*[A-Z]).+$/", $password) !== 1) 
                                    array_push($errors, new WP_Error ( 'password_structure' , "Password does not contain a mix of uppercase & lowercase characters."));
                                  
                                  //#### Test password has mix of letters and numbers
                                  if (preg_match("/^((?=.*[a-z])|(?=.*[A-Z]))(?=.*\d).+$/", $password) !== 1) 
                                    array_push($errors, new WP_Error ('password_structure', "Password does not contain a mix of letters and numbers." ));
                                  //#### Password looks good
                                  return $errors;
                                },
                                'class' => 'fui-lock',
                                'id' => 'reg-pass'),
                          array('name' => 'reg_password_confirm',
                                'type' => 'password',
                                'placeholder' => 'Confirm Password',
                                'class' => 'fui-lock',
                                'required' => true,
                                'id' => 'reg-pass-confirm'),
                          );
    
    add_shortcode('brightRegistrationForm', array($this, 'shortcodeHandler'));	
    add_action('wp_enqueue_scripts', array($this, 'initialize'));
    
    add_action('init', array($this, 'checkForRedirection'));
    
  }
  
  public function checkForRedirection() {
    if (isset($_REQUEST['redirect_to']))
      ob_start();
  }
  
  /**
   *
   */
  public function generateRegistrationForm()
  {
    
    ?>
    <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
    <div class="login-form">
    <?php
    foreach ($this->fields as $field) {
      ?>
      <div class="form-group">
      <input name="<?php echo $field['name']; ?>" type="<?php echo $field['type']; ?>" class="form-control login-field"
      value="<?php echo(isset($_REQUEST[$field['name']]) ? $_REQUEST[$field['name']] : null); ?>"
      placeholder="<?php echo $field['placeholder']; ?>" id="<?php echo $field['id']; ?>"><?php if ($field['required']) {
        echo ""; } ?>
      <label class="login-field-icon <?php echo $field['class']; ?>" for="<?php echo $field['id']; ?>"></label>
      </div>
      <?php                          
    }
    ?>
    <input name="redirect_to" type="hidden" id="redirect_to" value="<?php echo $this->redirect_to; ?>"/>
    <input class="btn btn-primary btn-lg btn-block" type="submit" name="reg_submit" value="Register"/>
    </form>
    </div>
    Already Have An Account? <a href="/wp-login.php?redirect_to=<?php echo $this->redirect_to; ?>">Click here.</a>
    
    <?php
  }
  /**
   *
   */
  function validateSubmittedData()
  {
    $errors = array();
    foreach ($this->fields as $field) {
      $name = $field['name'];
      $receivedData = $this->submittedData[$name];
      
      if ($field['required'] && empty($receivedData))
        array_push($errors, new WP_Error('field', "Required form field {$field['placeholder']} is missing"));
      
      if ($field['validation']) {
        $ret = $field['validation']($receivedData,$this);
        if ($ret) {
          if (is_array($ret)) {
            $all = array_merge($errors,$ret);
            $errors = $all;
          } else
            array_push($errors, $ret); 
        }
      }
      
    }
    return $errors;
  }
  
  /**
   *
   */
  function registration()
  {
    $validationErrors = $this->validateSubmittedData();
    if (sizeof($validationErrors) > 0) {
      foreach ($validationErrors as $isValid) {
        echo '<div style="margin-bottom: 6px" class="btn btn-block btn-lg btn-danger">';
        echo '<strong>' . $isValid->get_error_message() . '</strong>';
        echo '</div>';
      }
      $this->generateRegistrationForm();
    } else {
      $userdata = array();
      
      foreach ($this->fields as $field) {
        $name = $field['name'];
        
        if (isset($field['userData']))
          $userdata[$field['userData']] = $this->submittedData[$name];
      }
      
      $user_id = wp_insert_user($userdata);
      wp_set_auth_cookie( $user_id );
      if ($this->redirect_to)
        wp_safe_redirect($this->redirect_to);
      else 
        echo '<div style="margin-bottom: 6px" class="btn btn-block btn-lg">';
      echo '<strong>Thanks for registering!</strong>';
      echo '</div>';
    }
  }
  /**
   *
   */
  function shortcodeHandler($attributes)
  {
    $this->redirect_to = Bright\extractFromArray($_REQUEST, 'redirect_to', '/');
    
    if (isset($_REQUEST['reg_submit'])) {
      foreach ($this->fields as $field) 
        $this->submittedData[$field['name']] = esc_attr($_REQUEST[$field['name']]);
      $this->registration();
      
    } else {
      if (is_user_logged_in())
        wp_safe_redirect($this->redirect_to);        
      
      $this->generateRegistrationForm();
    }
    
    return ob_get_clean();
  }
  
  /**
   *
   */
  function initialize()
  {
    wp_enqueue_style('flat-ui-kit', plugins_url('css/flat-ui.css', __FILE__), false, false, 'screen');
  }
}

new brightRegistrationForm;