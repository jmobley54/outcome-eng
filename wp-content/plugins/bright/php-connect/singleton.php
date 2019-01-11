<?php

namespace Bright;

class Singleton
{
  /**
   * @var Singleton The reference to *Singleton* instance of this class
   */
  protected static $instance;
  
  /**
   * Returns the *Singleton* instance of this class.
   *
   * @return Singleton The *Singleton* instance.
   */
  public static function getInstance()
  {
	if (null === static::$instance) {
	  static::$instance = new static();
	  static::$instance->log('got new bright instance, url is '. ($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : 'N/A(command line or phpunit?)'));
	}
	
	return static::$instance;
  }

  /**
   * Protected constructor to prevent creating a new instance of the
   * *Singleton* via the `new` operator from outside of this class.
   */
  protected function __construct()
  {
  }

  /**
   * Private clone method to prevent cloning of the instance of the
   * *Singleton* instance.
   *
   * @return void
   */
  private function __clone()
  {
  }

  /**
   * Private unserialize method to prevent unserializing of the *Singleton*
   * instance.
   *
   * @return void
   */
  private function __wakeup()
  {
  }
  /**
   *
   */
  public static function __destroy() {
	static::$instance = null;
  }
}