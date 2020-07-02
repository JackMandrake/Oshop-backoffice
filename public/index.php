<?php

// POINT D'ENTRÉE UNIQUE : 
// FrontController

// inclusion des dépendances via Composer
// autoload.php permet de charger d'un coup toutes les dépendances installées avec composer
// mais aussi d'activer le chargement automatique des classes (convention PSR-4)
require_once '../vendor/autoload.php';

session_start();

/* ------------
--- ROUTAGE ---
-------------*/


// création de l'objet router
// Cet objet va gérer les routes pour nous, et surtout il va 
$router = new AltoRouter();

// le répertoire (après le nom de domaine) dans lequel on travaille est celui-ci
// Mais on pourrait travailler sans sous-répertoire
// Si il y a un sous-répertoire

// dd($_SERVER);
if (array_key_exists('BASE_URI', $_SERVER)) {
    /**
     * la fonction PHP arrray_key_exists permet de s'assurer de l'existence
     * d'une clé nommée 'BASE_URI' dans la variable $_SERVER (Tableau associatif)
     * 
     * Si c'est le cas, alors on peut appeler la méthode setBasePath de AltoRouter
     * Cette méthode permet de dire à AltoRouter quelle est la partie "statique" du
     * site internet
     * 
     * Voir la docu de array_key_exists : https://www.php.net/manual/fr/function.array-key-exists.php
     */
    $router->setBasePath($_SERVER['BASE_URI']);
    // Alors on définit le basePath d'AltoRouter
    // ainsi, nos routes correspondront à l'URL, après la suite de sous-répertoire
}
// sinon
else {
    // On donne une valeur par défaut à $_SERVER['BASE_URI'] car c'est utilisé dans le CoreController
    $_SERVER['BASE_URI'] = '/';
}

// On doit déclarer toutes les "routes" à AltoRouter, afin qu'il puisse nous donner LA "route" correspondante à l'URL courante
// On appelle cela "mapper" les routes
// 1. méthode HTTP : GET ou POST (pour résumer)
// 2. La route : la portion d'URL après le basePath
// 3. Target/Cible : informations contenant
//      - le nom de la méthode à utiliser pour répondre à cette route
//      - le nom du controller contenant la méthode
// 4. Le nom de la route : pour identifier la route, on va suivre une convention
//      - "NomDuController-NomDeLaMéthode"
//      - ainsi pour la route /, méthode "home" du MainController => "main-home"



/**=====================
 * Routes générales
 *=======================*/
$router->map(
    'GET',
    '/',
    [
        'method' => 'home',
        'controller' => '\App\Controllers\MainController'
    ],
    'main-home'
);



/**=====================
 * Routes Manage
 *=======================*/
$router->map(
    'GET',
    '/manage/category',
    [
        'method' => 'category',
        'controller' => '\App\Controllers\ManageController'
    ],
    'manage-category'
);

$router->map(
    'POST',
    '/manage/category',
    [
        'method' => 'categoryHome',
        'controller' => '\App\Controllers\ManageController'
    ],
    'manage-category-post'
);



/**=====================
 * Routes Category
 *=======================*/
$router->map(
    'GET',
    '/category/list', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'list',
        'controller' => '\App\Controllers\CategoryController'
    ],
    'category-list' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'GET',
    '/category/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'add',
        'controller' => '\App\Controllers\CategoryController'
    ],
    'category-add' // C'est le nom (unique) donné à notre Route
);

// Pas nécessaire de préciser le dernier argument (le nom de la route : category-add)
// quand on fait du POST
$router->map(
    'POST',
    '/category/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'create',
        'controller' => '\App\Controllers\CategoryController'
    ],
    'category-add-post' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'GET',
    '/category/update/[i:category_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'update',
        'controller' => '\App\Controllers\CategoryController'
    ],
    'category-update' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'POST',
    '/category/update/[i:category_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'edit',
        'controller' => '\App\Controllers\CategoryController'
    ],
    'category-update-post' // C'est le nom (unique) donné à notre Route
);


$router->map(
    'GET',
    '/category/delete/[i:category_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'delete',
        'controller' => '\App\Controllers\CategoryController'
    ],
    'category-delete' // C'est le nom (unique) donné à notre Route
);


/**=====================
 * Routes Product
 *=======================*/
$router->map(
    'GET',
    '/product/list', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'list',
        'controller' => '\App\Controllers\ProductController'
    ],
    'product-list' // C'est le nom (unique) donné à notre Route
);


$router->map(
    'GET',
    '/product/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'add',
        'controller' => '\App\Controllers\ProductController'
    ],
    'product-add' // C'est le nom (unique) donné à notre Route
);

// Pas nécessaire de préciser le dernier argument (le nom de la route : product-add)
// quand on fait du POST
$router->map(
    'POST',
    '/product/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'createOrUpdate',
        'controller' => '\App\Controllers\ProductController'
    ]
);

$router->map(
    'POST',
    '/product/update/[i:product_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'createOrUpdate',
        'controller' => '\App\Controllers\ProductController'
    ]
);

$router->map(
    'GET',
    '/product/update/[i:product_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'update',
        'controller' => '\App\Controllers\ProductController'
    ],
    'product-update' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'GET',
    '/product/delete/[i:product_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'delete',
        'controller' => '\App\Controllers\ProductController'
    ],
    'product-delete' // C'est le nom (unique) donné à notre Route
);


