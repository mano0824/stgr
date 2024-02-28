<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {

    // Sgsp
    $routes->connect('/sgsp/payCheck', ['controller' => 'Sgsp', 'action' => 'payCheck']);
    $routes->connect('/sgsp/payCheckDetail', ['controller' => 'Sgsp', 'action' => 'payCheckDetail']);
    $routes->connect('/sgsp/pay', ['controller' => 'Sgsp', 'action' => 'pay']);
    $routes->connect('/sgsp/payReset', ['controller' => 'Sgsp', 'action' => 'payReset']);
    $routes->connect('/sgsp/payReceipt', ['controller' => 'Sgsp', 'action' => 'payReceipt']);
    $routes->connect('/sgsp/payPurchase', ['controller' => 'Sgsp', 'action' => 'payPurchase']);
    $routes->connect('/sgsp/payPurchaseDecision', ['controller' => 'Sgsp', 'action' => 'payPurchaseDecision']);
    $routes->connect('/sgsp/pointPayCheck', ['controller' => 'Sgsp', 'action' => 'pointPayCheck']);

    // Api
    $routes->connect('/api/test', ['controller' => 'Api', 'action' => 'test']);
    $routes->connect('/api/getOperationStatus', ['controller' => 'Api', 'action' => 'getOperationStatus']);

    // SgSanwaAPI
    $routes->connect('/sgsanwaapi/payCheck', ['controller' => 'SgSanwaAPI', 'action' => 'payCheck']);
    $routes->connect('/sgsanwaapi/pay', ['controller' => 'SgSanwaAPI', 'action' => 'pay']);
    $routes->connect('/sgsanwaapi/payReset', ['controller' => 'SgSanwaAPI', 'action' => 'payReset']);
    $routes->connect('/sgsanwaapi/payReceipt', ['controller' => 'SgSanwaAPI', 'action' => 'payReceipt']);
    $routes->connect('/sgsanwaapi/pointPayCheck', ['controller' => 'SgSanwaAPI', 'action' => 'pointPayCheck']);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks('DashedRoute');
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
