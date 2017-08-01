(function () {
    "use strict";

    angular.module("thin.filters").filter("sanitize", function() {
        return function(text) {
            return String(text).replace(/<[^>]+>/gm, "");
        };
    });

})();
