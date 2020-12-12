<?php require_once "../inc/header.inc.php"; ?>


<?php
// Restriction d'accès à la page
if(!adminConnect()){
    header('location:../index1.php');
    exit();
}

// Gestion des annonces insertion éventuelle par l'admin et modification; 

// Ajouter contrôles au formulaire pour la taille des champs et leur format (cp, prix), 
// voir comment rendre facultatif un champ.
// Peut-être en donnant comme value par défaut '' si !(isset(champenquestion))

if(!empty($_POST)){

    // Sécurisation des données envoyées
    foreach($_POST as $key => $value){
        $_POST[$key] = htmlentities(addslashes($value));
    }
    extract($_POST);
    debug($prix);

    /*INSERTION ou MODIFICATION: */
    


    // Faire d'abord l'insertion dans la table photo pour avoir une cohérence avec l'id_photo qu'on insère ds la table annonce.
    
    // Insertion
    if(isset($_GET['action']) && $_GET['action'] == 'ajout'){
    // Insertion des photos dans la table photo

    // DANS LA BDD, AUTORISER LES CHAMPS NULL POUR LES PHOTOS, histoire de pouvoir avancer sans ça.

    /* if( !empty($_FILES['photo1']['name']) ){
        debug($_FILES);
        debug($_POST);
        // $nom_photo1 = ???
    } 
    
        $pdostatement = prepare_requete(" INSERT INTO photo(photo1, photo2, photo3, photo4, photo5) 
                                          VALUES(
                                        :photo1,
                                        :photo2,
                                        :photo3,
                                        :photo4,
                                        :photo5 )        
        ");

        $pdostatement->bindValue(':photo1', $photo1, PDO::PARAM_STR);
        $pdostatement->bindValue(':photo1', $photo1, PDO::PARAM_STR);
        $pdostatement->bindValue(':photo1', $photo1, PDO::PARAM_STR);
        $pdostatement->bindValue(':photo1', $photo1, PDO::PARAM_STR);
        $pdostatement->bindValue(':photo1', $photo1, PDO::PARAM_STR); */
        
        // Insertion dans la table annonce
        /* $pdostatement = prepare_requete(" INSERT INTO annonce(titre, description_courte, description_longue, prix, photo, pays, ville, adresse, cp, membre_id_membre, photo_id_photo, categorie_id_categorie, date_enregistrement)
                                            VALUES(:titre, :description_courte, :description_longue, :prix, :photo, :pays, :ville, :adresse, :cp, :membre_id_membre, :photo_id_photo, :categorie_id_categorie, NOW() )
        ");


        $pdostatement->bindValue(':titre', $titre, PDO::PARAM_STR);
        $pdostatement->bindValue(':description_courte', $description_courte, PDO::PARAM_STR);
        $pdostatement->bindValue(':description_longue', $description_longue, PDO::PARAM_STR);
        $pdostatement->bindValue(':prix', $prix, PDO::PARAM_STR);
        $pdostatement->bindValue(':photo', $photo, PDO::PARAM_STR);
        $pdostatement->bindValue(':pays', $pays, PDO::PARAM_STR);
        $pdostatement->bindValue(':ville', $ville, PDO::PARAM_STR);
        $pdostatement->bindValue(':adresse', $adresse, PDO::PARAM_STR);
        $pdostatement->bindValue(':cp', $cp, PDO::PARAM_STR);

        $pdostatement->execute(); */

        debug($membre_id_membre);

    }

}

?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Admin - Gestion des annonces | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "../inc/nav.inc.php"; ?>

    <!-- A ranger dans le PHP :D -->
    <!-- Insérer la value du select ayant pour name membre_id_membre dans la requête d'insertion -->
    <!-- Affichage des id des membres entrés dans la bdd -->
    <?php 
            $pdostatement = prepare_requete(" SELECT id_membre, pseudo FROM membre ");
            /* $pdostatement->bindValue(':id_membre', $id_membre, PDO::PARAM_STR);
            $pdostatement->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);   */          
            $pdostatement->execute();
            $list_id_membre = '';
            while( $id_en_bdd = $pdostatement->fetch(PDO::FETCH_ASSOC) ){
                $list_id_membre .= "<option value='";
                /* 1'>1</option>";
                $list_id_membre .= */ 
               
                foreach($id_en_bdd as $indice => $valeur){
                    if($indice == 'id_membre'){
                        $list_id_membre .= $valeur . "'>";
                        $list_id_membre .= $valeur . ' - ';
                    }
                    else{
                        $list_id_membre .= $valeur;
                    }
                   
                   /* echo $valeur;
                   echo '<br>'; */
                //    debug($id_en_bdd['id_membre']);
                    // if($indice == 'id_membre'){
                    //     debug($valeur);
                    // }

               }

               $list_id_membre .= "</option>";

            }

            
        ?>

    <main class="container">
        <h1>Admin - Gestion des annonces</h1>
        <a href="?action=ajout">Ajouter une annonce</a><br>
        <?= $error; ?>
        <?= $content; ?>

        <?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout')) :
        // S'il y a une action dans l'url et qu'elle a pour valeur ajout, on affiche le formulaire
        ?>

        <form method="post" enctype="multipart/form-data">
        
        <label>Titre</label><br>
        <input type="text" name="titre" class="form-control"><br>

        <label>Description courte</label><br>
        <input type="text" name="description_courte" class="form-control"><br>

        <label>Description longue</label><br>
        <textarea name="description_longue" id="" cols="30" rows="10" class="form-control"></textarea><br>

        <label>Prix</label><br>
        <input type="text" name="prix" class="form-control"><br>

        <label>Photo 1</label><br>
        <input type="file" name="photo1"><br>

        <label>Photo 2</label><br>
        <input type="file" name="photo2"><br>

        <label>Photo 3</label><br>
        <input type="file" name="photo3"><br>

        <label>Photo 4</label><br>
        <input type="file" name="photo4"><br>
        
        <label>Photo 5</label><br>
        <input type="file" name="photo5"><br>
        
        <label>Pays</label><br>
        <input type="text" name="pays" class="form-control"><br>

        <label>Ville</label><br>
        <input type="text" name="ville" class="form-control"><br>

        <label>Adresse</label><br>
        <input type="text" name="adresse" class="form-control"><br>

        <label>Code postal</label><br>
        <input type="text" name="cp" class="form-control"><br>

        <label>ID du membre auteur de l'annonce</label><br>
        <select name="membre_id_membre" id="" class="form-control">

            <?= $list_id_membre; ?>
        
        </select>
        
        <br>

        <input type="submit" value="Valider" class="btn btn-secondary">
        
        </form>
        <?php endif; ?>

        

    <?php require_once "../inc/footer.inc.php"; ?>