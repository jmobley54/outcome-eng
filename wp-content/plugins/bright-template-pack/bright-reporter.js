
function BubbleJsonDoc() {
  this.root = {
	"name": "bright",
	"children": [
	  {
		"name": "customer",
		"children": []
	  }
	]
  };
}

BubbleJsonDoc.prototype.populateFromBrightQuery = function(args) {
  console.log('in populateFromBrightQuery');
  var queryName = args.queryName;
  var titleFieldName = args.titleFieldName;
  var valueFieldName = args.valueFieldName;

  var data = {};
  data.accessToken = Bright.token();
  data.name = queryName;
  data.query_scope = 'bright'
	
  jQuery.ajax({
    url:Bright.url() + '/stored_query/run',	
    data: {
	  "accessToken": Bright.token(),
	  "name": queryName,
	  "query_scope": "bright"
	},
    "dataType": 'jsonp',
	"context": {
	  bubbleJsonDoc: this,
	  after: args.after
	},
    "success": function (data) {
	  data.forEach(function (d) {
		this.root.children[0].children.push(
		  {
			"name": d[titleFieldName], 
			"size": d[valueFieldName]
		  }
		);
	  },this.bubbleJsonDoc);
	  if (this.after) 
		this.after.call(this.bubbleJsonDoc);
	}
  });
};


// Class: BrightBubbleReport
// Prototype: hash
//
// Attributes:
// 
// * div - root div for the report.
//


function BrightBubbleReport(div) {
  'use strict';
  this.div = div;
  return this;
}

BrightBubbleReport.prototype.renderReport = function (args) {
  var bubbleJsonDoc = args.bubbleJsonDoc;
  if (!bubbleJsonDoc)
	throw new Error('set "bubbleJsonDoc" in call to BrightBubbleReport.renderReport()');
  var settings = args.settings || {};

  var diameter = 900;
  
  // set diameter.
  // it could be here
  // <div data-diameter="xxx"/>
  // or passed in the settings {} hash.

  if (this.div.data('diameter')) 
	diameter = this.div.data('diameter');

  if (settings && settings.diameter)
	diameter = settings.diameter;
  
  format = d3.format(",d"),
  color = d3.scale.category20c();

  var bubble = d3.layout.pack()
	.sort(null)
	.size([diameter, diameter])
	.padding(1.5);

  // http://stackoverflow.com/questions/28045107/how-to-convert-a-jquery-object-into-a-d3-object
  var svg = d3.selectAll(this.div).append("svg")
    .attr("width", diameter)
    .attr("height", diameter)
    .attr("class", "bubble");

  // Returns a flattened hierarchy containing all leaf nodes under the root.
  var classes = function(root) {
	var classes = [];

	function recurse(name, node) {
      if (node.children) node.children.forEach(function(child) { recurse(node.name, child); });
      else classes.push({packageName: name, className: node.name, value: node.size});
	}

	recurse(null, root);
	return {children: classes};
  }


  var node = svg.selectAll(".node")
    .data(bubble.nodes(classes(bubbleJsonDoc.root))
		  .filter(function(d) { return !d.children; }))
    .enter().append("g")
    .attr("class", "node")
    .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
  
  node.append("title")
    .text(function(d) { return d.className + ": " + format(d.value); });
  
  node.append("circle")
    .attr("r", function(d) { return d.r; })
    .style("fill", function(d) { 
	  console.log(d);
	  return color(d.className); 
	});
  
  node.append("text")
    .attr("dy", ".3em")
    .style("text-anchor", "middle")
    .text(function(d) { return d.className.substring(0, d.r / 3); });
};

Bright.addHook('after_load',function() {
  var ndx = 0;
  jQuery('div.bright-course-usage-bubbles').each(function () {
	console.log('processing course bubbles div');
	var data;
	data = new BubbleJsonDoc();
	report = new BrightBubbleReport(jQuery(this))
	data.populateFromBrightQuery({
	  "queryName": "registrations_by_course_for_realm",
	  "titleFieldName": "title",
	  "valueFieldName": "num_registrations",
	  "after": function () {
		console.log('in after callback');
		report.renderReport({"bubbleJsonDoc": this});
	  }
	});
	jQuery(this).css("font", "10px sans-serif");
  });
});
