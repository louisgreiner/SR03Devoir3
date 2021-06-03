<?php
  require_once('include.php');
  session_start();

    if(!(isset($_SESSION['connected_user']))){
      // pas de session active = d'utilisateur déjà connecté
      header('Location: vw_login.php');
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

<!doctype html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <head>
      <!-- Polices d'écriture et style CSS -->
      <link rel="stylesheet" href="css/newstyle.css">
      <link href="https://fonts.googleapis.com/css?family=Alegreya&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Crimson+Text&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Lora&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Nobile&display=swap" rel="stylesheet">
      <link rel="icon" href="images/favicon.png">
      <!-- <link rel="stylesheet" href="fonts/fonts.css" type="text/css"  /> -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <title>La banque du futur</title>
    </head>
    <body>
  <title>Messages</title>
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


    <h3 style="font-size:3.5rem;text-align:left;color: #333;padding-top: 5%;padding-left: 12%;font-family: 'boldfont', serif;font-weight: lighter;">Messages reçus</h3>


    <table width='1200px;'>
      <tr>
        <th>Expéditeur</th>
        <th>Sujet</th>
        <th>Message</th>
      </tr>

      <!-- Formulaire pour consulter un élève (loupe à la fin de chaque ligne du tableau) -->
      <form method="post" action="vw_virement.php" style="margin: 0; padding:0;">
        <?php


        foreach ($_SESSION['messagesRecus'] as $cle => $message) {
          echo "<tr>";
          echo '<td>'.$message['nom'].' '.$message['prenom'].'</td>';
          echo '<td>'.htmlentities($message['sujet_msg'], ENT_QUOTES).'</td>';
          echo '<td>'.htmlentities($message['corps_msg'], ENT_QUOTES).'</td>';
          // echo "<td><a class='buttonloupe' href='vw_virement.php?id=$id'> <img src='images/send_money.png' alt='virement' height='30' width='30' style='margin-left:5%;margin-right:auto;'> </a></td>";

          echo "</tr>";
        }
        if (empty($_SESSION['messagesRecus'])) {
          echo "<tr>";
          echo '<td></td>';
          echo '<td>Aucun nouveau message</td>';
          echo '<td></td>';
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
