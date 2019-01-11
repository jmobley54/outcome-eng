
# Debugging Statements

Including in Bright are some debugging statements that can be used in your template.

Specifically, Bright makes the following data available to your template:

 - "this"
 - "attributes"
 - "page"
 - "courses" [where type="courselister"]
 - "custom"

To view the contents of the available context, use the bright debugging template:

[bright template="bright_template_debugger" type="courselist" self_register="true"/]

Or for a course, like this:

[bright template="bright_template_debugger" course="A-Course-Guid"/]

If you have difficulty getting your template to work, some suggestions:

 - change the template name to "bright_template_debugger"
 - use the "dump2" function to see what is in your template context.

For example

{{#dump2 'page' page}}{{/dump2}}

Will allow you to introspect the data in the {{page}} context variable.




