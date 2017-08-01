(function() {
    "use strict";

    angular.module("thin.controllers")
        .controller("EmailerCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$filter",
            "$mdDialog",
            "Restangular",
            "Moment",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                $filter,
                $mdDialog,
                Restangular,
                Moment
            ){
                $scope.custom_fields = [];
                $scope.default_fields = [
                    {
                        name: "email",
                        req: true,
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

                $scope.statuses = [
                    {
                        label: "Unapproved",
                        value: "UNAPPROVED"
                    },
                    {
                        label: "Approved",
                        value: "APPROVED"
                    },
                    {
                        label: "Pending",
                        value: "PENDING"
                    },
                    {
                        label: "Running",
                        value: "RUNNING"
                    },
                    {
                        label: "Paused",
                        value: "PAUSED"
                    },
                    {
                        label: "Completed",
                        value: "COMPLETED"
                    }
                ];

                $scope.hasStatus = function(emailer, statuses){
                    return _.indexOf(statuses, emailer.status) !== -1;
                };

                $scope.saveOrCreate = function(form, event, redirect){
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Send %s now?", $scope.dict.TITLE_EMAILERS.toLowerCase()))
                        .textContent(sprintf("Are you sure you want to send this %s now? It will be sent immediately without further input.",
                                $scope.dict.TITLE_EMAILERS.toLowerCase()))
                        .ariaLabel(sprintf("Send %s now", $scope.dict.TITLE_EMAILERS.toLowerCase()))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");

                    // Convert the timezone to UTC
                    $scope.emailer.distribute_at = Moment($scope.emailer.distribute_at).utc().format();

                    if($scope.emailer.send_now){
                        $mdDialog.show(confirm).then(function() {
                            if($scope.emailer.id){
                                $scope.save(form, event, redirect);
                            } else {
                                $scope.create(event, redirect);
                            }
                        }, function() {
                            // Do nothing
                        });
                    } else {
                        if($scope.emailer.id){
                            $scope.save(form, event, redirect);
                        } else {
                            $scope.create(event, redirect);
                        }
                    }
                };

                $scope.create = function(event, redirect){
                    redirect = redirect || false;

                    // We just need to pass the list uuids
                    $scope.emailer.lists = _.map($scope.emailer.lists, "id");
                    Restangular.all("emailers").post($scope.emailer)
                        .then(function(record){
                            $scope.toast(sprintf("%s created", $scope.dict.TITLE_EMAILERS));
                            if(redirect){
                                $state.go("admin.emailers.list");
                            } else{
                                $state.go("admin.emailers.edit",{id : record.id});
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

                    // Save a copy of the lists so that we can
                    // restore them after saving
                    var lists = $scope.emailer.lists;

                    // We only need to send the list uuids
                    $scope.emailer.lists = _.map($scope.emailer.lists, "id");

                    $scope.emailer.save()
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_EMAILERS));
                            if(redirect){
                                $state.go("admin.emailers.list");
                            } else {
                                $scope.emailer = record;
                                $scope.emailer.lists = lists;
                                $scope.$broadcast("admin.emailers.saved", record);
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

                $scope.remove = function(emailer, event){
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_EMAILERS))
                        .textContent(sprintf("Are you sure you want to remove this %s? This cannot be undone.", $scope.dict.TITLE_EMAILERS.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_EMAILERS))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        emailer.remove().then(function(){
                            $scope.toast(sprintf("%s removed", $scope.dict.TITLE_EMAILERS));

                            $scope.$broadcast("admin.emailers.removed");
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

                $scope.pause = function(emailer, event){
                    event && event.stopPropagation();

                    // Keep track of existing lists to carry them over
                    var lists = emailer.lists;

                    Restangular.one("emailers", emailer.id)
                        .customPUT({}, 'pause')
                        .then(function(record){
                            $scope.toast(sprintf("%s paused", $scope.dict.TITLE_EMAILERS));
                            
                            $scope.$broadcast("admin.emailers.paused", record, lists);
                        }).catch(function(response){
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                };

                $scope.start = function(emailer, event){
                    event && event.stopPropagation();

                    // Keep track of existing lists to carry them over
                    var lists = emailer.lists;

                    Restangular.one("emailers", emailer.id)
                        .customPUT({}, 'start')
                        .then(function(record){
                            $scope.toast(sprintf("%s resumed", $scope.dict.TITLE_EMAILERS));
                            
                            $scope.$broadcast("admin.emailers.started", record, lists);
                        }).catch(function(response){
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                };

                $scope.toggleApproval = function(emailer, event){
                    event && event.stopPropagation();

                    // Keep track of existing lists to carry them over
                    var lists = emailer.lists;

                    var action = emailer.approved ? "unapprove" : "approve";
                    Restangular.one("emailers", emailer.id)
                        .customPUT({}, action)
                        .then(function(record){
                            $scope.toast(sprintf("%s %sd", $scope.dict.TITLE_EMAILERS, action));
                            
                            $scope.$broadcast("admin.emailers.approval.change", record, lists);
                        }).catch(function(response){
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                };

                $scope.parseCustomFields = function(){
                    var custom_fields = [];
                    _.each($scope.emailer.lists, function(l){
                        if(typeof l.custom_fields === "string") {
                            custom_fields = 
                                custom_fields.concat(angular.fromJson(l.custom_fields));
                        }
                    });

                    $scope.custom_fields = $scope.default_fields.concat(
                        _.uniq(custom_fields, function(field, n) { 
                            return {
                                name: field.name,
                                req: field.req
                            };
                        })
                    );

                    var field_counts = _.countBy(custom_fields, 'name');
                    _.each($scope.custom_fields, function(n){
                        if(n.name === "first_name" || n.name === "last_name") {
                            n.occurs = $scope.emailer.lists.length;
                        } else {
                            n.occurs = field_counts[n.name] || 0;
                        }
                    });
                    
                    $scope.$broadcast("custom_fields_changed", $scope.custom_fields);
                    return $scope.custom_fields;
                };

                $scope.parseSingleStats = function(emailer){
                    if(emailer.api_sending_status_numbers === null){
                        emailer.progress = 0;
                        return emailer;
                    }

                    if(typeof emailer.api_sending_status_numbers !== "object") {
                        emailer.api_sending_status_numbers = 
                            angular.fromJson(emailer.api_sending_status_numbers);

                            // If sending is all done, then calculate sent and unsent.
                            if(emailer.api_extended_status_received){
                                emailer.api_sending_status_numbers.Sent = 
                                    emailer.api_sending_status_numbers.Accepted + 
                                    emailer.api_sending_status_numbers.Bounced + 
                                    emailer.api_sending_status_numbers.Deferred;
                            }

                            // Calculate the current progress
                            var total = emailer.api_sending_status_numbers.Sent + 
                                emailer.api_sending_status_numbers.Unsent;
                            emailer.progress = (total - emailer.api_sending_status_numbers.Unsent) / total * 100;
                    }
                    return emailer;
                };

                $scope.parseStats = function(emailers){
                    _.each(emailers, function(emailer, key){
                        emailer = $scope.parseSingleStats(emailer);
                    });
                    return emailers;
                };

                $scope.minimumDateTime = function(){
                    return Moment().format();
                };

        }])
        .controller("EmailerListCtrl", [
            "$interval",
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            "emailers",
            function(
                $interval,
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular,
                emailers
            ){
                // Make sure our custom fields are parsed properly
                $scope.emailers = $scope.parseStats(emailers);
                $scope.compact = false;

                $scope.query = {
                    order: "-distribute_at",
                    limit: 10,
                    page: 1,
                    "with": ["lists"]
                };

                $scope.filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.getEmailers = function(){
                    $scope.stopWatcher();
                    var promise = $scope.emailers.getList($scope.query);
                    promise.then(function(records){
                        $scope.emailers = $scope.parseStats(records);
                        // $scope.startWatcher();
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
                    $scope.promise = $scope.getEmailers();
                };

                $scope.onPaginationChange = function(page, limit){
                    $scope.promise = $scope.getEmailers();
                };

                $scope.resetFilter = function(){
                    $scope.filter.show = false;

                    if($scope.filterForm.$dirty) {
                        $scope.query.q = "";
                        $scope.query.status = "";
                        $scope.query.page = 1;
                        $scope.filterForm.$setPristine();
                    }
                };

                $scope.removeMany = function(event) {
                    $scope.stopWatcher();
                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLES_EMAILERS))
                        .textContent(sprintf("Are you sure you want to remove these %s? This cannot be undone.", $scope.dict.TITLES_EMAILERS.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLES_EMAILERS))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        var promises = [];
                        _.forEach($scope.selected, function(emailer, i){
                            promises.push(emailer.remove().$promise);
                        });

                        $q.all(promises).then(function(){
                            $scope.toast(sprintf("%s removed", $scope.dict.TITLES_EMAILERS));

                            $scope.query.page = 1;
                            $scope.selected = [];
                            $scope.promise = $scope.getEmailers();
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

                $scope.$on("admin.emailers.approval.change", function(event, emailer, lists){
                    var i = _.findIndex($scope.emailers, { 'id': emailer.id });
                    $scope.emailers[i] = emailer;
                    $scope.emailers[i].lists = lists;
                });

                $scope.$on("admin.emailers.paused", function(event, emailer, lists){
                    var i = _.findIndex($scope.emailers, { 'id': emailer.id });
                    $scope.emailers[i] = $scope.parseSingleStats(emailer);
                    $scope.emailers[i].lists = lists;
                });

                $scope.$on("admin.emailers.started", function(event, emailer, lists){
                    var i = _.findIndex($scope.emailers, { 'id': emailer.id });
                    $scope.emailers[i] = $scope.parseSingleStats(emailer);
                    $scope.emailers[i].lists = lists;
                });

                $scope.$on("admin.emailers.removed", function(event){
                    $scope.stopWatcher();
                    $scope.query.page = 1;
                    $scope.promise = $scope.getEmailers();
                });

                $scope.$on('$destroy', function() {
                    // Make sure that the interval is destroyed too
                    $scope.stopWatcher();
                });

                $scope.$watch("[query.q, query.status]", function(){
                    $scope.stopWatcher();
                    $scope.query.page = 1;
                    $scope.selected = [];
                    $scope.promise = $scope.getEmailers();
                });

                $scope.stopWatcher = function(){
                    if(angular.isDefined($scope.watcher)){
                        $interval.cancel($scope.watcher);
                        $scope.watcher = undefined;
                    }
                }

                $scope.startWatcher = function(){
                    $scope.stopWatcher();
                    $scope.watcher = $interval(function(){
                        $scope.promise = $scope.getEmailers();
                    }, 30000);
                };
        }])
        .controller("EmailerCreateCtrl", [
            "$document",
            "$log",
            "$rootScope",
            "$scope",
            "$state",
            "$stateParams",
            "Restangular",
            "Moment",
            "templates",
            "lists",
            function(
                $document,
                $log,
                $rootScope,
                $scope,
                $state,
                $stateParams,
                Restangular,
                Moment,
                templates,
                lists
            ){
                $scope.$parent.templates = templates;
                $scope.$parent.lists = lists;
                $scope.$parent.emailer = {
                    return_address: $rootScope.user.email,
                    return_name: $rootScope.user.first_name
                        + " " + ($rootScope.user.last_name || ""),
                    signature: $rootScope.user.signature,
                    send_now: null,
                    lists: []
                };

                $scope.step = "emailer-details";

                // Creating an emailer from a specific template.
                if($stateParams.template){
                    $scope.$parent.emailer.template_id = $stateParams.template;
                    $scope.$parent.emailer.content = _.result(_.find(templates, "id", $stateParams.template), "default_content");
                    // Make sure we aren't marking it dirty.
                    // $scope.emailerForm.$dirty = false;
                }

                $scope.template_query = {
                    order: "-updated_at",
                    limit: 4,
                    page: 1
                };

                $scope.template_pagination = {
                    label: {
                        text: sprintf("%s Per Page", $scope.dict.TITLES_TEMPLATES)
                    }
                };

                $scope.template_filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.list_query = {
                    order: "name",
                    limit: 12,
                    page: 1
                };

                $scope.list_pagination = {
                    label: {
                        text: sprintf("%s Per Page", $scope.dict.TITLES_LISTS)
                    }
                };

                $scope.list_filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.setStep = function(step){
                    if(step.length > 0){
                        $scope.step = step;
                    }
                }

                $scope.getTemplates = function(){
                    var promise = $scope.templates.getList($scope.template_query);
                    promise.then(function(records){
                        $scope.templates = records;
                    }).catch(function(response){  
                        $scope.toast("An error occurred");
                        $log.warn(response);
                    }); 
                    return promise;
                };

                $scope.toggleTemplate = function(event, template){
                    if(!$scope.content_fields.$dirty || $scope.content_fields.content.$empty){
                        $scope.emailer.content = template.default_content;
                        // Make sure we aren"t marking it dirty.
                        $scope.content_fields.$dirty = false;
                    }
                    return false;
                };

                $scope.onTemplatePaginationChange = function(page, limit){
                    $scope.template_promise = $scope.getTemplates();
                };

                $scope.resetTemplateFilter = function(){
                    $scope.template_filter.show = false;

                    if($scope.template_filter_form.$dirty){
                        $scope.template_query.q = "";
                        $scope.template_query.page = 1;
                        $scope.template_filter_form.$setPristine();
                    }
                };

                $scope.getLists = function(){
                    var promise = $scope.lists.getList($scope.list_query);
                    promise.then(function(records){
                        $scope.lists = records;
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

                $scope.onListPaginationChange = function(page, limit){
                    $scope.list_promise = $scope.getTemplates();
                };

                $scope.toggleList = function(event, list) {
                    event && event.stopPropagation();
                    $scope.lists_fields.$pristine = false;

                    var exists = _.find($scope.emailer.lists, {"id": list.id});
                    if(typeof exists !== "undefined") {
                        $scope.emailer.lists = _.without($scope.emailer.lists, exists);
                    } else {
                        $scope.emailer.lists.push(list);
                    }
                    $scope.parseCustomFields();
                    return false;
                };

                $scope.resetListFilter = function(){
                    $scope.list_filter.show = false;

                    if($scope.list_filter_form.$dirty){
                        $scope.list_query.q = "";
                        $scope.list_query.page = 1;
                        $scope.list_filter_form.$setPristine();
                    }
                };

                $scope.listSelected = function(list) {
                    var exists = _.find($scope.emailer.lists, {"id": list.id});
                    return typeof exists !== "undefined";
                };

                $scope.preview = function(event, email){
                    event && event.stopPropagation();

                    var params = {};
                    // Default to live preview when not specified
                    email = typeof email === "boolean" ? email : false;

                    if(email){
                        params.address = $scope.preview_address;
                    }

                    Restangular.all("emailers").customPOST($scope.emailer, "preview", params)
                        .then(function(response){
                            if(email){
                                $scope.toast("Preview sent");
                            } else {
                                $scope.emailer_preview = atob(response.content);
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

                $scope.sendNow = function(event, toggle){
                    event && event.stopPropagation();

                    if(toggle){
                        $scope.emailer.distribute_at = Moment();
                        $scope.emailer.status = "APPROVED";
                        $scope.emailer.send_now = true;
                    } else {
                        $scope.emailer.send_now = false;
                        $scope.emailer.status = null;
                        delete $scope.emailer.status;
                    }
                };

                $scope.hide = function(){
                    $scope.emailer_preview = null;
                };

                $scope.$watch("[emailer.distribute_at]", function(newValue,oldValue){
                    if(Moment(newValue).isAfter(Moment())){
                        $scope.emailer.send_now = false;
                    }
                });

                $scope.$watch("template_query.q", function(){
                    $scope.template_query.page = 1;
                    $scope.promise = $scope.getTemplates();
                });

                $scope.$watch("list_query.q", function(){
                    $scope.list_query.page = 1;
                    $scope.promise = $scope.getLists();
                });

                // Make sure our custom fields are parsed properly
                $scope.parseCustomFields();
        }])
        .controller("EmailerEditCtrl", [
            "$document",
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "Restangular",
            "Moment",
            "emailer",
            "templates",
            "lists",
            function(
                $document,
                $log,
                $scope,
                $state,
                $stateParams,
                Restangular,
                Moment,
                emailer,
                templates,
                lists
            ){
                $scope.$parent.templates = templates;
                $scope.$parent.lists = lists;

                // Convert the distribute time from UTC
                emailer.distribute_at = Moment(emailer.distribute_at).local().format();
                emailer.send_now = false;
                $scope.$parent.emailer = emailer;

                $scope.step = "emailer-details";
                
                $scope.template_query = {
                    order: "-updated_at",
                    limit: 4,
                    page: 1
                };

                $scope.template_pagination = {
                    label: {
                        text: sprintf("%s Per Page", $scope.dict.TITLES_TEMPLATES)
                    }
                };

                $scope.template_filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.list_query = {
                    order: "name",
                    limit: 12,
                    page: 1
                };

                $scope.list_pagination = {
                    label: {
                        text: sprintf("%s Per Page", $scope.dict.TITLES_LISTS)
                    }
                };

                $scope.list_filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.setStep = function(step){
                    if(step.length > 0){
                        $scope.step = step;
                    }
                }

                $scope.getTemplates = function(){
                    $scope.templates.getList($scope.template_query).then(function(records){
                        $scope.templates = records;
                    }).catch(function(response){  
                        $scope.toast("An error occurred");
                        $log.warn(response);
                    }); 
                };

                $scope.resetTemplateFilter = function(){
                    $scope.template_filter.show = false;

                    console.log($scope);
                    if($scope.template_filter_form.$dirty){
                        $scope.template_query.q = "";
                        $scope.template_query.page = 1;
                        $scope.template_filter_form.$setPristine();
                    }
                };

                $scope.toggleTemplate = function(event, template){
                    return false;
                };

                $scope.onTemplatePaginationChange = function(page, limit){
                    $scope.getTemplates();
                };

                $scope.getLists = function(){
                    $scope.lists.getList($scope.list_query).then(function(records){
                        $scope.lists = records;
                    }).catch(function(response){  
                        if(response.data && response.data.message){
                            $scope.toast("Error: " + response.data.message);
                        } else {
                            $scope.toast("An error occurred");
                        }
                        $log.warn(response);
                    }); 
                };

                $scope.resetListFilter = function(){
                    $scope.list_filter.show = false;

                    if($scope.list_filter_form.$dirty){
                        $scope.list_query.q = "";
                        $scope.list_query.page = 1;
                        $scope.list_filter_form.$setPristine();
                    }
                };

                $scope.onListPaginationChange = function(page, limit){
                    $scope.getTemplates();
                };

                $scope.toggleList = function(event, list) {
                    event && event.stopPropagation();
                    $scope.lists_fields.$pristine = false;

                    var exists = _.find($scope.emailer.lists, {"id": list.id});
                    if(typeof exists !== "undefined") {
                        $scope.emailer.lists = _.without($scope.emailer.lists, exists);
                    } else {
                        $scope.emailer.lists.push(list);
                    }
                    $scope.parseCustomFields();
                    return false;
                };

                $scope.listSelected = function(list) {
                    var exists = _.find($scope.emailer.lists, {"id": list.id});
                    return typeof exists !== "undefined";
                };

                $scope.preview = function(event, email){
                    event && event.stopPropagation();
                    // Default to live preview when not specified
                    email = typeof email === "boolean" ? email : false;

                    if(email){
                        Restangular.one("emailers", $scope.emailer.id).customGET("preview",{
                            address: $scope.preview_address
                        }).then(function(response){
                            $scope.toast("Preview sent");
                        }).catch(function(response){
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                    } else {
                        Restangular.all("emailers").customPOST($scope.emailer, "preview")
                            .then(function(response){
                                $scope.emailer_preview = atob(response.content);
                            }).catch(function(response){
                                if(response.data && response.data.message){
                                    $scope.toast("Error: " + response.data.message);
                                } else {
                                    $scope.toast("An error occurred");
                                }
                                $log.warn(response);
                            });
                    }
                };

                $scope.sendNow = function(event, toggle){
                    event && event.stopPropagation();

                    if(toggle){
                        $scope.emailer.distribute_at = Moment();
                        $scope.emailer.status = "APPROVED";
                        $scope.emailer.send_now = true;
                    } else {
                        $scope.emailer.send_now = false;
                        $scope.emailer.status = null;
                        delete $scope.emailer.status;
                    }
                };

                $scope.hide = function(){
                    $scope.emailer_preview = null;
                };

                $scope.$on("admin.emailers.approval.change", function(event, emailer){
                    $scope.$parent.emailer = emailer;

                    $scope.emailerForm.$setPristine();
                });

                $scope.$on("admin.emailers.saved", function(event, emailer){
                    $scope.$parent.emailer = emailer;

                    $scope.emailerForm.$setPristine();
                });

                $scope.$on("admin.emailers.removed", function(event, emailer){
                    $state.go("admin.emailers.list");
                });

                $scope.$watch("[emailer.distribute_at]", function(newValue,oldValue){
                    if(Moment(newValue).isAfter(Moment())){
                        $scope.emailer.send_now = false;
                    }
                });

                $scope.$watch("template_query.q", function(){
                    $scope.template_query.page = 1;
                    $scope.promise = $scope.getTemplates();
                });

                $scope.$watch("list_query.q", function(){
                    $scope.list_query.page = 1;
                    $scope.promise = $scope.getLists();
                });

                // Make sure our custom fields are parsed properly
                $scope.parseCustomFields();
        }])
        .controller("EmailerStatsCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "Restangular",
            "emailer",
            "stats",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                Restangular,
                emailer,
                stats
            ){
                $scope.$parent.emailer = emailer;
                $scope.$parent.stats = stats;

                $scope.$parent.statuses = [
                    {
                        label: "Accepted",
                        value: "ACCEPTED"
                    },
                    {
                        label: "Deferred",
                        value: "DEFERRED"
                    },
                    {
                        label: "Bounced",
                        value: "BOUNCED"
                    }
                ];              

                $scope.query = {
                    limit: 50,
                    page: 1
                };

                $scope.filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.getStats = function(){
                    var promise = $scope.stats.getList($scope.query);
                    promise.then(function(records){
                        $scope.stats = records;
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
                    $scope.promise = $scope.getStats();
                };

                $scope.onPaginationChange = function(page, limit){
                    $scope.promise = $scope.getStats();
                };

                $scope.resetFilter = function(event){
                    event && event.stopPropagation();

                    if($scope.filterForm.$dirty) {
                        $scope.query.q = "";
                        $scope.query.status = "";
                        $scope.query.page = 1;
                        $scope.filterForm.$setPristine();
                    }
                    $scope.filter.show = false;
                };

                $scope.$watch("[query.q, query.status]", function(){
                    $scope.query.page = 1;
                    $scope.selected = [];
                    $scope.promise = $scope.getStats();
                });

                // Parse our basic stats
                $scope.parseSingleStats(emailer);
        }]);
})();
