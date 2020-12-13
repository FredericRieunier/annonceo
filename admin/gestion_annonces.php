<?php require_once "../inc/header.inc.php"; ?>


<?php
// Restriction d'accès à la page
if(!adminConnect()){
    header('location:../index1.php');
    exit();
}

// Gestion des annonces insertion éventuelle par l'admin et modification;

// Affichage dans le select du membre auteur de l'annonce de son id et de son pseudo
$pdostatement = execute_requete(" SELECT id_membre, pseudo FROM membre ");
    /* $pdostatement->bindValue(':id_membre', $id_membre, PDO::PARAM_STR);
    $pdostatement->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);             
    $pdostatement->execute(); */
    // J'avais commencé à faire une requête préparée (en pensant qu'il était toujours mieux de procéder ainsi), mais j'ai finalement compris que comme je n'avais pas de variable utilisateur ici, il suffisait d'exécuter directement la requête.

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

        }

        $list_id_membre .= "</option>";

    }

// Affichage dans le select de la catégorie de l'annonce de son id et de son titre
$pdostatement = execute_requete(" SELECT id_categorie, titre FROM categorie ");

    $list_id_categorie = '';
    while( $id_categorie_en_bdd = $pdostatement->fetch(PDO::FETCH_ASSOC) ){
        $list_id_categorie .= "<option value='";
        
        foreach($id_categorie_en_bdd as $indice => $valeur){
            if($indice == 'id_categorie'){
                $list_id_categorie .= $valeur . "'>";
                $list_id_categorie .= $valeur . ' - ';
            }
            else{
                $list_id_categorie .= $valeur;
            }          
        }

        $list_id_categorie .= "</option>";

    }

