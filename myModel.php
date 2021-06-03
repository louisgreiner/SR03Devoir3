<?php
require_once('include.php');
require_once('config/config.php');

function getMySqliConnection() {
  return new mysqli(DB_HOST, DB_USER, DB_PASSWD,DB_NAME);
}

function findUserByLoginPwd($login, $pwd, $ip) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
      $utilisateur = false;
  } else {
      $stmt = $mysqli->prepare("SELECT nom,prenom,mot_de_passe,login,id_user,numero_compte,profil_user,solde_compte FROM users WHERE login=?");
      $stmt->bind_param("s", $login);
      $login = htmlspecialchars($login, ENT_QUOTES);//Changer les caractères interdits au ENT_QUOTES
      // $pwd = htmlspecialchars($pwd, ENT_QUOTES);
      $stmt->execute();
      $stmt->bind_result($nom,$prenom,$mot_de_passe,$login,$id_user,$numero_compte,$profil_user,$solde_compte); // on prépare les variables qui recevront le résultat

      if (!$result = $stmt->get_result()) {
          echo 'Erreur requête BDD ['.$stmt.'] (' . $mysqli->errno . ') '. $mysqli->error;
          $utilisateur = false;
      } else {
          while ($unUser = $result->fetch_assoc()) {
            if (password_verify($pwd, $unUser['mot_de_passe'])) {
              $utilisateur = $unUser;
              $stmt->close();
              $mysqli->close();
              return $utilisateur;
            }
            $result->free();
          }
          // on log l'IP ayant généré l'erreur
          $stmt_insert = $mysqli->prepare("insert into connection_errors(ip,error_date) values(?,CURTIME())");
          $stmt_insert->bind_param("s", $ip); // Eventuellement, gérer le cas où l'utilisateur on est derrière un proxy en utilisant $_SERVER['HTTP_X_FORWARDED_FOR'] 
          $stmt_insert->execute();
      }
      $stmt->close();
      $mysqli->close();
  }

  return false;
}


function findUserByNumCompte($num) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
      $utilisateur = false;
  } else {
      $stmt = $mysqli->prepare("SELECT nom,prenom,login,id_user,numero_compte,profil_user,solde_compte FROM users where numero_compte=?");
      $stmt->bind_param("s", $num);
      $num = htmlspecialchars($num, ENT_QUOTES);
      $stmt->execute();
      $stmt->bind_result($nom,$prenom,$login,$id_user,$numero_compte,$profil_user,$solde_compte); // on prépare les variables qui recevront le résultat
      if (!$result = $stmt->get_result()) {
          echo 'Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
          $utilisateur = false;
      } else {
          if ($result->num_rows === 0) {
            $utilisateur = false;
          } else {
            $utilisateur = $result->fetch_assoc();
          }
          $result->free();
      }
      $mysqli->close();
  }

  return $utilisateur;
}


function findUserByID($num) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
      $utilisateur = false;
  } else {
      $stmt = $mysqli->prepare("SELECT nom,prenom,login,id_user,numero_compte,profil_user,solde_compte FROM users where id_user=?");
      $stmt->bind_param("s", $num);
      $num = htmlspecialchars($num, ENT_QUOTES);
      $stmt->execute();
      $stmt->bind_result($nom,$prenom,$login,$id_user,$numero_compte,$profil_user,$solde_compte); 
      if (!$result = $stmt->get_result()) {
        echo 'Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
        $utilisateur = false;
      } else {
        if ($result->num_rows === 0) {
          $utilisateur = false;
        } else {
          $utilisateur = $result->fetch_assoc();
        }
        $result->free();
      }
      $mysqli->close();
  }

  return $utilisateur;
}


