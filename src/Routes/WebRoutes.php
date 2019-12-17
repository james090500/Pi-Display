<?php
  Namespace PiDisplay\Routes;

  use \PiDisplay\Controllers\HomeController;

  class WebRoutes {

    public static function start($app) {
      //General Pages
      $app->get('/', [ HomeController::class, 'getHome' ]);
    }
  }
