(function () {
    "use strict";

    angular.module("thin.filters").filter("zpad", function() {
        return function(input, n) {
            if(input === undefined)
                input = ""
            if(input.length >= n)
                return input
            var zeros = "0".repeat(n);
            return (zeros + input).slice(-1 * n)
        };
    });

})();
