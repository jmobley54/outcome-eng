The bright-course-list template is a standard Bright course lister. It can be used to represent a flexible list of courses on the page, in a standard format.

Some of the things you can do with the course table is:

 * control which courses are represented
 * control the appearance and criteria of a certificate link.
 * control whether the table is interactively searchable and sortable.
 * you can also enable or disable self-registration.

Got more ideas for it? [Tell us][1]!

[<img src="http://help.aura-software.com/wp-content/uploads/sites/3/2016/05/2016-10-15_1040.png" alt="2016-10-15_1040" width="967" height="358" class="alignnone size-full wp-image-597" />][2]

Need even more functionality? [Click here][3] on how to capture template code and start building your own Bright courselist template.

# bright-course-table

    [bright template="bright-course-table" type="courselist"/]

# Setting Filter Criteria

There are two filtering models, by explicit field value [like the value of a category or Bright custom field], or by a custom JavaScript function

### Filtering by a Course Metadata Field

You can add custom metadata for a course with the [Embedded Bright Administrative Console][4].

In this example, our courses have a Bright custom field called category, and we would like to show those courses with a value of 'test':

    [bright template="bright-course-table" type="courselist" filter_field="category" filter_namespace="custom" filter_value="test"/]
    

'custom' is the namespace to use for custom fields created via the Embedded Bright Administrative Console. For now, a single namespace level is allowed. For multiple level variables [like custom.my_document.another_value], use a filter function.

[<img src="http://help.aura-software.com/wp-content/uploads/sites/3/2016/05/2016-10-14_1646.png" alt="2016-10-14_1646" width="650" height="368" class="alignnone size-full wp-image-592" />][5]

### Filtering by a Javascript Function

    MyBrightExtensions = {
      filter_courses: function(courses) {
        return [courses[2]];
      },
    }
    

Then:

    [bright template="bright-course-table" type="courselist" self_register="true" launch_button_text="Launch" certificate_link="/certificate" filter_namespace="MyBrightExtensions" filter_function="filter_courses"/]
    
# Changing Sort Criteria

By default, the bright-course-table will sort by the course title. There are two ways to modify this

### Sort criteria by context field.

#### Example, sort by score:

    [bright template="bright-course-table" type="courselist" sort_field="score"/]
    

#### Example, sort by SCORM metadata description:

    [bright template="bright-course-table" type="courselist" sort_namespace="metadata" sort_field="description"/]
    

#### Example, sort in custom function

Some javascript somewhere:

    MyBrightExtensions = {
      sortCourses: function(courses) {
      // sort the courses here by whatever method your prefer.
        return courses;
      }
    }
    
Now:

    [bright template="bright-course-table" type="courselist" sort_namespace="MyBrightExtensions" sort_function="sortCourses"/]
    

### Reversing the sort order

Use [bright template="bright-course-table" .... reverse="true"/]

to reverse the sorted order.

# Allowing Self Registration

This template defaults to requiring pre-registration. Just set self-registration=true to allow user's to self register:

    [bright type="courselist" template="bright-course-table" self_register="true"/]
    
# Certificates

By default:

 * the certificate link is enabled.  Use 'no_certificate="true"' to disabled it 
 * the certificate link is '/certificate' ; use the certificate_link attribute to override this.
 * the certificate criteria is the SCORM variable 'success' is set to passed.   This can be changed[ \[see below\]][6].

    [bright type="courselist" template="bright-course-table" certificate_link="/certificate"/]

For more on the default Bright certificate template, see [here][7].   Usually, you will create a page called 'Certificate' and add the
certificate shortcode to that page like:

    [bright type="course" template="certificate"/]

Note if you set certificate criteria, you'll want the same certificate criteria in that shortcode.

# Suppress Certificate Link

    [bright type="courselist" template="bright-course-table" no_certificate="true"/]

# Change Certificate Link

    [bright type="courselist" template="bright-course-table" certificate_link="/my-certificate"/]

# Controlling Certificate Criteria

You have fine-grained control over by what criteria a certificate can be offered, and what message to show the user if they've failed to meet their criteria:

    [bright type="courselist" template="bright-course-table" certificate_link="/certificate" criteria_field="score" criteria_value="80" criteria_operator=">" criteria_comment="You must pass with at least a score of 80 to access your certificate"]    

In the above, we use the following attributes:

*   criteria_field - describes the field in the user's registration to use as the certificate criteria. Defaults to 'success'.
*   criteria_value - the value to compare with the {{criteria_field} above. Defaults to 'passed'.
*   criteria_operator - the operator to use in comparison. The default is '=='. 
*   criteria_comment - If the user fails the criteria, they can mouse over the 'N/A' that is displayed in lieu of the certificate link to receive this message. Defaults to 'You must pass and/or complete the course to receive your certificate.'

# Changing the Launch Button Text

Use the **launch_button_text** embedder attribute:

    [bright type="courselist" template="bright-course-table" launch_button_text="Launch Quiz"]
    

# Changing the not-started message

    [bright template="bright-course-table" type="courselist" not_started_message="begin now"/]
    

# Suppress searching and sorting [Datatables]

Disabling datatables on your course table is a snap, just add:

    [bright template="bright-course-table" type="courselist" self_register="true" launch_button_text="Launch" certificate_link="/certificate" no_datatables="true"/]


  [1]: http://www.aura-software.com/contact-us/
  [2]: http://help.aura-software.com/wp-content/uploads/sites/3/2016/05/2016-10-15_1040.png
  [3]: http://help.aura-software.com/adding-a-bright-template-to-a-wordpress-page-or-post/#using-the-template-embedder-to-capture-the-text-of-an-existing-template
  [4]: http://help.aura-software.com/editing-course-metadata/
  [5]: http://help.aura-software.com/wp-content/uploads/sites/3/2016/05/2016-10-14_1646.png
  [6]: http://help.aura-software.com/template-bright-course-table/#controlling-certificate-criteria
  [7]: http://help.aura-software.com/template-certificate/

