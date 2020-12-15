<?php require_once "../inc/header.inc.php"; ?>


<?php
// Restriction d'accès à la page
if(!adminConnect() && !userConnect()){
    header('location:../index1.php');
    exit();
}



// Gestion des annonces par l'admin

if( adminConnect() ){

    // SUPPRESSION

    if( isset( $_GET['action'] ) && $_GET['action'] == 'suppression' ){ 
        //S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'suppression'

        //récupération de la colonne 'photo' dans la table 'annonces' à condition que l'id_annonce correponde à l'id passée dans l'URL
        $r = execute_requete(" SELECT photo_id_photo FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
        $id_photo_a_supprimer = $r->fetch(PDO::FETCH_ASSOC);
        $id_photo_a_supprimer = $id_photo_a_supprimer['photo_id_photo'];
        
        debug($id_photo_a_supprimer);

        $r = execute_requete(" SELECT * FROM photo WHERE id_photo = '$id_photo_a_supprimer' ");
        $tab_photos_a_supprimer = $r->fetch(PDO::FETCH_ASSOC);
        
        $photo1_a_supprimer = $tab_photos_a_supprimer['photo1'];
        $photo2_a_supprimer = $tab_photos_a_supprimer['photo2'];
        $photo3_a_supprimer = $tab_photos_a_supprimer['photo3'];
        $photo4_a_supprimer = $tab_photos_a_supprimer['photo4'];
        $photo5_a_supprimer = $tab_photos_a_supprimer['photo5'];


        
        // La photo en tant que fichier à supprimer est dans un array avec le localhost : 
        // [photo] => http://localhost/PHP/boutique/photo/531_chemise-2.jpg
        
        // Le chemin de la photo à supprimer est dans le htdocs :
        // C:/MAMP/htdocs/PHP/boutique/photo/531_chemise-2.jpg


        $chemin_photo1_a_supprimer = str_replace( 'http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo1_a_supprimer );
        $chemin_photo2_a_supprimer = str_replace( 'http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo2_a_supprimer );
        $chemin_photo3_a_supprimer = str_replace( 'http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo3_a_supprimer );
        $chemin_photo4_a_supprimer = str_replace( 'http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo4_a_supprimer );
        $chemin_photo5_a_supprimer = str_replace( 'http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo5_a_supprimer );

        if( !empty( $chemin_photo1_a_supprimer ) && file_exists( $chemin_photo1_a_supprimer ) ){
            unlink( $chemin_photo1_a_supprimer );
        }

        if( !empty( $chemin_photo2_a_supprimer ) && file_exists( $chemin_photo2_a_supprimer ) ){
            unlink( $chemin_photo2_a_supprimer );
        }

        if( !empty( $chemin_photo3_a_supprimer ) && file_exists( $chemin_photo3_a_supprimer ) ){
            unlink( $chemin_photo3_a_supprimer );
        }

        if( !empty( $chemin_photo4_a_supprimer ) && file_exists( $chemin_photo4_a_supprimer ) ){
            unlink( $chemin_photo4_a_supprimer );
        }

        if( !empty( $chemin_photo5_a_supprimer ) && file_exists( $chemin_photo5_a_supprimer ) ){
            unlink( $chemin_photo5_a_supprimer );
        }
        
        
        // On supprime d'abord dans la table photo, sinon, on n'a plus la ligne correspondante dans la table annonce
        execute_requete(" DELETE FROM photo WHERE id_photo IN
                            (SELECT photo_id_photo FROM annonce WHERE id_annonce = '$_GET[id_annonce]') ");

        //Suppression dans la table 'annonce' A CONDITION que l'id produit corresponde à l'id_annonce que l'on récupère dans l'URL
        execute_requete(" DELETE FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");

        header('location:?action=affichage');
        exit();

    }


    /*  AFFICHAGE DES SELECT (pour la modif comme pour l'insertion) */
    
    // On récupère l'id de l'auteur de l'annonce avec une requête pour pouvoir le prés-sélectionner dans le select en cas de modification
    if( isset($_GET['action']) && $_GET['action'] == 'modification' ){
        $pdostatement = execute_requete(" SELECT membre_id_membre FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
        $id_auteur_annonce_modifiee = $pdostatement->fetch(PDO::FETCH_ASSOC);
        $id_auteur_annonce_modifiee = $id_auteur_annonce_modifiee['membre_id_membre'];

        // On fait de même pour la catégorie
        $pdostatement = execute_requete(" SELECT categorie_id_categorie FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
        $id_categorie_annonce_modifiee = $pdostatement->fetch(PDO::FETCH_ASSOC);
        $id_categorie_annonce_modifiee = $id_categorie_annonce_modifiee['categorie_id_categorie'];
    }
    

    // Affichage dans le select du membre auteur de l'annonce de son id et de son pseudo
    $pdostatement = execute_requete(" SELECT id_membre, pseudo FROM membre ");
    
    // On initialise la liste des membres qui s'affichera dans un select
    $list_id_membre = '';
        

    while( $id_en_bdd = $pdostatement->fetch(PDO::FETCH_ASSOC) ){
        
        $list_id_membre .= "<option value='";
        foreach($id_en_bdd as $indice => $valeur){
            
            if($indice == 'id_membre'){
                if($_GET['action'] == 'modification' && $valeur == $id_auteur_annonce_modifiee){
                    $list_id_membre .= $valeur . "' selected>";
                    $list_id_membre .= $valeur . ' - ';
                }

                else{
                $list_id_membre .= $valeur . "'>";
                $list_id_membre .= $valeur . ' - ';
                }
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
                    if($_GET['action'] == 'modification' && $valeur == $id_categorie_annonce_modifiee){
                        $list_id_categorie .= $valeur . "' selected>";
                        $list_id_categorie .= $valeur . ' - ';
                    }
                    else{
                        $list_id_categorie .= $valeur . "'>";
                        $list_id_categorie .= $valeur . ' - ';
                    }
                
                }
                else{
                    $list_id_categorie .= $valeur;
                }          
            }

            $list_id_categorie .= "</option>";

        }

    
    
    
    
        // Si l'admin a rempli le formulaire (modification ou insertion)
    if( !empty($_POST) ){

        // Sécurisation des données envoyées
        foreach($_POST as $key => $value){
            $_POST[$key] = htmlentities(addslashes($value));
        }
        extract($_POST);

        // Vérification de conformité des données
        if( !empty($_FILES['photo1']['name']) && !empty($titre) && !empty($prix) 
        && strlen($titre) <= 255 && strlen($description_courte) <= 255 && strlen($prix) <= 11 && strlen($pays) <= 20 && strlen($ville) <= 20 && strlen($adresse) <= 50
        && ( preg_match("#^[0-9]{5}$#", $cp) || empty($cp) )
        && preg_match("#^[0-9]{1,11}$#", $prix)
        ){



            // MODIFICATION
            
            if( isset($_GET['action']) && $_GET['action'] == 'modification' ){ //S'il y a une 'action' dans l'URL ET que cette action est égale à 'modification', alors on effectue une requête de modification :

                $pdostatement = prepare_requete(" UPDATE annonce SET 	
                titre = '$titre',
                description_courte = '$description_courte',
                description_longue = '$description_longue',
                prix = '$prix',
                photo = '$photo',
                -- XXXXXX
                pays = '$pays',
                ville = '$ville',
                adresse = '$adresse',
                cp = '$cp'
                WHERE id_annonce = '$_GET[id_annonce]'
                            ");


                            debug($cp);

                $pdostatement->bindValue(':titre', $titre, PDO::PARAM_STR);
                $pdostatement->bindValue(':description_courte', $description_courte, PDO::PARAM_STR);
                $pdostatement->bindValue(':description_longue', $description_longue, PDO::PARAM_STR);
                $pdostatement->bindValue(':prix', $prix, PDO::PARAM_STR);
                $pdostatement->bindValue(':photo', $photo, PDO::PARAM_STR);
                $pdostatement->bindValue(':pays', $pays, PDO::PARAM_STR);
                $pdostatement->bindValue(':ville', $ville, PDO::PARAM_STR);
                $pdostatement->bindValue(':adresse', $adresse, PDO::PARAM_STR);
                // $pdostatement->bindValue(':cp', $cp, PDO::PARAM_STR);

                $pdostatement->execute();
                            

                //redirection vers l'affichage :
                // header('location:?action=affichage');

            }

            /*INSERTION  */
                  
            // Insertion
            if(isset($_GET['action']) && $_GET['action'] == 'ajout'){
        
        
                // Insertion des photos dans la table photo
                // On fait d'abord l'insertion dans la table photo pour pouvoir insérer l'id_photo dans la table annonce.

                // S'il y a au moins une photo chargée (en présumant que ce soit la 1), cf vérification de conformité des données ci-dessus
            
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

                $pdostatement->bindValue(':photo1', $photo1_bdd, PDO::PARAM_STR);

                // Pour les photos 2 à 5, on s'assure qu'il y a une photo avant d'essayer d'insérer son chemin d'accès.
                
                if( empty($nom_photo2) ){
                    $photo2_dossier = '';
                }
                if( empty($nom_photo3) ){
                    $photo3_dossier = '';
                }
                if( empty($nom_photo4) ){
                    $photo4_dossier = '';
                }
                if( empty($nom_photo5) ){
                    $photo5_dossier = '';
                }

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
                $pdostatement->bindValue(':photo', $photo1_bdd, PDO::PARAM_STR);
                $pdostatement->bindValue(':pays', $pays, PDO::PARAM_STR);
                $pdostatement->bindValue(':ville', $ville, PDO::PARAM_STR);
                $pdostatement->bindValue(':adresse', $adresse, PDO::PARAM_STR);
                $pdostatement->bindValue(':cp', $cp, PDO::PARAM_STR);
                $pdostatement->bindValue(':membre_id_membre', $membre_id_membre, PDO::PARAM_STR);
                $pdostatement->bindValue(':categorie_id_categorie', $categorie_id_categorie, PDO::PARAM_STR);
                
                $pdostatement->execute();

                // Récupération de l'id de l'annonce insérée dans la table
                $last_id_annonce = $pdo->lastInsertId();
                
                // Message de validation
                $content .= "<div class='alert alert-success'>L'annonce a été correctement enregistrée.</div>";
                
                // Renommage de la photo en ajoutant un indice pour l'identifier

                // Ajout de l'indice
                $new_nom_photo1 = $last_id_photo . '_' . $nom_photo1;
                $new_nom_photo2 = $last_id_photo . '_' . $nom_photo2;
                $new_nom_photo3 = $last_id_photo . '_' . $nom_photo3;
                $new_nom_photo4 = $last_id_photo . '_' . $nom_photo4;
                $new_nom_photo5 = $last_id_photo . '_' . $nom_photo5;
                
                $new_photo1_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$new_nom_photo1";
                $new_photo2_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$new_nom_photo2";
                $new_photo3_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$new_nom_photo3";
                $new_photo4_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$new_nom_photo4";
                $new_photo5_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/$new_nom_photo5";

                // Réinitialisation des new_photoX_dossier si ces photos n'existent pas
                if( empty($nom_photo2) ){
                    $new_photo2_dossier = '';
                }
                if( empty($nom_photo3) ){
                    $new_photo3_dossier = '';
                }
                if( empty($nom_photo4) ){
                    $new_photo4_dossier = '';
                }
                if( empty($nom_photo5) ){
                    $new_photo5_dossier = '';
                }

                debug($photo1_dossier);

                // Exécution du renommage
                rename($photo1_dossier, $new_photo1_dossier);

                // Pour les photos 2 à 5, on s'assure qu'il y a une photo avant d'essayer de renommer.
                if( !empty($nom_photo2) ){
                    rename($photo2_dossier, $new_photo2_dossier);
                }
                if( !empty($nom_photo3) ){
                    rename($photo3_dossier, $new_photo3_dossier);
                }
                if( !empty($nom_photo4) ){
                    rename($photo4_dossier, $new_photo4_dossier);
                }
                if( !empty($nom_photo5) ){
                    rename($photo5_dossier, $new_photo5_dossier);
                }
                
                // On transforme ensuite le chemin vers les images pour l'envoi vers la bdd

                $new_photo1_dossier = str_replace( $_SERVER['DOCUMENT_ROOT'], 'http://localhost', $new_photo1_dossier );
                $new_photo2_dossier = str_replace( $_SERVER['DOCUMENT_ROOT'], 'http://localhost', $new_photo2_dossier );
                $new_photo3_dossier = str_replace( $_SERVER['DOCUMENT_ROOT'], 'http://localhost', $new_photo3_dossier );
                $new_photo4_dossier = str_replace( $_SERVER['DOCUMENT_ROOT'], 'http://localhost', $new_photo4_dossier );
                $new_photo5_dossier = str_replace( $_SERVER['DOCUMENT_ROOT'], 'http://localhost', $new_photo5_dossier );

            
                // Une fois le remplacement de fichier fait, on met à jour les photos ds les tables annonce et photo
                execute_requete(" UPDATE photo 
                                SET photo1 = '$new_photo1_dossier',
                                    photo2 = '$new_photo2_dossier', 
                                    photo3 = '$new_photo3_dossier',
                                    photo4 = '$new_photo4_dossier',
                                    photo5 = '$new_photo5_dossier'
                                WHERE id_photo = '$last_id_photo'
                                    ");

                execute_requete(" UPDATE annonce 
                                SET photo = '$new_photo1_dossier'
                                WHERE id_annonce = '$last_id_annonce'
                    ");

                header('location:?action=affichage');
                exit();

            }    //Fin de l'insertion
                
        }   // Fin des conditions de conformité des données
        
        else{
            // Si au moins une des conditions de conformité des données n'est pas respectée, on affiche un message d'erreur général 
            $error .= "<div class='alert alert-warning'>Il y a eu une erreur. Merci de vérifier les points suivants.<br><br> 
                            <ul>
                                <li>Il est nécessaire de saisir au moins un titre et un prix, et de charger au moins une photo. 
                                </li>
                                <li>Le prix doit être un nombre entier (sans virgule) au minimum de 1 euro et au maximum de 99 999 999 999&nbsp;euros. Il doit être saisi en chiffres, sans séparation entre les chiffres (ni espaces, ni tirets, ni barres obliques, ni points, par exemple).</li>
                                <li>Le code postal doit être composé exactement de 5 chiffres.</li>
                                <li>Le pays et la ville ne doivent pas comporter plus de 20 caractères.</li>
                                <li>Le titre et la description courte ne doivent pas comporter plus de 255 caractères.</li>
                                <li>L'adresse ne doit pas comporter plus de 50 caractères.</li>
                            </ul>
                </div>";
        }

    // Fin du if(!empty($_POST)) correspondant à si l'admin a rempli le formulaire :
    }



    // Affichage de toutes les annonces pour l'admin
    if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
        //S'il existe une 'action' dans mons URL ET que cette 'action' est égale à 'affichage', alors on affiche la liste des annonces

        //On récupère les annonces en bdd:
        $r = execute_requete(" SELECT * FROM annonce ");

        $content .= '<h2>Liste des annonces</h2>';
        $content .= '<p>Nombre d\'annonces dans la boutique : '. $r->rowCount() .'</p>';

        $content .= '<table border="2" cellpadding="5" >';
            $content .= '<tr>';
                for( $i = 0; $i < $r->columnCount(); $i++ ){

                    $colonne = $r->getColumnMeta( $i );
                        //debug($colonne);
                    $content .= "<th>$colonne[name]</th>";
                }
                $content .= '<th>Suppression</th>';
                $content .= '<th>Modification</th>';
            $content .= '</tr>';

            while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
                $content .= '<tr>';
                    //debug( $ligne );

                    // On affiche les informations et la photo
                    foreach( $ligne as $indice => $valeur ){
                        //Si l'index du tableau '$ligne' est égal à 'photo', on affiche une cellule avec une balise <img>
                        if( $indice == 'photo' ){ 

                            $content .= "<td><img src='$valeur' width='50'></td>";
                        }
                        //Sinon, on affiche juste la valeur
                        else{ 

                            $content .= "<td> $valeur </td>";
                        }
                    }
                    $content .= '<td class="text-center">
                                    <a href="?action=suppression&id_annonce='. $ligne['id_annonce'] .'" onclick="return( confirm(\'En etes vous certain ?\') )">
                                        <i class="far fa-trash-alt"></i>
                                    </a>	
                                </td>';
                    $content .= '<td class="text-center">
                                    <a href="?action=modification&id_annonce='. $ligne['id_annonce'] .'">
                                        <i class="far fa-edit"></i>
                                    </a>	
                                </td>';
                $content .= '</tr>';
            }
        $content .= '</table>';
    }


// Fin du if(adminConnect())
}

elseif( userConnect() ){
    $content .= "<p>C'est un utilisateur qui est connecté</p>";
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
        <a href="?action=affichage">Afficher toutes les annonces</a><br>
        <?= $error; ?>
        <?= $content; ?>

        <?php 
        if( isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification') ) :
        // S'il y a une action dans l'url et qu'elle a pour valeur ajout ou à 'modification', on affiche le formulaire

            if( isset( $_GET['id_annonce']) ){ 
                //S'il existe 'id_annonce' dans l'URL, c'est que c'est une modification

                //récupération des infos à modifier :
                $r = execute_requete(" SELECT * FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
                //exploitation des données :
                $annonce_actuelle = $r->fetch( PDO::FETCH_ASSOC );
            }
        
            //conditions pour vérifier l'existence des variables :
           
            $titre = ( isset($annonce_actuelle['titre']) ) ? $annonce_actuelle['titre'] : '';
            $description_courte = ( isset($annonce_actuelle['description_courte']) ) ? $annonce_actuelle['description_courte'] : '';
            $description_longue = ( isset($annonce_actuelle['description_longue']) ) ? $annonce_actuelle['description_longue'] : '';
            debug($description_longue);
            $prix = ( isset($annonce_actuelle['prix']) ) ? $annonce_actuelle['prix'] : '';
            $pays = ( isset($annonce_actuelle['pays']) ) ? $annonce_actuelle['pays'] : '';
            $ville = ( isset($annonce_actuelle['ville']) ) ? $annonce_actuelle['ville'] : '';
            $adresse = ( isset($annonce_actuelle['adresse']) ) ? $annonce_actuelle['adresse'] : '';
            $cp = ( isset($annonce_actuelle['cp']) ) ? $annonce_actuelle['cp'] : '';
        
            // Gestion des photos
                // Initialisation des emplacements d'affichage des photos chargées
            $add_photo1 = '';
            $add_photo2 = '';
            $add_photo3 = '';
            $add_photo4 = '';
            $add_photo5 = '';

            if( isset( $annonce_actuelle['photo']) ){ 
                //S'il existe $annonce_actuelle['photo'] : c'est qu'on est dans le cadre d'une modification
                // On fait donc une requête pour ouvrir dans la table photo la ligne correspondant aux photos de l'annonce en cours de modification 
                $pdostatement = execute_requete(" SELECT * FROM photo WHERE id_photo IN 
                                                    (SELECT photo_id_photo FROM annonce WHERE id_annonce = '$_GET[id_annonce]') ");
                
                $id_photos_actuelles = $pdostatement->fetch(PDO::FETCH_ASSOC);
                // $id_photos_actuelles = $id_photos_modifiees['id_photo'];

                debug($id_photos_actuelles);

                $add_photo1 .= "<br><p style='font-style: italic;'>Vous pouvez uploader une nouvelle photo.</p>";
    
                $add_photo1 .= "<img src='$annonce_actuelle[photo]' width='80' ><br><br>";
    
                $add_photo1 .= "<input type='hidden' class='form-control' name='photo_actuelle' value='$annonce_actuelle[photo]' >";

                if( isset($id_photos_actuelles['photo2']) ){
                    $add_photo2 .= "<br><p style='font-style: italic;'>Vous pouvez uploader une nouvelle photo.</p>";
    
                    $add_photo2 .= "<img src='$id_photos_actuelles[photo2]' width='80' ><br><br>";
        
                    $add_photo2 .= "<input type='hidden' class='form-control' name='photo_actuelle' value='$id_photos_actuelles[photo2]' >";
                }

                if( isset($id_photos_actuelles['photo2']) ){
                    $add_photo2 .= "<br><p style='font-style: italic;'>Vous pouvez uploader une nouvelle photo.</p>";
    
                    $add_photo2 .= "<img src='$id_photos_actuelles[photo2]' width='80' ><br><br>";
        
                    $add_photo2 .= "<input type='hidden' class='form-control' name='photo_actuelle' value='$id_photos_actuelles[photo2]' >";
                }

                if( isset($id_photos_actuelles['photo3']) ){
                    $add_photo3 .= "<br><p style='font-style: italic;'>Vous pouvez uploader une nouvelle photo.</p>";
    
                    $add_photo3 .= "<img src='$id_photos_actuelles[photo3]' width='80' ><br><br>";
        
                    $add_photo3 .= "<input type='hidden' class='form-control' name='photo_actuelle' value='$id_photos_actuelles[photo3]' >";
                }

                if( isset($id_photos_actuelles['photo4']) ){
                    $add_photo4 .= "<br><p style='font-style: italic;'>Vous pouvez uploader une nouvelle photo.</p>";
    
                    $add_photo4 .= "<img src='$id_photos_actuelles[photo4]' width='80' ><br><br>";
        
                    $add_photo4 .= "<input type='hidden' class='form-control' name='photo_actuelle' value='$id_photos_actuelles[photo4]' >";
                }

                if( isset($id_photos_actuelles['photo5']) ){
                    $add_photo5 .= "<br><p style='font-style: italic;'>Vous pouvez uploader une nouvelle photo.</p>";
    
                    $add_photo5 .= "<img src='$id_photos_actuelles[photo5]' width='80' ><br><br>";
        
                    $add_photo5 .= "<input type='hidden' class='form-control' name='photo_actuelle' value='$id_photos_actuelles[photo5]' >";
                }
            }
    
            

        ?>

        <form method="post" enctype="multipart/form-data">
        
        <label>Titre</label><br>
        <input type="text" name="titre" class="form-control" value="<?= $titre ?>"><br>

        <label>Description courte</label><br>
        <input type="text" name="description_courte" class="form-control" value="<?= $description_courte ?>"><br>

        <label>Description longue</label><br>
        <textarea name="description_longue" id="" cols="30" rows="10" class="form-control"><?= $description_longue ?></textarea><br>

        <label>Prix</label><br>
        <input type="text" name="prix" class="form-control" value="<?= $prix ?>"><br>

        <label>Photo 1</label><br>
        <input type="file" name="photo1">
        <?= $add_photo1; ?><br>

        <label>Photo 2</label><br>
        <input type="file" name="photo2">
        <?= $add_photo2; ?><br>

        <label>Photo 3</label><br>
        <input type="file" name="photo3">
        <?= $add_photo3; ?><br>

        <label>Photo 4</label><br>
        <input type="file" name="photo4">
        <?= $add_photo4; ?><br>
        
        <label>Photo 5</label><br>
        <input type="file" name="photo5">
        <?= $add_photo5; ?><br>
        
        <label>Pays</label><br>
        <input type="text" name="pays" class="form-control" value="<?= $pays ?>"><br>

        <label>Ville</label><br>
        <input type="text" name="ville" class="form-control" value="<?= $ville ?>"><br>

        <label>Adresse</label><br>
        <input type="text" name="adresse" class="form-control" value="<?= $adresse ?>"><br>

        <label>Code postal</label><br>
        <input type="text" name="cp" class="form-control" value="<?= $cp ?>"><br>

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