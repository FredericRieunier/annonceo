<?php require_once "inc/header.inc.php"; ?>

<?php

if( !userConnect() ){
	header('location:connexion.php');
	exit();
}

// Si l'utilisateur a saisi des infos
if($_POST){
    foreach($_POST as $indice => $valeur){
        $_POST[$indice] = htmlentities(addslashes($valeur));
    }

    extract($_POST);

        // Vérification de conformité des données
    if( 
    ( empty($note) && empty($avis) && empty($commentaire) )
    || strlen($avis) > 255
    ){
        $error .= "<div class='alert alert-warning'>Il y a eu une erreur. Veuillez vérifier que tous les champs sont remplis et correspondent aux règles suivantes.
            <ul>
            <li>Au moins un des champs note, avis et commentaire doit être rempli.</li>
            <li>L'avis ne doit pas comporter plus de 255 caractères</li>
            </div>";
    }
    // INSERTION
    elseif( empty($error) ){
        
        $id_membre_notant = $_SESSION['membre']['id_membre'];
        
        $r = execute_requete( "SELECT m.id_membre 
        FROM membre m, annonce a
        WHERE m.id_membre = a.membre_id_membre
        AND a.id_annonce = '$_GET[id_annonce]' 
        ");
        $tab_membre_note = $r->fetch(PDO::FETCH_ASSOC);
        $id_membre_note = $tab_membre_note['id_membre'];
        debug($id_membre_notant);


        // On envoie dans la table note
        
        $pdostatement = prepare_requete(" INSERT INTO note(membre_id_membre1, membre_id_membre2, note, avis, date_enregistrement)
                                            VALUES(:membre_id_membre1, :membre_id_membre2, :note, :avis, NOW())
            ");

        $pdostatement->bindValue(':membre_id_membre1', $id_membre_note, PDO::PARAM_STR);
        $pdostatement->bindValue(':membre_id_membre2', $id_membre_notant, PDO::PARAM_STR);
        if($note != "none"){
            $pdostatement->bindValue(':note', $note, PDO::PARAM_STR);
        }
        $pdostatement->bindValue(':avis', $avis, PDO::PARAM_STR);

        $pdostatement->execute();

        // Puis dans la table commentaire, s'il y a un commentaire

        if( !empty($commentaire) ){
            $pdostatement = prepare_requete(" INSERT INTO commentaire(membre_id_membre, annonce_id_annonce, commentaire, date_enregistrement)
            VALUES(:membre_id_membre, :annonce_id_annonce, :commentaire, NOW())
            ");


            $pdostatement->bindValue(':membre_id_membre', $id_membre_notant, PDO::PARAM_STR);
            $pdostatement->bindValue(':annonce_id_annonce', $_GET['id_annonce'], PDO::PARAM_STR);
            $pdostatement->bindValue(':commentaire', $commentaire, PDO::PARAM_STR);

            $pdostatement->execute();
        }

        header("location:fiche_annonce.php?id_annonce=$_GET[id_annonce]");
        exit();

    }

}



?>
    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Déposer un commentaire ou une note | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
<?php require_once "inc/nav.inc.php"; ?>
    <!-- <main class="container"> -->

    <h1>Déposer un commentaire ou une note</h1>
    <?= $error; //affichage des message d'erreurs ?>
    <?= $content; //affichage du contenu ?>



    <form method="post">

        <h2>Donner une note ou un avis au vendeur</h2>
        <label>Note</label>
        <select name="note" id="">
            <option value="none"></option>
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option> 
        </select>
        <br>

        <label>Avis</label>
        <input type="text" name="avis" class="form-control" ><br>

        <label><h2>Commenter l'annonce</h2></label><br>
        <textarea name="commentaire" id="" cols="40" rows="5"></textarea><br>


        <input type="submit" value="Valider" class="btn btn-secondary">
    </form>



<?php require_once "inc/footer.inc.php"; ?>