// Si l'admin a rempli le formulaire
if(!empty($_POST)){

    // Sécurisation des données envoyées
    foreach($_POST as $key => $value){
        $_POST[$key] = htmlentities(addslashes($value));
    }
    extract($_POST);


    /*INSERTION ou MODIFICATION: */
    
    // On fait d'abord l'insertion dans la table photo pour pouvoir insérer l'id_photo dans la table annonce.
    
    // Insertion
    if(isset($_GET['action']) && $_GET['action'] == 'ajout'){
        
        // Vérification de conformité des données
        if( !empty($_FILES['photo1']['name']) && !empty($titre) && !empty($prix) 
        && strlen($titre) <= 255 && strlen($description_courte) <= 255 && strlen($prix) <= 11 && strlen($pays) <= 20 && strlen($ville) <= 20 && strlen($adresse) <= 50
        && ( preg_match("#^[0-9]{5}$#", $cp) || empty($cp) )
        && preg_match("#^[0-9]{1,11}$#", $prix)
        ){

            // Insertion des photos dans la table photo

            // S'il y a au moins une photo chargé (en présumant que ce soit la 1), cf vérificatino de conformité des données ci-dessus
        
            // On reprend le nom de la photo
            $nom_photo1 = $_FILES['photo1']['name'];
            $nom_photo2 = $_FILES['photo2']['name'];
            $nom_photo3 = $_FILES['photo3']['name'];
            $nom_photo4 = $_FILES['photo4']['name'];
            $nom_photo5 = $_FILES['photo5']['name'];

            // Chemin d'accès à la photo qui sera inséré en bdd
            $photo1_bdd = URL . "img/$nom_photo1";
            $photo2_bdd = URL . "img/$nom_photo2";
            $photo3_bdd = URL . "img/$nom_photo3";
            $photo4_bdd = URL . "img/$nom_photo4";
            $photo5_bdd = URL . "img/$nom_photo5";
            
            // Lieu d'enregistrement du fichier physique de la photo :
            $photo1_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$nom_photo1";
            $photo2_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$nom_photo2";
            $photo3_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$nom_photo3";
            $photo4_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$nom_photo4";
            $photo5_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$nom_photo5";


            // Enregistrement de la photo dans le dossier img
            
            copy( $_FILES['photo1']['tmp_name'], $photo1_dossier );
            
            copy_photo( $_FILES['photo2']['tmp_name'], $photo2_dossier );
            copy_photo( $_FILES['photo3']['tmp_name'], $photo3_dossier );
            copy_photo( $_FILES['photo4']['tmp_name'], $photo4_dossier );
            copy_photo( $_FILES['photo5']['tmp_name'], $photo5_dossier );
                    
            $pdostatement = prepare_requete(" INSERT INTO photo(photo1, photo2, photo3, photo4, photo5) 
                                            VALUES(
                                            :photo1,
                                            :photo2,
                                            :photo3,
                                            :photo4,
                                            :photo5
                                            )        
            ");

            $pdostatement->bindValue(':photo1', $photo1_dossier, PDO::PARAM_STR);
            $pdostatement->bindValue(':photo2', $photo2_dossier, PDO::PARAM_STR);
            $pdostatement->bindValue(':photo3', $photo3_dossier, PDO::PARAM_STR);
            $pdostatement->bindValue(':photo4', $photo4_dossier, PDO::PARAM_STR);
            $pdostatement->bindValue(':photo5', $photo5_dossier, PDO::PARAM_STR);

            $pdostatement->execute();

            $last_id_photo = $pdo->lastInsertId();
        
            // Insertion dans la table annonce
            // Insérer la value du select ayant pour name membre_id_membre dans la requête d'insertion
            
            
            $pdostatement = prepare_requete(" INSERT INTO annonce(
                                                        titre, 
                                                        description_courte, 
                                                        description_longue, 
                                                        prix, 
                                                        photo,
                                                        pays, 
                                                        ville, 
                                                        adresse, 
                                                        cp, 
                                                        membre_id_membre, 
                                                        categorie_id_categorie, 
                                                        date_enregistrement,
                                                        photo_id_photo
                                                        )
                                                VALUES(
                                                        :titre, 
                                                        :description_courte, 
                                                        :description_longue, 
                                                        :prix, 
                                                        :photo,
                                                        :pays, 
                                                        :ville, 
                                                        :adresse, 
                                                        :cp, 
                                                        :membre_id_membre, 
                                                        :categorie_id_categorie, NOW(),
                                                        '$last_id_photo'
                                                        )
            ");

            if( empty($cp) ){
                $cp = NULL;
            }

            $pdostatement->bindValue(':titre', $titre, PDO::PARAM_STR);
            $pdostatement->bindValue(':description_courte', $description_courte, PDO::PARAM_STR);
            $pdostatement->bindValue(':description_longue', $description_longue, PDO::PARAM_STR);
            $pdostatement->bindValue(':prix', $prix, PDO::PARAM_STR);
            $pdostatement->bindValue(':photo', $photo1_dossier, PDO::PARAM_STR);
            $pdostatement->bindValue(':pays', $pays, PDO::PARAM_STR);
            $pdostatement->bindValue(':ville', $ville, PDO::PARAM_STR);
            $pdostatement->bindValue(':adresse', $adresse, PDO::PARAM_STR);
            $pdostatement->bindValue(':cp', $cp, PDO::PARAM_STR);
            $pdostatement->bindValue(':membre_id_membre', $membre_id_membre, PDO::PARAM_STR);
            $pdostatement->bindValue(':categorie_id_categorie', $categorie_id_categorie, PDO::PARAM_STR);
            
            $pdostatement->execute();
            
            // Message de validation
            if( empty($error) ){
            $content .= "<div class='alert alert-success'>L'annonce a été correctement enregistrée.</div>";
            }

        }
        else{
            $error .= "<div class='alert alert-warning'>Il y a eu une erreur. Merci de vérifier les points suivants.<br><br> 
                            <ul>
                                <li>Il est nécessaire de saisir au moins un titre et un prix, et de charger au moins une photo. 
                                </li>
                                <li>Le prix doit être au minimum de 1 euro et au maximum de 99 999 999 999&nbsp;euros. Il doit être saisi en chiffres, sans séparation entre les chiffres (ni espaces, ni tirets, ni barres obliques, ni points, par exemple).</li>
                                <li>Le code postal doit être composé exactement de 5 chiffres.</li>
                                <li>Le pays et la ville ne doivent pas comporter plus de 20 caractères.</li>
                                <li>Le titre et la description courte ne doivent pas comporter plus de 255 caractères.</li>
                                <li>L'adresse ne doit pas comporter plus de 50 caractères.</li>
                            </ul>
                </div>";
        }
        
    }

}

?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Admin - Gestion des annonces | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "../inc/nav.inc.php"; ?>

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

        <label>Membre auteur de l'annonce</label><br>
        <select name="membre_id_membre" id="" class="form-control">

            <?= $list_id_membre; ?>
        
        </select>
        
        <br>

        <label>Catégorie de l'annonce</label><br>
        <select name="categorie_id_categorie" id="" class="form-control">

            <?= $list_id_categorie; ?>
        
        </select>
        
        <br>

        <input type="submit" value="Valider" class="btn btn-secondary">
        
        </form>
        <?php endif; ?>

        

    <?php require_once "../inc/footer.inc.php"; ?>