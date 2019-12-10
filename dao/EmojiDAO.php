<?php

require_once( __DIR__ . '/DAO.php');

class EmojiDAO extends DAO {

  public function selectEmojiById($id){
    $sql = 'SELECT * FROM `emojis` WHERE `id` = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id',$id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function insertEmoji($conversation_id, $message_id, $emoji){
    $errors = $this->validate($emoji);
    if(empty($errors)){
      $sql = "INSERT INTO `emojis` (`conversation_id`,`message_id`, `symbol`) VALUES(:conversation_id,:message_id,:symbol)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':conversation_id',$conversation_id);
      $stmt->bindValue(':message_id',$message_id);
      $stmt->bindValue(':symbol',$emoji);
      if($stmt->execute()){
        return $this->selectEmojiById($this->pdo->lastInsertId());
      }
    }
    return false;
  }

  public function validate($emoji){
    $errors = [];
    if(empty($emoji)) {
      $errors['emoji'] = 'Geen emoji gevonden voor dit bericht';
    }
    return $errors;
  }
}
