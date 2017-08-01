(function () {
    "use strict";

    angular.module("thin.controllers")
        .controller("HelpCtrl", [
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
                $scope.sections = [
                    {
                        label: "General",
                        value: "GENERAL"
                    },
                    {
                        label: $scope.dict.TITLES_EMAILERS,
                        value: "EMAILERS"
                    },
                    {
                        label: $scope.dict.TITLES_TEMPLATES,
                        value: "TEMPLATES"
                    },
                    {
                        label: $scope.dict.TITLES_LISTS,
                        value: "LISTS"
                    }
                ];

                $scope.saveOrCreate = function(form, event, redirect){
                    event && event.stopPropagation();

                    if($scope.article.id){
                        $scope.save(form, event, redirect);
                    } else {
                        $scope.create(event, redirect);
                    }
                };

                $scope.create = function(event, redirect){
                    redirect = redirect || false;
                    Restangular.all("help-articles").post($scope.article)
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_ARTICLES));
                            if(redirect){
                                $state.go("admin.help.index.list.all");
                            } else {
                                $state.go("admin.help.edit",{id : record.id});
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

                    $scope.article.save()
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_ARTICLES));
                            if(redirect){
                                $state.go("admin.help.index.list.all");
                            } else {
                                $scope.$broadcast("admin.help.saved", record);
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

                $scope.remove = function(article, event){
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_ARTICLES))
                        .textContent(sprintf("Are you sure you want to remove this %s? This cannot be undone.", $scope.dict.TITLE_ARTICLES.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_ARTICLES))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        article.remove().then(function(){
                            $scope.toast(sprintf("%s removed", $scope.dict.TITLE_ARTICLES));

                            $scope.$broadcast("admin.help.removed");
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
        .controller("HelpListCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            "articles",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular,
                articles
            ){
                $scope.articles = articles;
                $scope.selected = $state.params.id || -1;

                $scope.query = {
                    section: "GENERAL",
                    order: "-updated_at",
                    limit: 50
                };

                $scope.filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.getArticles = function(){
                    Restangular.all("help-articles").getList($scope.query).then(function(records){
                        $scope.articles = records;
                    }).catch(function(response){  
                        if(response.data && response.data.message){
                            $scope.toast("Error: " + response.data.message);
                        } else {
                            $scope.toast("An error occurred");
                        }
                        $log.warn(response);
                    }); 
                };

                $scope.view = function(article, event){
                    event && event.stopPropagation();

                    $scope.selected = article.id;
                    $state.go("admin.help.index.list.view", {id: article.id});
                };

                $scope.edit = function(article, event){
                    event && event.stopPropagation();

                    $state.go("admin.help.edit", {id: article.id});
                };

                $scope.$on("admin.help.removed", function(event){
                    $scope.selected = -1;
                });

                $scope.$on("admin.help.closed", function(event){
                    $scope.selected = -1;
                });

                $scope.$watch("query.q", function(){
                    $scope.query.page = 1;
                    $scope.getArticles();
                });

        }])
        .controller("HelpListSectionsCtrl", [
            "$scope",
            "$state",
            "$stateParams",
            function(
                $scope,
                $state,
                $stateParams
            ){
                $scope.resetFilter = function(event){
                    event && event.stopPropagation();

                    if($scope.filterForm.$dirty){
                        $scope.query.q = "";
                    }
                    $scope.filter.show = false;
                };

                $scope.$watch("selectedIndex", function(current, prev){
                    if($scope.sections[current]){
                        if($scope.sections[current].value === $scope.query.section){
                            return;
                        }

                        $scope.query.section = $scope.sections[current].value;
                        $scope.getArticles();
                    }
                });

                $scope.$on("admin.help.removed", function(event){
                    $scope.resetFilter();
                    $scope.getArticles();
                });
        }])
        .controller("HelpCreateCtrl", [
            "$scope",
            "$state",
            "$stateParams",
             function(
                $scope,
                $state,
                $stateParams
            ){
                $scope.$parent.article = {};

        }])
        .controller("HelpEditCtrl", [
            "$scope",
            "$state",
            "$stateParams",
            "article",
            function(
                $scope,
                $state,
                $stateParams,
                article
            ){
                $scope.$parent.article = article;

                $scope.$on("admin.help.removed", function(event){
                    $state.go("admin.help.index.list.all");
                });

        }])
        .controller("HelpViewCtrl", [
            "$scope",
            "$state",
            "$stateParams",
            "article",
            function(
                $scope,
                $state,
                $stateParams,
                article
            ){
                $scope.$parent.article = article;

                $scope.close = function(){
                    $scope.$emit("admin.help.closed");
                    
                    $state.go("admin.help.index.list.all");
                };
        }]);
})();
