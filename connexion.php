<?php require_once "inc/header.inc.php"; ?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

<?php 

// Déconnexion
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion'){ 
    session_destroy();
}

// Restriction à l'accès à la page si on est connecté :
if(userConnect()){
    header('location:profil.php');
    exit();
}

// Connexion
if($_POST){
    extract($_POST);

    debug($_POST);

    if( stristr($_POST['pseudo'], '\'') || stristr($_POST['pseudo'], '\"') || stristr($_POST['mdp'], '\'') || stristr($_POST['mdp'], '\"') || stristr($_POST['pseudo'], '\\') || stristr($_POST['mdp'], '\\') ){
        $error .= '<div class="alert alert-danger">Les champs pseudo et mot de passe ne peuvent pas recevoir d\'apostrophes, de barres obliques inversées ou de guillemets.</div>';
    }
    else{
        // On compare le pseudo posté et celui en bdd
        $r = execute_requete(" SELECT * FROM membre WHERE pseudo = '$pseudo' ");

        if($r->rowCount() >= 1){
            $membre = $r->fetch(PDO::FETCH_ASSOC);

            if(password_verify($mdp, $membre['mdp'])){
                $content .= '<div class="alert alert-success">Vous êtes connecté.</div>';
                foreach( $membre as $index => $valeur ){

                    $_SESSION['membre'][$index] = $valeur;
                }
                
                // redirection vers la page de profil
                header('location:profil.php');
            }
            else{
                $error .= '<div class="alert alert-danger">Le mot de passe ne correspond pas au pseudo saisi.</div>';
            }
        }
        else{
            $error .= '<div class="alert alert-danger">Ce pseudo n\'existe pas.</div>';
        }
    }
}


?>

    <title>Connexion | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
<?php require_once "inc/nav.inc.php"; ?>

    <!-- <main class="container"> -->
        <h1>Connexion</h1>

        <?= $content; ?>
        <?= $error; ?>

        <form method="post">

    <label>Pseudo :</label><br>
    <input type="text" name="pseudo" class="form-control"><br><br>

    <label>Mot de passe :</label><br>
    <input type="password" name="mdp" class="form-control"><br><br>

    <input type="submit" value="Se connecter" class="btn btn-secondary">

</form>


<?php require_once "inc/footer.inc.php"; ?>