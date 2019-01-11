<?php

class BrightBaseTest extends BrightApiTestBase {
  function test_resetAuthenticationToken() {
	$tok = $this->bright->accessToken;
	$r= $this->bright->resetAuthenticationToken();
	$this->assertNull($this->bright->accessToken);
	$this->bright->authenticateCurrentUser();
	$this->assertNotNull($this->bright->accessToken);	
	$this->assertNotEquals($tok,$this->bright->accessToken);
  }

  function test_templateadd() {
    $bright = $this->bright;

    $tmplText = 'this is my template';

    $bright->addTemplate('mytemplate',$tmplText);
    $text = $bright->getTemplateText(array('template' => 'mytemplate'));

    $this->assertNotNull($text);
    $this->assertEquals($text,$tmplText);
  }
}
