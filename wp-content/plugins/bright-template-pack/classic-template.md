The classic template is a standard Bright course launcher.  It can be used to embed a course in a page, post, header, footer, sidebar, or anywhere else in WordPress that you can use a shortcode.

# classic

    [bright course="my-course-id" template="classic"/]

# Setting the Course Description

While we can use course metadata for description [like from the SCORM manifest], this can be overriden using a description field:

    [bright course="my-course-id" template="classic" description="A fine course.  We sincerely hope you enjoy it."/]

See the screen shot below for where the description appears.  It will default to "No description set." if not found in the SCORM
manifest or in the shortcode attributes.

# Changing the Course Image

You can set the course image via shortcode attribute:

    [bright course="my-course-id" template="classic" course_image="http://....."/]

If not set, this template will look for a Course Metadata field called 'course_image'.

For this, see also <a href="http://help.aura-software.com/editing-course-metadata/">Editing Course Metadata</a> and add a custom field for
the course called 'course_image'.

If neither of these is found, the template will look for a featured image on the post and use that.

If none of these is set, a stock bright image will be used.

# Adding a Certificate Link

    [bright course="my-course-id" template="classic" certificate_link="/certificate"/]

# Controlling Certificate Criteria

You have fine-grained control over by what criteria a certificate can be offered, and what message to show the user if they've failed to meet their criteria:

    [bright course="... a course guid ..." template="classic" certificate_link="/certificate" criteria_field="score" criteria_value="80" criteria_operator=">" criteria_comment="You must pass with at least a score of 80 to access your certificate"]

In the above, we use the following attributes:

* criteria_field - describes the field in the user's registration to use as the certificate criteria.  Defaults to 'success'.
* criteria_value - the value to compare with the {{criteria_field} above.  Defaults to 'passed'.
* criteria_operator - the operator to use in comparison.   The default is '=='.  
* criteria_comment - If the user fails the criteria, they can mouse over the 'N/A' that is displayed in lieu of the certificate link to receive this message.   Defaults to 'You must pass and/or complete the course to receive your certificate.'

# ScreenShot

<a href="http://help.aura-software.com/wp-content/uploads/sites/3/2015/11/classic-template.png"><img src="http://help.aura-software.com/wp-content/uploads/sites/3/2015/11/classic-template.png" alt="classic template" width="1042" height="544" class="alignnone size-full wp-image-354" /></a>
