<?php

// Various UI Widgets To Be Used When Generating Reports
add_filter('bright_extend_on_generic','BrightUIFilters::courseSelector',1,2);

global $bright_embedder_templates;

$bright_embedder_templates['bright_ui_course_select'] =<<<EOF
<label for="course_guid">{{#bGetDefaultValue attributes.label 'Course: '}}{{/bGetDefaultValue}}</label>
<select name="course_guid" id="course_guid">
{{#bSortCourses this courses sortBy="title"}}
{{#bCompare (bGetQueryParameter 'course_guid') course_guid operator='=='}}
  <option value="{{course_guid}}" selected="true">{{title}}</option>
{{else}}
  <option value="{{course_guid}}">{{title}}</option>
{{/bCompare}}
{{/bSortCourses}}
</select>
EOF;

class BrightUIFilters {
    static function courseSelector($coursedata,$attr) {
        return $coursedata;
    }
}
