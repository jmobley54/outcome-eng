
// Only loads if jstest=true in Bright options.

QUnit.test('basic',function(assert) {
  assert.equal(typeof(Bright),"object",'Bright exists as an Object');
  assert.equal(Bright.bright_template_list instanceof Array,true, 'Bright.bright_template_list exists and is an Array');
  assert.equal(typeof(Bright.bright_template_list),"object");
});

QUnit.test('api',function(assert) {
  assert.equal(typeof(Bright.apiKey),"function");
  assert.equal(typeof(Bright.apiKey()),"string");
  assert.equal(Bright.apiKey().length,32);
});

Bright.addHook('before_load_tests', function () {
  if (Bright.bright_template_list && Bright.bright_template_list.length > 0) {
	Bright.bright_template_list.forEach(function(template) {
	  QUnit.test('basic template',function(assert) {
		assert.equal(template instanceof BrightTemplate,true,'each member of Bright.bright_template_list is an object of type BrightTemplate');
		assert.equal(template.brightTemplate,true);
		assert.equal(typeof template.data,"object");
		assert.equal(typeof template.divId,"string");
		assert.equal(template.getTemplateDiv().length,1); // fetching the div gives us one and only one div!
		assert.equal(Bright.bright_template_list.get(template.id),template); // test that the get function returns the template
	  });
	  QUnit.test(template.type + ': ' + template.id,function(assert) {
		if (template.type == "generic") {
		  assert.equal(1,1,"just a dummy test for " + template.type);
		}
		else if (template.type == "courselist") {
		  assert.equal(1,1,"just a dummy test for " + template.type);
		} else if (template.type == "course") {
		  assert.equal(1,1,"just a dummy test for " + template.type);
		}
	  });
	});
  }
});


Bright.addHook('after_load_tests', function () {
  if (Bright.bright_template_list && Bright.bright_template_list.length > 0) {
	Bright.bright_template_list.forEach(function(template) {
      var name = template.data.templateName;
	  QUnit.test(name,function(assert) {
		assert.equal(template instanceof BrightTemplate,true,'each member of Bright.bright_template_list is an object of type BrightTemplate');
		assert.equal(template.brightTemplate,true);
		assert.equal(typeof template.data,"object");
		assert.equal(typeof template.divId,"string");
		assert.equal(template.getTemplateDiv().length,1); // fetching the div gives us one and only one div!
		assert.equal(Bright.bright_template_list.get(template.id),template); // test that the get function returns the template
        var selected = jQuery('#' + template.divId);
        assert.ok(selected.length > 0, 'found a div with the expanded template for ' + name);
	  });
	  QUnit.test(template.type + ': ' + template.id,function(assert) {
		if (template.type == "generic") {
		  assert.equal(1,1,"just a dummy test for " + template.type);
		}
		else if (template.type == "courselist") {
		  assert.equal(1,1,"just a dummy test for " + template.type);
		} else if (template.type == "course") {
		  assert.equal(1,1,"just a dummy test for " + template.type);
		}
        var selected = jQuery('#' + template.divId);
        assert.ok(selected.length > 0, 'found a div id of ' + template.divId + ' for expanded template for ' + name);
	  });
	});
  }
});
