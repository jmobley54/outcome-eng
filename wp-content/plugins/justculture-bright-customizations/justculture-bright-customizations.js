Bright.addHook('after_load',function() {
  setTimeout(function () {

	var matrix = function () {
	  var tds = jQuery("td.course");
	  var courses = [];
	  tds.each(
		function () {courses.push(jQuery(this).text());}
	  );
	  var courselist = _.uniq(courses);

      // this can happen if someone dequeues the jquery data table code for instance [Trac #780].
	  if (typeof(jQuery(this).dataTable) == "undefined") 
        return;

      var dt = jQuery(this).dataTable({
        iDisplayLength: 10,
		"dom": 'T<"clear">lrtip',
		"bAutoWidth": false,
        "order": [[ 4, "desc" ]],
        aoColumns: [
            {"sType": "string" }, // ,"sWidth": "8%"},
            {"sType": "string" }, // ,"sWidth": "8%"},
            {"sType": "string" }, // ,"sWidth": "8%"},
			{"sType": "string" }, // ,"sWidth": "8%"},
			{"sType": "string" }, // ,"sWidth": "8%"},
            {"sType": "string" }, // ,"sWidth": "8%"},
            {"sType": "numeric" }, // ,"sWidth": "8%"},
            {"sType": "numeric" }, // ,"sWidth": "8%"},
          ],


		"tableTools": {
		  "sSwfPath": Bright.template_pack.table_tools_swf_path,
          "sRowSelect": "multi",
          "aButtons": [
            {
              "sExtends": "csv",
              "sButtonText": "Save Filtered Data Set",
			  "oSelectorOpts": { filter: 'applied', order: 'current' },
			  "bFooter": false,
			  "sNewLine": "auto"
            }
          ]
        },
      });
      dt.columnFilter(
        {
          sPlaceHolder: "head:before",
          aoColumns: [
            {type: "text"}, //         <th>Email</th>
            {type: "text"}, //      
            {type: "text"}, //      
            {type: "text"}, //      
	  		{type: "date-range"}, //
            {type: "text"}, //      
	  		{type: "number-range"}, 
	  		{type: "number-range"}
            // {type: "text"}, //         <th>Job Type</th>
            // {type: "text"}, //         <th>Manager Name</th>
//            {type: "select", values: courselist.sort()}, //         <th>Course</th>
//            {type: "select", values: ['complete','incomplete','unknown'], bRegex: false, bSmart: false}, //         <th>Status</th>
//	  		{type: "number-range"}, // 
//	  		{type: "date-range"}, //

          ]
        }
      );
      jQuery.datepicker.regional[""].dateFormat = 'yy-mm-dd';
      jQuery.datepicker.setDefaults(jQuery.datepicker.regional['']);
	};

    jQuery("table.jc_license-results-matrix").each(matrix);
    jQuery('span.filter_column').removeClass('form-control');
  }, 1);
});
