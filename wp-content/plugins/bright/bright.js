/*
  Source code created by Aura Software, LLC is licensed under a
  Attribution-NoDerivs 3.0 Unported United States License
  http://creativecommons.org/licenses/by-nd/3.0/
*/

(function(_,e,rr,s){
  _errs=[s];
  var c=_.onerror;
  _.onerror=function(){
    var a=arguments;
    _errs.push(a);
    c&&c.apply(this,a)
  };
  var b=function(){
    var c=e.createElement(rr),b=e.getElementsByTagName(rr)[0];
    c.src="//beacon.errorception.com/"+s+".js";c.async=!0;b.parentNode.insertBefore(c,b)};
  _.addEventListener?_.addEventListener("load",b,!1):_.attachEvent("onload",b)}
)(window,document,"script","553aa947a1b3d5160900249b");

if (_errs) {
  _errs.allow = function (error) {
    if (error &&
        error.object &&
        error.object.match(/bright/i))
      return true;

    if (error &&
        error.stack &&
        error.stack.match(/bright/i))
      return true;
    return false;
  }
}


if (!Array.prototype.forEach) {Array.prototype.forEach = function(f,s) {for(var i = 0,l=this.length;i<l;++i) {f.call(s,this[i],i,this);}}}

// From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
if (!Object.keys) {
  Object.keys = (function () {
    'use strict';
    var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
        dontEnums = [
          'toString',
          'toLocaleString',
          'valueOf',
          'hasOwnProperty',
          'isPrototypeOf',
          'propertyIsEnumerable',
          'constructor'
        ],
        dontEnumsLength = dontEnums.length;

    return function (obj) {
      if (typeof obj !== 'object' && (typeof obj !== 'function' || obj === null)) {
        throw new TypeError('Object.keys called on non-object');
      }

      var result = [], prop, i;

      for (prop in obj) {
        if (hasOwnProperty.call(obj, prop)) {
          result.push(prop);
        }
      }

      if (hasDontEnumBug) {
        for (i = 0; i < dontEnumsLength; i++) {
          if (hasOwnProperty.call(obj, dontEnums[i])) {
            result.push(dontEnums[i]);
          }
        }
      }
      return result;
    };
  }());
}


// ********************************************************************************
// Bright CourseRegistration Class
//

function BrightRegistration(obj) {
  'use strict';

  Object.keys(obj).forEach(function(r) {
    this.newObject[r] = this.oldObject[r];
  },{
    newObject: this,
    oldObject: obj
  });

  /* TODO: this pattern should be in a parent class [BrightObject?] and BrightCourse and BrightRegistration should inherit */
  if (this.custom)
    try {
      this.custom = eval("(" + this.custom + ")");
    } catch (err) {
      Bright.log(err);
    }

  if (this.invitation_custom)
    try {
      this.invitation_custom = eval("(" + this.invitation_custom + ")");
    } catch (err) {
      Bright.log(err);
    }
}

// return the invitation start or end data; if set in invitation custom
BrightRegistration.prototype.getInvitationDate = function(field) {
  if (this.invitation_custom &&
      this.invitation_custom[field])
    try {
      return new Date(this.invitation_custom[field])
    } catch (err) {
      Bright.log('failed to convert ' + this.invitation_custom[field] + 'to date for registration ' + this.registration.guid + '; field: invitation_custom.' + field);
    }
  return null
}


function BrightCourse(course) {
  'use strict';

  Object.keys(course).forEach(function(r) {
    this.newObject[r] = this.oldObject[r];
  },{
    newObject: this,
    oldObject: course
  });

  if (this.registrations) {
    var good = []; // remove nested .records attributes in the registrations list.  In Bright 8.0 its here; not sure why...
    this.registrations.forEach(function (r) {good.push(r.records ? r.records : r);});
    this.registrations = good;

    var good = [];
    this.registrations.forEach(function (r) {
      if (!(r instanceof BrightRegistration))
        r = new BrightRegistration(r);
      good.push(r);
    });
    this.registrations = good;

    // If we have a single registrations, implement syntactic sugar for {{registration}}
    if (this.registrations.length > 1)
      this.registration = _.sortBy(this.registrations, function(c) { return c.provider_created_at;}).reverse()[0];
    else
      this.registration = this.registrations[0];

  }
  if (this.custom) {
    try {
      this.custom = eval("(" + this.custom + ")");
    } catch (err) {
      Bright.log(err);
    }
  }

  if (this.metadata) {
    try {
      this.metadata = eval("(" + this.metadata + ")");
    } catch (err) {
      Bright.log(err);
    }
  }
}

// return the invitation start or end data; if set in invitation custom
BrightCourse.prototype.getInvitationDate = function(field) {
  if (this.registration)
    return this.registration.getInvitationDate(field);
  return null;
}



// ********************************************************************************
// Bright Hooks Classes
//
function BrightHook(fn,priority) {
  'use strict';
  this.fn = fn;
  this.priority = (priority ? priority : 10);
};

function BrightHookList() {
  'use strict';
  this.hooks = {};
};

BrightHookList.prototype.addHook = function (name,hook) {
  if (hook instanceof BrightHook) {
	if (! this.hooks[name])
	  this.hooks[name] = [];

	this.hooks[name].push(hook);
	return true;
  }
  return false
};

BrightHookList.prototype.orderedHookList = function(name) {
  if (this.hooks[name] && this.hooks[name] instanceof Array)
	return _.sortBy(this.hooks[name],function(hook) { return hook.priority;});
  return false
}

BrightHookList.prototype.runHook = function(name) {
  var list = this.orderedHookList(name);
  var r = 0;

  if (! list)
	return false;

  list.forEach(function(hook) {
	hook.fn();
	r++;
  });
  return r
}

