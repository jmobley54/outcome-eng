<?php

class BrightTestFactory {
  static function aValidCourseGuid() {
  	return "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446";
  }
  
  /**
   * @return Array of valid guids for bright tests 
   */

  static function validCourseGuids() {
    return array( 'OmniPodStrategyAndTacticsSCORM6663448a-9c5c-4616-9bf1-19aaf64a957c',
                  'a-test-course__dfc1d5fd-2a53-4977-8ac2-e3c6d72f3bdb',
                  'SequencingSimpleRemediation_SCORM20043rdEditioncf47e718-3a10-47ce-8a86-ecd1bec21fae',
                  'PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446',
                  'OmniPodStrategyAndTacticsSCORM4fb4f4e1-16eb-4255-9abf-69dd36e79563',
                  'YogaforWeightLossmorningandeveningroutine8eae0014-ca8b-4e52-abcb-e08044760103');
    
  }
}