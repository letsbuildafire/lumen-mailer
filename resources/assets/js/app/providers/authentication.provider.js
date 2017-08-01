(function () {
    "use strict";

    angular.module("thin.providers").factory("Auth", [
        "$log",
        "$http", 
        "$timeout", 
        "$rootScope", 
        "$window", 
        "api_base_url", 
        function (
            $log,
            $http,
            $timeout,
            $rootScope,
            $window,
            api_base_url
        ){
            return {
                authorize: function(state){
                    if(typeof state.access === "undefined"){
                        return true;
                    } else {
                        return (this.isAuthenticated() && 
                            this.isAuthorized(state.access));
                    }
                },
                isAuthenticated: function(){
                    var token = $window.localStorage.token;
                    if(token){
                        // Has the token expired?
                        var expires = $window.localStorage.expires;
                        if(!expires || Math.round(new Date().getTime() / 1000) > expires){
                            this.logout("admin.login");
                        } else {
                            // TODO: Add a dialog when nearing expiration to renew token.
                            return true;
                        }
                    } else {
                        return false;
                    }
                },
                isAuthorized: function(roles){
                    if(this.isAuthenticated()){
                        var user = angular.fromJson($window.localStorage.user);
                        return (roles.indexOf(user.role) !== -1);
                    }
                    return false;
                },
                saveToken: function(response){
                    $window.localStorage.token = response.token;
                    $window.localStorage.expires = response.expires;
                    $window.localStorage.user = angular.toJson(response.user);
                    $rootScope.user = response.user;
                },
                removeToken: function(){
                    $window.localStorage.removeItem('token');
                    $window.localStorage.removeItem('expires');
                    $window.localStorage.removeItem('user');
                    $rootScope.user = null;
                },
                getToken: function(){
                    return $window.localStorage.token;
                },
                login: function(user,next){
                    var auth = this;
                    $http.post(api_base_url + "/authenticate", user)
                        .success(function (data, status, headers, config){
                            auth.saveToken(data);
                            $rootScope.last_error = null;
                            
                            if(next){
                                next = angular.fromJson(atob(next));
                                $rootScope.$state.go(next.name,next.params);
                            }
                            else{
                                $rootScope.$state.go("admin.index");
                            }
                        })
                        .error(function (data, status, headers, config){
                            // Erase the token if the user fails to log in
                            auth.removeToken();

                            $rootScope.last_error = {
                                type: "AUTH",
                                message: data.message
                            };
                            $log.warn(data.message);
                        });
                },
                logout: function(next){
                    this.removeToken();

                    // Navigate to the specified state
                    // using a timeout to prevent an
                    // issue where the transitions from a
                    // logged in state to a logged out state
                    // overlap.
                    // https://github.com/angular-ui/ui-router/issues/326
                    next = next || "admin.login";
                    $timeout(function(){
                        $rootScope.$state.go(next);
                    }, 100);
                }
            }
    }]);
        
    angular.module("thin.providers").factory("AuthInterceptor", [
        "$q", 
        "$rootScope", 
        "$window",
        "$injector",
        function (
            $q,
            $rootScope,
            $window,
            $injector
        ){
            return {
                request: function(config){
                    config.headers = config.headers || {};
                    if ($window.localStorage.token) {
                        config.headers.Authorization = "Bearer " +
                            $window.localStorage.token;
                    }
                    return config;
                },
                requestError: function(rejection) {
                    return $q.reject(rejection);
                },
                response: function(response) {
                    var auth = $injector.get('Auth');
                    // TODO: Save renewed token
                    return response;
                },
                responseError: function(rejection){
                    var auth = $injector.get('Auth');

                    if(rejection.status === 401){
                        if(_.has(rejection,"data") && _.has(rejection.data, "code")){
                            if(_.includes([4010,4011,4013], rejection.data.code)){
                                auth.logout("admin.login");
                            }
                        }
                    }
                    return $q.reject(rejection);
                }
            }
    }]);
})();
