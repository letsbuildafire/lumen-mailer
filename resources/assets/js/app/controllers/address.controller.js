(function () {
    "use strict";

    angular.module("thin.controllers")
        .controller("AddressListCtrl", [
            "$log",
            "$rootScope",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            function(
                $log,
                $rootScope,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular
            ){
                $scope.placeholder = {
                    first_name: "first_name",
                    last_name: "last_name",
                };
                $scope.defaultFields = [
                    "email",
                    "first_name",
                    "last_name"
                ];

                $scope.saveOrCreate = function(form, event, redirect){
                    event && event.stopPropagation();

                    if($scope.list.id){
                        $scope.save(form, event, redirect);
                    } else {
                        $scope.create(event, redirect);
                    }
                };

                $scope.create = function(event, redirect){
                    redirect = redirect || false;
                    Restangular.all("lists").post($scope.list)
                        .then(function(updated){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_LISTS));
                            $state.go("admin.lists.edit",{id : updated.id});
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

                    $scope.list.save()
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_LISTS));
                            if(redirect){
                                $state.go("admin.lists.list");
                            } else {
                                $scope.$broadcast("admin.lists.saved");
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
                
                $scope.remove = function(list, event){
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_LISTS))
                        .textContent(sprintf("Are you sure you want to remove this %s? This cannot be undone.", $scope.dict.TITLE_LISTS.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_LISTS))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        list.remove().then(function(){
                            $scope.toast(sprintf("%s removed", $scope.dict.TITLE_LISTS));

                            $scope.$broadcast("admin.lists.removed");
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

                $scope.addCustomField = function() {
                    // Initialize to an empty array if there are no custom fields
                    if(!$scope.list.hasOwnProperty("custom_fields") ||
                        $scope.list.custom_fields === null){
                        $scope.list.custom_fields = [];
                    }

                    $scope.list.custom_fields.push({name: "", req: 0});
                };

                $scope.removeCustomField = function(field, event) {
                    event && event.stopPropagation();

                    $scope.list.custom_fields = _.without($scope.list.custom_fields, field);
                };

                $scope.parseCustomFields = function(){
                    if(typeof $scope.list.custom_fields === "string") {
                        $scope.list.custom_fields = angular.fromJson($scope.list.custom_fields);
                    }
                };

        }])
        .controller("AddressListListCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            "lists",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular,
                lists
            ){
                $scope.lists = lists;
                $scope.selected = [];

                $scope.query = {
                    order: "name",
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
                        text: sprintf("%ss Per Page", $scope.dict.TITLE_LISTS)
                    }
                };

                $scope.getLists = function(){
                    var promise = $scope.lists.getList($scope.query);
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

                $scope.onOrderChange = function(order){
                    $scope.promise = $scope.getLists();
                };

                $scope.onPaginationChange = function(page, limit){
                    $scope.promise = $scope.getLists();
                };

                $scope.resetFilter = function(){
                    $scope.filter.show = false;

                    if($scope.filterForm.$dirty) {
                        $scope.query.q = "";
                        $scope.query.page = 1;
                        $scope.filterForm.$setPristine();
                    }
                };

                $scope.removeMany = function(event) {
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLES_LISTS))
                        .textContent(sprintf("Are you sure you want to remove these %s? This cannot be undone.", $scope.dict.TITLES_LISTS.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLES_LISTS))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        // We just need to pass uuids
                        var lists = _.map($scope.selected, "id");
                        Restangular.all("lists")
                            .customDELETE("", {"lists[]": lists})
                            .then(function(){
                                $scope.toast(sprintf("%s removed", $scope.dict.TITLES_LISTS));

                                $scope.query.page = 1;
                                $scope.selected = [];
                                $scope.promise = $scope.getLists();
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

                $scope.$on("admin.lists.removed", function(event){
                    $scope.query.page = 1;
                    $scope.promise = $scope.getLists();
                });

                $scope.$watch("query.q", function(){
                    $scope.query.page = 1;
                    $scope.selected = [];
                    $scope.promise = $scope.getLists();
                });

        }])
        .controller("AddressListCreateCtrl", [
            "$scope",
            "$state",
            "$stateParams",
            function(
                $scope,
                $state,
                $stateParams
            ){
                $scope.$parent.list = {};
            
        }])
        .controller("AddressListEditCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            "list",
            "addresses",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular,
                list,
                addresses
            ){
                $scope.$parent.list = list;
                $scope.addresses = addresses;
                $scope.selected = [];
                  
                $scope.query = {
                    order: "-created_at",
                    limit: 10,
                    page: 1
                };

                $scope.address_filter = {
                    options: {
                        debounce: 700
                    }
                };

                $scope.pagination = {
                    label: {
                        text: sprintf("%s Per Page", $scope.dict.TITLES_ADDRESSES)
                    }
                };

                $scope.getAddresses = function(){
                    var promise = $scope.addresses.getList($scope.query);
                    promise.then(function(records){
                        $scope.addresses = records;
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
                    $scope.promise = $scope.getAddresses();
                };

                $scope.onPaginationChange = function(page, limit){
                    $scope.promise = $scope.getAddresses();
                };

                $scope.resetFilter = function(){
                    $scope.address_filter.show = false;

                    if($scope.filterForm.$dirty) {
                        $scope.query.q = "";
                        $scope.query.page = 1;
                        $scope.filterForm.$setPristine();
                    }
                };
                
                $scope.remove = function(address, event){
                    event && event.stopPropagation();

                     var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_ADDRESSES))
                        .textContent(sprintf("Are you sure you want to remove this %s? This cannot be undone.", $scope.dict.TITLE_ADDRESSES.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_ADDRESSES))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        Restangular.one("lists", $scope.$parent.list.id)
                            .one("addresses", address.id).remove()
                            .then(function(){
                                $scope.toast(sprintf("%s removed", $scope.dict.TITLE_ADDRESSES));

                                $scope.query.page = 1;
                                $scope.promise = $scope.getAddresses();
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

                $scope.removeMany = function (event) {
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_ADDRESSES))
                        .textContent(sprintf("Are you sure you want to remove these %s? This cannot be undone.", $scope.dict.TITLE_ADDRESSES.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_ADDRESSES))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        // We just need to pass the address uuids
                        var addresses = _.map($scope.selected, "id");
                        Restangular.one("lists", $scope.$parent.list.id)
                            .customDELETE("addresses", {"uuid[]": addresses})
                            .then(function(){
                                $scope.toast(sprintf("%s removed", $scope.dict.TITLE_ADDRESSES));

                                $scope.query.page = 1;
                                $scope.selected = [];
                                $scope.promise = $scope.getAddresses();
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

                $scope.canBlock = function(){
                    if($scope.selected.length){
                        return !_.filter($scope.selected,{spam:false}).length;
                    }
                    return false;
                };

                $scope.canUnblock = function(){
                    if($scope.selected.length){
                        return !_.filter($scope.selected,{spam:true}).length;
                    }
                    return false;
                };

                $scope.block = function(address, event){
                    event && event.stopPropagation();

                    Restangular.one("lists", $scope.$parent.list.id)
                        .one("addresses", address.id).customPOST({},"block")
                        .then(function(record){
                            if(record.spam){
                                $scope.toast(sprintf("%s unblocked", $scope.dict.TITLE_ADDRESSES));
                            } else {
                                $scope.toast(sprintf("%s blocked", $scope.dict.TITLE_ADDRESSES));
                            }

                            $scope.promise = $scope.getAddresses();
                        }).catch(function(response){
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                };

                $scope.blockMany = function(event) {
                    event && event.stopPropagation();

                    // We just need to pass the address uuids
                    var addresses = _.map($scope.selected, "id");
                    Restangular.one("lists", $scope.$parent.list.id).all("addresses")
                        .customPOST({addresses: addresses}, 'block')
                        .then(function(){
                            $scope.toast(sprintf("%s updated", $scope.dict.TITLES_ADDRESSES));

                            $scope.selected = [];
                            $scope.promise = $scope.getAddresses();
                        }).catch(function(response){
                            if(response.data && response.data.message){
                                $scope.toast("Error: " + response.data.message);
                            } else {
                                $scope.toast("An error occurred");
                            }
                            $log.warn(response);
                        });
                };

                $scope.blocked = function(address){
                    return (!address.spam || parseInt(address.spam) === 0);
                };

                $scope.$watch("query.q", function(){
                    $scope.query.page = 1;
                    $scope.selected = [];
                    $scope.promise = $scope.getAddresses();
                });

                // Make sure our custom fields are parsed properly
                $scope.parseCustomFields();

        }])
        .controller("AddressListImportCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            "Papa",
            "list",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular,
                Papa,
                list
            ){
                $scope.$parent.list = list;
                $scope.addresses = {};

                $scope.text = "";

                $scope.step = "file-select";
                $scope.input_file = null;
                $scope.use_list_fields = false;
                
                $scope.query = {
                    order: "email",
                    limit: 20,
                    page: 1
                };

                $scope.pagination = {
                    label: {
                        text: sprintf("%s Per Page", $scope.dict.TITLES_ADDRESSES)
                    }
                };

                $scope.fields = function(parsed, join){
                    parsed = parsed || false;
                    join = join || false;

                    // If we passed in a CSV file with a header row
                    // then we should use the field names from there
                    if(parsed 
                        && $scope.addresses.count
                        && $scope.addresses.meta.hasOwnProperty("fields")
                        && $scope.addresses.meta.fields.length){
                        return $scope.addresses.meta.fields;
                    }

                    var fields = $scope.$parent.defaultFields;
                    var custom_fields = $scope.$parent.list.custom_fields;

                    if(custom_fields && custom_fields.length) {
                        fields = fields.concat(_.map(custom_fields, "name"));
                    }

                    return join ? fields.join(" , ") : fields;
                };

                $scope.setStep = function(step){
                    if(step.length > 0){
                        $scope.step = step;
                    }
                }

                $scope.fileChanged = function(event){
                    event && event && event.stopPropagation();

                    var target = event.target;
                    if(target.files && target.files.length){
                        $scope.$apply(function() {
                            if (!target.files[0].type.match("text/csv")){
                                $scope.toast("Invalid file. Please select a CSV file.")
                                $scope.input_file = null;
                                $scope.setStep("file-select"); 
                                return false;
                            }
                            $scope.input_file = target.files[0];
                            $scope.setStep("header-option"); 
                        });
                    }
                };

                $scope.toggleHeader = function(state, event){
                    event && event && event.stopPropagation();

                    if(typeof state !== 'boolean') state = false;
                    $scope.use_list_fields = state;
                };

                $scope.parseText = function(event){
                    event && event.stopPropagation();

                    var parsed = Papa.parse($scope.text,{
                        header: false,
                        skipEmptyLines: true
                    });
                    parsed.count = parsed.data.length;
                    // If there was no header row, just assume that
                    // it matches the text area label format
                    if(!parsed.meta.hasOwnProperty("fields") || !parsed.meta.fields.length){
                        parsed.data = _.map(parsed.data, function(d){
                            return _.zipObject($scope.fields(), d);
                        });
                    }
                    $scope.addresses = parsed;
                    $scope.setStep("preview-addresses");
                    $scope.toast("Parse complete");
                };

                $scope.parseFile = function(event){
                    event && event.stopPropagation();

                    var target = $scope.input_file;
                    if(target){
                        if (!target.type.match("text/csv")){
                            $scope.toast("Invalid file. Please select a CSV file.")
                            return false;
                        }

                        $scope.textInput = "";
                        Papa.parse(target, {
                            header: !$scope.use_list_fields,
                            skipEmptyLines: true,
                            complete: function(results) {
                                // If there was no header row, just assume it follows
                                // the label format of the text area.
                                if(!results.meta.hasOwnProperty("fields")
                                    || !results.meta.fields.length){
                                    var fields = $scope.fields();
                                    results.data = _.map(results.data, function(d){
                                        return _.zipObject(fields, d);
                                    });
                                }

                                $scope.$apply(function(){
                                    results.count = results.data.length;
                                    $scope.addresses = results;
                                });
                                $scope.setStep("preview-addresses");
                                $scope.toast("Parse complete");
                            }
                        });
                    } else {
                        $scope.toast("Please select a valid CSV file first.")
                        return false;
                    }
                };

                $scope.import = function(event){
                    event && event.stopPropagation();

                    var confirm = $mdDialog.confirm()
                        .title("Are these emails CASL compliant?")
                        .textContent("I confirm that each email address entered here has been gathered in compliance with CASL")
                        .ariaLabel("Confirm compliance")
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        // We need to build the custom_data property manually.
                        var addresses = $scope.addresses.data;
                        addresses = _.map(addresses, function(a){
                            var defaults = _.reduce(a, function(result, n, key){
                                if(_.includes($scope.defaultFields, key)){
                                    result[key] = n;
                                }
                                return result;
                            }, {});

                            return _.extend(defaults, {
                                custom_data: _.reduce(a, function(result, n, key){
                                    if(_.includes(_.map($scope.list.custom_fields,"name"),key)){
                                        result[key] = n;
                                    }
                                    return result;
                                }, {})
                            });
                        });

                        $scope.list.all("addresses").customPOST(addresses)
                            .then(function(records){
                                $scope.toast("Import completed");
                                $state.go("admin.lists.edit",{id: $scope.list.id});
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

                $scope.remove = function(address, event){
                    event && event.stopPropagation();

                     var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_ADDRESSES))
                        .textContent(sprintf("Are you sure you want to remove this %s? This cannot be undone.", $scope.dict.TITLE_ADDRESSES.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_ADDRESSES))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        $scope.addresses.data.splice(address,1);
                        $scope.addresses.count--;
                    }, function() {
                        // Do nothing
                    });
                };

                // Make sure our custom fields are parsed properly
                $scope.parseCustomFields();

        }])
        .controller("AddressCtrl", [
            "$log",
            "$rootScope",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            function(
                $log,
                $rootScope,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular
            ){

                $scope.saveOrCreate = function(form, event, redirect){
                    event && event.stopPropagation();

                    if($scope.address.id){
                        $scope.save(form, event, redirect);
                    } else {
                        $scope.create(event, redirect);
                    }
                };

                $scope.create = function(event, redirect){
                    redirect = redirect || false;

                    var confirm = $mdDialog.confirm()
                        .title("Is this email CASL compliant?")
                        .textContent("I confirm that the email address entered here has been gathered in compliance with CASL")
                        .ariaLabel("Confirm compliance")
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        $scope.list.all("addresses").post($scope.address)
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_ADDRESSES));
                            $scope.$broadcast("admin.lists.address.saved", record);
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

                $scope.save = function(form, event, redirect){
                    redirect = redirect || false;
                    $scope.address.put({sync_list: $scope.list.id})
                        .then(function(record){
                            $scope.toast(sprintf("%s saved", $scope.dict.TITLE_ADDRESSES));
                            if(redirect){
                                $state.go("admin.lists.edit",{id: $scope.list.id});
                            } else {
                                $scope.$broadcast("admin.lists.address.saved", record);
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

                $scope.remove = function(address, event){
                    event && event.stopPropagation();

                     var confirm = $mdDialog.confirm()
                        .title(sprintf("Remove %s?", $scope.dict.TITLE_ADDRESSES))
                        .textContent(sprintf("Are you sure you want to remove this %s? This cannot be undone.", $scope.dict.TITLE_ADDRESSES.toLowerCase()))
                        .ariaLabel(sprintf("Remove %s", $scope.dict.TITLE_ADDRESSES))
                        .targetEvent(event)
                        .ok("Yes")
                        .cancel("Maybe Not");
                    $mdDialog.show(confirm).then(function() {
                        Restangular.one("lists", $scope.list.id)
                            .one("addresses", address.id).remove().then(function(){
                                $scope.toast(sprintf("%s removed", $scope.dict.TITLE_ADDRESSES));

                                $scope.$broadcast("admin.lists.address.removed");
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
                
                $scope.parseCustomFields = function(){
                    // Custom fields from the list
                    if(typeof $scope.list.custom_fields === "string") {
                        $scope.list.custom_fields = angular.fromJson($scope.list.custom_fields);
                    }
                    // Address specific custom data
                    if(typeof $scope.address.custom_data === "string") {
                        $scope.address.custom_data = angular.fromJson($scope.address.custom_data);
                    }
                };

        }])
        .controller("AddressCreateCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "$mdDialog",
            "Restangular",
            "list",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                $mdDialog,
                Restangular,
                list
            ){
                $scope.$parent.list = list;
                $scope.$parent.address = {};

                // Make sure our custom fields are parsed properly
                $scope.parseCustomFields();

                $scope.$on("admin.lists.address.saved", function(event, address){
                    $state.go("admin.lists.edit",{id: $scope.list.id});
                });
                
        }])
        .controller("AddressEditCtrl", [
            "$log",
            "$scope",
            "$state",
            "$stateParams",
            "Restangular",
            "list",
            "address",
            function(
                $log,
                $scope,
                $state,
                $stateParams,
                Restangular,
                list,
                address
            ){
            $scope.$parent.list = list;
            $scope.$parent.address = address;

            $scope.$on("admin.lists.address.saved", function(event, address){
                $scope.$parent.address = address;
                $scope.parseCustomFields();

                $scope.addressForm.$setPristine();
            });

            $scope.$on("admin.lists.address.removed", function(event){
                $state.go("admin.lists.edit", {id: $scope.$parent.list.id});
            });

            // Make sure our custom fields are parsed properly
            $scope.parseCustomFields();

        }]);
})();
