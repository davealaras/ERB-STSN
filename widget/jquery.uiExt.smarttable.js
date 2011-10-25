	(function($) {
	
    $.fn.smarttable = function(options) {
      return this.each(function() {
         (new $.smarttable($(this), options));              
      });        
	};
	
   $.smarttable = function(element, options) {
      this.options = {};
       
      element.data('smarttable', this);
      
      this.init = function(element, options) {         
         this.options = $.extend({}, $.smarttable.defaultOptions, options); 
		 var _markup = 	'<table><tr>';
			_markup += '<td>DATA 1</td>';
			_markup +=	'</tr></table>';
		 $(element).html(_markup);
      };
      
      //Public function
      this.greet = function(name) {
         console.log('Hello, ' + name + ', welcome to Script Junkies!');
      };
      
      this.init(element, options);
   };
   $.smarttable.defaultOptions = {
      class: 'smarttable',
      text: 'Enter Text Here',
	  max: 5,
	  min: 2
	}
})(jQuery);