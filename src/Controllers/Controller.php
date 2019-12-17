<?php

  Namespace PiDisplay\Controllers;

  abstract class Controller {

    private static $app;

    /**
     * Create instance of the controller so it doesn't get remade every time it's extended
     * @param  App $app The Slim App
     * @return Void
     */
    public static function createInstance($app) {
      self::$app = $app;
    }

    /**
     * Gets the view from the Slim Container
     * @return Twig The Twig Template Engine
     */
    public static function getView() {
      return self::$app->getContainer()->get('view');
    }

    /**
     * Makes the render function a little less repative
     * @param  Response $response The Response
     * @param  String $page       The .twig in /Pages/
     * @param  array  $args       Optional args
     * @return Twig               The rendered response
     */
    public static function render($response, $page, $args = []) {
      return self::getView()->render($response, "Pages/$page.twig", $args);
    }

    /**
     * Makes the render function a little less repative
     * @param  Response $response The Response
     * @param  String $page       The .twig in /Pages/
     * @param  array  $args       Optional args
     * @return Twig               The rendered response
     */
    public static function render($response, $page, $args = []) {
      return self::getView()->render($response, "Pages/$page.twig", $args);
    }    

    /**
     * Shows the error page
     * @param  Response $response The Response Object
     * @param  String $title    The title of the error page
     * @param  String $content  The description of the error page
     * @return Twig The Error page
     */
    public static function doError($response, $title, $content) {
      return self::getView()->render($response, 'Pages/admin/error.twig', [
        'error' => [
          'title' => $title,
          'msg' => $content
        ]
      ]);
    }
  }
