(function () {
    "use strict";

    angular.module("thin.controllers")
        .controller("TemplateCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular
            ){
                $scope.custom_fields = [
                    {
                        name: "email",
                        req: true
                    },
                    {
                        name: "first_name",
                        req: true
                    },
                    {
                        name: "last_name",
                        req: false
                    }
                ];

                $scope.saveOrCreate = function(form, event, redirect){
                    event && event.stopPropagation();

                    if($scope.template.id){
                        $scope.save(form, event, redirect);
                    } else {
                        $scope.create(event, redirect);
                    }
                };

                $scope.create = function(event, redirect){
                    redirect = redirect || false;

                    Restangular.all("templates").post($scope.template)
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_TEMPLATES));
                            if(redirect){
                                $state.go("admin.templates.list");
                            }
                            else{
                                $state.go("admin.templates.edit",{id : record.id});
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

                    $scope.template.save()
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_TEMPLATES));
                            if(redirect){
                                $state.go("admin.templates.list");
                            } else {
                                $scope.$broadcast("admin.templates.saved", record);
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

                $scope.remove = function(template, event){
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_TEMPLATES))
                        .textContent(sprintf("Are you sure you want to remove this %s? This cannot be undone.", $scope.dict.TITLE_TEMPLATES.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_TEMPLATES))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        template.remove().then(function(){
                            $scope.toast(sprintf("%s removed", $scope.dict.TITLE_TEMPLATES));

                            $scope.$broadcast("admin.templates.removed");
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
        .controller("TemplateListCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            "templates",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular,
                templates
            ){
                $scope.templates = templates;
                $scope.selected = {};

                $scope.query = {
                    order: "name",
                    limit: 12,
                    page: 1
                };

                $scope.pagination = {
                    label: {
                        text: sprintf("%s Per Page", $scope.dict.TITLES_TEMPLATES)
                    }
                };

                $scope.filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.getTemplates = function(){
                    var promise = $scope.templates.getList($scope.query);
                    promise.then(function(records){
                        $scope.templates = records;
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
                    $scope.promise = $scope.getTemplates();
                };

                $scope.onPaginationChange = function(page, limit){
                    $scope.promise = $scope.getTemplates();
                };

                $scope.resetFilter = function(){
                    $scope.filter.show = false;

                    if($scope.filterForm.$dirty) {
                        $scope.query.q = "";
                        $scope.query.page = 1;
                        $scope.filterForm.$setPristine();
                    }
                };

                $scope.preview = function(template, event){
                    event && event.stopPropagation();

                    $scope.selected = angular.copy(template);
                };

                $scope.hide = function(){
                    $scope.selected = {};
                };

                $scope.$on("admin.templates.removed", function(event){
                    $scope.query.page = 1;
                    $scope.promise = $scope.getTemplates();
                });

                $scope.$watch("query.q", function(){
                    $scope.query.page = 1;
                    $scope.promise = $scope.getTemplates();
                });
        }])
        .controller("TemplateCreateCtrl", [
            "$scope",
            "$state",
            "$stateParams",
            function(
                $scope,
                $state,
                $stateParams
            ){
                $scope.$parent.template = {};
                
        }])
        .controller("TemplateEditCtrl", [
            "$document",
            "$scope",
            "$state",
            "$stateParams",
            "template",
            function(
                $document,
                $scope,
                $state,
                $stateParams,
                template
            ){
                $scope.$parent.template = template;

                $scope.$on("admin.templates.saved", function(event, template){
                    $scope.$parent.template = template;

                    // Refresh the iframe.
                    var frame = $document[0].querySelector("iframe");
                    frame.src = frame.src + "?" + (Math.floor(Math.random() * 10000 + 1));

                    $scope.templateForm.$setPristine();
                });

                $scope.$on("admin.templates.removed", function(event){
                    $state.go("admin.templates.list");
                });

        }]);
})();