function findAllUsers($order = "id_user", $conditions = ["nom" => "%","prenom" => "%","numero_compte" => "%",]) {
  $mysqli = getMySqliConnection();
  $listeUsers = array();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
  } else {
    $stmt = $mysqli->prepare("SELECT nom,prenom,login,id_user,numero_compte,profil_user from users where nom like ? or prenom like ? or numero_compte like ? order by $order");
    $stmt->bind_param("sss", $conditions["nom"], $conditions["prenom"], $conditions["numero_compte"]);
    $stmt->execute();
    $stmt->bind_result($nom,$prenom,$login,$id_user,$numero_compte,$profil_user); // on prépare les variables qui recevront le résultat
    if (!$result = $stmt->get_result()) {
        echo 'Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
    } else {
        while ($unUser = $result->fetch_assoc()) {
            $listeUsers[$unUser['id_user']] = $unUser;
        }
        $result->free();
    }
    $stmt->close();
  }
  $mysqli->close();
  return $listeUsers;
}

// function transfert($dest, $src, $mt) {
//   $mysqli = getMySqliConnection();

//   $dest = htmlspecialchars($dest, ENT_QUOTES);
//   $src = htmlspecialchars($src, ENT_QUOTES);
//   $mt = htmlspecialchars($mt, ENT_QUOTES);

//   if ($mysqli->connect_error) {
//       echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
//       $utilisateur = false;
//   } else {
//     $stmt = $mysqli->prepare("UPDATE users set solde_compte=solde_compte+? where numero_compte=?");
//     $stmt->bind_param("ds", $mt,$dest);
//     $stmt->execute();
//       if (!$result = $stmt->get_result()) {
//           echo 'Transfert 1 : Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
//       }
//       $stmt = $mysqli->prepare("UPDATE users set solde_compte=solde_compte-? where numero_compte=?");
//       $stmt->bind_param("ds", $mt,$src);
//       $stmt->execute();
//       if (!$result = $stmt->get_result()) {
//           echo 'Transfert 2 : Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
//       }
//       $mysqli->close();
//   }

//   return $utilisateur;
// }

function transfert($dest, $src, $mt) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error, E_USER_ERROR);
      $utilisateur = false;
  } else {
      // Pour faire vraiment propre, on devrait tester si le execute et le prepare se passent bien
      $stmt = $mysqli->prepare("update users set solde_compte=solde_compte+? where numero_compte=?");  
      $stmt->bind_param("ds", $mt, $dest); // on lie les paramètres de la requête préparée avec les variables
      $stmt->execute(); 
      $stmt->close();

      $stmt = $mysqli->prepare("update users set solde_compte=solde_compte-? where numero_compte=?");  
      $stmt->bind_param("ds", $mt, $src); // on lie les paramètres de la requête préparée avec les variables
      $stmt->execute();
      $stmt->close();
  
      $stmt = $mysqli->prepare("INSERT into transfer_history(compte_emetteur, compte_destinataire, montant,date_transaction) values(?,?,?,CURTIME())");
      $stmt->bind_param("ssd", $src,$dest, $mt);
      $stmt->execute();

      // Vérification du virement
      if (!$result = $stmt->get_result()) {
          echo 'Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
      } else {
          $virement = $result->fetch_assoc();
          $result->free();
      }
      $stmt->close();
  }
  $mysqli->close();
}

function compteIsBanned($numero_compte, $mt) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error, E_USER_ERROR);
      return false;
  } else {
      $stmt = $mysqli->prepare("SELECT count(*) as nb_tentatives from transfer_history where compte_emetteur=? and date_transaction > CURTIME() - INTERVAL 1 DAY"); 
      $stmt->bind_param("s",  $numero_compte); 
      $stmt->execute();
      $stmt->bind_result($count);
      $stmt->fetch();
      $stmt->close();
      $stmt = $mysqli->prepare("SELECT sum(montant) as total from transfer_history where compte_emetteur=? and date_transaction > CURTIME() - INTERVAL 1 DAY;"); 
      $stmt->bind_param("s",  $numero_compte); 
      $stmt->execute();
      $stmt->bind_result($total);
      $stmt->fetch();
      if($count > 4 || $mt + $total > 10000) {
        return true; // ce compte a déjà fait 5 virements, ou souhaite virer plus de 10,000€  aujourd'hui
      } else {
        return false;
      }
      $stmt->close();
      $mysqli->close();
  } 
}
// function findUserByNumCompte($num) {
//   $mysqli = getMySqliConnection();

