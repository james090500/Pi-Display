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
      $directory = realpath(__DIR__ . '/../Public/pres/');
      $uploadedFile = $request->getUploadedFiles();
      if(isset($uploadedFile['presFile'])) {
        $tmpDir = $directory . '/tmp';
        $presFile = $directory . '/live.odp';
        $uploadedFile['presFile']->moveTo($presFile);

        $unzipArchive = new ZipArchive();
        $unzipArchive->open($presFile);
        $unzipArchive->extractTo($tmpDir);
        $unzipArchive->close();

        $xml = simplexml_load_file($tmpDir . '/content.xml', null, null, 'office', true);
        //$xml->registerXPathNamespace('presentation', 'urn:oasis:names:tc:opendocument:xmlns:presentation:1.0');

        $presSettings = $xml->body->presentation->children('urn:oasis:names:tc:opendocument:xmlns:presentation:1.0');
        $presSettings->addAttribute('presentation:presentation:endless', 'true');
        $presSettings->addAttribute('presentation:presentation:mouse-visible', 'false');
        $presSettings->addAttribute('presentation:presentation:pause', 'PT00H00M00S');
        $presSettings->addAttribute('presentation:presentation:full-screen', 'true');
        $xml->asXml($tmpDir . '/content.xml');

        $zip = new ZipArchive();
        $zip->open($presFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmpDir), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file) {
          // Skip directories (they would be added automatically)
          if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($tmpDir) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
          }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        shell_exec(" ( sleep 5 ; sudo reboot ) > /dev/null 2>/dev/null &");
        return $response->withHeader('Location', '/success')->withStatus(302);
      } else {
        return self::renderAlert($response, 'home', 'danger', 'Please select a file');
      }
    }
}
