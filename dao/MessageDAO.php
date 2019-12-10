<?php

require_once( __DIR__ . '/DAO.php');

class MessageDAO extends DAO {

  public function selectMessageById($id){
    $sql = 'SELECT * FROM `messages` WHERE `id` = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id',$id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function insertMessage($conversation_id, $message){
    $errors = $this->validate($message);
    if(empty($errors)){
      $sql = "INSERT INTO `messages` (`conversation_id`,`author`, `date`,`text`) VALUES(:conversation_id,:author,:date,:text)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':conversation_id',$conversation_id);
      $stmt->bindValue(':author',$message['author']);
      $stmt->bindValue(':date', $message['formatdate']);
      $stmt->bindValue(':text',$message['text']);
      if($stmt->execute()){
        return $this->selectMessageById($this->pdo->lastInsertId());
      }
    }
    return false;
  }

  public function validate($data){
    $errors = [];
    if(!isset($data['author'])) {
      $errors['author'] = 'Geen auteur gevonden voor dit bericht';
    }
    if(empty($data['date'])) {
      $errors['date'] = 'Geen datum gevonden voor dit bericht';
    }
    if(!isset($data['text'])) {
      $errors['text'] = 'Geen tekst gevonden voor dit bericht';
    }
    
    return $errors;
  }

}
