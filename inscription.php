<?php require_once "inc/header.inc.php"; ?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

<?php

/* Si l'utilisateur est connecté et modifie ses données */
if(userConnect()){
    // Le bouton affiche 'Mettre à jour' et non 'S'inscrire'
    $bouton = '<input type="submit" value="Mettre à jour" class="btn btn-secondary">';

    if(isset($_GET['action']) && $_GET['action'] == 'modification'){
        // Pré-saisie du formulaire en cas de modification des données personnelles
        $rappelMdp = '<div class="alert alert-secondary">Pensez à saisir votre mot de passe (actuel ou modifié) pour valider la modification.</div>';
        $pseudoValue = $_SESSION['membre']['pseudo'];
        $mdpValue = $_SESSION['membre']['mdp'];
        $nomValue = $_SESSION['membre']['nom'];
        $prenomValue = $_SESSION['membre']['prenom'];
        $telephoneValue = $_SESSION['membre']['telephone'];
        $emailValue = $_SESSION['membre']['email'];

        $homme = '';
        $femme = '';
        if($_SESSION['membre']['civilite'] == 'm'){
            $homme = "checked";
        }
        else{
            $femme = "checked";
        }

        // Requête de modification dans la BDD
        

        if($_POST){

            foreach($_POST as $indice => $valeur){
                $_POST[$indice] = htmlentities(addslashes($valeur));
            }
        
            extract($_POST);
        
            
                // Vérification de conformité des données
            if( 
            empty($pseudo) || empty($mdp) || empty($nom) || empty($prenom) || empty($telephone) || empty($email) || empty($civilite) 
            || strlen($pseudo) > 20 || strlen($nom) > 20 || strlen($prenom) > 20 || strlen($mdp) > 60 || strlen($email) > 50 
            || !(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
            || !(preg_match("#^0[1-68]([-. ]?[0-9]{2}){4}$#", $telephone))
            ){
                $error .= "<div class='alert alert-warning'>Il y a eu une erreur. Veuillez vérifier que tous les champs sont remplis et correspondent aux formats suivants&nbsp;: 
                    <ul>
                    <li>le pseudo, le nom et le prénom ne doiventt pas comporter plus de 20 caractères</li>
                    <li>le mot de passe ne doit pas comporter plus de 60 caractères</li>
                    <li>le mail ne doit pas comporter plus de 50 caractères</li>
                    <li>le format du mail doit être correct (par exemple&nbsp;: prenom@hebergeur.fr ou pseudo@site.com)</li>
                    <li>le numéro de téléphone doit correspondre au format en vigueur en France, ses paires de nombres peuvent être jointes, séparées par des espaces, des tirets ou des points et le numéro ne doit pas comporter de lettres</li>
                    </ul>
                    </div>";
            }
            // MODIFICATION
            elseif( empty($error) ){
                // On crypte le mot de passe
                $mdp = password_hash($mdp, PASSWORD_DEFAULT);
        
               $id_membre = $_SESSION['membre']['id_membre'];
               $pdostatement = prepare_requete(" UPDATE membre SET 
               pseudo = :pseudo,
               mdp = :mdp,
               nom = :nom,
               prenom = :prenom,
               telephone = :telephone,
               email = :email,
               civilite = :civilite
               WHERE id_membre = '$id_membre'
                ");
        
        
               $pdostatement->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
               $pdostatement->bindValue(':mdp', $mdp, PDO::PARAM_STR);
               $pdostatement->bindValue(':nom', $nom, PDO::PARAM_STR);
               $pdostatement->bindValue(':prenom', $prenom, PDO::PARAM_STR);
               $pdostatement->bindValue(':telephone', $telephone, PDO::PARAM_STR);
               $pdostatement->bindValue(':email', $email, PDO::PARAM_STR);
               $pdostatement->bindValue(':civilite', $civilite, PDO::PARAM_STR);
        
               $pdostatement->execute();
        
               $content .= '<div class="alert alert-success">Modification validée.
                            </div>';
                header('location:profil.php');
                exit();
        
            }
        
        }


        // Fin de requête de modification
    }
    else{
    header('location:profil.php');
    exit();
    }
}
// Sinon, l'utilisateur n'est pas connecté
else{
        // Le bouton affiche 'S'inscrire' et non 'Mettre à jour'
        $bouton = '<input type="submit" value="S\'inscrire" class="btn btn-secondary">';

        // On initialise les variables de value du formulaire, prévues pour la modification, et le rappel de mdp.
        $pseudoValue = '';
        $nomValue = '';
        $prenomValue = '';
        $telephoneValue = '';
        $emailValue = '';
        $femme = '';
        $homme = '';
        $rappelMdp = '';

    /* Si l'utilisateur a saisi des infos */
    if($_POST){
        foreach($_POST as $indice => $valeur){
            $_POST[$indice] = htmlentities(addslashes($valeur));
        }

        extract($_POST);

        // Vérification de la disponibilité du pseudo
        $pdostatement = prepare_requete(" SELECT pseudo FROM membre WHERE pseudo = '$pseudo' ");
        $pdostatement->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $pdostatement->execute();

        if($pdostatement->rowCount() >= 1){
            $error .= "<div class='alert alert-warning'>Le pseudo que vous avez choisi est déjà utilisé. Veuillez en choisir un autre.</div>";
        }
            // Vérification de conformité des données
        elseif( 
        empty($pseudo) || empty($mdp) || empty($nom) || empty($prenom) || empty($telephone) || empty($email) || empty($civilite) 
        || strlen($pseudo) > 20 || strlen($nom) > 20 || strlen($prenom) > 20 || strlen($mdp) > 60 || strlen($email) > 50 
        || !(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
        || !(preg_match("#^0[1-68]([-. ]?[0-9]{2}){4}$#", $telephone))
        ){
            $error .= "<div class='alert alert-warning'>Il y a eu une erreur. Veuillez vérifier que tous les champs sont remplis et correspondent aux formats suivants&nbsp;: 
                <ul>
                <li>le pseudo, le nom et le prénom ne doiventt pas comporter plus de 20 caractères</li>
                <li>le mot de passe ne doit pas comporter plus de 60 caractères</li>
                <li>le mail ne doit pas comporter plus de 50 caractères</li>
                <li>le format du mail doit être correct (par exemple&nbsp;: prenom@hebergeur.fr ou pseudo@site.com)</li>
                <li>le numéro de téléphone doit correspondre au format en vigueur en France, ses paires de nombres peuvent être jointes, séparées par des espaces, des tirets ou des points et le numéro ne doit pas comporter de lettres</li>
                </ul>
                </div>";
        }
        // INSERTION
        elseif( empty($error) ){
            // On crypte le mot de passe
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);

        $pdostatement = prepare_requete(" INSERT INTO membre(pseudo, mdp, nom, prenom, telephone, email, civilite, date_enregistrement)
                                            VALUES(:pseudo, :mdp, :nom, :prenom, :telephone, :email, :civilite, NOW())
            ");


        $pdostatement->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $pdostatement->bindValue(':mdp', $mdp, PDO::PARAM_STR);
        $pdostatement->bindValue(':nom', $nom, PDO::PARAM_STR);
        $pdostatement->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $pdostatement->bindValue(':telephone', $telephone, PDO::PARAM_STR);
        $pdostatement->bindValue(':email', $email, PDO::PARAM_STR);
        $pdostatement->bindValue(':civilite', $civilite, PDO::PARAM_STR);

        $pdostatement->execute();

        $content .= '<div class="alert alert-success">Inscription validée. 
                        <a href="' .URL. 'connexion.php">Cliquez ici pour vous connecter.</a>
                        </div>';

        }

    }

}



?>

    <title>Inscription | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
<?php require_once "inc/nav.inc.php"; ?>
    <!-- <main class="container"> -->

    <h1>Inscription</h1>
    <?= $error; //affichage des message d'erreurs ?>
    <?= $content; //affichage du contenu ?>



    <form method="post">

        <?= $rappelMdp; ?>

        <label>Pseudo</label>
        <input type="text" name="pseudo" class="form-control" value="<?= $pseudoValue; ?>"><br>

        <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= $nomValue; ?>"><br>

        <label>Prénom</label>
        <input type="text" name="prenom" class="form-control"value="<?= $prenomValue; ?>"><br>

        <label>Téléphone</label>
        <input type="text" name="telephone" class="form-control" value="<?= $telephoneValue; ?>"><br>

        <label>E-mail</label>
        <input type="text" name="email" class="form-control" value="<?= $emailValue; ?>"><br>

        <label>Civilité</label><br>
        <input type="radio" name="civilite" value="f" <?= $femme?> > Femme<br>
        <input type="radio" name="civilite" value="m" <?= $homme ?> > Homme<br><br>

        <label>Mot de passe</label>
        <input type="password" name="mdp" class="form-control"><br>

        <?= $rappelMdp; ?><br>

        <?= $bouton; ?>
        <!-- <input type="submit" value="S'inscrire" class="btn btn-secondary"> -->
    </form>



<?php require_once "inc/footer.inc.php"; ?>