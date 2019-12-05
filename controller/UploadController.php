<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../dao/ConversationDAO.php';

class UploadController extends Controller {

  function __construct() {
    $this->conversationDAO = new ConversationDAO();
  }

  public function index() {
    $this->set('title', 'Home');
  }

  public function upload(){
    $this->set('title', 'Uploading...');

    // init a session for status updates
    if (!isset($_SESSION['upload-success'])) {
      $_SESSION['upload-success'] = [];
    }

    // Step 1: upload the file to a newly created folder
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      // check if a file is found
      if(isset($_FILES['conversation-zip'])){
        $file = $_FILES['conversation-zip'];
      
        // getting file info
        $file_name = explode('.zip', $file['name'])[0];
        $file_tempfolder = explode('tmp/',$file['tmp_name'])[1];
        
        // check if it is a real zip file
        if($file['type']=== 'application/zip' && strlen($file_name) < strlen($file['name'])){
          // creating a directory to upload the file
          if(mkdir('./uploads/' . $file_tempfolder)){
            // moving the file if no errors were found
              $path = './uploads/' . $file_tempfolder . '/' . $file_name . '.zip';
              move_uploaded_file($file['tmp_name'], $path);
              $_SESSION['upload-success'][] = 'Bestand werd succesvol geupload';
              $_SESSION['upload-next'] = 'Starten met unzippen...';
              header('Location:index.php?page=upload&status=unzip&title=' . $file_name . '&path=' . urlencode($path));
              //exit();
          }else{
            $_SESSION['error'] = 'Kon geen nieuwe map maken op de server';
          }
        }else{
          $_SESSION['error'] = 'Het doorgestuurde bestand is geen zip bestand';
        }        
      }else{
        $_SESSION['upload-error'] = 'Er werd geen bestand doorgestuurd naar de server';
      }
    }
    
    if(!empty($_GET['status']) && !empty($_GET['path'])){
      // Step 2: unzip the file in the newly created folder
      if($_GET['status'] === 'unzip'){
        $zip = new ZipArchive();
        $path = urldecode($_GET['path']);
          if($zip->open($path)){      
            $zip->extractTo(dirname($path));
            $zip->close();
            $_SESSION['upload-success'][] = 'Bestand is succesvol unzipt';
            $_SESSION['upload-next'] = 'Bestand met conversatie zoeken...';
            header('Location:index.php?page=upload&status=search&title=' . $_GET['title'] . '&path=' . urlencode(dirname($path)));
            //exit();
          }else{
            $_SESSION['error'] = 'Kan bestand niet unzippen';
          }
      }

      // Step 3: check if the unzipped file contains a txt or json file
      if($_GET['status']=== 'search'){
        $path = urldecode($_GET['path']);
        $files = array_merge(glob($path . '/*.txt'),glob($path . '/*/*.txt'),glob($path . '/*.json'),glob($path . '/*/*.json'));
        
        if(count($files) === 1){
          $_SESSION['upload-success'][] = 'Bestand met conversatie is succesvol gevonden';
          $_SESSION['upload-next'] = 'Parsen van de conversatie';
          header('Location:index.php?page=upload&status=parse&title=' . $_GET['title'] . '&path=' . urlencode($files[0]));
          //exit();
        }else{
          $_SESSION['error'] = 'Kan geen bestand met conversatie vinden.';
        }
      }

      // Step 4: parse the txt or json file
      if($_GET['status'] === 'parse'){
        $path = urldecode($_GET['path']);
        echo $path;
        
        $type = pathinfo($path)['extension'];
        if($type === 'txt'){
          $this->convertTxtFileToArray($path);
        }
        if($type === 'json'){
          $file = file_get_contents($path);
          $data = json_decode($file,true);
          
        }

        var_dump($data);
        $insertedConversation = $this->conversationDAO->insertConversation(array('title' => $_GET['title'], 'path' => dirname($path)));
        if(!$insertedConversation){
           $_SESSION['error'] = 'Niet gelukt';
        }else{
          var_dump($insertedConversation);
        }

        // TODO: eventueel de zip file nog dumpen?
      }
    }
    
    // in case of error: redirect to fail state
    //header('Location:index.php?page=upload&status=fail');
    //exit();
  }

  private function convertTxtFileToArray($path){
    $file = fopen($path, 'r');
    $lines = [];
    while(!feof($file)){
      $line = fgets($file);
      $lines[] = $line;
    }
    fclose($file);
    //var_dump($lines);

    // export met Android: koppelteken (- ) tussen datum en boodschap
    // export met iOS: vierkante haakjes [] met daartussen datum en boodschapo
    // Daarna telkens gebruiker en een dubbel punt
    // TODO: export functies schrijven -> resultaat moet een assoc array zijn
  }
}
