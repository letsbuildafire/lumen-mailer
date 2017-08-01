(function () {
    "use strict";

    angular.module("thin.controllers")
        .controller("UserCtrl", [
            "$log",
            "$rootScope",
            "$scope",
            "$state",
            "$stateParams",
            "$previousState",
            "$mdDialog",
            "Restangular",
            function(
                $log,
                $rootScope,
                $scope,
                $state,
                $stateParams,
                $previousState,
                $mdDialog,
                Restangular
            ){
                $scope.roles = [
                    {
                        label: "Admin",
                        value: "ADMIN"
                    },
                    {
                        label: "Content Admin",
                        value: "CONTENTADMIN"
                    },
                    {
                        label: "User",
                        value: "USER"
                    }
                ];

                $scope.back = function(){
                    if($rootScope.user.role === "ADMIN"){
                        $state.go("admin.users.list");
                    } else {
                        if($previousState.get() !== null){
                            $previousState.go();
                        } else {
                            $state.go("admin.index");
                        }
                    }
                };

                $scope.saveOrCreate = function(form, event, redirect){
                    event && event.stopPropagation();
                    
                    if($scope.user.id){
                        $scope.save(form, event, redirect);
                    } else {
                        $scope.create(event, redirect);
                    }
                };

                $scope.create = function(event, redirect){
                    redirect = redirect || false;
                    Restangular.all("users").post($scope.user)
                        .then(function(record){
                            $scope.toast(sprintf("%s created", $scope.dict.TITLE_USERS));
                            if(redirect){
                                if($previousState.get() !== null){
                                    $previousState.go();
                                } else {
                                    $state.go("admin.users.list");
                                }
                            }
                            else{
                                $state.go("admin.users.edit",{id : record.id});
                            }
                        }).catch(function(response){
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                };

                $scope.save = function(form, event, redirect){
                    redirect = redirect || false;
                    $scope.user.save()
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_USERS));
                            if(redirect){
                                if($previousState.get() !== null){
                                    $previousState.go();
                                } else {
                                    $state.go("admin.users.list");
                                }
                            } else {
                                $scope.$broadcast("admin.users.saved", record);
                            }
                        }).catch(function(response){
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                };

                $scope.canRemove = function(user){
                    return $rootScope.user.role === "ADMIN"
                        && $rootScope.user.id !== user.id
                        && user.id;
                };

                $scope.remove = function(user, event){
                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_USERS))
                        .textContent(sprintf("Are you sure you want to remove this %s? This cannot be undone.", $scope.dict.TITLE_USERS.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_USERS))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        user.remove().then(function(){
                            $scope.toast(sprintf("%s removed", $scope.dict.TITLE_USERS));
                            $scope.$broadcast("admin.users.removed");
                        }).catch(function(response){  
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                    }, function() {
                        // Do nothing
                    });
                };
              
        }])
        .controller("UserListCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            "users",
             function(
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular,
                users
            ){
                $scope.users = users;
                $scope.selected = [];

                $scope.query = {
                    order: "username",
                    limit: 10,
                    page: 1
                };

                $scope.filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.pagination = {
                    label: {
                        text: sprintf("%s Per Page", $scope.dict.TITLES_USERS)
                    }
                };

                $scope.getUsers = function(){
                    var promise = $scope.users.getList($scope.query);
                    promise.then(function(records){
                        $scope.users = records;
                    }).catch(function(response){  
                        if(response.data && response.data.message){
                            $scope.toast("Error: " + response.data.message);
                        } else {
                            $scope.toast("An error occurred");
                        }
                        $log.warn(response);
                    });
                    return promise;
                };

                $scope.onOrderChange = function(order){
                    $scope.promise = $scope.getUsers();
                };

                $scope.onPaginationChange = function(page, limit){
                    $scope.promise = $scope.getUsers();
                };

                $scope.resetFilter = function(event){
                    event && event.stopPropagation();

                    if($scope.filterForm.$dirty) {
                        $scope.query.q = "";
                        $scope.query.role = "";
                        $scope.query.page = 1;
                        $scope.filterForm.$setPristine();
                    }
                    $scope.filter.show = false;
                };

                $scope.removeMany = function(event) {
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLES_USERS))
                        .textContent(sprintf("Are you sure you want to remove these %s? This cannot be undone.", $scope.dict.TITLES_USERS.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLES_USERS))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        // We just need to pass uuids
                        var users = _.map($scope.selected, "id");
                        Restangular.all("users")
                            .customDELETE("", {"users[]": users})
                            .then(function(){
                                $scope.toast(sprintf("%s removed", $scope.dict.TITLES_USERS));

                                $scope.query.page = 1;
                                $scope.selected = [];
                                $scope.getUsers();
                            }).catch(function(response){
                                if(response.data && response.data.message){
                                    $scope.toast("Error: " + response.data.message);
                                } else {
                                    $scope.toast("An error occurred");
                                }
                                $log.warn(response);
                            });
                    }, function() {
                        // Do nothing
                    });
                };

                $scope.$on("admin.users.removed", function(event){
                    $scope.query.page = 1;
                    $scope.promise = $scope.getUsers();
                });

                $scope.$watch("[query.q, query.role]", function(){
                    $scope.query.page = 1;
                    $scope.selected = [];
                    $scope.promise = $scope.getUsers();
                });

        }])
        .controller("UserCreateCtrl", [
            "$scope",
            "$state",
            "$stateParams",
             function(
                $scope,
                $state,
                $stateParams
            ){
                $scope.$parent.user = {};
        }])
        .controller("UserEditCtrl", [
            "$scope",
            "$state",
            "$stateParams",
            "user",
            function(
                $scope,
                $state,
                $stateParams,
                user
            ){
                $scope.$parent.user = user;

                $scope.$on("admin.users.saved", function(event, user){
                    $scope.$parent.user = user;
                    $scope.$parent.user.password = null;
                    $scope.confirm_password = null;
                    $scope.userForm.$setPristine();
                });

                $scope.$on("admin.users.removed", function(event){
                    $state.go("admin.users.list");
                });
        }]);
})();
