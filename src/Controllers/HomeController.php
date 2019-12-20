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
     * Upload the PPTX from the home page
     * @param  Request  $request  The request
     * @param  Response $response The response
     * @param  Array    $args     Args for the page if any
     * @return Twig               The view
     */
    public static function doHome($request, $response, $args) {
      $directory = __DIR__ . '/../Public/pptx/';
      $uploadedFile = $request->getUploadedFiles();
      if(isset($uploadedFile['pptxFile'])) {
        $uploadedFile['pptxFile']->moveTo($directory . DIRECTORY_SEPARATOR  . 'live.pptx');
        #exec("pkill soffice.bin");
        exec("nohup soffice --norestore --view --nologo --display :0 --show /var/www/Public/pptx/live.pptx")        
        return self::renderModal($response, 'home', 'success', 'Rebooting...');
      } else {
        return self::renderAlert($response, 'home', 'danger', 'Please select a file');
      }
    }
}
