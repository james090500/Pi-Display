<?php
  Namespace PiDisplay\Routes;

  use \PiDisplay\Controllers\HomeController;
  use \PiDisplay\Controllers\AccountController;

  class WebRoutes {

    public static function start($app) {
      $app->get('/login', [ AccountController::class, 'getLogin' ]);
      $app->post('/login', [ AccountController::class, 'doLogin' ]);

      //General Pages
      $app->group('/', function($group) {

        $group->get('', [ HomeController::class, 'getHome' ]);
        $group->post('', [ HomeController::class, 'doHome' ]);

        $group->get('reboot', [ HomeController::class, 'doReboot' ]);

      })->add(function($request, $handler) use ($app) {
        $response = $handler->handle($request);
        return (isset($_SESSION['MEMBER'])) ? $response : $response->withHeader('Location', '/login')->withStatus(302);
      });

      $app->get('/success', [ HomeController::class, 'getSuccess' ]);
    }
  }