// Bright Template Class.
// Current supported types:
//   - course aka "launchbox". Launchbox was the canonical term but is deprecated.
//   - courselist aka "courselist"
//   - generic aka blank.
//
// This is the front end to the Bright rendering engine, built on Handlebars.
// Originally Bright was just a namespace, but it is slowly being migrated to Javascript OO.
//
// id: otherwise known as 'container ID'.  A <div> will have an ID [see divIdentifier property] which is the
//     screen real-estate where the template will be rendered.
//
// type: see above.
//
// data: generally speaking, the data becomes the handlebars contenxt.  Different template types
//       have a different symantic understanding of what should be in here.
//
// isRendered: it's set to true once the template is rendered.
//
// divId: if id is '3242348132048123', the the divId is something like 'bright-launchbox-3242348132048123', depending
//        on the template type.
//
// divIdentifier: is used to derive the divId, it's one of 'launchbox' [deprecated], 'courselist', or 'blank'.

function BrightTemplate(id,type,data) {
  'use strict';
  typeof(Bright) !== "undefined" ? Bright.log('new BrightTemplate') : console.log('new BrightTemplate')
  this.id = id;
  this.type = type;
  this.data = data;
  this.isrendered = false;
  this.divIdentifier = this.type; // (type == 'course') ? 'launchbox' : this.type; // ugh; but why?
  this.divId = 'bright-' + this.divIdentifier + '-' + this.id;
  this.brightTemplate = true; // easy to check later
}

// return a jQuery object for the <div> associated with the template.
BrightTemplate.prototype.getTemplateDiv = function() {
  return jQuery('#' + this.divId);
}

// this object holds the template for the page in an Array; and maintains a hash of ids for lookup
function BrightTemplateList() {
  this.hash = {};
}

BrightTemplateList.prototype = new Array();

// push a template on
BrightTemplateList.prototype.add = function(o) {
  this.hash[o.id] = o;
  this.push(o);
}

BrightTemplateList.prototype.get = function(id) {
  return this.hash[id];
}

// returns a list of the templates of a certain type.
BrightTemplateList.prototype.getByType = function(type)  {
  return _.filter(this,function(template) {
    // console.log("type is " + type);
    // console.log(" template.type is " + template.type);
    return template.type == type;
  });
}


