<?php

  Namespace PiDisplay\Controllers;

  use PiDisplay\Controllers\Controller;

  class HomeController extends Controller {

    /**
     * Shows the home page
     * @param  Request  $request  The request
     * @param  Response $response The response
     * @param  Array    $args     Args for the page if any
     * @return Twig               The view
     */
    public static function getHome($request, $response, $args) {
      return self::render($response, 'home');
    }

    /**
     * Shows the success page
     * @param  Request  $request  The request
     * @param  Response $response The response
     * @param  Array    $args     Args for the page if any
     * @return Twig               The view
     */
    public static function getSuccess($request, $response, $args) {
      return self::render($response, 'success');
    }

    /**
     * Upload the PPTX from the home page
     * @param  Request  $request  The request
     * @param  Response $response The response
     * @param  Array    $args     Args for the page if any
     * @return Twig               The view
     */
    public static function doHome($request, $response, $args) {
      $directory = __DIR__ . '/../Public/pres/';
      $uploadedFile = $request->getUploadedFiles();
      if(isset($uploadedFile['presFile'])) {
        $uploadedFile['presFile']->moveTo($directory . DIRECTORY_SEPARATOR  . 'live.odp');
        shell_exec(" ( sleep 5 ; sudo reboot ) > /dev/null 2>/dev/null &");
        return $response->withHeader('Location', '/success')->withStatus(302);
      } else {
        return self::renderAlert($response, 'home', 'danger', 'Please select a file');
      }
    }
}
