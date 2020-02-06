<?php

  Namespace PiDisplay\Controllers;

  use PiDisplay\Controllers\Controller;
  use ZipArchive;
  use RecursiveDirectoryIterator;
  use RecursiveIteratorIterator;

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
      //Check if file uploaded
      $uploadedFile = $request->getUploadedFiles();
      if(isset($uploadedFile['presFile'])) {
        //Sets directory variables
        $directory = realpath(__DIR__ . '/../Public/pres/');
        $tmpDir = $directory . '/tmp';
        $presFile = $directory . '/live.mp4';

        //Moves uploaded file
        $uploadedFile['presFile']->moveTo($presFile);

        //Reboot the system
        if(!DEVELOPMENT_MODE) {
          shell_exec("( sleep 5 ; sudo reboot ) > /dev/null 2>/dev/null &");
        }
        return $response->withHeader('Location', '/success')->withStatus(302);
      } else {
        return self::renderAlert($response, 'home', 'danger', 'Please select a file');
      }
    }

    /**
     * Forces a reboot of the system
     * @param  Request  $request  The request
     * @param  Response $response The response
     * @param  Array    $args     Args for the page if any
     * @return Twig               The view
     */
    public static function doReboot($request, $response, $args) {
      shell_exec("( sleep 5 ; sudo reboot ) > /dev/null 2>/dev/null &");
      return $response->withHeader('Location', '/success')->withStatus(302);
    }

    /**
     * Recursively deletes a folder and files
     * @param  String $dir Folder location
     * @return Boolean     Whether folder was deleted
     */
    private function rrmdir($dir) {
      if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
          if ($object != "." && $object != "..") {
            if (is_dir($dir."/".$object) && !is_link($dir."/".$object))
              self::rrmdir($dir."/".$object);
            else
              unlink($dir."/".$object);
          }
        }
        rmdir($dir);
      }
    }
}
