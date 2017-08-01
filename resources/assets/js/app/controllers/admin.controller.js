(function () {
    "use strict";

    angular.module("thin.controllers")
        .controller("AdminDashboardCtrl", [
            "$interval",
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "Restangular",
            "Moment",
            "emailers",
            "templates",
            "lists",
            function(
                $interval,
                $log,
                $scope,
                $state,
                $stateParams,
                Restangular,
                Moment,
                emailers,
                templates,
                lists
            ){
                $scope.emailers = emailers;
                $scope.templates = templates;
                $scope.lists = lists;
                $scope.users;

                $scope.view = function(event, state, id){
                    event && event.stopPropagation();
                    
                    $state.go(state,{id:id});
                    return false;
                };

                if($scope.$root.user.role === "ADMIN"){
                    var last_week = Moment().subtract(7, "days").format("YYYY-MM-DD HH:mm:ss");
                    Restangular.all("users").getList({
                        order: "-updated_at",
                        updated_at: "%3E" + last_week,
                        limit: 3
                    }).then(function(records){
                        $scope.users = records;
                    }).catch(function(response){  
                        if(response.data && response.data.message){
                            $scope.toast("Error: " + response.data.message);
                        } else {
                            $scope.toast("An error occurred");
                        }
                        $log.warn(response);
                    });
                }
        }]);
})();
