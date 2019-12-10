<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/Helpers/Parser.php';

require_once __DIR__ . '/../dao/ConversationDAO.php';
require_once __DIR__ . '/../dao/MessageDAO.php';
require_once __DIR__ . '/../dao/MediaDAO.php';
require_once __DIR__ . '/../dao/EmojiDAO.php';

class UploadController extends Controller {

  function __construct() {
    $this->parser = new Parser();

    $this->conversationDAO = new ConversationDAO();
    $this->messageDAO = new MessageDAO();
    $this->mediaDAO = new MediaDAO();
    $this->emojiDAO = new EmojiDAO();
  }

  public function index() {
    $this->set('title', 'Home');
  }

  public function result(){
    $this->set('title', 'Upload');
    $error = '';
    $success = '';

    // Step 1: upload the file to a newly created folder
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      // check if a file is found
      if(!empty($_FILES['conversation-zip']['name'])){
        $file = $_FILES['conversation-zip'];      
      
        // getting file info
        $file_name = pathinfo($file['name'])['filename'];
        $file_tempfolder = explode('/tmp/',$file['tmp_name'])[1];
        
        // check if it is a real zip file
        if($file['type']=== 'application/zip' && pathinfo($file['name'])['extension'] === 'zip'){
          // creating a directory to upload the file
          if(mkdir('./uploads/' . $file_tempfolder)){
            // moving the file if no errors were found
              $path = './uploads/' . $file_tempfolder . '/' . $file_name . '.zip';
              move_uploaded_file($file['tmp_name'], $path);
              header('Location:index.php?page=upload&status=unzip&title=' . $file_name . '&path=' . urlencode($path));
              //exit();
          }else{
            $error = 'Kon geen nieuwe map maken op de server';
          }
        }else{
          $error = 'Het doorgestuurde bestand is geen zip bestand';
        }        
      }else{
        $error = 'Er werd geen bestand doorgestuurd naar de server';
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
          header('Location:index.php?page=upload&status=search&title=' . $_GET['title'] . '&path=' . urlencode(dirname($path)));
          //exit();
        }else{
          $error = 'Kan bestand niet unzippen';
        }
      }

      // Step 3: check if the unzipped file contains a txt file
      if($_GET['status']=== 'search'){
        $path = urldecode($_GET['path']);
        $files = array_merge(glob($path . '/*.txt'),glob($path . '/*/*.txt'));
        
        if(count($files) === 1){
          header('Location:index.php?page=upload&status=parse&title=' . $_GET['title'] . '&path=' . urlencode($files[0]));
          //exit();
        }else{
          $error = 'Kan geen tekstbestand met de conversatie vinden.';
        }
      }

      // Step 4: parse the txt or json file
      if($_GET['status'] === 'parse'){
        $path = urldecode($_GET['path']);
       
        $type = pathinfo($path)['extension'];
        if($type === 'txt'){
          try{
            $messages = $this->parser->parseFile($path);
            $this->insertNewConversation($_GET['title'], dirname($path), $messages);
          } catch(Exception $e){
            $error = $e->getMessage();
          }

          $success = 'Het bestand werd correct naar de server verzonden';

          $this->set('parsed_lines', $messages['parsed_lines']);
          $this->set('error_lines',$messages['error_lines']);
        }

        // TODO: eventueel de zip file nog dumpen?
      }
    }
    
    $this->set('success', $success);
    $this->set('error',$error);
    
  }

  private function insertNewConversation($title, $path, $messages){
    // inserting a new conversation line
    $insertedConversation = $this->conversationDAO->insertConversation(array('title' => $title, 'path' => $path));
    if($insertedConversation){
      // loop over the messages
      foreach($messages['parsed_lines'] as $message){
        $insertedMessage = $this->messageDAO->insertMessage($insertedConversation['id'],$message);
        if($insertedMessage){      
          // insert media if present
          if(count($message['media']) > 0){
            $insertedMedia = $this->mediaDAO->insertMedia($insertedConversation['id'], $insertedMessage['id'], $message['media']);
          }
          // insert emoji if present   
          if(count($message['emojis']) > 0){
            foreach($message['emojis'] as $emoji){
              $insertedEmoji = $this->emojiDAO->insertEmoji($insertedConversation['id'], $insertedMessage['id'], $emoji);
            }
          }
        }
      }  
    }
  }
}
