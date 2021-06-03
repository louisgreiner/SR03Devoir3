<?php
  require_once('include.php');
  require_once('myModel.php');
  session_start();

    if(!(isset($_SESSION['connected_user'])) || $_SESSION['connected_user']['profil_user'] != "EMPLOYE"){
      // pas de session active = d'utilisateur déjà connecté et user = admin
      header('Location: vw_moncompte.php');
    }

  // Vérification que la session n'a pas expiré
  if(isset($_SESSION["connected_user"]["id_user"]))
  {
      if(time()-$_SESSION["login_time"] > 600) // drop la session au bout de 10min = 600 sec
      {
          session_unset();
          session_destroy();
          header("Location:vw_login.php?timeout");
      }
  }
  else
  {
      header("Location:vw_login.php?notconnected");
  }

?>
<!DOCTYPE html>
<html>
  <meta charset="UTF-8">
    <style>
    ::placeholder {
      color: white;
      opacity: 0.8;
    }
    </style>
    <head>
      <!-- Polices d'écriture et style CSS -->
      <link rel="stylesheet" href="css/newstyle.css">
      <link href="https://fonts.googleapis.com/css?family=Alegreya&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Crimson+Text&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Lora&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Nobile&display=swap" rel="stylesheet">
      <link rel="icon" href="images/favicon.png">
      <link rel="stylesheet" href="fonts/fonts.css" type="text/css"  />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <title>La banque du futur</title>
    </head>
    <body>

    <!-- Barre de navigation -->
    <div class="newnav">
      <div style="background-color:white;height:80px">
          <a href="vw_moncompte.php"><img class="logo" height="60px" style="margin-left:95px;margin-top:-8px;" src="images/logoRoro.png" alt="logo"></a>
          <div style="margin-left:3.5%" class="dropdown"><a href="vw_envoyermessage.php"><button class="dropbtn">Message</button></a></div>
          <div class="dropdown"><a href="vw_virement.php"><button class="dropbtn">Virement</button></a></div>
          <div class="dropdown">
          <form action="myController.php">
            <input name="action" type="hidden" value="msglist">
            <input type="submit" id="dropbtn" value="Messagerie">
          </form>
          </div>
          <form action="myController.php" method="POST">
            <input type="hidden" name="action" value="disconnect">
            <input type="hidden" name="loginPage" value="vw_login.php?disconnect">
            <button class="decobutton">Déconnexion</button>
          </form>
         <br>
      </div>
      <!-- Ombre sous la barre -->
      <div class="shadow"></div>
    </div><br><br>

    <center>
    <form action="vw_ficheclient.php" method="post">
    <h3 style="font-size:3.5rem;text-align:left;color: #333;padding-top: 5%;padding-left: 12%;font-family: 'boldfont', serif;font-weight: lighter;">Fiches clients</h3>
    
    <?php

      // Si un bouton de tri est cliqué, on récupère sa valeur, sinon tri par id_user par défaut
      $tri = "id_user";
      $val_search = "";
      $num_rows = 0;
      if (isset($_REQUEST['tri']) && ($_REQUEST['tri'] == "nom" || $_REQUEST['tri'] == "prenom" || $_REQUEST['tri'] == "numero_compte")) {
        $tri = $_REQUEST['tri'];
      }

      // Si l'utilisateur a précedemment cherché un client, on ne garde que les clients correspondants à la recherche
      $conditions = ["nom" => "%","prenom" => "%","numero_compte" => "%",];

      if (isset($_REQUEST['client'])) {
        $search = $_REQUEST['client'];
        echo "<input type='hidden'  name='prevclient' value='".$search."'>";
        $conditions = ["nom" => $search."%","prenom" => $search."%","numero_compte" => $search."%",]; 
        $val_search = "value='".$_REQUEST['client']."' ";
      } 
      else if (isset($_REQUEST['prevclient']) &&  $_REQUEST['prevclient'] != '') {
        $search = $_REQUEST['prevclient'];
        echo "<input type='hidden'  name='prevclient' value='".$search."'>";
        $conditions = ["nom" => $search."%","prenom" => $search."%","numero_compte" => $search."%",];
        $val_search = "value='".$_REQUEST['prevclient']."' ";
      }

      
      // Sinon on regarde la nouvelle recherche
    
    ?>


    <!-- Formulaire de recherche d'un client et de tri des résultats -->
      <a class="buttontri" style="padding:5px 10px;background-color:#ff7770;" href="vw_ficheclient.php">X</a>
      <button class="buttontri" type="submit" name="tri" value="id_user" style="background-color:#858585;" href="vw_ficheclient.php">Défaut <img src="images/triangle.png" alt=""></button>
      <button class="buttontri" type="submit" name="tri" value="nom">Par nom <img src="images/triangle.png" alt=""></button>
      <button class="buttontri" type="submit" name="tri" value="prenom">Par prenom <img src="images/triangle.png" alt=""></button>
      <button class="buttontri" type="submit" name="tri" value="numero_compte">Par n° compte <img src="images/triangle.png" alt=""></button>
      

      <!-- Champ de recherche du client, bouton loupe pour submit le form -->
      <input type="search" style="text-align:left;width:240px;background-color:#008ef2;" placeholder="Nom, prenom, compte..." class="buttonsearch" name="client"  <?php echo $val_search; ?>>
      <button class="buttonloupe" style="position:absolute;margin-left:-47px;margin-top:12px;" type="submit"><img src='images/loupe2.png' alt='loupe' height='20' width='20' ></button>
    </form>
    
    <?php
    
    ?>
    </center>


    <!-- Affichage des clients -->
    <table width='880px;'>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>N° Compte</th>
        <th>Statut</th>
        <th>Virement</th>
      </tr>

      <!-- Formulaire pour faire un virement -->
      <form method="post" action="vw_virement.php" style="margin: 0; padding:0;">
        <?php
          $listeUsers = findAllUsers($tri,$conditions);
          foreach ($listeUsers as $id => $user) {
            if($_SESSION["connected_user"]["id_user"] != $id){
              $num_rows = $num_rows + 1; 
              echo "<tr>";
              echo '<td>'.$user['id_user'].'</td>';
              echo '<td>'.$user['nom'].'</td>';
              echo '<td>'.$user['prenom'].'</td>';
              echo '<td>'.$user['numero_compte'].'</td>';
              echo '<td>'.$user['profil_user'].'</td>';
              echo "<td><a class='buttonloupe' href='vw_virement.php?id=$id'> <img src='images/send_money.png' alt='virement' height='30' width='30' style='margin-left:5%;margin-right:auto;'> </a></td>";

              echo "</tr>";
            }
          }
          if ($num_rows == 0) {
            echo "<tr>";
            echo '<td></td>';
            echo '<td></td>';
            echo '<td>Aucun utilisateur trouvé</td>';
            echo '<td></td>';
            echo '<td></td>';
            echo "<td></td>";

            echo "</tr>";
          }
        ?>
     </form>

     
   </table>
   <br><br>
   <center>
    <form action=""><input id="homebutton" style="margin-right: 0px" type="submit" formaction="vw_moncompte.php" value="Accueil" formnovalidate></form>
    </center><br><br><br>

  </body>
</html>
