<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * API Routes (v1)
 */
$app->group(['prefix' => 'api/v1.0', 'namespace' => 'App\Http\Controllers\API'], function ($app) {

    /**
     * Users
     */
    $app->get('users', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'UserController@all']);
    $app->post('users', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'UserController@post']);
    $app->delete('users', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'UserController@deleteMany']);
    $app->get('users/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'UserController@get']);
    $app->put('users/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'UserController@put']);
    $app->delete('users/{id}', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'UserController@delete']);

    /**
     * Lists
     */
    $app->get('lists', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@all']);
    $app->post('lists', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@post']);
    $app->delete('lists', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'AddressListController@deleteMany']);
    $app->get('lists/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@get']);
    $app->put('lists/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@put']);
    $app->delete('lists/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@delete']);
    $app->get('lists/{id}/addresses', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@getAddresses']);
    $app->post('lists/{id}/addresses', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@postAddress']);
    $app->delete('lists/{id}/addresses', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@deleteAddresses']);
    $app->delete('lists/{id}/addresses/{address}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@deleteAddress']);
    $app->post('lists/{id}/addresses/block', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@blockAddresses']);
    $app->post('lists/{id}/addresses/{address}/block', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressListController@blockAddress']);
 
    /**
     * Addresses
     */
    $app->get('addresses', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressController@all']);
    $app->post('addresses', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressController@post']);
    $app->delete('addresses', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'AddressController@truncate']);
    $app->get('addresses/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressController@get']);
    $app->put('addresses/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressController@put']);
    $app->delete('addresses/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressController@delete']);
    $app->get('addresses/{id}/lists', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'AddressController@getLists']);
    
    /**
     * Templates
     */
    $app->get('templates', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'TemplateController@all']);
    $app->post('templates', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'TemplateController@post']);
    $app->delete('templates', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'TemplateController@truncate']);
    $app->get('templates/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'TemplateController@get']);
    $app->put('templates/{id}', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'TemplateController@put']);
    $app->delete('templates/{id}', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'TemplateController@delete']);
    
    /**
     * Emailers
     */
    $app->get('emailers', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@all']);
    $app->post('emailers', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@post']);
    $app->delete('emailers', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'EmailerController@truncate']);
    $app->get('emailers/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@get']);
    $app->put('emailers/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@put']);
    $app->delete('emailers/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@delete']);
    $app->put('emailers/{id}/pause', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@pause']);
    $app->put('emailers/{id}/start', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@start']);
    $app->put('emailers/{id}/approve', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@approve']);
    $app->put('emailers/{id}/unapprove', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@unapprove']);
    $app->get('emailers/{id}/stats', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@stats']);
    $app->post('emailers/preview', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'EmailerController@preview']);
    
    /**
     * Help Articles
     */
    $app->get('help-articles', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'HelpArticleController@all']);
    $app->post('help-articles', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'HelpArticleController@post']);
    $app->delete('help-articles', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'HelpArticleController@truncate']);
    $app->get('help-articles/{id}', ['middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'uses' => 'HelpArticleController@get']);
    $app->put('help-articles/{id}', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'HelpArticleController@put']);
    $app->delete('help-articles/{id}', ['middleware' => 'jwtreq:ADMIN', 'uses' => 'HelpArticleController@delete']);

    /**
     * Authentication
     */
    $app->post('authenticate', ['uses' => 'JWTAuthenticationController@authenticate']);

});

/**
 * Export Routes
 */
$app->group(['prefix' => 'export', 'middleware' => 'jwtreq:ADMIN,CONTENTADMIN', 'namespace' => 'App\Http\Controllers\API'], function ($app) {
    
    // Export 
    $app->get('/lists/{id}', ['uses' => 'AddressListController@exportAddresses']);

});

/**
 * Public routes
 */
$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {

    // Admin partials
    $app->get('tpl/admin/{controller}', 'PartialController@showDefaultAdmin');
    $app->get('tpl/admin/{controller}/{partial}', 'PartialController@showAdmin');

    // Partials
    $app->get('tpl/{controller}', ['uses' => 'PartialController@showDefault']);
    $app->get('tpl/{controller}/{partial}', ['uses' => 'PartialController@show']);

    // Email templates
    $app->get('templates/{template:[A-Za-z0-9-_]*}', ['as' => 'email', 'uses' => 'TemplateController@show']);
    $app->get('emails/{emailer_uuid}', ['uses' => 'EmailerController@show']);
    $app->get('emails/{emailer_uuid}/o/{address_uuid}', ['uses' => 'EmailerController@show']);
    $app->get('emails/{emailer_uuid}/o/{address_uuid}/p.png', ['uses' => 'EmailerController@pixel']);
    $app->get('emails/{emailer_uuid}/c/{url}/{address_uuid}', ['uses' => 'EmailerController@click']);
    $app->get('emails/{emailer_uuid}/r/{address_uuid}', ['uses' => 'EmailerController@resubscribe']);
    $app->get('emails/{emailer_uuid}/u/{address_uuid}', ['uses' => 'EmailerController@unsubscribe']);
    $app->put('emails/{emailer_uuid}/u/{address_uuid}', ['uses' => 'EmailerController@confirmUnsubscribe']);

    // Admin Interface Routes
    $app->group(['prefix' => 'admin'], function ($app) {
        
        // Catch-all admin route
        $app->get('{page:.*}', ['as' => 'page', function ($page) {
            return view('default-admin');
        }]);

        // Default admin route
        $app->get('/', function () {
            return view('default-admin');
        });
    });

    // Default public route
    $app->get('{page:.*}', ['as' => 'page', function ($page) {
        return view('default');
    }]);
});
