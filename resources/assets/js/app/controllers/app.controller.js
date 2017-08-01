(function () {
    "use strict";

    angular.module("thin.controllers")
        .controller("AppCtrl", [
            "$document",
            "$anchorScroll",
            "$scope",
            "$state",
            "$stateParams",
            "$mdToast",
            "Auth",
            "Moment",
            "Dictionary",
            function(
                $document,
                $anchorScroll,
                $scope,
                $state,
                $stateParams,
                $mdToast,
                Auth,
                Moment,
                Dictionary
            ){
                $scope.dict = Dictionary;
                $scope.offcanvas = false;

                $scope.userIsLoggedIn = function(){
                    return Auth.isAuthenticated();
                };

                $scope.userHasPermission = function(permission){
                    return Auth.isAuthorized(permission);
                };

                $scope.getUserToken = function(){
                    return Auth.getToken();
                }

                $scope.toggleOffCanvas = function(toggle){
                    if(typeof toggle === "boolean"){
                        $scope.offcanvas = toggle;
                    } else {
                        $scope.offcanvas = !$scope.offcanvas;
                    }
                };

                $scope.toast = function(content, delay){
                    delay = delay || 4200;
                    return $mdToast.show(
                        $mdToast.simple()
                            .content(content)
                            .position("bottom left")
                            .parent($document[0].querySelector("body"))
                            .hideDelay(delay)
                    );
                };

                $scope.dateToUnix = function(date,timezone){
                    return Moment(date).valueOf();
                };

                $scope.dateAdjustTZ = function(date,src,dest){
                    return Moment.tz(date, src).tz(dest).format("YYYY-MM-DD HH:mm:ss");
                };

                $scope.$on("$stateChangeStart", function(e){
                    $scope.toggleOffCanvas(false);
                    $anchorScroll();
                });
        }]);
})();
