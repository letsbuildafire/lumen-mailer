(function () {
    "use strict";

    angular.module("thin.directives").directive("match", ["$parse", function($parse) {
        return {
            restrict: "A",
            require: "ngModel",
            link: function (scope, element, attrs, ctrl) {
                scope.$watch(function () {
                    return [scope.$eval(attrs.match), ctrl.$viewValue];
                }, function (values) {
                    ctrl.$setValidity("match", values[0] === values[1]);
                }, true);
            }
        };
    }]);

})();
