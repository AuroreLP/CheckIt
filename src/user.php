<?php

function verifyUserLoginPassword(PDO $pdo, string $username, string $password):bool|array
{
  $query = $pdo->prepare("SELECT * FROM user WHERE username = :username");
  $query->bindValue(':username', $username, PDO::PARAM_STR);
  $query->execute();
  // fecth nous permet de récupérer une seule ligne
  $user = $query->fetch(PDO::FETCH_ASSOC);

 if ($user && password_verify($password, $user['password'])) {
  // verify ok
  return $user;

 } else {
  // username ou password incorrect
  return false;
 }
}