// jCombo v2.0
// Carlos De Oliveira cardeol@gmail.com (c) Feb 2013
;(function ($, window, document, undefined) {
	$.fn.jCombo = function(url, opt) {		
		var defaults = {
				parentField: null,
				first_optval : "__jcombo__",
				selected_value : "0",
				initial_text: "-- Please Select --",
				method: "GET",
				dataType: "jsonp"								
		};				
		var opt = $.extend( defaults, opt) ;
		var obj = $(this);
		if(opt.parentField!=null) {
			var $parentField = $(opt.parentField);			
			$parentField.removeAttr("disabled","disabled");
			$parentField.bind('change',  function(e) {
				obj.attr("disabled","disabled");
				if($(this).val() != opt.first_optval || globalParent != null) obj.removeAttr("disabled");
				__render(	obj, { 
					url: url, 
					id: $(this).val(),
					first_optval: opt.first_optval, 
					initext: opt.initial_text, 
					inival: opt.selected_value,
					method: opt.method,
					dataType: opt.dataType
				});
			});
		} else __render(obj,{ 
			url: url,
			id: "",
			first_optval: opt.first_optval,
			initext: opt.initial_text,
			inival: opt.selected_value,
			method: opt.method,
			dataType: opt.dataType
		});					
		function __render($obj,$options) {			
            var parentColumn = (globalParent != null) ? globalParent.split('-') : (opt.parentField) ? opt.parentField.split('-') : '';
			if($options.id==null) return false;
            if(parentColumn){
                parentColumn = parentColumn[2];
            }
            var locId = (globalParent != null) ? $(globalParent).val() : $options.id;
			$.ajax({
				type: $options.method,
				dataType: $options.dataType,					
				url: $options.url,
                data: {
                    parent: parentColumn,
                    id: locId
                },
                error:function(){
                    globalParent = (globalParent != null) ? globalParent : opt.parentField;
                    var response = '<option value="" selected="selected">N/A</option>';
					$obj.html(response);					           										
					$obj.trigger("change");
                },
				success: function(data){
                    globalParent = null;
					var response = '<option value="' + $options.first_optval + '">' + $options.initext + '</option>';
					var selected;
					for(var index in data) {
						selected = (index==$options.inival)?' selected="selected"':'';
						response += '<option value="' + index + '"' + selected + '>' + data[index] + '</option>';
					}
					$obj.html(response);					           										
					$obj.trigger("change");
				}
			});					
		}
	}
})( jQuery, window, document );
