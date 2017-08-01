(function(){

    "use strict";

    angular.module("thin",[
        "ui.router",
        "ct.ui.router.extras",
        "ui.router.title",
        "ngAnimate",
        "ngAria",
        "ngMessages",
        "ngMaterial",
        "md.data.table",
        "scDateTime",
        "textAngular",
        "restangular",
        "sprintf",
        "angularMoment",
        "angular-loading-bar",
        "thin.controllers",
        "thin.directives",
        "thin.filters",
        "thin.providers",
        "thin.states"
        ]).constant("api_base_url", "/api/v1.0")
          .constant("Modernizr", Modernizr)
          .constant("Moment", moment)
          .constant("Papa", Papa)
          .constant("Dictionary",{
                TITLE_DASHBOARD: "Dashboard",
                TITLE_EMAILERS: "Newsletter",
                TITLE_TEMPLATES: "Template",
                TITLE_LISTS: "List",
                TITLE_ADDRESSES: "Subscriber",
                TITLE_USERS: "User",
                TITLE_ARTICLES: "Article",
                TITLES_EMAILERS: "Newsletters",
                TITLES_TEMPLATES: "Templates",
                TITLES_LISTS: "Lists",
                TITLES_ADDRESSES: "Subscribers",
                TITLES_USERS: "Users",
                TITLES_ARTICLES: "Help Articles",
          })
          .config(["$httpProvider",
            "$stateProvider",
            "$urlRouterProvider",
            "$locationProvider",
            "$interpolateProvider",
            "RestangularProvider",
            "cfpLoadingBarProvider",
            "$mdThemingProvider",
            "Modernizr",
            "api_base_url",
            function($httpProvider,
                $stateProvider,
                $urlRouterProvider,
                $locationProvider,
                $interpolateProvider,
                RestangularProvider,
                cfpLoadingBarProvider,
                $mdThemingProvider,
                Modernizr,
                api_base_url
            ){
                // Use URLs without a hash(#)
                if(Modernizr.history){
                    $locationProvider.html5Mode(true);
                }
                
                // Use $urlRouterProvider to configure any redirects(when) and invalid urls(otherwise).
                $urlRouterProvider.otherwise("/admin/404");
                
                // Intercept our XHR calls to authenticate them
                $httpProvider.interceptors.push("AuthInterceptor");

                // Configure Restangular for our API
                RestangularProvider.setBaseUrl(api_base_url);

                RestangularProvider.addRequestInterceptor(function(element, operation, route, url, headers, params, httpConfig) {
                    // Don't pass meta information on 
                    if(_.includes(["put","post","delete"], operation)){
                        delete element._meta;
                        return element;
                    }
                });

                RestangularProvider.addResponseInterceptor(function(data, operation, what, url, response, deferred) {
                    var extractedData = data.data;
                    // parse our range headers if they exist
                    var limit = parseInt(response.headers("X-Pagination-Per-Page"));
                    var total = parseInt(response.headers("X-Pagination-Total-Entries"));
                    var page = parseInt(response.headers("X-Pagination-Current-Page"));
                    var pages = parseInt(response.headers("X-Pagination-Total-Pages"));
                    
                    if(typeof extractedData === "object"){
                        extractedData._meta = {
                            "page": page || null,
                            "pages": pages || null,
                            "limit": limit || null,
                            "total": total || null
                        }
                    }
                    
                    // if we have a message (used for errors, etc)
                    if(data.message){
                        extractedData._message = data.message;
                    }
                
                    return extractedData;
                });
                
                // Configure the loading bar and indicator
                cfpLoadingBarProvider.spinnerTemplate = '<div class="spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>';
                cfpLoadingBarProvider.includeSpinner = true;
                cfpLoadingBarProvider.latencyThreshold = 500;

                // The amber looks better with white text.
                var amber = $mdThemingProvider.extendPalette("amber", {
                    "contrastDefaultColor": "dark",
                    "contrastLightColors": [
                        "A100",
                        "A200",
                        "A400",
                        "A700"
                    ]
                });
                $mdThemingProvider.definePalette("novo-amber", amber);

                // So does green
                var green = $mdThemingProvider.extendPalette("green", {
                    "contrastDefaultColor": "light"
                });
                $mdThemingProvider.definePalette("novo-green", green);

                $mdThemingProvider.theme("default")
                    .primaryPalette("blue",{
                        "default": "600",
                        "hue-1": "100",
                        "hue-2": "600",
                        "hue-3": "A100"
                    })
                    .accentPalette("novo-amber");

                $mdThemingProvider.theme("extended")
                    .primaryPalette("novo-green");
        }])
        .run(["$rootScope", "$location", "$state", "$stateParams", "Auth",
            function($rootScope, $location, $state, $stateParams, Auth){
                // It"s very handy to add references to $state and $stateParams to the $rootScope
                // so that you can access them from any scope within your applications.
                $rootScope.$state = $state;
                $rootScope.$stateParams = $stateParams;
                $rootScope.location = $location;
                
                if(localStorage.token){
                    $rootScope.user = angular.fromJson(localStorage.user);
                }
                
                $rootScope.$on("$stateChangeStart", 
                    function(event, toState, toParams, fromState, fromParams){
                        // Authorize access to the state
                        if(!Auth.authorize(toState)){
                            event.preventDefault();
                            $state.go("admin.login",{next: btoa(angular.toJson({name: toState.name, params: toParams}))});
                        }
                    }
                );
                
                $rootScope.$on("$stateChangeSuccess", 
                    function(event, toState, toParams, fromState, fromParams){
                        // Build our body class names from the state
                        var segments = toState.name.split(".");
                        segments = _.map(segments.reverse(),function(segment){
                            return _.union(segments.slice(_.indexOf(segments,segment)),["state"]).reverse().join("-");
                        });
                        $rootScope.page_class = segments.reverse().join(" ");
                        $rootScope.is_admin = _.includes(segments, "state-admin");
                    }
                );
                
                $rootScope.$on("$stateChangeError",
                    function(event, toState, toParams, fromState, fromParams) {
                        event.preventDefault();
                        $state.go("admin.404");
                    }
                );
            }
        ]);

    /* Configure controllers and dependencies */
    angular.module("thin.controllers", ["thin.providers"]);

    /* Configure filters and dependencies */
    angular.module("thin.filters", []);

    /* Configure directives and dependencies */
    angular.module("thin.directives", []);

    /* Configure services and dependencies */
    angular.module("thin.providers", ["restangular"]);

    /* Configure states and dependencies */
    angular.module("thin.states", ["ui.router"])
        .config(["$stateProvider", "RestangularProvider", 
            function($stateProvider, RestangularProvider){
                /* Base states */           
                $stateProvider
                    .state("admin", {
                        url: "/admin",
                        abstract: true,
                        views: {
                            "navigation": {
                                templateUrl: "/tpl/admin/base/navigation"
                            },
                            "content": {
                                templateUrl: "/tpl/admin/page"
                            },
                            "offcanvas": {
                                templateUrl: "/tpl/admin/base/offcanvas"
                            }
                        }
                    })
                    .state("admin.404", {
                        url: "/404",
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/page/404"
                            },
                        },
                        resolve: {
                            $title: ["Dictionary", function(Dictionary){ return "Not Found"; }]
                        }
                    })
                    .state("admin.index", {
                        url: "",
                        access: ["ADMIN", "CONTENTADMIN"],
                        resolve: {
                            $title: ["Dictionary", function(Dictionary){ return Dictionary.TITLE_DASHBOARD; }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/page/dashboard",
                                controller: "AdminDashboardCtrl",
                                resolve: {
                                    emailers: ["Restangular", "Moment", "$stateParams", function(Restangular, Moment, $stateParams){
                                        var last_week = Moment().subtract(7, "days").format("YYYY-MM-DD HH:mm:ss");
                                        return Restangular.all("emailers")
                                            .getList({order: "-updated_at", updated_at: "%3E" + last_week, "with": ["lists"], limit: 3});
                                    }],
                                    templates: ["Restangular", "Moment", "$stateParams", function(Restangular, Moment, $stateParams){
                                        var last_week = Moment().subtract(7, "days").format("YYYY-MM-DD HH:mm:ss");
                                        return Restangular.all("templates")
                                            .getList({order: "-updated_at", updated_at: "%3E" + last_week, limit: 3});
                                    }],
                                    lists: ["Restangular", "Moment", "$stateParams", function(Restangular, Moment, $stateParams){
                                        var last_week = Moment().subtract(7, "days").format("YYYY-MM-DD HH:mm:ss");
                                        return Restangular.all("lists")
                                            .getList({order: "-updated_at", updated_at: "%3E" + last_week, limit: 3});
                                    }]
                                }
                            }
                        }
                    })
                    .state("admin.login", {
                        url: "/login?next",
                        resolve: {
                            $title: ["Dictionary", function(Dictionary){ return "Login"; }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/page/login",
                                controller: ["$rootScope", "$scope", "$http", "$window", "$stateParams", "Auth", function($rootScope, $scope, $http, $window, $stateParams, Auth){
                                    $scope.user = {};
                                    $scope.submit = function(){
                                        Auth.login($scope.user, $stateParams.next);
                                    };
                                }]
                            }
                        },
                        onEnter: ["$rootScope", "$state", "Auth", function($rootScope, $state, Auth){
                            if(Auth.isAuthenticated()){
                                $state.go("admin.index");
                            }
                        }]
                    })
                    .state("admin.logout", {
                        url: "/logout",
                        onEnter: ["$rootScope", "$state", "Auth", function($rootScope, $state, Auth){
                            Auth.logout("admin.index");
                        }]
                    });
                    
                /* User Routes */
                $stateProvider
                    .state("admin.users", {
                        url: "/users",
                        abstract: true,
                        views: {
                            "content@": {
                                templateUrl: "/tpl/admin/page",
                                controller: "UserCtrl"
                            }
                        },
                    })
                    .state("admin.users.list", {
                        url: "",
                        access: ["ADMIN"],
                        resolve: {
                            $title: ["Dictionary", function(Dictionary){ return Dictionary.TITLES_USERS; }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/user/list",
                                controller: "UserListCtrl",
                                resolve: {
                                    users: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                        return Restangular.all("users").getList({order: "username", limit: 10});
                                    }]
                                }
                            }
                        }
                    })
                    .state("admin.users.edit", {
                        url: "/{id}/edit",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            user: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("users", $stateParams.id).get();
                            }],
                            $title: ["Dictionary", "user", function(Dictionary, user){ return sprintf("Edit %s", user.username); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/user/edit",
                                controller: "UserEditCtrl",
                                onEnter: ["$rootScope", "$state", "$stateParams", "$previousState", function($rootScope, $state, $stateParams, $previousState){
                                    // Hacky way to ensure that this check occurs after the state loads
                                    // and prevents and infinite loop.
                                    $rootScope.$on("$stateChangeSuccess", function(event, toState, toParams, fromState, fromParams){
                                        // Only allow the user to modify their own user if they aren't an admin
                                        if(fromState.name !== "admin.users.edit"
                                            && toParams.id !== $rootScope.user.id
                                            && $rootScope.user.role !== "ADMIN"){
                                            $previousState.get() && $previousState.go();
                                        }
                                    })();
                                }]
                            }
                        }
                    })
                    .state("admin.users.new", {
                        url: "/new",
                        access: ["ADMIN"],
                        resolve: {
                            $title: ["Dictionary", function(Dictionary){ return sprintf("New %s", Dictionary.TITLE_USERS); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/user/edit",
                                controller: "UserCreateCtrl"
                            }
                        }
                    });
                    
                /* List Routes */
                $stateProvider
                    .state("admin.lists", {
                        url: "/lists",
                        abstract: true,
                        views: {
                            "content@": {
                                templateUrl: "/tpl/admin/page",
                                controller: "AddressListCtrl"
                            }
                        }
                    })
                    .state("admin.lists.list", {
                        url: "",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            lists: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.all("lists").getList({order: "name", limit: 10});
                            }],
                            $title: ["Dictionary", function(Dictionary){ return Dictionary.TITLES_LISTS; }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/address-list/list",
                                controller: "AddressListListCtrl"
                            }
                        }
                    })
                    .state("admin.lists.edit", {
                        url: "/{id}/edit",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            list: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("lists",$stateParams.id).get();
                            }],
                            addresses: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("lists", $stateParams.id).all("addresses")
                                    .getList({order: "-created_at", limit: 10});
                            }],
                            $title: ["Dictionary", "list", function(Dictionary, list){ return sprintf("Edit %s", list.name); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/address-list/edit",
                                controller: "AddressListEditCtrl"
                            }
                        }
                    })
                    .state("admin.lists.new", {
                        url: "/new",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            $title: ["Dictionary", function(Dictionary){ return sprintf("New %s", Dictionary.TITLE_LISTS); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/address-list/edit",
                                controller: "AddressListCreateCtrl"
                            }
                        }
                    })
                    .state("admin.lists.import", {
                        url: "/{id}/import",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            list: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("lists", $stateParams.id).get();
                            }],
                            $title: ["Dictionary", "list", function(Dictionary, list){ return sprintf("Import %s | %s", Dictionary.TITLES_ADDRESSES, list.name); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/address-list/import",
                                controller: "AddressListImportCtrl"
                            }
                        }
                    })
                    .state("admin.lists.import.raw", {
                        url: "/raw",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            list: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("lists", $stateParams.id).get();
                            }],
                            $title: ["Dictionary", "list", function(Dictionary, list){ return sprintf("Add %s | %s", Dictionary.TITLES_ADDRESSES, list.name); }]
                        },
                        views: {
                            'page-content@admin.lists': {
                                templateUrl: "/tpl/admin/address-list/import-text",
                                controller: "AddressListImportCtrl"
                            }
                        }
                    })
                    .state("admin.lists.address", {
                        url: "/{id}/addresses",
                        abstract: true,
                        views: {
                            "content@": {
                                templateUrl: "/tpl/admin/page",
                                controller: "AddressCtrl"
                            }
                        }
                    })
                    .state("admin.lists.address.edit", {
                        url: "/{address_id}/edit",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            list: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("lists", $stateParams.id).get();
                            }],
                            address: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("addresses", $stateParams.address_id)
                                    .get({custom_data: $stateParams.id});
                            }],
                            $title: ["Dictionary", "address", function(Dictionary, address){ return sprintf("Edit %s", address.email); }]
                        },
                        views: {
                            "page-content@admin.lists.address": {
                                templateUrl: "/tpl/admin/address/edit",
                                controller: "AddressEditCtrl"
                            }
                        }
                    })
                    .state("admin.lists.address.new", {
                        url: "/new",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            list: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("lists", $stateParams.id).get();
                            }],
                            $title: ["Dictionary", "list", function(Dictionary, list){ return sprintf("New %s | %s", Dictionary.TITLE_ADDRESSES, list.name); }]
                        },
                        views: {
                            "page-content@admin.lists.address": {
                                templateUrl: "/tpl/admin/address/edit",
                                controller: "AddressCreateCtrl"
                            }
                        }
                    });
                    
                /* Template Routes */
                $stateProvider
                    .state("admin.templates", {
                        url: "/templates",
                        abstract: true,
                        views: {
                            "content@": {
                                templateUrl: "/tpl/admin/page",
                                controller: "TemplateCtrl"
                            }
                        }
                    })
                    .state("admin.templates.list", {
                        url: "",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            templates: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.all("templates").getList({limit: 12});
                            }],
                            $title: ["Dictionary", function(Dictionary){ return Dictionary.TITLES_TEMPLATES; }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/template/list",
                                controller: "TemplateListCtrl"
                            }
                        }
                    })
                    .state("admin.templates.edit", {
                        url: "/{id}/edit",
                        access: ["ADMIN"],
                        resolve: {
                            template: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("templates", $stateParams.id).get();
                            }],
                            $title: ["Dictionary", "template", function(Dictionary, template){ return sprintf("Edit %s", template.name); }]
                        },   
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/template/edit",
                                controller: "TemplateEditCtrl"
                            }
                        }
                    })
                    .state("admin.templates.new", {
                        url: "/new",
                        access: ["ADMIN"],
                        resolve: {
                            $title: ["Dictionary", function(Dictionary){ return sprintf("New %s", Dictionary.TITLE_TEMPLATES); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/template/edit",
                                controller: "TemplateCreateCtrl"
                            }
                        }
                    });
        
                /* Campaign Routes */
                $stateProvider
                    .state("admin.emailers", {
                        url: "/campaigns",
                        abstract: true,
                        views: {
                            "content@": {
                                templateUrl: "/tpl/admin/page",
                                controller: "EmailerCtrl"
                            }
                        }
                    })
                    .state("admin.emailers.list", {
                        url: "",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            emailers: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.all("emailers")
                                    .getList({order: "-distribute_at", "with": ["lists"], limit: 10});
                            }],
                            $title: ["Dictionary", function(Dictionary){ return Dictionary.TITLES_EMAILERS; }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/emailer/list",
                                controller: "EmailerListCtrl"
                            }
                        }
                    })
                    .state("admin.emailers.edit", {
                        url: "/{id}/edit",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            emailer: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("emailers",$stateParams.id).get({"with": ["lists"]});
                            }],
                            templates: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.all("templates").getList({order: "-updated_at", limit: 4});
                            }],
                            lists: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.all("lists").getList({order: "name", limit: 12});
                            }],
                            $title: ["Dictionary", "emailer", function(Dictionary, emailer){ return sprintf("Edit %s", emailer.subject); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/emailer/edit",
                                controller: "EmailerEditCtrl"
                            }
                        }
                    })
                    .state("admin.emailers.new", {
                        url: "/new?template",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            templates: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.all("templates").getList({limit: 4});
                            }],
                            lists: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.all("lists").getList({limit: 24});
                            }],
                            $title: ["Dictionary", function(Dictionary){ return sprintf("New %s", Dictionary.TITLE_EMAILERS); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/emailer/edit",
                                controller: "EmailerCreateCtrl"
                            }
                        }
                    })
                    .state("admin.emailers.stats", {
                        url: "/{id}/stats",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            emailer: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("emailers",$stateParams.id).get({"with": ["lists"]});
                            }],
                            stats: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("emailers",$stateParams.id)
                                    .all("stats").getList({limit: 50});
                            }],
                            $title: ["Dictionary", "emailer", function(Dictionary, emailer){ return sprintf("%s Stats", emailer.subject); }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/emailer/stats",
                                controller: "EmailerStatsCtrl"
                            }
                        }
                    });
        
                /* Help Routes */
                $stateProvider
                    .state("admin.help", {
                        url: "/help",
                        abstract: true,
                        views: {
                            "content@": {
                                templateUrl: "/tpl/admin/page",
                                controller: "HelpCtrl"
                            }
                        }
                    })
                    .state("admin.help.index", {
                        url: "",
                        abstract: true,
                        resolve: {
                            articles: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.all("help-articles")
                                    .getList({section: "EMAILERS", order: "-updated_at", limit: 50});
                            }],
                            $title: ["Dictionary", function(Dictionary){ return Dictionary.TITLES_ARTICLES; }]
                        },
                        views: {
                            "page-content": {
                                templateUrl: "/tpl/admin/help/index",
                                controller: "HelpListCtrl"
                            }
                        }
                        
                    })
                    .state("admin.help.index.list", {
                        url: "",
                        abstract: true,
                        views: {
                            "list": {
                                templateUrl: "/tpl/admin/help/list",
                                controller: "HelpListSectionsCtrl"
                            }
                        }
                    })
                    .state("admin.help.index.list.all", {
                        url: "",
                        access: ["ADMIN","CONTENTADMIN"]
                    })
                    .state("admin.help.new", {
                        url: "/new",
                        access: ["ADMIN"],
                        resolve: {
                            $title: ["Dictionary", function(Dictionary){ return sprintf("New %s", Dictionary.TITLE_ARTICLES); }]
                        },
                        views: {
                            "page-content@admin.help": {
                                templateUrl: "/tpl/admin/help/edit",
                                controller: "HelpCreateCtrl"
                            }
                        }
                    })
                    .state("admin.help.index.list.view", {
                        url: "/{id}",
                        access: ["ADMIN","CONTENTADMIN"],
                        resolve: {
                            article: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("help-articles", $stateParams.id).get();
                            }],
                            $title: ["Dictionary", "article", function(Dictionary, article){ return article.title; }]
                        },
                        views: {
                            "single@admin.help.index": {
                                templateUrl: "/tpl/admin/help/view",
                                controller: "HelpViewCtrl"
                            }
                        }
                    })
                    .state("admin.help.edit", {
                        url: "/{id}/edit",
                        access: ["ADMIN"],
                        resolve: {
                            article: ["Restangular", "$stateParams", function(Restangular, $stateParams){
                                return Restangular.one("help-articles", $stateParams.id).get();
                            }],
                            $title: ["Dictionary", "article", function(Dictionary, article){ return sprintf("Edit %s", article.title); }]
                        },
                        views: {
                            "page-content@admin.help": {
                                templateUrl: "/tpl/admin/help/edit",
                                controller: "HelpEditCtrl"
                            }
                        }
                    });
            }
        ]);
})();
