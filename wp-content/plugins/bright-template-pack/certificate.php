<?php

global $bright_embedder_templates;

class BrightTemplatePackCertificate {

  /*
   * For templates that cooperate on certificates, this is a re-usable piece of code that encapsulates the logic by which a user
   * should be offered a message if they fail to meet the certificate criteria.
   */

  public static function getCertificateDeniedMessageLogic() {
	return "{{bGetDefaultValue attributes.criteria_comment 'You must pass and/or complete the course to receive your certificate.'}}";
  }

  /*
   * For templates that cooperate on certificates, this is a re-usable piece of code that encapsulates the logic by which a user
   * should be offered or is able to view a certificate.
   */
  public static function getCertificateCriteriaLogic() {
	return "{{#bCompare (bGetValueFromObject registration (bGetDefaultValue attributes.criteria_field 'success')) (bGetDefaultValue attributes.criteria_value 'passed')  operator=(bGetDefaultValue attributes.criteria_operator '==')}}";
  }

  /**
   * Attributes:
   *
   * 1. success_criteria - defaults to 'passed'.  Set this to 'unknown' or 'failed' when testing to see a test template.
   */
  public static function addTemplate() {
   if (class_exists('\Bright\Wordpress'))
     \Bright\Wordpress::getInstance()->addTemplate('certificate',BrightTemplatePackCertificate::getCertificateText());
  }

  public static function getTitleLogic() {
    return '{{bGetDefaultValue custom.certificate_title (bGetDefaultValue attributes.title title)}}';
  }

  public static function getCompletedDateLogic() {
    return '(bGetDefaultValue registration.provider_completed_at registration.provider_accessed_at)';
  }

  /***
   *
   * build the template code.  This is parameterized so you can swap out various things like the certificate logic.  Another place this is called is from the learning path
   * certificate.
   *
   ***/

  public static function getCertificateText(array $args = array()) {
    $endBlock = Bright\extractFromArray($args,'endBlock','{{/bCompare}}');
    $certificateLogic = Bright\extractFromArray($args, 'certificateLogic', BrightTemplatePackCertificate::getCertificateCriteriaLogic());
	$certificateMessageLogic = Bright\extractFromArray($args, 'certificateMessageLogic', BrightTemplatePackCertificate::getCertificateDeniedMessageLogic());
    $titleLogic = Bright\extractFromArray($args,'titleLogic',BrightTemplatePackCertificate::getTitleLogic());
    $completedDateLogic = Bright\extractFromArray($args,'completedDateLogic', BrightTemplatePackCertificate::getCompletedDateLogic());

    $text = <<<EOF
{$certificateLogic}
<div class="{{bGetDefaultValue attributes.cssclass 'certificate'}}">
  <img class='printer-cert-image' src="{{bGetDefaultValue attributes.background '/wp-content/plugins/bright-template-pack/images/certificate-blank-notext.jpg'}}"/>
  <div style="margin-top: 160px;" class="certificate-intro wrapping-cert-line cert-intro content-over-cert-image"">
    <span class="certificate-intro">
{{bGetDefaultValue attributes.introduction 'This Certificate of Completion is presented to'}}
    </span>
  </div>

  <div style="text-align:center;" class="certificate-nameblock content-over-cert-image"><span class="certificate-nameblock">
{{#if user.display_name}}
{{bGetDefaultValue attributes.display_name user.display_name}}
{{else}}
{{#if user.meta.last_name}}
{{user.meta.first_name}} {{user.meta.last_name}}
{{else}}
{{user.email}}
{{/if}}
{{/if}}
</span></div>

<div style="text-align:center;" class="certificate-forcourse wrapping-cert-line content-over-cert-image">
<span class="certificate-forcourse">
for successfully completing: 
</span>
</div>

<div style="text-align:center;" class="certificate-course-title wrapping-cert-line content-over-cert-image">
<span class="certificate-course-title">
{$titleLogic}
</span>
</div>


<div style="position:absolute;left:585.40px;top:375.95px" class="content-over-cert-image">
<span class="certificate-forcourse">
{{{bHumanizeDate {$completedDateLogic} (bGetDefaultValue attributes.date_model 'US')}}}
</span>
</div>

</div>
<div class="clear">
<div class="print-me">
  <a href="javascript:alert('We will attempt to force landscape printing for you.  If your computer decides it still wants to print in portrait mode, you may need to select landscape manually.');window.print();">Print Me...</a>   
</div>
{{else}}
{$certificateMessageLogic}
{$endBlock}
EOF;
    return $text;
  }

}

add_action('plugins_loaded','BrightTemplatePackCertificate::addTemplate');


