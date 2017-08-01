(function(){
    "use strict";

    angular.module("thin.directives")
        .directive("laPreview", ["$document", function($document) {
            return {
                restrict: "A",
                scope: {
                    previewContent: "="
                },
                link: function(scope, $elm, attr) {
                    scope.$watch("previewContent", function(newValue, oldValue) {
                        if (newValue && newValue.length){
                            var iframe = angular.element("<iframe/>");
                            $elm.append(iframe);

                            iframe.attr("sandbox", "allow-same-origin");
                            iframe = iframe[0].contentWindow ? 
                                     iframe[0].contentWindow : 
                                     (
                                         iframe[0].contentDocument.document ?
                                         iframe[0].contentDocument.document : 
                                         iframe[0].contentDocument
                                     );
                            iframe.document.open();
                            iframe.document.write(newValue);
                            iframe.document.close();
                        } else {
                            $elm.find("iframe").remove();
                        }
                    });
                }
    }}]);

})();
