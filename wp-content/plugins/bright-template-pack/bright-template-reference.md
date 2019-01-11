The [bright] shortcode, aside from the shortcode attributes, can include template text, either inline or defined by a plugin.

This document covers functions available in the Handlebars model of double braces, '{{}}' within the template text.

Numerous builtin helper functions are available, documented here.

# bFetch

Retrieves a value previously stored with {{bStore}}:

    {{#bStore 'name' 'Heisenberg'}}{{/bStore}}
    {{#bFetch 'name'}}{{/bFetch}}
    

Renders

"Heisenberg"

# bGetDefaultValue

Returns a default value in place of an unset template attribute, for example.

    {{#bCompare (bGetDefaultValue attributes.success_criteria 'passed') registration.success}}
    matched
    {{else}}
    did not match
    {{/bCompare}}
    

In this example, we are looking for a template argument called 'success_criteria'. If it is set, we will use that in our comparison statement with the value in registration.success.

If not set, we use the default value of 'passed'.

# bGetValueFromObject

Usage: You can use this to conditionally access an object by a different field name.

Example:

    {{(bGetValueFromObject registration (bGetDefaultValue attributes.criteria_field 'success'))}}
    

Thus bGetDefaultValue evaluates the attributes field, and if 'criteria_field' is selected, this is used to extract data from the registration object.

Otherwise, we will use the default of 'success'.

# bI18n

    {{#bI18n 'started'}}{{/bI18n}}

Represent a possibly internationalized string.

# bHumanizeDate

Converts a date that comes from the Bright API into something human readable.

It defaults to the US model, but can write out a European style [day,month,year] by setting model="EU"

     {{bHumanizeDate registration.provider_completed_at model="EU"}}

# bLog

    {{#bLog 'Message'}}{/bLog}}

Write a message to the browser javascript console.
    

# bStore

Stores a value that can be fetched later with a {{#bFetch 'value}}

    {{#bStore 'name' 'Heisenberg'}}{{/bStore}}
    {{#bFetch 'name'}}{{/bFetch}}
    

Renders

"Heisenberg"

# bCompare

You can bCompare two values using a passable operator:

For example, to check that a course has passed:

    {{#bCompare registration 'passed' operator='==='}}
    <!-- do something ... -->
    {{/bCompare}}
    

The default is a '=='

    {{#bCompare registration 'passed'}} <!-- no operator required -->
    <!-- do something ... -->
    {{/bCompare}}
    

Note if you omit the 'operator' part you will get an error like:

TypeError: Cannot read property 'operator' of undefined

    Error: Third argument to {{#bCompare}} must be in format operator='===' ; for instance
    

Operators Supported:

*   '=='
*   '==='
*   '!='
*   '!=='
*   '<'
*   '>'
*   '<='
*   '>='
*   'typeof'

See also [Javascript Guide for Comparison Operators][1].

# debug

This will generate a debugging statement in your template about the nested values associated with a context variable.

For example

    {{{debug this}}}
    

Will show all values available in the template.

You can change the title of the generated document with:

    {{{debug registration title='The most recent registration'}}}
    

**Note:** When dumping 'this', the toplevel fields can be accessed withouth 'this' prepended.

So instead of {{{this.user}}}, you can always use just {{{user}}}.

# dump

Dump provides a mechanism to "view" the data in the template context.

For example, to see the values of the attributes in the initial shortcode, use:

    {{#dump attributes}}{{/dump}}
    

In your template.

So for an embedder code like:

    [bright type="courselist" template="results_matrix" query="all_registrations" suppress_header="true"/]
    

You will get the following output in the page:

    {
     "type": "courselist",
     "template": "results_matrix",
     "query": "all_registrations",
     "suppress header": "true",
    }
    

# dump2

The descendant of {{dump}}, gives some useful pointers on how to view nested data:

{{#dump2 'user' user}}{{/dump2}}

    As passed to the templates the {{user}} variable are:
    
    rendering {{#dump2 'user' user}}{{/dump2}}   ..... 
    Top Level Names Are: meta, site_roles, email, avatar
    Data Currently In Variable:
    {
     "meta": "nested, use {{#dump2 'user.meta' user.meta}}{{/dump2}} to view.",
     "site_roles": "nested, use {{#dump2 'user.site_roles' user.site_roles}}{{/dump2}} to view.",
     "email": "admin@aura-software.com",
     "avatar": "http://0.gravatar.com/avatar/f1715eb1efc1cb4ec494c95a7b5fa584?s=96&d=mm&r=g
    }
    

# getQueryParameter

You extract query parameter arguments with this function:

     http://[my-url]/invitation-report/?invitation_name=land50234base
    

In the template, try

    {{#getQueryParameter 'invitation_name'}}{{/getQueryParameter}}
    

So as an example, for invitation reports we use the shortcode embedder, if not present, then we use the query parameter:

    <h3>{{#if attributes.invitation_name}}
    {{attributes.invitation_name}}
    {{else}}
    {{#getQueryParameter 'invitation_name'}}{{/getQueryParameter}}
    {{/if}}
    </h3>
    

# rightnow

Display current server date:

    [bright type="generic"]
    {{#rightnow}}{{/rightnow}}
    [/bright]
    

As:

    Wed Nov 11 2015 16:07:28 GMT-0700 (MST)

 [1]: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Expressions_and_Operators#Comparison_operators
