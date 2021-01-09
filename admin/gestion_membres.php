<?php require_once "../inc/header.inc.php"; ?>

<?php
//Restriction de l'accès
if( !adminConnect() ){
	header('location:../connexion.php');
	exit();
}

//Gestion de la SUPPRESSION :

if( isset( $_GET['action'] ) && $_GET['action'] == 'suppression' ){ 
	
	/* execute_requete(" DELETE FROM photo, annonce, membre, note, commentaire 
						WHERE annonce.membre_id_membre = '$_GET[id_membre]'
						AND membre.id_membre = '$_GET[id_membre]'
						AND annonce.photo_id_photo = photo.id_photo
						AND note.membre_id_membre2 = '$_GET[id_membre]'
						AND note.membre_id_membre1 = '$_GET[id_membre]'
						AND commentaire.membre_id_membre = '$_GET[id_membre]'											
	"); */

	// On récupère les id_photo photos déposées par membre en bdd
	$r = execute_requete(" SELECT p.id_photo 
						FROM photo p, annonce a
						WHERE a.membre_id_membre = '$_GET[id_membre]'
						AND a.photo_id_photo = p.id_photo	
	");

	// Supprimer photo physiquement

	while( $photo_membre = $r->fetch(PDO::FETCH_ASSOC) ){
		execute_requete(" DELETE FROM photo
						WHERE id_photo = '$photo_membre[id_photo]'								
		");

	}
	
	execute_requete(" DELETE FROM membre
						WHERE membre.id_membre = '$_GET[id_membre]'									
	");

	// execute_requete(" DELETE FROM annonce WHERE membre_id_membre = '$_GET[id_membre]' ");
    // execute_requete(" DELETE FROM membre WHERE id_membre = '$_GET[id_membre]' ");
    header('location:?action=affichage');
    exit();
}

//---------------------------------------------
//MODIFICATION (pas d'insertion, les membres s'inscrivent sur inscription.php) :
if( !empty( $_POST ) ){

	foreach( $_POST as $key => $value ){
		$_POST[$key] = htmlentities( addslashes( $value ) );
	}

	if( isset($_GET['action']) && $_GET['action'] == 'modification' ){

		$pdostatement = prepare_requete(" UPDATE membre SET 
               pseudo = :pseudo,
               nom = :nom,
               prenom = :prenom,
               telephone = :telephone,
               email = :email,
               civilite = :civilite,
			   statut = :statut
               WHERE id_membre = '$_GET[id_membre]'
                ");
        
        
               $pdostatement->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
               $pdostatement->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
               $pdostatement->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
               $pdostatement->bindValue(':telephone', $_POST['telephone'], PDO::PARAM_STR);
               $pdostatement->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
			   $pdostatement->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
			   $pdostatement->bindValue(':statut', $_POST['statut'], PDO::PARAM_STR);
        
			   $pdostatement->execute();

		//redirection vers l'affichage :
        header('location:?action=affichage');
        exit();

	}
}

if( !isset($_GET['action']) && empty($_POST) ){
	header('location:?action=affichage');
}

//Affichage des membres :
if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
	//S'il existe une 'action' dans mons URL ET que cette 'action' est égale à 'affichage', alors on affiche la liste des membres ;

	//Je récupère les membre en bdd:
	$r = execute_requete(" SELECT * FROM membre ");

	$content .= '<h2>Liste des membres</h2>';
	$content .= '<p>Nombre de membres répertoriés : '. $r->rowCount() .'</p>';

	$content .= '<table border="2" cellpadding="5" >';
		$content .= '<tr>';
			for( $i = 0; $i < $r->columnCount(); $i++ ){

				$colonne = $r->getColumnMeta( $i );
					//debug($colonne);
				if($colonne['name'] != 'mdp'){
					$content .= "<th>$colonne[name]</th>";
				}
			}
			$content .= '<th>Voir le profil</th>';
			$content .= '<th>Suppression</th>';
			$content .= '<th>Modification</th>';
		$content .= '</tr>';

		while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
			$content .= '<tr>';

				foreach( $ligne as $indice => $valeur ){

					if($indice != 'mdp'){
						$content .= "<td> $valeur </td>";
					}
				}
				$content .= '<td class="text-center">
								<a href="../profil.php?action=affichage&id_membre='. $ligne['id_membre'] .'" title="Afficher">
									<i class="fas fa-search"></i>
								</a>	
							</td>';
				$content .= '<td class="text-center">
								<a href="?action=suppression&id_membre='. $ligne['id_membre'] .'" onclick="return( confirm(\'En êtes-vous certain ?\') )" title="Supprimer">
									<i class="far fa-trash-alt"></i>
								</a>	
							</td>';
				$content .= '<td class="text-center">
								<a href="?action=modification&id_membre='. $ligne['id_membre'] .'" title="Modifier">
									<i class="far fa-edit"></i>
								</a>	
							</td>';
			$content .= '</tr>';
		}
	$content .= '</table>';
}

?>


    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Admin - Gestion des membres | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "../inc/nav.inc.php"; ?>

    <!-- <main class="container"> -->
        <h1>Admin - Gestion des membres</h1>
        <a href="?action=affichage">Afficher tous les membres</a><hr>
        <?= $error; ?>
        <?= $content; ?>


        <?php 
if( isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')  ) : //S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'ajout' OU à 'modification', alors on affiche le formulaire 

    if( isset( $_GET['id_membre']) ){ //S'il existe 'id_membre' dans l'URL, c'est que je suis dans le cadre d'une modification

        //récupération des infos à modifier :
        $r = execute_requete(" SELECT * FROM membre WHERE id_membre = '$_GET[id_membre]' ");
        //exploitation des données :
        $membre_actuel = $r->fetch( PDO::FETCH_ASSOC );
            // debug( $membre_actuel );
	}

    //conditions pour vérifier l'existence des variables :
    $pseudo = ( isset($membre_actuel['pseudo']) ) ? $membre_actuel['pseudo'] : '';
	$nom = ( isset($membre_actuel['nom']) ) ? $membre_actuel['nom'] : '';
	$prenom = ( isset($membre_actuel['prenom']) ) ? $membre_actuel['prenom'] : '';
	$telephone = ( isset($membre_actuel['telephone']) ) ? $membre_actuel['telephone'] : '';
	$email = ( isset($membre_actuel['email']) ) ? $membre_actuel['email'] : '';
	$civilite = ( isset($membre_actuel['civilite']) ) ? $membre_actuel['civilite'] : '';
	$statut = ( isset($membre_actuel['statut']) ) ? $membre_actuel['statut'] : '';

	$homme = '';
	$femme = '';
	if($membre_actuel['civilite'] == 'm'){
		$homme = "checked";
	}
	else{
		$femme = "checked";
	}

	$utilisateur = '';
	$admin = '';
	if($membre_actuel['statut'] == 0){
		$utilisateur = "selected";
	}
	else{
		$admin = "selected";
	}

    

?>


<form method="post">


<label>Pseudo</label>
<input type="text" name="pseudo" class="form-control" value="<?= $pseudo; ?>"><br>

<label>Nom</label>
<input type="text" name="nom" class="form-control" value="<?= $nom; ?>"><br>

<label>Prénom</label>
<input type="text" name="prenom" class="form-control"value="<?= $prenom; ?>"><br>

<label>Téléphone</label>
<input type="text" name="telephone" class="form-control" value="<?= $telephone; ?>"><br>

<label>E-mail</label>
<input type="text" name="email" class="form-control" value="<?= $email; ?>"><br>

<label>Civilité</label><br>
<input type="radio" name="civilite" value="f" <?= $femme?> > Femme<br>
<input type="radio" name="civilite" value="m" <?= $homme ?> > Homme<br>

<label>Statut</label>
<select name="statut" id="" class="form-control">
		<option value="0" <?= $utilisateur ?> >Utilisateur</option>
		<option value="1" <?= $admin ?> >Admin</option>
</select><br><br>


<input type="submit" value="<?= ucfirst($_GET['action']) ?>" class="btn btn-secondary">

</form>

<?php 
endif; 
?>


    <?php require_once "../inc/footer.inc.php"; ?>