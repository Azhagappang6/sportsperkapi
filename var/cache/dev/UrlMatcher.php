<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/oauth/v2/token' => [[['_route' => 'fos_oauth_server_token', '_controller' => 'fos_oauth_server.controller.token:tokenAction'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/oauth/v2/auth' => [[['_route' => 'fos_oauth_server_authorize', '_controller' => 'fos_oauth_server.controller.authorize:authorizeAction'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/([^/]++)/([^/]++)(?:/([^/]++))?(*:78)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_twig_error_test', '_controller' => 'twig.controller.preview_error::previewErrorPageAction', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        78 => [
            [['_route' => 'app_main_executepost', 'typeOrId' => null, '_controller' => 'App\\Controller\\MainController::ExecutePost'], ['controller', 'action', 'typeOrId'], ['POST' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
