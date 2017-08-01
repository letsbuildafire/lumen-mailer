(function(){
    "use strict";

    angular.module("thin.directives")
        .directive("scrollToItem", ['$timeout', function($timeout) {                                                      
            return {                                                                                 
                restrict: "A",                                                                       
                scope: {                                                                             
                    scrollTo: "@"                                                                    
                },                                                                                   
                link: function(scope, $elm,attr) {                                                   
                    $elm.on("click", function() { 
                        if(this.disabled) return false;                                                   
                        $timeout(function(){
                            document.querySelector(scope.scrollTo).scrollIntoView();
                        }, 400);
                    });                                                                              
                }                                                                                    
    }}]);

})();
