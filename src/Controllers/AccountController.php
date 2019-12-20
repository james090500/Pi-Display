<?php

  Namespace PiDisplay\Controllers;

  use PiDisplay\Controllers\Controller;
  use PiDisplay\System\Database as DB;

  class AccountController extends Controller {

    /**
     * Shows the home page
     * @param  Request  $request  The request
     * @param  Response $response The response
     * @param  Array    $args     Args for the page if any
     * @return Twig               The view
     */
    public static function getLogin($request, $response, $args) {
      return self::render($response, 'login');
    }

    /**
     * Shows the home page
     * @param  Request  $request  The request
     * @param  Response $response The response
     * @param  Array    $args     Args for the page if any
     * @return Twig               The view
     */
    public static function doLogin($request, $response, $args) {
      $username = strtolower($request->getParsedBody()['username']);
      $password = $request->getParsedBody()['password'];

      //Check login isn't empty
      if(empty($username) || empty($password)) {
        return self::renderAlert($response, 'login', 'danger', 'Username or password incorrect');
      }

      //Check user exist
      $user = DB::getInstance()->get('users', '*', [ 'username' => $username ]);
      if(empty($user)) {
        return self::renderAlert($response, 'login', 'danger', 'Username or password incorrect');
      }

      //Check password
      if(!password_verify($password, $user['password'])) {
        return self::renderAlert($response, 'login', 'danger', 'Username or password incorrect');
      }

      $_SESSION['MEMBER']['username'] = $username;
      return $response->withHeader('Location', '/')->withStatus(302);
    }
}