if (typeof Bright === "undefined") {
  Bright = {
    // return true if bright is loaded; else return 
    ready: function() {
      return (Bright.token() === undefined) != true;
    },
    
    
    // This it the interval passed to setTimeout for the health check
    "healthCheckintervalTime": (1000 * 45),
    // result of health check.  starts as unknown
    health: null,
    healthAttempts: 0,
    healthSuccess: 0,
    healthFail: 0,
    healthTotalTime: 0,

    // this is the interval 
    // healthInterval: null,

    "apiTimeout": 20 * 1000, // how long to give the API to respond before giving up
    

    // the template store is used by the 'bStore' and 'bFetch' helpers to store and retrieve variables
    // from inside Bright templates;

    "templateStore": {},

    "updateRealmUserMeta": function(args) {
      data = args.data;
      key = args.key;
      data.api_key = Bright.token();
      data.email = Bright.email();
      if (key)
        data.key = key;
      jQuery.ajax({
        url:Bright.url() + '/realm_user/gcustom.json',
        data:data,
        dataType: 'jsonp',
        success:function (data) {
          Bright.log('in gcustom callback'); // this doesn't actually work.
        },
      })
    },
    updateRegistrationMeta: function(registration,data) {
      data["api_key"] = Bright.token
      jQuery.ajax({
        url:Bright.url() + Bright.getController('registration/' + registration.registration_guid + '/gcustom'),
        data:data,
        dataType: 'jsonp',
        success:function (data) {
          Bright.log('in gcustom callback'); // this doesn't actually work.
        },
      })
    },

    launchBoxTemplates: function() {
      return Bright.bright_template_list.getByType('course');
    },

    first_name: function () {
      return jQuery('meta[name=bright-first-name]').attr('content')
    },
    email: function () {
      return jQuery('meta[name=bright-email]').attr('content')
    },
    last_name: function () {
      return jQuery('meta[name=bright-last-name]').attr('content')
    },

    /**
     * Determine if Bright can be contacted properly.
     */

    /* if we discover we've been redirected from launch, flip this flag to true!
       we can check it later so as to perform conditional functionality in case we've returned from launch
       as opposed to just going to a page */

    redirectedFromLaunch: false,

    removeURIParameter: function(uriparse,param) {
      var ret;
      if (uriparse && uriparse.params) {
        ret = uriparse.protocol + ':' + '//' + uriparse.host;
        if (uriparse.port)
          ret += ':' + uriparse.port;
        ret += uriparse.path;
        delete uriparse.params[param];
        if (Object.keys(uriparse.params).length > 0) {
          ret += '?';
          var i = 0;
          for(var propt in uriparse.params){
            if (i++ != 0)
              ret += '&';
            ret += propt + '=' + uriparse.params[propt];
          }
        }
      }
      return ret;
    },

    parseURL: function(url) {
      var a =  document.createElement('a');
      a.href = url;
      return {
        source: url,
        protocol: a.protocol.replace(':',''),
        host: a.hostname,
        port: a.port,
        query: a.search,
        params: (function(){
          var ret = {},
          seg = a.search.replace(/^\?/,'').split('&'),
          len = seg.length, i = 0, s;
          for (;i<len;i++) {
            if (!seg[i]) { continue; }
            s = seg[i].split('=');
            ret[s[0]] = s[1];
          }
          return ret;
        })(),
        file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
        hash: a.hash.replace('#',''),
        path: a.pathname.replace(/^([^\/])/,'/$1'),
        relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
        segments: a.pathname.replace(/^\//,'').split('/')
      };
    },

    // course data is of format useful for launchers.  Since their can be multiples on each page, rendered without knowledge of each other
    // courseData is a hash like:
    //
    // courseData = {'course_id' => {}, ...}
    //
    // This when the data is rendered, we see elements in the page like
    //
    // bright_courses['course_id'] = {};
    //
    // IN the page header we see
    // var bright_courses = {};
    //
    // So basically this works no matter when a launchblock is rendered, how may times etc.
    courseData:{},
    // courseList is just as a list of courses in an array.  If the page requests it, it is rendered once and only once.
    // You *could* use the course list everywhere, but its a big a block of data and not necessary for the launch buttons.
    courseList:{},
    bright_template_list: new BrightTemplateList(),

    addTemplate: function(id,type,template) {
      return Bright.bright_template_list.add(new BrightTemplate(id,type,template))
    },
    getTemplate: function(id) {
      return Bright.bright_template_list.get(id);
    },

    // technically we shouldn't need to do this.....
    setCourseListTemplate: function(tag,value) {
      Bright.addTemplate(tag,value,'courselist')
    },

    // API independent
    getCourseList: function() {
      if (typeof(Bright.courseList.records) == "undefined")
        return Bright.courseList;
      return Bright.courseList.records;
    },

    renderGenericTemplate: function (id) {
      var template = Bright.getTemplate(id);

      if (template.isRendered) {
        Bright.log('courselist template already rendered for id: ' + id + '. Skipping...');
        return;
      }
      var templateType = template.type;
      Bright.log('Bright.renderGenericTemplate called with id: ' + id);

      var embedClass = template.data.embedClass;
      var embedLocale = template.data.embedLocale;
      var embedAttributes = template.data.embedAttributes;
      var pageAttributes = template.data.pageAttributes;
      var userAttributes = template.data.userAttributes;
      var customData =  template.data.customData;

      var handleBarSource = Bright.decodePhpRawEncode(template.data.template);

      var expansionDivName = 'bright-' + templateType + '-' + id;
      var brightElement = Bright.getContainer(templateType, id);

      Bright.log('templateCode is ' + handleBarSource);
      var handlebars_template = Handlebars.compile(handleBarSource);

      try {
        var html;
        var context = {
          attributes:embedAttributes,
          page:pageAttributes,
          user:userAttributes,
          custom:customData,
          containerId:id
        };

        if (template.type === 'courselist')
          context.courses = Bright.getCourseList();

        Bright.evaluateJsonStringField(context, 'attributes');
        Bright.evaluateJsonStringField(context, 'page');
        Bright.evaluateJsonStringField(context, 'user');
        Bright.evaluateJsonStringField(context, 'custom');

        if (context.attributes && context.attributes.refresh) {
          var refresh = context.attributes.refresh;
          Bright.setupRefresh(
            {
              fn: Bright.renderGenericTemplate,
              type: template.type,
              id: id,
              timevalue: refresh
            }
          );
        }

        html = handlebars_template(context);
        Bright.log('html is ' + html);
        brightElement.html(html);
      }
      catch (err) {
        brightElement.html("We are sorry but Bright cannot render your template.<br/><br/>An error follows:<br/><pre>" + err.stack + "</pre>Typically this is a coding error and should be repaired (if you are a developer) or reported to your site system administrator (if you are a user).<br/>");
      }
      template.isRendered = true;
    },

    // doh, you can't pass HTML into a DIV and get it out unmolested consistently.
    // AND php URIencode and JS don't work reflexively.  Well screw me.
    decodePhpRawEncode:function (text) {
      return decodeURIComponent(text);
    },

    hooks: function() {
      if (typeof(Bright.hooklist) === "undefined")
        Bright.hooklist = new BrightHookList(); // see top of file
      return Bright.hooklist;
    },

    addHook:function (hook,fn,priority) {
      return Bright.hooks().addHook(hook, new BrightHook(fn,priority));
    },

    runHook:function (hook) {
      return Bright.hooks().runHook(hook);
    },
    // END Hooks code

    evaluateJsonStringField:function (object, field) {
      if (typeof(object[field]) != "undefined" &&
          object[field] != "") {
        Bright.log("evaluating json of " + object[field]);
        try {
          object[field] = eval("(" + object[field] + ")");
        } catch (err) {
          Bright.log(err.stack);
        }
      }
    },

    // pulls out the course from the courselist based on courseId
    fetchCourseFromCourseList: function(courseId) {
      var ret = _.filter(Bright.getCourseList(),function(course) { return course.course_guid == this.courseId }, {courseId: courseId});

      if (ret.length == 1)
        return ret[0];
      return null;
    },

    getLaunchButtonClass: function(custom,review,template_type) {
      var button_type = (review ? "review" : "launch");
      return custom + " " + "bright-launchable bright-" + button_type + " " + "bright-" + template_type + "-" + button_type + "button";
    },

    launchBox:function (id) {
      var template = Bright.getTemplate(id);

      if (template.isRendered) {
        Bright.log('course template already rendered for id: ' + id + '. Skipping...');
        return;
      }
      var templateType = template.type;
      Bright.log('Bright.launchBox called with id: ' + id);

      var courseId = template.data.courseId;
      var courseLocale = template.data.embedLocale;
      var embedAttributes = template.data.embedAttributes;
      var pageAttributes = template.data.pageAttributes;
      var userAttributes = template.data.userAttributes;
      var customData = template.data.customData;

      var handleBarSource = Bright.decodePhpRawEncode(template.data.template);

      // the embed class should form the root of any element class="" entries
      // this allows some CSS customizations....

      var embedClass = template.data.embedClass;
      var launchButtonClass = embedClass + '-button';
      var reviewButtonClass = embedClass + '-review-button';

      Bright.setLocale(courseLocale);
      // this next line actually injects the launchbox ... not sure why its not created when the document is created though.
      var expansionDivName = 'bright-course-' + id;
      var brightElement = Bright.getLaunchBox(id);
      Bright.log('templateCode is ' + handleBarSource);
      var launchBoxTemplate = Handlebars.compile(handleBarSource);

      var courseData = Bright.courseData[courseId];

      if (!typeof courseData.attributes == "undefined")
        brightElement.html("A problem occured (error code 1000): Name conflict for attributes");
	  else {
        courseData.attributes = embedAttributes;
        courseData.page = pageAttributes;
        courseData.user = userAttributes;
        courseData.custom = customData;
        Bright.evaluateJsonStringField(courseData, 'attributes');
        Bright.evaluateJsonStringField(courseData, 'page');
        Bright.evaluateJsonStringField(courseData, 'user');
        Bright.evaluateJsonStringField(courseData, 'custom');

	    var launchCourseMsg = courseData.attributes['launch_button_text'] || Bright.t('Launch course');
	    var reviewCourseMsg = courseData.attributes['review_button_text'] || Bright.t('Review course');

        // Inject a launch button HTML doc into your context
        // Not that Handlebars requires users to use three handlebars to disable
        // html escaping, i.e. {{{launchbutton}}}

        courseData.launchbutton = '<input type="button" value="' +
		  launchCourseMsg + '" class="' +  Bright.getLaunchButtonClass(launchButtonClass,false,'course') + '"/>';
        courseData.reviewbutton = '<input type="button" value="' +
		  reviewCourseMsg + '" class="' + Bright.getLaunchButtonClass(reviewButtonClass,true,'course') + '"/>';

        if (courseData.attributes && courseData.attributes.refresh)
          Bright.setupRefresh({
            fn: Bright.launchBox,
            type: 'course',
            id: id,
            timevalue: courseData.attributes.refresh
          });

        try {
          brightElement.html(launchBoxTemplate(courseData));
          var launchButton = brightElement.find('.' + launchButtonClass).attr('disabled', 'disabled');
          var reviewButton = brightElement.find('.' + reviewButtonClass).attr('disabled', 'disabled');
          if (launchButton) {
            launchButton.attr('disabled', 'disabled');
            launchButton.click(function () {
              Bright.launch(id);
            });
            brightElement.find('.' + launchButtonClass).removeAttr('disabled');
          }
          if (reviewButton) {
            reviewButton.attr('disabled', 'disabled');
            reviewButton.click(function () {
              Bright.launch(id, true);
            });
            brightElement.find('.' + reviewButtonClass).removeAttr('disabled');
          }
        } catch (err) {
          brightElement.html("We are sorry but Bright cannot render your template.<br/><br/>An error follows:<br/><pre>" + err + "</pre>Typically this is a coding error and should be repaired (if you are a developer) or reported to your site system administrator (if you are a user).");
        }
      }
      template.isRendered = true;
    },
    launchCourseFromCourselister:function (course_guid, containerId, useReviewMode) {
      Bright.log('Bright.launchCourseFromCourselister called with course_guid: ' + course_guid + ', containerId: ' + containerId);
      var templateType = 'courselist';
      var templateElement = Bright.getContainerTemplate(templateType, containerId);

      var embedClass = templateElement.find('.embed-class').text();
      var courselister = jQuery('#bright' + '-' + templateType + '-' + containerId);
      var launchButton = [];
      var buttonPrefix = '.launchbutton-';
      if (useReviewMode)
        buttonPrefix = '.reviewbutton-';
      if (courselister.length == 1)
        launchButton = jQuery('input' + buttonPrefix + course_guid.replace('\.','\\.'));
      else {
        Bright.log('Your courselister template has not exported the Bright containerID into the contect for an individual course.  Because of this, this type of template will not be able to use multiplet times on a single page.  See the bright-learning-paths plugin for an example on how to create a courselister who\'s launchbuttons can be disabled correctly during the launch sequence');
        Bright.log('We will see if we can find a single launchbutton for this course.  If so, we will continue.  Otherwise your template will need to be modified to push the containerID into the course context');
        // since we don't know what container we were launched from, let's see if we can find a single launchbutton on the page.
        // If not, we can't disable the button .... die, or continue.  That is the question.
        // Note, "."s in the course guid must be escaped!
        launchButton = jQuery('input' + buttonPrefix + course_guid.replace('\.','\\.'));
      }

      if (launchButton.length == 1)
        Bright.launchCourseFromLaunchbutton(Bright.fetchCourseFromCourseList(course_guid),course_guid,launchButton, null, useReviewMode);
      else {
        Bright.log(launchButton);
        alert('We are sorry, but your course cannot be launched due to a misconfiguration.  Please contact your site administrator.  Typically this error relates to an error in the course template, and/or the inclusion of that template on a page multiple times.');
      }
    },

    /**
     * Return a jQuery reference to the Bright LaunchBox with the given root id.
     * This is different from the HTML element id.
     */

    getContainer:function (templateType, containerId) {
      var selector = '#bright-' + templateType + '-' + containerId;
      Bright.log('getContainer searching for ' + selector);
      return jQuery(selector)
    },

    getController: function(type) {
      var version = 2;
      if (Bright.url().match(/v1/)) {
        version = 1;
      }
      if (version == 1) {
        return '/' + 'scorm_cloud_' + type;
      } else {
        return '/' + type;
      }
    },

    getErrorDiv:function (templateType, containerId) {
      return jQuery('#bright-' + templateType + '-' + containerId + '-error');
    },

    getLaunchBoxErrorDiv:function (containerId) {
      return Bright.getErrorDiv('launchbox',containerId);
    },

    getLaunchBox:function (launchBoxId) {
      return Bright.getContainer('course', launchBoxId);
    },
    /**
     * Return a jQuery reference to the Bright LaunchBox Template with the given
     * root id. This is different from the HTML element id.
     */
    getContainerTemplate:function (templateType, containerId) {
      return jQuery('#bright-' + templateType + '-template-' + containerId);
    },

    getLaunchBoxTemplate:function (launchBoxId) {
      return Bright.getContainerTemplate('course', launchBoxId);
    },

    helper:function (name, f) {  // Syntactic sugar for Bright.helper
      Handlebars.registerHelper(name, f);
    },

    /**
     * This is a placeholder for the translation dictionaries stored in
     * bright.lang.js.
     */
    lang:{},
    /**
     * Launch the course contained within the Bright LaunchBox with the given
     * root id. This is different from the HTML element id.
     */
    launchCourseFromLaunchbutton: function(courseData, courseId, launchButton,
                                           errorDiv, useReviewMode) {
      var regId = ''
      var review = '';
      if (useReviewMode)
        review = 'true';

      if (courseData && courseData.registration)
        regId = courseData.registration.registration_guid;
      else
        regId = '';

      if (regId.length == 0) {
        launchButton.attr({
          'disabled':'disabled',
          'value':Bright.t('Retrieving launch registration and URL ...')
        });

        jQuery.ajax({
          url:Bright.url() + Bright.getController('registration/gcreate'),
          timeout: Bright.apiTimeout,
          data:{
            dont_duplicate:'1',
            api_key:Bright.token,
            course_guid:courseId,
            fname: Bright.first_name(),
            lname: Bright.last_name(),
            format: 'js'
          },
          dataType:'jsonp',
          error: function(XHR, textStatus, errorThrown) {
            Bright.errorPopup({
              'courseId': courseId,
              'errorThrown': errorThrown,
              'review': review,
              'controller': Bright.getController('registration/gcreate')
            });
          },
          success:function (data) {
            Bright.log('in gcreate callback'); // this doesn't actually work.
            if (data.error_code) {
              launchButton.attr({
                'disabled':'enabled',
                'value':Bright.t(data.error_message)
              });
            } else {
              if (data.registration_guid) {
                regId = data.registration_guid;
                Bright.log('Retrived registration ID ' + regId); // this doesn't actually work.
              } else {
                Bright.log('Odd no regid');
                launchButton.attr({
                  'disabled':'enabled',
                  'value':Bright.t('Something went wrong [no reg id], try again?')
                });
              }
              if (data.id) {
                Bright.launch_from_url({
                  launchButton: launchButton,
                  review: review,
                  regId: regId,
                  courseData: courseData
                });
              } else {
                Bright.log('Odd no bright ID');
                // what do we do NOW!?
                launchButton.attr({
                  'disabled':'enabled',
                  'value':Bright.t('Something went wrong [no bright id], try again?')
                });
              }
            }
          }
        });
      } else {
        Bright.launch_from_url({
          launchButton: launchButton,
          review: review,
          regId: regId,
          courseData: courseData
        });
      }
    },

    launch:function (id, review) {
      var errorDiv = Bright.getLaunchBoxErrorDiv(id); // right now, not used as I can't seem to get access to this element from the AJax success handler.
      var template = Bright.getTemplate(id);

      var embedClass = template.data.embedClass;
      var launchButtonClass = embedClass + '-button';
      if (review)
        launchButtonClass = embedClass + '-review-button';
      var launchButton = Bright.getLaunchBox(id)
        .find('.' + launchButtonClass);
      var courseLocale = template.data.courseLocale;
      Bright.setLocale(courseLocale); // can't be done at render time actually.

      var courseId = template.data.courseId;

      Bright.launchCourseFromLaunchbutton(Bright.courseData[courseId],courseId,
                                          launchButton,null,review);
    },

    "errorPopup": function(args) {
      var courseId = args.courseId;
      var errorThrown = args.errorThrown;
      var regId = args.regId;
      var review = args.review;
      var request = args.request;

      if (Bright.accessTokenExpired()) {
        alert('Your authentication token for the Bright learning server has expired.   We will now refresh the page; please try your request again.');
      } else {
        var errormsg = Bright.t("The Bright Learning Server did not respond.  If this is the first time you've seen this error, we will refresh the page for you and you should try again.\n\nIf the problem persists, please contact support@aura-software.com.  \n\nPLEASE include the following (preferably via cut and paste) in your crash report:\n\n");
        errormsg += Bright.t("Site URL: ") + window.location.href + "\n";
        errormsg += Bright.t("API Token:") + Bright.token() + "\n";
        errormsg += Bright.t("Bright URL: ") + Bright.url() + "\n";
        errormsg += Bright.t("User: ") + Bright.email() + "\n";
        if (courseId)
          errormsg += Bright.t("Course GUID: ") + courseId + "\n";
        if (regId)
          errormsg += Bright.t("Registration GUID: ") + regId + "\n";

        errormsg += Bright.t("ReviewMode: ") + review + "\n";
        if (request)
          errormsg += Bright.t("Request: ") + Bright.url() + request + "\n";
        errormsg += Bright.t("Date: ") + new Date().toString() + "\n";
        errormsg += Bright.t("HealthCheck: ") + Bright.healthReportShort() + "\n";
        errormsg += Bright.t("Error: ") + errorThrown + "\n\n";
        errormsg += Bright.t("We are committed to resolving this issue promptly.\n\n");
        errormsg += Bright.t("Team Bright - Aura Software");
        if (_errs)
          _errs.push(new Error(errormsg));
        alert(errormsg);
      }
      location.reload();
    },

    launch_from_url:function (args){
      var launchButton = args.launchButton;
      var regId = args.regId;
      var courseData = args.courseData;
      var review = args.review;

      launchButton.attr({
        'disabled':'disabled',
        'value':Bright.t('Retrieving launch URL...')
      });

      Bright.log('retrieving launch URL for ' + regId);

      var jsonpData = {
        format: 'json',
        api_key: Bright.token,
        review: review,
        redirect_url: window.location.href,
        launching: 1
      };

      // emulate the functionality of the old SCORMCloud plugin if "autotag" is true.
      // that is tag the course in the course provider with the page/post categories.
      //
      // Kind of a broken feature, but the customer asks for it.

      if (courseData &&
          courseData.attributes &&
          courseData.attributes.autotag &&
          courseData.page &&
          courseData.page.categories
         ) {
        jsonpData.tags = _.collect(courseData.page.categories, function (cat) { return cat.name;}).join(',');
      }

      jQuery.ajax({
        url:Bright.url() + Bright.getController('registration/') + regId + '/launch_url',
        data: jsonpData,
        dataType:'jsonp',
        timeout: Bright.apiTimeout,
        error: function(XHR, textStatus, errorThrown) {
          Bright.errorPopup({
            regId: regId,
            errorThrown: errorThrown,
            review: review,
            request: Bright.getController('registration/') + regId + '/launch_url'
          });
        },

        success:function (data) {
          // this message does not appear on the console....
          Bright.log('Retrieved launch url: ' + data.launch_url);
          launchButton.attr({
            'value':Bright.t('Launching course...')
          });
          window.location = data.launch_url;
        }
      });

    },
    // the load function causes the bright templates on the page to render.
    render: function(template) {
      if (template.type === 'course')
        return Bright.launchBox(template.id);
      else if (template.type === 'courselist' || template.type === 'generic')
        return Bright.renderGenericTemplate(template.id);
      else
        alert('never heard of template of type ' + template.type);
    },
    load: function() {
      // execute tests if loaded.
      Bright.runHook('before_load_tests');
      Bright.runHook('before_load');
      Bright.refreshLock = false; // this restarts the refresh logic
      Bright.preProcessCourseData(); // implement syntactic sugar
      for (var i = 0; i < Bright.bright_template_list.length; i++)
        Bright.render(Bright.bright_template_list[i]);
      Bright.runHook('after_load');
      // execute tests if loaded.
      Bright.runHook('after_load_tests');
    },
    /**
     * A safe way to log to the browser console.
     */
    log:function (message) {
      if (Bright.verbose) {
        if (typeof console != 'undefined') {
          console.log(message);
        }
      }
    },

    /*
     * preProcessCourseData()
     *
     * Implements any kind of syntactic sugar for the elements of Bright.courseData
     *
     * So far, we:
     *
     *   - If there is one and only one registration, we set a field called 'registration:'.
     *
     */

    preProcessCourseData:function () {

      /* do bright_course data */
      if (typeof(bright_courses) != "undefined") { // just sort of by convention for the moment.
        Bright.setCourseData(bright_courses);
        for (courseId in Bright.courseData) {
          if (! (Bright.courseData[courseId] instanceof BrightCourse))
            Bright.courseData[courseId] = new BrightCourse(Bright.courseData[courseId]);
        }
      }
      if (typeof(bright_courselist) != "undefined") {
        var course_list_length = bright_courselist
        good_courses = [];
        bright_courselist.forEach(function (course) {
          if (! (course instanceof BrightCourse))
            course = new BrightCourse(course);
          good_courses.push(course);
        });

        Bright.setCourseList(good_courses);
      }
    },
    /**
     * Render a Bright LaunchBox in an element given an ID.
     */

    setCourseData:function (data) {
      Bright.courseData = data;
    },

    setCourseList:function (data) {
      Bright.courseList = data;
    },


    /**
     * Set the locale of the translation dictionary.  This is expected to be
     * something like en-US or de-DE.  See bright.lang.js for available locales.
     */
    setLocale:function (locale) {
      if (!locale) {
        locale = jQuery('html').attr('lang');
      }
      Bright.log('Setting Bright locale to ' + locale);
      if (Bright.lang[locale]) {
        jQuery.i18n.setDictionary(Bright.lang[locale]);
      }
    },

    refreshLock: false, /* if we are hitting the BrightApi and rewriting the value of Variables ... this will be true */

    reload: function(type,refreshArgs) {
      var ajaxData = jQuery.extend(true, {}, refreshArgs); // deep copy these.

      if (type == 'course') {
        // jQuery.ajax({
        //   url: Bright.url() + '/course',
        //   data: {
        //     course_guid:
        Bright.log('nothing yet....');
      } else if (type == 'courselist') {
        //      ajaxData.format = 'json';
        ajaxData.api_key = Bright.token();
        ajaxData.include_registrations = 1;
        ajaxData.api_template = 'public_minimum';
        jQuery.ajax({
          url: Bright.url() + '/course',
          data: ajaxData,
          dataType:'jsonp',
          success: function(data) {
            bright_courselist = data;
            Bright.preProcessCourseData(); // implement syntactic sugar
            Bright.log('in ajax callback');
          }
        });
      }
    },

    setupRefresh: function(args) {
      var type = args.type;
      var id = args.id;
      var timevalue = args.timevalue;
      var fn = args.fn;
      var refreshArgs = args.refreshArgs;

      setTimeout(function () {
        if (Bright.refreshLock != true) {
          Bright.refreshLock = true;
          try {
            Bright.reload(type,refreshArgs);
            fn(id);
            Bright.runHook('after_refresh');
          } catch (err) {
            Bright.refreshLock = false;
            Bright.log(err);
          }
        }

        Bright.refreshLock = false;
      }, timevalue);
    },
    /**
     * Return the Bright authentication token.  This token is located in a
     * <meta/> element within the head of the HTML document.
     */
    token:function () {
      return jQuery('meta[name=bright-token]').attr('content');
    },
    apiKey: function() {
      return Bright.token();
    },
    /**
     * Translate the given string.  Translations are stored in bright.lang.js.
     */
    deprecation: function(msg) {
      msg = "DEPRECATION: " + msg;
      if (Bright.testing)
        alert(msg)
      else
        Bright.log(msg);
    },

    deprecateHelperFor: function(oldHelper,newHelper) {
      Bright.deprecation(oldHelper + " Bright/Handlebars helper is deprecated.  Use " + newHelper + " instead");
    },

    t:function (s) {
      return jQuery.i18n._(s);
    },
    /**
     * Return the Bright API url.  This address is located in a
     * <meta/> element within the head of the HTML document.
     */
    url:function () {
      return jQuery('meta[name=bright-api-url]').attr('content');
    },
    urlBase: function () {
      pathArray = Bright.url().split( '/' );
      protocol = pathArray[0];
      host = pathArray[2];
      return protocol + '//' + host;
    },
    getHealth: function () {
      return Bright.health;
    },
    enableLaunchButtons: function () {
      jQuery('.bright-launchable').removeAttr('disabled');
      jQuery('.bright-launchable').removeAttr('title');
      jQuery('.bright-launchable').removeClass('health-failed-button-disabled');
      Bright.runHook('enableLaunchButtons');
    },
    disableLaunchButtons: function() {
      jQuery('.bright-launchable').attr('disabled','disabled');
      jQuery('.bright-launchable').attr('title',Bright.t('Your device does not have connectivity to the learning server, please check your network or contact support.  Err: ' + Bright.healthReportShort()));
      jQuery('.bright-launchable').addClass('health-failed-button-disabled');
      Bright.runHook('disableLaunchButtons');
    },
    checkHealth: function (async) {
      var ajaxTime= new Date().getTime();      
      return jQuery.ajax({
        url: Bright.urlBase() + '/health_check',
        timeout: Bright.apiTimeout,
        success: function() {
          var time_taken = new Date().getTime()-ajaxTime;
          Bright.healthAttempts += 1;
          Bright.healthSuccess += 1;
          Bright.healthTotalTime += time_taken;
          Bright.enableLaunchButtons();
          Bright.log(new Date() + ': bright health is good. (' + time_taken + 'ms)');
          Bright.health = true;
        },
        error: function (request, status, error) {
          var time_taken = new Date().getTime()-ajaxTime;
          Bright.healthAttempts += 1;
          Bright.healthFail += 1;
          Bright.healthTotalTime += time_taken;
          Bright.disableLaunchButtons();
          Bright.log(new Date() + ': bright health is bad, status: ' + status + ', error: ' + error + '(' + time_taken + 'ms)');
          Bright.health = false;
        }
      });
    },

    healthReportShort: function() {
      return Bright.healthAttempts + '.' + Bright.healthSuccess + '.' + Bright.healthFail + '.' + Math.round(Bright.healthTotalTime / Bright.healthAttempts);
    },

    healthReport: function() {
      Bright.log('Current Health: ' + Bright.health);
      Bright.log('Total health attempts: ' + Bright.healthAttempts);
      Bright.log('Total success: ' + Bright.healthSuccess);
      Bright.log('Total failure: ' + Bright.healthFail);
      Bright.log('Average Time(ms): ' + Bright.healthTotalTime / Bright.healthAttempts);

    },
    /**
     * Used to determine if console logging is enabled.
     */
    "verbose": true,
    "tokenExpiration": function() {
      var bright_token_expiration_selected = jQuery('meta[name=bright-token-expiration]');
      if (bright_token_expiration_selected.length < 1)
        return null;
      return new Date(bright_token_expiration_selected.attr("content"));
    },

    "accessTokenExpired": function() {
      var bright_token_expiration = Bright.tokenExpiration();
      if (bright_token_expiration)
        return (new Date() > bright_token_expiration ) ? true : false;
      return false;
    }
  }
}

jQuery(document).ready(function () {
  var uriparse = Bright.parseURL(window.location.href);
  // we are redirecting from a course launch.
  // lets force a crawl and strip away the GET bright_redirect_from_launch parameter,
  // then refresh the page.
  //
  // This works around the issue in SCORMCloud where you can't really tell if a course is running/exited or not.

  if (uriparse.params && uriparse.params.bright_redirect_from_launch) {
    Bright.redirectedFromLaunch = true;
    var regId = uriparse.params.bright_redirect_from_launch;

    if (regId) {
      jQuery.ajax({
        url:Bright.url() + Bright.getController('registration'),
        data:{
          registration_guid: regId,
          api_key:Bright.token,
          crawl: 't',
          format:'json'
        },
        dataType:'jsonp',
        success: function() {
          // strip away the bright_redirect_from_launch paramater and refresh the page.
          var replace = Bright.removeURIParameter(uriparse,'bright_redirect_from_launch');
          if (replace) {
            replace += ((replace.indexOf("?") != -1) ? "&" : "?") + "bright_registration_refreshed=t";

            if (window.location.replace) {
              window.location.replace(replace);
            } else {
              window.location = replace;
            }
          }
        },
        error: function (request, status, error) {
          if (request.responseText) {
            alert(request.responseText);
          } else {
            alert("error E1094 in request: " + error + ".  Please contact your system adminstrator or support.");
          }
        }
      });
    } else {
      Bright.load();
    }
  } else {
    Bright.load();
  }
});

jQuery(document).ready(function () {
  setTimeout(function () {
    var uriparse = Bright.parseURL(window.location.href);

    if (typeof(bright_user_attributes) != "undefined" && ! uriparse.params.bright_block_usermeta_sync) {
      var data = {};
      data[window.location.hostname] = eval('('+bright_user_attributes+')');
      Bright.updateRealmUserMeta({
        data: data,
        key: 'hostdata'
      });
    }
  }, 500);
});

/* add 'auto_launch' functionality to single course launchers
   templates that set 'auto_launch=[any value]', the launch button
   will click itself */

Bright.addHook('after_load', function () {
  setTimeout(function () {
    Bright.bright_template_list.getByType('course').forEach(function(template) {
      if (template.data.embedAttributes) {
        var arguments = eval("(" + template.data.embedAttributes + ")");
        if (arguments.auto_launch) {
          var uriparse = Bright.parseURL(window.location.href);
          if ((uriparse.params &&
               (uriparse.params.bright_redirect_from_launch ||
                uriparse.params.bright_registration_refreshed)))
            1;
          else
            jQuery('div#bright-course-' + template.id + ' input[type="button"]').click();
        }
      }
    });
  });
});


Bright.helper('rightnow',function(context) {
    return new Date().toString();
});

// look up a course by ID, and pull out a field.  Requires a field='title' type argument.
Bright.helper('fetchforcourse', function(context,options) {
  var c = Bright.fetchCourseFromCourseList(context);
  return c[options.hash['field']];
});

Bright.name_dump = function (obj) {
  names = Object.getOwnPropertyNames(obj);
  var ret = '';
  for (var i=0; i< names.length; i++) {
    if (ret.length > 0)
      ret += ', ';
    ret += names[i];
  }
  return ret;
}

Bright.var_dump = function(obj,variable_name,indent) {
  if (indent > 500)
    return 'Bright.var_dump(): something fishy here ... nested too deep';

  var names = Object.getOwnPropertyNames(obj);

  var ret = '{\n';

  for (var i=0; i< names.length; i++) {
    if (i > 0) {ret += ",\n"}

    for (var j=0; j<= indent; j++) {ret += ' '}

    ret += '"' + names[i] + '": ';
    if (obj[names[i]])
      if (obj[names[i]].toString().match(/\[object Object\]/))
        ret += variable_name ? Bright.var_dump(obj[names[i]],names[i],indent+2) : '{nested data; skipped }'; // not sure if we really need this nested data thing anymore
      else
        ret += '"' + obj[names[i]] + '"';
    else
      ret += '""';
  }
  if (names.length > 0) {ret += "\n"}
  for (var i=0; i< indent; i++) {ret += ' '}

  return ret + '}';
};

Bright.helper('dump2', function(variable_name,context,options) {
  var ret = 'Debug data for rendering {{{ \'' + variable_name + '\' ' + variable_name + '}}}  <br/>';
  ret += 'Top Level Names Are: ' + Bright.name_dump(context);
  ret += '<br/>Data Currently In Variable:<br/><pre>';
  ret += Bright.var_dump(context,variable_name,0);
  ret += '</pre><hr/>';
  return ret;
});

Bright.helper('debug', function(context,options) {
  var variable_name = options.hash["title"] || 'anonymous passed value; use {{{debug object title=\'[name]\'}}} to change this message';
  var ret = '{{{debug}}} Information for ' + variable_name + ":<br/>\n";
  return ret + '<pre>' + Bright.var_dump(context,variable_name,0) + '</pre><hr/>';
});

// Deprecated ; now bGetQueryParameter
Bright.helper('getQueryParameter', function(param) {
  Bright.log('DEPRECATION WARNING : Don\'t use getQueryParameter ; bGetQueryParameter()');
  var uriparse = Bright.parseURL(window.location.href);
  return (uriparse && uriparse.params) ? uriparse.params[param] : '';
});

// use the first row as the context
Bright.helper('dump', function(context,options) {
  var ret = 'Top Level Names Are: ' + Bright.name_dump(context);
  ret += '<br/>Data Currently In Variable:<br/><pre>';
  var mydata = '';
  ret += Bright.var_dump(context,null,0);
  ret += '</pre>';
  return ret;
});

Bright.helper('first', function(context, options) {
  var ret = '';
  var up_to = options.hash['up_to'] || 1;

  for (var i = 0; i <= (up_to - 1); i++ ) {               //reverse iteration as may be destructive
    if (typeof(context) != "undefined" && typeof(context[i]) != "undefined") {
      ret += options.fn(context[i]);
    } else {
      ret += options.inverse(context);
    }
  }
  return ret;
});

Bright.helper('bUnlessBlank', function(item, block) {
  return (item && item.replace(/\s/g,"").length) ? block.fn(this) : block.inverse(this);
});



Bright.helper('bStore',function(tag,value) {
  Bright.templateStore[tag] = value;
});

Bright.helper('bFetch',function(tag) {
  return Bright.templateStore[tag];
});


// bGetValueFromObject
//
// Usage: You can use this to conditionally access an object by a different field name.
//
// Example:
//
//   {{(bGetValueFromObject registration (bGetDefaultValue attributes.criteria_field 'success'))}}
//
// Thus bGetDefaultValue evaluates the attributes field, and if 'criteria_field' is selected, this is used to extract
// data from the registration object.
// Otherwise, we will use the default of 'success'.

Bright.helper('bGetValueFromObject',function(object,fieldName) {
  return object ? object[fieldName] : null;
});

//
// bGetDefaultValue
//
// Returns a default value in place of an unset template attribute, for example.
//
// Usage:
//
// {{#compare (bGetDefaultValue attributes.success_criteria 'passed') registration.success}}
// matched
// {{else}}
// did not match
// {{/compare}}

Bright.helper('bGetDefaultValue',function(value,defaultValue) {
  return value ? value : defaultValue;
});

// Updates brightHealth on a regular basis

jQuery(document).ready(function () {
  if (Bright.ready()) {
    Bright.checkHealth();
    if (Bright.healthInterval === undefined) 
      Bright.healthInterval = setInterval(Bright.checkHealth, Bright.healthCheckintervalTime);
    // Bright.healthInterval is a code that can be used to cancel the timer.
    // see https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/setInterval
    //
  }
});


// Local variables:
// indent-tabs-mode: nil
// js-indent-level: 2
// End:
