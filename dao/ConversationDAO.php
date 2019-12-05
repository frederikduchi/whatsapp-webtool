<?php

require_once( __DIR__ . '/DAO.php');

class ConversationDAO extends DAO {

  public function selectConversationById($id){
    $sql = 'SELECT * FROM `conversations` WHERE `id` = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id',$id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function insertConversation($data){
    $errors = $this->validate($data);
    if(empty($errors)){
      $sql = "INSERT INTO `conversations` (`title`,`path`) VALUES(:title,:path)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':title',$data['title']);
      $stmt->bindValue(':path',$data['path']);
      if($stmt->execute()){
        return $this->selectConversationById($this->pdo->lastInsertId());
      }
    }
    return false;
  }

  public function validate($data){
    $errors = [];
    if (empty($data['title'])) {
      $errors['title'] = 'Geen titel gevonden voor de conversatie';
    }
    if (empty($data['path'])) {
      $errors['path'] = 'Geen path gevonden voor de conversatie';
    }
    return $errors;
  }

}
