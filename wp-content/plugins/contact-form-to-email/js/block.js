function cfte_renderForm(id) {      
    if ($("#form_structure"+id).length)
    {
        try {
        var cp_appbooking_fbuilder_myconfig = {"obj":"{\"pub\":true,\"identifier\":\"_"+id+"\",\"messages\": {}}"};
        var f = $("#fbuilder_"+id).fbuilder($.parseJSON(cp_appbooking_fbuilder_myconfig.obj));
        f.fBuild.loadData("form_structure"+id);                     
        } catch (e) { setTimeout ('cfte_renderForm('+id+')',500); }
    }
    else
    {
        setTimeout ('cfte_renderForm('+id+')',50);
    }
}  
jQuery(function()
	{
		(function( blocks, element ) {
            var el = wp.element.createElement,
                source 		= blocks.source,
	            InspectorControls = wp.editor.InspectorControls,
				category 	= {slug:'contact-form-to-email', title : 'Contact Form to Email'};
                
				
		    var _wp$components = wp.components,
                SelectControl = _wp$components.SelectControl,
                ServerSideRender = _wp$components.ServerSideRender;                

			/* Plugin Category */
			blocks.getCategories().push({slug: 'cpcfte', title: 'Contact Form to Email'}) ;

			
            /* ICONS */
         	const iconCPCFTE = el('img', { width: 20, height: 20, src:  "data:image/gif;base64,R0lGODlhFAAQAOYAAP//////AP8A//8AAAD//wD/AAAA/wAAAPH2+/T4/Ofw+Mne79Pk8t3q9d/r9ePu9wxrtQxstQ1stg1rtQ1stQ5ttg9ttg9uthBtthButhFuthhzuRl0uRx1uh11uh12uh52ux53ux93uyF4uyF5uyJ5uyN6vCR6vCd8vid7vSh9vSp+vi6AvzOEwTeFwjmHwz2JxECLxUCMxUSNxkSOxkWPxlOXy1SXy12czl6ezl+ezmCezmKfz2Kgz2Sh0Gai0Wai0Gei0Wej0Wij0Wum0mym02ym0nmt1nuv132w2H6x2ICx2ICy2IGz2YS02oa12oa22om424q424y53I+73Y663JG83pG83ZvC4ZrC4JzD4Z/F4qbJ5KjK5afJ5KjK5L3X68HZ7MTb7cne7uHt9uDs9evz+fL3+9Lk8eLu9uny+PD2+vj7/fP4+/b6/P///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAG8ALAAAAAAUABAAAAfhgACCg4SFhl84QUNDQoxCjY+MQztUAEpkPREQEZybnhCeNA5XGjBqWJqfoJucSAk3FSQRJgtiHxSaFJ8YXQ4uESEjoBlcZS66mhEUJ2NhHbogI8qgR2Y2uJovDlnYECAlERKrVgBJmzoAYMqcESCyFLg5DkxnWk1sT2hLuLjRrDxuYkRg0aBNDQgoFDgBRUGEsAhCEAgEtQEcPBUPpkCYEIIEBCFrZqiCgC3CijRSIniwAMSNDFDJSKbSlEJNlAtQ0rRAVhLeKlAcGGwB4MUHkSJIixgxohTp0iI/qhiaSjUQADs=" } );             

			/* Form's shortcode */
			blocks.registerBlockType( 'cfte/form-rendering', {
                title: 'Contact Form to Email', 
                icon: iconCPCFTE,    
                category: 'cpcfte',
				supports: {
					customClassName: false,
					className: false
				},
				attributes: {
			      	  formId: {
			            type: 'string'
		              },
			      	  instanceId: {
			            type: 'string'
		              }
			      },           
	        edit: function( { attributes, className, isSelected, setAttributes }  ) {             
                    const formOptions = cfte_forms.forms;
                    if (!formOptions.length)
                        return el("div", null, 'Please create a contact form first.' );
                    var iId = attributes.instanceId;
                    if (!iId)
                    {                        
                        iId = formOptions[0].value+parseInt(Math.random()*100000);
                        setAttributes({instanceId: iId });
                    }
                    if (!attributes.formId)
                        setAttributes({formId: formOptions[0].value });
                    cfte_renderForm(iId);
			    	var focus = isSelected; 
					return [
						!!focus && el(
							InspectorControls,
							{
								key: 'cpcfte_inspector'
							},
							[
								el(
									'span',
									{
										key: 'cpcfte_inspector_help',
										style:{fontStyle: 'italic'}
									},
									'If you need help: '
								),
								el(
									'a',
									{
										key		: 'cpcfte_inspector_help_link',
										href	: 'https://form2email.dwbooster.com/contact-us',
										target	: '_blank'
									},
									'CLICK HERE'
								)
							]
						),
						el(SelectControl, {
                                value: attributes.formId,
                                options: formOptions,
                                onChange: function(evt){         
                                    setAttributes({formId: evt});
                                    iId = evt+parseInt(Math.random()*100000);
                                    setAttributes({instanceId: iId });
                                    cfte_renderForm(iId);                                   
			    				},
                        }),
                        el(ServerSideRender, {
                             block: "cfte/form-rendering",
                             attributes: attributes
                        })		
					];
				},

				save: function( props ) {
			    	return null; 
				}
			});

		} )(
			window.wp.blocks,
			window.wp.element
		);
	}
);