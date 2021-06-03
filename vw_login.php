<!DOCTYPE html>
<html>
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
      <link rel="stylesheet" href="fonts/fonts.css" type="text/css"  />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <title>La banque du futur</title>
    </head>
    <script src="https://www.google.com/recaptcha/api.js" async defer>
    </script>
    <body>

      <!-- Portail de connexion -->
      <?php
      $style = "";
      if (isset($_REQUEST["badvalue"])) {
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Identifiants incorrects

            </div>';
          $style = "margin: 2% 35% 0%;";
        } else if(isset($_REQUEST["misscaptcha"])){
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Veuillez remplir le captcha

            </div>';
          $style = "margin: 2% 35% 0%;";
        } else if(isset($_REQUEST["badcaptcha"])){
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Veuillez réessayer le captcha

            </div>';
          $style = "margin: 2% 35% 0%;";
        } else if(isset($_REQUEST["nullvalue"])){
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Login et mot de passe null

            </div>';
          $style = "margin: 2% 35% 0%;";
        } else if(isset($_REQUEST["disconnect"])){
          echo '
            <div class="formulaire" style="background-color:#7ac27e;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Déconnexion réussie

            </div>';
          $style = "margin: 2% 35% 150px;";
        } else if(isset($_REQUEST["timeout"])){
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Session expirée
            </div>';
          $style = "margin: 2% 35% 150px;";
        } else if(isset($_REQUEST["notconnected"])){
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Vous n\'êtes pas connecté

            </div>';
          $style = "margin: 2% 35% 150px;";
        } else if(isset($_REQUEST["ipbanned"])){
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Trop de tentatives... Réessayez plus tard

            </div>';
          $style = "margin: 2% 35% 150px;";
          $disabled="disabled='disabled' placeholder='ACCES REFUSE'";
        }
      ?>
      <div class="formulaire" style="height:620px;<?php echo ("$style");?>padding:10px;border-width:2px;">
          <div class="inscription" style="  margin-top:1%">
            <img height="70px" style="padding:0px;margin-left:3.25%" src="images/character.png" alt="icon">
            <h3 style="font-size:3.5rem;text-align:center;color: #333"> Connexion</h3>
          </div>
        <form style=" font-family: 'Roboto', sans-serif;font-size:1.5rem;margin-left:60px; margin-right:60px;" class="" action="myController.php" method="post">
          <!-- Renseignement du login/password -->
          <input type="hidden" name="action" value="authenticate">
          <label for="login">Nom d'utilisateur</label><br>
            <input id="login" name="login" required minlength="1" <?php if(isset($disabled)) echo ("$disabled");?> placeholder="example"><br>
            <hr class="underrule"><br>
          <label for="prenom">Mot de passe</label><br>
            <input type="password" id="mdp" name="mdp" required minlength="1" <?php if(isset($disabled)) echo ("$disabled");?> placeholder="********"><br>
            <hr class="underrule">
            <center>
            <div class="g-recaptcha" data-sitekey="6Lfb-C4aAAAAAHIxCzLbB2wYtFlh-iRSswQx0Fdo"></div><br>
            <input type="submit">
          </center>
        </form>
      </div>

  </body>
</html>
