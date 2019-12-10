<?php

require_once( __DIR__ . '/DAO.php');

class MediaDAO extends DAO {

  public function selectMediaById($id){
    $sql = 'SELECT * FROM `media` WHERE `id` = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id',$id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function insertMedia($conversation_id, $message_id, $media){
    $errors = $this->validate($media);
    if(empty($errors)){
      $sql = "INSERT INTO `media` (`conversation_id`,`message_id`, `type`,`filename`) VALUES(:conversation_id,:message_id,:type,:filename)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':conversation_id',$conversation_id);
      $stmt->bindValue(':message_id',$message_id);
      $stmt->bindValue(':type',$media['type']);
      $stmt->bindValue(':filename',$media['path']);
      if($stmt->execute()){
        return $this->selectMediaById($this->pdo->lastInsertId());
      }
    }
    return false;
  }

  public function validate($data){
    $errors = [];
    if(empty($data['type'])) {
      $errors['type'] = 'Geen type gevonden voor de media';
    }
    if(empty($data['path'])) {
      $errors['path'] = 'Geen bestandsnaam gevonden voor de media';
    }
    return $errors;
  }
}