/**=====================
 * Routes Type
 *=======================*/
$router->map(
    'GET',
    '/type/list', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'list',
        'controller' => '\App\Controllers\TypeController'
    ],
    'type-list' // C'est le nom (unique) donné à notre Route
);


$router->map(
    'GET',
    '/type/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'add',
        'controller' => '\App\Controllers\TypeController'
    ],
    'type-add' // C'est le nom (unique) donné à notre Route
);

// Pas nécessaire de préciser le dernier argument (le nom de la route : product-add)
// quand on fait du POST
$router->map(
    'POST',
    '/type/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'createOrUpdate',
        'controller' => '\App\Controllers\TypeController'
    ],
    'type-add-update' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'POST',
    '/type/update/[i:type_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'createOrUpdate',
        'controller' => '\App\Controllers\TypeController'
    ]
);

$router->map(
    'GET',
    '/type/update/[i:type_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'update',
        'controller' => '\App\Controllers\TypeController'
    ],
    'type-update' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'GET',
    '/type/delete/[i:type_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'delete',
        'controller' => '\App\Controllers\TypeController'
    ],
    'type-delete' // C'est le nom (unique) donné à notre Route
);


/**=====================
 * Routes Brand
 *=======================*/

$router->map(
    'GET',
    '/brand/list', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'list',
        'controller' => '\App\Controllers\BrandController'
    ],
    'brand-list' // C'est le nom (unique) donné à notre Route
);


$router->map(
    'GET',
    '/brand/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'add',
        'controller' => '\App\Controllers\BrandController'
    ],
    'brand-add' // C'est le nom (unique) donné à notre Route
);

// Pas nécessaire de préciser le dernier argument (le nom de la route : product-add)
// quand on fait du POST
$router->map(
    'POST',
    '/brand/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'createOrUpdate',
        'controller' => '\App\Controllers\BrandController'
    ]
);

$router->map(
    'POST',
    '/brand/update/[i:brand_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'createOrUpdate',
        'controller' => '\App\Controllers\BrandController'
    ]
);

$router->map(
    'GET',
    '/brand/update/[i:brand_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'update',
        'controller' => '\App\Controllers\BrandController'
    ],
    'brand-update' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'GET',
    '/brand/delete/[i:brand_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'delete',
        'controller' => '\App\Controllers\BrandController'
    ],
    'brand-delete' // C'est le nom (unique) donné à notre Route
);


/**=====================
 * Routes user
 *=======================*/
$router->map(
    'GET',
    '/user/login', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'login',
        'controller' => '\App\Controllers\UserController'
    ],
    'user-login' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'POST',
    '/user/login', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'validate',
        'controller' => '\App\Controllers\UserController'
    ]
    // 'user-login' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'GET',
    '/user/logout', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'logout',
        'controller' => '\App\Controllers\UserController'
    ],
    'user-logout' // C'est le nom (unique) donné à notre Route
);


$router->map(
    'GET',
    '/user/list', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'list',
        'controller' => '\App\Controllers\UserController'
    ],
    'user-list' // C'est le nom (unique) donné à notre Route
);


$router->map(
    'GET',
    '/user/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'add',
        'controller' => '\App\Controllers\UserController'
    ],
    'user-add' // C'est le nom (unique) donné à notre Route
);

// Pas nécessaire de préciser le dernier argument (le nom de la route : product-add)
// quand on fait du POST
$router->map(
    'POST',
    '/user/add', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'createOrUpdate',
        'controller' => '\App\Controllers\UserController'
    ],
    'user-add-post' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'POST',
    '/user/update/[i:user_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'createOrUpdate',
        'controller' => '\App\Controllers\UserController'
    ],
    'user-update-post' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'GET',
    '/user/update/[i:user_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'update',
        'controller' => '\App\Controllers\UserController'
    ],
    'user-update' // C'est le nom (unique) donné à notre Route
);

$router->map(
    'GET',
    '/user/delete/[i:user_id]', // C'est l'URL qui permet le matching de la route
    [
        'method' => 'delete',
        'controller' => '\App\Controllers\UserController'
    ],
    'user-delete' // C'est le nom (unique) donné à notre Route
);

/**
 * Le nom d'une route permettra la génération dynamique de nos URL
 * grâce à la méthode generate de la class AltoRouter
 * Ex : $hrefAbsolu = $router->generate('main-home');
 */


/* -------------
--- DISPATCH ---
--------------*/
/**
 * Le dispatching permet d'associer une route à une action particulière
 */

// On demande à AltoRouter de trouver une route qui correspond à l'URL courante
$match = $router->match();

// Ensuite, pour dispatcher le code dans la bonne méthode, du bon Controller
// On délègue à une librairie externe : https://packagist.org/packages/benoclock/alto-dispatcher
// 1er argument : la variable $match retournée par AltoRouter
// 2e argument : le "target" (controller & méthode) pour afficher la page 404
$dispatcher = new Dispatcher($match, '\App\Controllers\ErrorController::err404');
// Une fois le "dispatcher" configuré, on lance le dispatch qui va exécuter la méthode du controller
$dispatcher->dispatch();