//   if ($mysqli->connect_error) {
//       echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
//       $utilisateur = false;
//   } else {
//       $stmt = $mysqli->prepare("SELECT nom,prenom,login,id_user,numero_compte,profil_user,solde_compte FROM users where numero_compte=?");
//       $stmt->bind_param("s", $num);
//       $num = htmlspecialchars($num, ENT_QUOTES);
//       $stmt->execute();
//       $stmt->bind_result($nom,$prenom,$login,$id_user,$numero_compte,$profil_user,$solde_compte); // on prépare les variables qui recevront le résultat
//       if (!$result = $stmt->get_result()) {
//           echo 'Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
//           $utilisateur = false;
//       } else {
//           if ($result->num_rows === 0) {
//             $utilisateur = false;
//           } else {
//             $utilisateur = $result->fetch_assoc();
//           }
//           $result->free();
//       }
//       $mysqli->close();
//   }

//   return $utilisateur;
// }

function getSolde($id) {
  $mysqli = getMySqliConnection();
  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
  } else {
    $stmt = $mysqli->prepare("SELECT solde_compte from users where id_user=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if (!$result = $stmt->get_result()) {
      echo 'Pas cool : Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
      // $solde_compte=123456;
    } else {
      $utilisateur = $result->fetch_assoc();
      $result->free();
    }
    $mysqli->close();
  }

  return $utilisateur["solde_compte"];
}

function findMessagesInbox($userid) {
  $mysqli = getMySqliConnection();

  $userid = htmlspecialchars($userid, ENT_QUOTES);

  $listeMessages = array();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
  } else {
    $stmt = $mysqli->prepare("SELECT id_msg,sujet_msg,corps_msg,u.nom,u.prenom from messages m, users u where m.id_user_from=u.id_user and id_user_to=?");
    $stmt->bind_param("d", $userid);
    $stmt->execute();
    $stmt->bind_result($id_msg,$sujet_msg,$coprs_msg,$nom,$prenom); // on prépare les variables qui recevront le résultat
      if (!$result = $stmt->get_result()) {
          echo 'Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
      } else {
          while ($unMessage = $result->fetch_assoc()) {
            $listeMessages[$unMessage['id_msg']] = $unMessage; 
          }
          $result->free();
      }
      $mysqli->close();
  }

  return $listeMessages;
}


function addMessage($to,$from,$subject,$body) {
  $mysqli = getMySqliConnection();

  $subject = htmlspecialchars($subject, ENT_QUOTES);
  $body = htmlspecialchars($body, ENT_QUOTES);

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
  } else {
    $stmt = $mysqli->prepare("INSERT into messages(id_user_to,id_user_from,sujet_msg,corps_msg) values(?,?,?,?)");
    $stmt->bind_param("ddss", $to,$from,$subject,$body);
    $stmt->execute();
    if (!$result = $stmt->get_result()) {
        echo 'Erreur requête BDD (' . $mysqli->errno . ') '. $mysqli->error;
    }
    $mysqli->close();
  }

}

function ipIsBanned($ip) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error, E_USER_ERROR);
      return false;
  } else {
    // 5 tentatives infructueuses en moins de 5min entraîne un IP ban, au bout de 5min, une tentative infructueuse n'affecte plus l'utilisateur
    // défense contre pwd brute forcing
      $stmt = $mysqli->prepare("SELECT count(*) as nb_tentatives from connection_errors where ip=? and error_date > CURTIME() - INTERVAL 5 MINUTE"); 
      $stmt->bind_param("s",  $ip); 
      $stmt->execute();
      $stmt->bind_result($count);
      $stmt->fetch();
      if($count > 4) {
        return true; // cette IP a atteint le nombre maxi de 5 tentatives infructueuses
      } else {
        return false;
      }
      $mysqli->close();
  } 
}

?>
