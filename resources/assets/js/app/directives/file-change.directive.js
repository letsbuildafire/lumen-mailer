(function () {
    "use strict";

    angular.module("thin.directives").directive("watchChange", function() {
        return {
            restrict: "A",
            link: function (scope, element, attrs) {
                var onChangeFunc = scope.$eval(attrs.watchChange);
                element.bind("change", onChangeFunc);
            }
        };
    });

})();
