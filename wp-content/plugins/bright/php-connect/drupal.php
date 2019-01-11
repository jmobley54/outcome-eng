<?php

namespace Bright;

class Drupal7 extends Base {
  public function getUserByEmail($email) {
	return user_load_by_mail($email);
  }
}

class Drupal6 extends Base {
  public function getUserByEmail($email) {
	return user_load(array('mail'=> $email));
  }
}

