<?php require_once '../inc/header.inc.php'; ?>
<?php
//Restriction de l'accès à la page administrative : 
if( !adminConnect() ){ //SI l'admin N'EST PAS connecté, on le redirige vers la page de connexion

	header('location:../connexion.php');
	exit();
}

//---------------------------------------------
//Gestion de la SUPPRESSION :
//debug( $_GET );

if( isset( $_GET['action'] ) && $_GET['action'] == 'suppression' ){ 
	
	//S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'suppression'

        //récupération de la colonne 'photo' dans la table 'annonces' à condition que l'id_annonce correponde à l'id passée dans l'URL
        $r = execute_requete(" SELECT photo_id_photo FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
        $id_photo_a_supprimer = $r->fetch(PDO::FETCH_ASSOC);
        $id_photo_a_supprimer = $id_photo_a_supprimer['photo_id_photo'];

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
		
		// debug($chemin_photo1_a_supprimer);
		

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

//---------------------------------------------
//Gestion des produits (INSERTION et MODIFICATION) :
if( !empty( $_POST ) && isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification' ) ){ //SI le formulaire a été validé ET qu'il n'est pas vide

	//debug( $_POST );

	foreach( $_POST as $key => $value ){ //Ici, je passe toutes les informations postées dans les fonctions hmlentities() et addslashes()

		$_POST[$key] = htmlentities( addslashes( $value ) );
	}
	extract($_POST);
	debug($_POST);

	// Vérification de conformité des données
	if( 
		// empty($_FILES['photo1']['name']) || 
	empty($titre) || empty($prix) 
	|| strlen($titre) > 255 || strlen($description_courte) > 255 || strlen($prix) > 11 || strlen($pays) > 20 || strlen($ville) > 20 || strlen($adresse) > 50
	|| ( !preg_match("#^[0-9]{5}$#", $cp) && !empty($cp) )
	|| !preg_match("#^[0-9]{1,11}$#", $prix)
	){
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


	//---------------------------------------------
	//GESTION DE LA PHOTO :
	//debug( $_FILES );
	//debug( $_SERVER );

	elseif( isset( $_GET['action']) && $_GET['action'] == 'modification' ){ //SI je suis dans le cadre d'une modification, je récupère le chemin en bdd (grâce à l'input type="hidden") que je stocke dans la variable $photo1_bdd !

		$photo1_bdd = $_POST['photo1_actuelle'];
		// debug($_POST);
	}
	//---------------------------------------------

	// TRAITEMENT DES PHOTOS
	// INSERTION DE PHOTOS
	if( !empty( $_FILES['photo1']['name'] ) ){ //SI le nom de la photo (dans $_FILES) n'est pas vide, c'est que l'on a uploadé un fichier !
		// On gère la copie des images et leurs chemins d'accès par des fonctions

		$nom_photo1 = name_photo('photo1');
		$photo1_bdd = add_photo_to_bdd($nom_photo1);
		copy_photo($nom_photo1, 'photo1');

		$nom_photo2 = name_photo('photo2');
		$photo2_bdd = add_photo_to_bdd($nom_photo2);
		copy_photo($nom_photo2, 'photo2');

		$nom_photo3 = name_photo('photo3');
		$photo3_bdd = add_photo_to_bdd($nom_photo3);
		copy_photo($nom_photo3, 'photo3');

		$nom_photo4 = name_photo('photo4');
		$photo4_bdd = add_photo_to_bdd($nom_photo4);
		copy_photo($nom_photo4, 'photo4');

		$nom_photo5 = name_photo('photo5');
		$photo5_bdd = add_photo_to_bdd($nom_photo5);
		copy_photo($nom_photo5, 'photo5');

		$photo_dossier = DOSSIER_PHOTO_LOCAL . $nom_photo1;

		// Si les champs de photos 2 à 5 sont vides, on réinitialise le chemin d'accès
		// if(empty($nom_photo2)){ $photo2_bdd = ''; }
		// if(empty($nom_photo3)){ $photo3_bdd = ''; }
		// if(empty($nom_photo4)){ $photo4_bdd = ''; }
		// if(empty($nom_photo5)){ $photo5_bdd = ''; }

		// Insertion dans la table photo
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
		$pdostatement->bindValue(':photo2', $photo2_bdd, PDO::PARAM_STR);
		$pdostatement->bindValue(':photo3', $photo3_bdd, PDO::PARAM_STR);
		$pdostatement->bindValue(':photo4', $photo4_bdd, PDO::PARAM_STR);
		$pdostatement->bindValue(':photo5', $photo5_bdd, PDO::PARAM_STR);

		$pdostatement->execute();

		// Renommage des photos pour qu'elles aient un identifiant en préfixe correspondant à l'id_photo
		$last_id_photo = $pdo->lastInsertId();

		// Chemin local complet (avec nom de fichier sans préfixe) :
		$current_photo1_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo1;
		$current_photo2_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo2;
		$current_photo3_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo3;
		$current_photo4_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo4;
		$current_photo5_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo5;

		// Des fonctions dédiées ajoutent l'indice puis renomment la photo
		$new_photo1_bdd = add_index($nom_photo1, $last_id_photo);
		$new_photo2_bdd = add_index($nom_photo2, $last_id_photo);
		$new_photo3_bdd = add_index($nom_photo3, $last_id_photo);
		$new_photo4_bdd = add_index($nom_photo4, $last_id_photo);
		$new_photo5_bdd = add_index($nom_photo5, $last_id_photo);

		$new_photo1_bdd = rename_photo($nom_photo1, $new_photo1_bdd, $current_photo1_bdd);
		$new_photo2_bdd = rename_photo($nom_photo2, $new_photo2_bdd, $current_photo2_bdd);
		$new_photo3_bdd = rename_photo($nom_photo3, $new_photo3_bdd, $current_photo3_bdd);
		$new_photo4_bdd = rename_photo($nom_photo4, $new_photo4_bdd, $current_photo4_bdd);
		$new_photo5_bdd = rename_photo($nom_photo5, $new_photo5_bdd, $current_photo5_bdd);
		
		// Une fois le remplacement de fichier fait, on met à jour les photos ds les tables annonce et photo
		execute_requete(" UPDATE photo 
							SET photo1 = '$new_photo1_bdd',
								photo2 = '$new_photo2_bdd',
								photo3 = '$new_photo3_bdd',
								photo4 = '$new_photo4_bdd',
								photo5 = '$new_photo5_bdd'
								
							WHERE id_photo = '$last_id_photo'
								");

	}
	/* elseif( isset($_GET['action']) && !$_GET['action'] == 'ajout' ){

		//$photo_bdd =''; //Si pas de message d'erreur, on insèrera du 'vide'
		$error .= '<div class="alert alert-warning">Aucun fichier n\'a été chargé.</div>';
	} */

	//---------------------------------------------
	//INSERTION  ou MODIFICATION d'une annonce :
	if( isset($_GET['action']) && $_GET['action'] == 'modification' ){ //S'il y a une 'action' dans l'URL ET que cette action est égale à 'modification', alors on effectue une requête de modification :

		// Modification dans la table photo
		/* Début modif table photo */

		if( !empty( $_FILES['photo1']['name'] ) ){ //SI le nom de la photo (dans $_FILES) n'est pas vide, c'est que l'on a uploadé un fichier !
			// On gère la copie des images et leurs chemins d'accès par des fonctions
	
			$nom_photo1 = name_photo('photo1');
			$photo1_bdd = add_photo_to_bdd($nom_photo1);
			copy_photo($nom_photo1, 'photo1');
	
			$nom_photo2 = name_photo('photo2');
			$photo2_bdd = add_photo_to_bdd($nom_photo2);
			copy_photo($nom_photo2, 'photo2');
	
			$nom_photo3 = name_photo('photo3');
			$photo3_bdd = add_photo_to_bdd($nom_photo3);
			copy_photo($nom_photo3, 'photo3');
	
			$nom_photo4 = name_photo('photo4');
			$photo4_bdd = add_photo_to_bdd($nom_photo4);
			copy_photo($nom_photo4, 'photo4');
	
			$nom_photo5 = name_photo('photo5');
			$photo5_bdd = add_photo_to_bdd($nom_photo5);
			copy_photo($nom_photo5, 'photo5');
	
			$photo_dossier = DOSSIER_PHOTO_LOCAL . $nom_photo1;
	
			// Si les champs de photos 2 à 5 sont vides, on réinitialise le chemin d'accès
			if(empty($nom_photo2)){ $photo2_bdd = ''; }
			if(empty($nom_photo3)){ $photo3_bdd = ''; }
			if(empty($nom_photo4)){ $photo4_bdd = ''; }
			if(empty($nom_photo5)){ $photo5_bdd = ''; }
	
			// Insertion dans la table photo
			$pdostatement = prepare_requete(" UPDATE photo SET
													photo1 = :photo1,
													photo2 = :photo2,
													photo3 = :photo3,
													photo4 = :photo4,
													photo5 = :photo5
													WHERE id_photo IN
                            						(SELECT photo_id_photo FROM annonce WHERE id_annonce = '$_GET[id_annonce]'
													)        
											");
	
	
			$pdostatement->bindValue(':photo1', $photo1_bdd, PDO::PARAM_STR);
			$pdostatement->bindValue(':photo2', $photo2_bdd, PDO::PARAM_STR);
			$pdostatement->bindValue(':photo3', $photo3_bdd, PDO::PARAM_STR);
			$pdostatement->bindValue(':photo4', $photo4_bdd, PDO::PARAM_STR);
			$pdostatement->bindValue(':photo5', $photo5_bdd, PDO::PARAM_STR);
	
			$pdostatement->execute();
	
			// Renommage des photos pour qu'elles aient un identifiant en préfixe correspondant à l'id_photo
			$last_id_photo = $pdo->lastInsertId();
	
			// Chemin local complet (avec nom de fichier sans préfixe) :
			$current_photo1_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo1;
			$current_photo2_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo2;
			$current_photo3_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo3;
			$current_photo4_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo4;
			$current_photo5_bdd = DOSSIER_PHOTO_LOCAL . $nom_photo5;
	
			// Des fonctions dédiées ajoutent l'indice puis renomment la photo
			$new_photo1_bdd = add_index($nom_photo1, $last_id_photo);
			$new_photo2_bdd = add_index($nom_photo2, $last_id_photo);
			$new_photo3_bdd = add_index($nom_photo3, $last_id_photo);
			$new_photo4_bdd = add_index($nom_photo4, $last_id_photo);
			$new_photo5_bdd = add_index($nom_photo5, $last_id_photo);
	
			$new_photo1_bdd = rename_photo($nom_photo1, $new_photo1_bdd, $current_photo1_bdd);
			$new_photo2_bdd = rename_photo($nom_photo2, $new_photo2_bdd, $current_photo2_bdd);
			$new_photo3_bdd = rename_photo($nom_photo3, $new_photo3_bdd, $current_photo3_bdd);
			$new_photo4_bdd = rename_photo($nom_photo4, $new_photo4_bdd, $current_photo4_bdd);
			$new_photo5_bdd = rename_photo($nom_photo5, $new_photo5_bdd, $current_photo5_bdd);
			
			// Une fois le remplacement de fichier fait, on met à jour les photos ds les tables annonce et photo
			execute_requete(" UPDATE photo 
								SET photo1 = '$new_photo1_bdd',
									photo2 = '$new_photo2_bdd',
									photo3 = '$new_photo3_bdd',
									photo4 = '$new_photo4_bdd',
									photo5 = '$new_photo5_bdd'
									
								WHERE id_photo = '$last_id_photo'
									");
	
			// Ajout de la $new_photo1_bdd à la table annonce
			$pdostatement = prepare_requete(" UPDATE annonce SET 
												photo = '$new_photo1_bdd'
												WHERE id_annonce = '$_GET[id_annonce]'			
			");
			$pdostatement->bindValue(':photo', $new_photo1_bdd, PDO::PARAM_STR);
			$pdostatement->execute();

		}
		/* else{
	
			//$photo_bdd =''; //Si pas de message d'erreur, on insèrera du 'vide'
			$error .= '<div class="alert alert-warning">Aucun fichier n\'a été chargé.</div>';
		} */


		/* Fin modif table photo */

		

		// Modification dans la table annonce
		$pdostatement = prepare_requete(" UPDATE annonce SET 	
                titre = '$titre',
                description_courte = '$description_courte',
                description_longue = '$description_longue',
                prix = '$prix',
                
                pays = '$pays',
                ville = '$ville',
                adresse = '$adresse',
                cp = '$cp'
                WHERE id_annonce = '$_GET[id_annonce]'
							");
							
		

		$pdostatement->bindValue(':titre', $titre, PDO::PARAM_STR);
		$pdostatement->bindValue(':description_courte', $description_courte, PDO::PARAM_STR);
		$pdostatement->bindValue(':description_longue', $description_longue, PDO::PARAM_STR);
		$pdostatement->bindValue(':prix', $prix, PDO::PARAM_STR);
		// $pdostatement->bindValue(':photo', $new_photo1_bdd, PDO::PARAM_STR);
		$pdostatement->bindValue(':pays', $pays, PDO::PARAM_STR);
		$pdostatement->bindValue(':ville', $ville, PDO::PARAM_STR);
		$pdostatement->bindValue(':adresse', $adresse, PDO::PARAM_STR);
		$pdostatement->bindValue(':cp', $cp, PDO::PARAM_STR);

		$pdostatement->execute();
					

		//redirection vers l'affichage :
		header('location:?action=affichage');
		exit();

	}
	elseif( empty( $error) ) { //SI $error est vide, insertion dans la table annonce:
		// debug($_POST);

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
			:categorie_id_categorie, 
			NOW(),
			:photo_id_photo
			)
		");

		// Mettre '$last_id_photo' sur la ligne suivant NOW()


		if( empty($cp) ){
		$cp = NULL;
		}

		$pdostatement->bindValue(':titre', $titre, PDO::PARAM_STR);
		$pdostatement->bindValue(':description_courte', $description_courte, PDO::PARAM_STR);
		$pdostatement->bindValue(':description_longue', $description_longue, PDO::PARAM_STR);
		$pdostatement->bindValue(':prix', $prix, PDO::PARAM_STR);
		$pdostatement->bindValue(':photo', $new_photo1_bdd, PDO::PARAM_STR);
		$pdostatement->bindValue(':pays', $pays, PDO::PARAM_STR);
		$pdostatement->bindValue(':ville', $ville, PDO::PARAM_STR);
		$pdostatement->bindValue(':adresse', $adresse, PDO::PARAM_STR);
		$pdostatement->bindValue(':cp', $cp, PDO::PARAM_STR);
		$pdostatement->bindValue(':membre_id_membre', $membre_id_membre, PDO::PARAM_STR);
		$pdostatement->bindValue(':categorie_id_categorie', $categorie_id_categorie, PDO::PARAM_STR);
		$pdostatement->bindValue(':photo_id_photo', $last_id_photo, PDO::PARAM_STR);
		$pdostatement->execute();

		//redirection vers l'affichage :
		// header('location:?action=affichage');
		// exit();

	}
}

//---------------------------------------------

//Affichage des annonces :
if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
	//S'il existe une 'action' dans mons URL ET que cette 'action' est égale à 'affichage', alors on affiche la liste des annonces

	// On prépare une variable qui ajoutera dans la requête l'id de la catégorie choisie en select s'il y en a une (cf la fonction add_AND_in_request dans fonction.inc.php)
	$search_categorie = '';
	if( isset($_POST['categorie_id_categorie']) ){
		$search_categorie = add_AND_in_request($_POST['categorie_id_categorie'], 'categorie_id_categorie');
	}

	
	// debug($search_categorie);

	// On prépare dans le select les catégories :
	$pdostatement = execute_requete(" SELECT id_categorie, titreCat FROM categorie ");

	$list_id_categorie = '<option value="0"></option>';
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

	//On récupère les annonces en bdd:
	// Si une catégorie a été cliquée et figure dans GET :
	if( isset($_GET['id_categorie']) && empty($_POST) ){
		$r = execute_requete(" SELECT a.id_annonce, a.titre, a.description_courte, a.prix, a.photo, a.pays, a.ville, a.adresse, a.cp, 
		m.pseudo, c.titreCat, a.date_enregistrement, c.id_categorie, m.id_membre 
		FROM annonce a, membre m, categorie c
		WHERE 1 = 1 $search_categorie 
		AND a.membre_id_membre = m.id_membre 
		AND a.categorie_id_categorie = c.id_categorie
		AND c.id_categorie = '$_GET[id_categorie]'
		ORDER BY a.id_annonce DESC");
		// debug($_GET['id_categorie']);
	} 
	else{
		$r = execute_requete(" SELECT a.id_annonce, a.titre, a.description_courte, a.prix, a.photo, a.pays, a.ville, a.adresse, a.cp, 
		m.pseudo, c.titreCat, a.date_enregistrement, c.id_categorie, m.id_membre 
		FROM annonce a, membre m, categorie c
		WHERE 1 = 1 $search_categorie 
		AND a.membre_id_membre = m.id_membre 
		AND a.categorie_id_categorie = c.id_categorie 
		ORDER BY a.id_annonce DESC");
	}
	
	

	// $r = execute_requete(" SELECT a.id_annonce, a.titre, a.description_courte, a.prix, a.photo, a.pays, a.ville, a.adresse, a.cp, 
	// m.pseudo, c.titreCat, a.date_enregistrement, c.id_categorie, m.id_membre 
	// FROM annonce a, membre m, categorie c
	// WHERE 1 = 1 $search_categorie 
	// AND a.membre_id_membre = m.id_membre 
	// AND a.categorie_id_categorie = c.id_categorie 
	// ORDER BY a.id_annonce DESC");

	$content .= '<h2>Liste des annonces</h2>';
	$content .= '<p>Nombre d\'annonces en ligne : '. $r->rowCount() .'</p>';
	// On affiche la liste des catégories
	$content .= '<form action="" method="post">';
	$content .= '<label>Catégorie</label><br>';
	$content .= '<select name="categorie_id_categorie" id="" class="form-control">';
	$content .= $list_id_categorie . '</select>';
	$content .=  '<input type="submit" value="Valider">';
	$content .= '</form><br>';

	// On affiche le tableau des annonces
	$content .= '<table border="2" cellpadding="5" id="table_id">';
		$content .= '<thead><tr>';
			for( $i = 0; $i < $r->columnCount(); $i++ ){
				$colonne = $r->getColumnMeta( $i );
				// debug($colonne['name']);
				if($colonne['name'] == 'pseudo'){
					$content .= "<th>Membre auteur</th>";
				}
				elseif($colonne['name'] == 'titreCat'){
					$content .= "<th>Catégorie</th>";
				}
				elseif($colonne['name'] == 'description_courte'){
					$content .= "<th>Description</th>";
				}
				elseif($colonne['name'] == 'date_enregistrement'){
					$content .= "<th>Date</th>";
				}
				elseif($colonne['name'] == 'id_annonce'){
					$content .= "<th>ID</th>";
				}
				elseif( ($colonne['name'] == 'id_membre') || ($colonne['name'] == 'id_categorie')){
					// $content .= "<th>Non</th>";
				}
				else{
					
					//debug($colonne);
					$content .= "<th>" . ucfirst($colonne['name']) . "</th>";
				}
			}
			$content .= '<th>Actions</th>';
			// $content .= '<th>Modification</th>';
		$content .= '</tr></thead>';

		while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
			foreach($ligne as $indice => $valeur){
				$ligne[$indice] = stripcslashes($valeur);
			}
			$content .= '<tbody><tr>';
				//debug( $ligne );

				// On affiche les informations et la photo
				foreach( $ligne as $indice => $valeur ){
					// debug($ligne);
					//Si l'index du tableau '$ligne' est égal à 'photo', on affiche une cellule avec une balise <img>
					// debug($indice);
					if( $indice == 'photo' ){ 
						$content .= "<td><img src='$valeur' width='50'></td>";
					}
					elseif( $indice == 'pseudo' ){ 
						$content .= "<td> <a href='../profil.php?action=affichage&id_membre=" .$ligne['id_membre'] . "'>" . $valeur . "</a></td>";
					}			
					elseif( ($indice == 'id_membre') || ($indice == 'id_categorie') ){ 
						// $content .= '<td>oui</td>';
					}			
					
					elseif( $indice == 'titreCat' ){
						$content .= "<td> <a href='?action=affichage&id_categorie=" .$ligne['id_categorie'] . "'>" . $valeur . "</a></td>";
					}
					//Sinon, on affiche juste la valeur
					else{ 

						$content .= "<td> $valeur </td>";
					}
				}
				$content .= '<td class="text-center">
								<a href="?action=suppression&id_annonce='. $ligne['id_annonce'] .'" onclick="return( confirm(\'En etes vous certain ?\') )" title="Supprimer">
									<i class="far fa-trash-alt"></i>
								</a> 
								<a href="?action=modification&id_annonce='. $ligne['id_annonce'] .'" title="Modifier">
									<i class="far fa-edit"></i>
								</a><br>
								<a href="../fiche_annonce.php?id_annonce='. $ligne['id_annonce'] .'" title="Afficher">
									<i class="fas fa-search"></i>
								</a>	
							</td>';
			$content .= '</tr></tbody>';
		}
	$content .= '</table>';
}

//----------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------
?>

<!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

<title>Admin - Gestion des annonces | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "../inc/nav.inc.php"; ?>

    <!-- <main class="container"> -->
<h1>Admin - Gestion des annonces </h1>

<a href="?action=ajout">Ajout d'une annonce</a><br>
<a href="?action=affichage">Affichage de toutes les annonces</a><hr>

<?= $error; ?>
<?= $content; ?>

<?php if( isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')  ) : //S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'ajout' OU à 'modification', alors on affiche le formulaire 

	/* DEBUT AJOUT */

	if( isset( $_GET['id_annonce']) ){ 
		//S'il existe 'id_annonce' dans l'URL, c'est que c'est une modification

		//récupération des infos à modifier :
		$r = execute_requete(" SELECT * FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
		//exploitation des données :
		$annonce_actuelle = $r->fetch( PDO::FETCH_ASSOC );

		

		
	}

	//conditions pour vérifier l'existence des variables :
   
	$titre_value = ( isset($annonce_actuelle['titre']) ) ? $annonce_actuelle['titre'] : '';
	$description_courte_value = ( isset($annonce_actuelle['description_courte']) ) ? $annonce_actuelle['description_courte'] : '';
	$description_longue_value = ( isset($annonce_actuelle['description_longue']) ) ? $annonce_actuelle['description_longue'] : '';
	$prix_value = ( isset($annonce_actuelle['prix']) ) ? $annonce_actuelle['prix'] : '';
	$pays_value = ( isset($annonce_actuelle['pays']) ) ? $annonce_actuelle['pays'] : '';
	$ville_value = ( isset($annonce_actuelle['ville']) ) ? $annonce_actuelle['ville'] : '';
	$adresse_value = ( isset($annonce_actuelle['adresse']) ) ? $annonce_actuelle['adresse'] : '';
	$cp_value = ( isset($annonce_actuelle['cp']) ) ? $annonce_actuelle['cp'] : '';

	// Gestion des photos
		// Initialisation des emplacements d'affichage des photos chargées
	$add_photo1 = '';
	$add_photo2 = '';
	$add_photo3 = '';
	$add_photo4 = '';
	$add_photo5 = '';

	/* if( isset( $annonce_actuelle['photo']) ){ 
		//S'il existe $annonce_actuelle['photo'] : c'est qu'on est dans le cadre d'une modification
		// On fait donc une requête pour ouvrir dans la table photo la ligne correspondant aux photos de l'annonce en cours de modification 
		$pdostatement = execute_requete(" SELECT * FROM photo WHERE id_photo IN 
											(SELECT photo_id_photo FROM annonce WHERE id_annonce = '$_GET[id_annonce]') ");
		
		$id_photos_actuelles = $pdostatement->fetch(PDO::FETCH_ASSOC);
		// $id_photos_actuelles = $id_photos_modifiees['id_photo'];

		if( isset($id_photos_actuelles['photo1']) ){
			$add_photo1 .= "<br><p style='font-style: italic;'>Vous pouvez uploader une nouvelle photo.</p>";
			$add_photo1 .= "<img src='$annonce_actuelle[photo]' width='80' ><br><br>";
			$add_photo1 .= "<input type='hidden' class='form-control' name='photo_actuelle' value='$annonce_actuelle[photo]' >";
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
	} */

	/* FIN AJOUT */

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
    $pdostatement = execute_requete(" SELECT id_categorie, titreCat FROM categorie ");

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

?>

<form method="post" enctype="multipart/form-data">
        
	<label>Titre</label><br>
	<input type="text" name="titre" class="form-control" value="<?= $titre_value ?>"><br>
	<!-- value="<?= $titre_value ?>" -->

	<label>Description courte</label><br>
	<input type="text" name="description_courte" class="form-control" value="<?= $description_courte_value ?>"><br>
	<!-- value="<?= $description_courte_value ?>" -->

	<label>Description longue</label><br>
	<textarea name="description_longue" id="" cols="30" rows="10" class="form-control"><?= $description_longue_value ?></textarea><br>
	<!-- <?= $description_longue_value ?> -->

	<label>Prix</label><br>
	<input type="text" name="prix" class="form-control" value="<?= $prix_value ?>"><br>
	<!-- value="<?= $prix_value ?>" -->

	<label>Photo 1</label><br>
	<input type="file" name="photo1">
	<?= $add_photo1; ?>
	<?php

		if( isset( $annonce_actuelle['photo']) ){ //S'il existe $article_actuel['photo'] : c'est que je suis dans le cadre d'une modification

			echo "<i>Vous pouvez uploader une nouvelle photo</i>";
			echo "<img src='$annonce_actuelle[photo]' width='80' ><br><br>";
			echo "<input type='hidden' class='form-control' name='photo1_actuelle' value='$annonce_actuelle[photo]' >";
		}

	?>		
	<br>

	<label>Photo 2</label><br>
	<input type="file" name="photo2">
	<?= $add_photo2; ?>
	<br>

	<label>Photo 3</label><br>
	<input type="file" name="photo3">
	<?= $add_photo3; ?>
	<br>

	<label>Photo 4</label><br>
	<input type="file" name="photo4">
	<?= $add_photo4; ?>
	<br>
	
	<label>Photo 5</label><br>
	<input type="file" name="photo5">
	<?= $add_photo5; ?>
	<br>
	
	<label>Pays</label><br>
	<input type="text" name="pays" class="form-control" value="<?= $pays_value ?>"><br>
	<!-- value="<?= $pays_value ?>" -->

	<label>Ville</label><br>
	<input type="text" name="ville" class="form-control" value="<?= $ville_value ?>"><br>
	<!-- value="<?= $ville_value ?>" -->

	<label>Adresse</label><br>
	<input type="text" name="adresse" class="form-control" value="<?= $adresse_value ?>"><br>
	<!-- value="<?= $adresse_value ?>" -->

	<label>Code postal</label><br>
	<input type="text" name="cp" class="form-control" value="<?= $cp_value ?>"><br>
	<!-- value="<?= $cp_value ?>" -->

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

<?php require_once '../inc/footer.inc.php'; ?>