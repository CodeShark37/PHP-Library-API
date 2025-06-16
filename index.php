<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;charset=UTF-8');
header('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once 'util/Route.php';
require_once 'config/Constants.php';
require_once 'util/PermissionHandler.php';
require_once 'controller/AuthController.php';
require_once 'controller/AuthorController.php';
require_once 'controller/BookController.php';
require_once 'controller/UserController.php';

// Route::add($request_method, $request_path, $callback_function, $enable_auth = true)

/*
 *
 * AUTHENTICATION ROUTES
 *
 */

// LOGIN
if(Constants::JWT['TOKEN_AUTHENTICATION']) {
    Route::add('POST', '/api/auth/login/?', function (Request $request) {
        echo (new AuthController())->login($request->getJSON());
    }, false);
}
/*
 *
 * USER ROUTES
 *
 */

// LIST ALL USERS AND LIST USER BY ID
Route::add('GET', '/api/(?P<resource>user)(/(?P<id>[0-9]+))?/?', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'view',$request->params['id']??null)) {
        echo (!isset($request->params['id']))?
            (new UserController())->findAll():
            (new UserController())->findById($request->params['id']);
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

// CREATE USER
Route::add('POST', '/api/user/?', function (Request $request) {
    echo (new UserController())->create($request->getJSON());
}, false);

// UPDATE A USER
Route::add('PUT', '/api/(?P<resource>user)/?', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'update')) {
        echo (new UserController())->update($request->getJSON());
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

// DELETE USER BY ID
Route::add('DELETE', '/api/(?P<resource>user)/(?P<id>[0-9]+)', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'delete', $request->params['id'])) {
        echo (new UserController())->delete($request->params['id']);
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

/*
 *
 * BOOK ROUTES
 *
 */

// LIST ALL BOOKS AND LIST A BOOK BY ID
Route::add('GET', '/api/(?P<resource>book)(/(?P<id>[0-9]+))?/?', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'view',$request->params['id']??null)) {
        echo (!isset($request->params['id']))?
            (new BookController())->findAll():
            (new BookController())->findById($request->params['id']);
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

// CREATE A NEW BOOK
Route::add('POST', '/api/(?P<resource>book)/?', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'create')) {
        echo (new BookController())->create($request->getJSON());
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

// UPDATE A BOOK
Route::add('PUT', '/api/(?P<resource>book)/?', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'update')) {
        echo (new BookController())->update($request->getJSON());
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

// DELETE A BOOK BY ID
Route::add('DELETE', '/api/(?P<resource>book)/(?P<id>[0-9]+)', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'delete', $request->params['id'])) {
        echo (new BookController())->delete($request->params['id']);
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

/*
 *
 * AUTHOR ROUTES
 *
 */

// LIST ALL AUTHORS AND LIST A AUTHOR BY ID
Route::add('GET', '/api/(?P<resource>author)(/(?P<id>[0-9]+))?/?', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'view',$request->params['id']??null)) {
        echo (!isset($request->params['id']))?
            (new AuthorController())->findAll():
            (new AuthorController())->findById($request->params['id']);
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

// CREATE A NEW AUTHOR
Route::add('POST', '/api/(?P<resource>author)/?', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'create')) {
        echo (new AuthorController())->create($request->getJSON());
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

// UPDATE A AUTHOR
Route::add('PUT', '/api/(?P<resource>author)/?', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'update')) {
        echo (new AuthorController())->update($request->getJSON());
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});

// DELETE A AUTHOR BY ID
Route::add('DELETE', '/api/(?P<resource>author)/(?P<id>[0-9]+)', function (Request $request) {
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'delete', $request->params['id'])) {
        echo (new AuthorController())->delete($request->params['id']);
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});


Route::run();

// DELETE USER BY ID OR ALL USERS

/*Route::add('DELETE', '/api/(?P<resource>user)/?', function (Request $request) {
    //check if has a resource_id
    $resource_id = $request->getJSON()->id ?? null;
    if (PermissionHandler::hasPermissionOn($request->params['resource'], 'delete', $resource_id)) {
        echo (new UserController())->delete($request->getJSON());
    } else {
        echo Response::sendWithCode(403, Constants::MSG['PERMISSION_DENY']);
    }
});*/
// LIST A BOOK BY TITLE
/*Route::add('GET', '/api/book/([a-zA-Z0-9]+)', function (Request $request) {
    echo (new BookController())->findByTitle($request->params['resource']);
});
*/
