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
        $presFile = $directory . '/live.odp';

        //Moves uploaded file
        $uploadedFile['presFile']->moveTo($presFile);

        //Extracts the ODP to a tmp folder
        $unzipArchive = new ZipArchive();
        $unzipArchive->open($presFile);
        $unzipArchive->extractTo($tmpDir);
        $unzipArchive->close();

        //Adds the xml values to force full screen
        $xml = simplexml_load_file($tmpDir . '/content.xml', null, null, 'office', true);
        $presSettings = $xml->body->presentation->children('urn:oasis:names:tc:opendocument:xmlns:presentation:1.0');
        $presSettings->addAttribute('presentation:presentation:endless', 'true');
        $presSettings->addAttribute('presentation:presentation:mouse-visible', 'false');
        $presSettings->addAttribute('presentation:presentation:pause', 'PT00H00M00S');
        $presSettings->addAttribute('presentation:presentation:full-screen', 'true');
        $xml->asXml($tmpDir . '/content.xml');

        //Rezips the document back
        $zip = new ZipArchive();
        $zip->open($presFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmpDir), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file) {
          if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($tmpDir) + 1);
            $zip->addFile($filePath, $relativePath);
          }
        }
        $zip->close();

        //Remove all files and folders
        self::rrmdir($tmpDir);

        //Reboot the system
        if(!getenv('DEV_MODE')) {
          shell_exec(" ( sleep 5 ; sudo reboot ) > /dev/null 2>/dev/null &");
        }
        return $response->withHeader('Location', '/success')->withStatus(302);
      } else {
        return self::renderAlert($response, 'home', 'danger', 'Please select a file');
      }
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
