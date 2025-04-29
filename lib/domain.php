<?php

function getAllDomains(PDO $pdo):array
{
  $query = $pdo->prepare("SELECT * FROM domain");
  $query->execute();

  // fecth nous permet de récupérer une seule ligne
  return $query->fetchAll(PDO::FETCH_ASSOC);
}
