<?php

  Namespace PiDisplay\Controllers;

  use PiDisplay\Controllers\Controller;

  class AccountController extends Controller {
    /**
     * Shows the home page
     * @param  Request  $request  The request
     * @param  Response $response The response
     * @param  Array    $args     Args for the page if any
     * @return Twig               The view
     */
    public static function getLogin($request, $response, $args) {
      return self::render($response, 'login', [
        'host' => gethostname()
      ]);
    }
}
