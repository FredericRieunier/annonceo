<?php require_once "inc/header.inc.php"; ?>
<!-- Page 15 dans le CDC -->

<?php
if(!userConnect()){
    header('location:connexion.php');  
    exit();  
}

/* Affichage des différents profils pour l'admin s'il a cliqué sur la loupe dans gestion_membres */
if( adminConnect()){
    if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
        $pdostatement = execute_requete(" SELECT * FROM membre WHERE id_membre = '$_GET[id_membre]' ");
        $tab_infos_membre = $pdostatement->fetch(PDO::FETCH_ASSOC);
        // debug($tab_infos_membre);
        extract($tab_infos_membre);

        $content .= "<h2>Informations personnelles du membre</h2>
        <p><strong>ID&nbsp;:</strong> $id_membre</p>
        <p><strong>Pseudo&nbsp;:</strong> $pseudo</p>
        <p><strong>Prénom&nbsp;: </strong> $prenom</p>
        <p><strong>Nom&nbsp;: </strong> $nom</p>
        <p><strong>Téléphone&nbsp;: </strong> $telephone</p>
        <p><strong>E-mail&nbsp;: </strong> $email</p>
        <p><strong>Civilité&nbsp;: </strong>" . civilite() . "</p>";
    }

    /* Fin de l'affichage des différents profils pour l'admin */

    // Ajout au profil de l'admin s'il a juste cliqué sur profil :

    if( !$_GET ){
        $content .= '<h2 style="color: darkred;">Administrateur</h2>';
    }

}

if( !$_GET ){

    // Pour être sûr que les infos affichées dans le profil soient à jour (en cas de modification du profil), on réactualise la session.
    $id_membre = $_SESSION['membre']['id_membre'];
    $r = execute_requete(" SELECT * FROM membre WHERE id_membre = '$id_membre' ");
    $membre = $r->fetch(PDO::FETCH_ASSOC);
    foreach( $membre as $index => $valeur ){

        $_SESSION['membre'][$index] = $valeur;
    }


    extract($_SESSION['membre']);

    $content .= "<h2>Vos informations personnelles</h2>
            <p><strong>Pseudo&nbsp;:</strong> $pseudo</p>
            <p><strong>Prénom&nbsp;: </strong> $prenom</p>
            <p><strong>Nom&nbsp;: </strong> $nom</p>
            <p><strong>Téléphone&nbsp;: </strong> $telephone</p>
            <p><strong>E-mail&nbsp;: </strong> $email</p>
            <p><strong>Civilité&nbsp;: </strong>" . civilite() . "</p>";


    // Affichage de la liste des annonces publiées par le membre

	//On récupère les annonces en bdd:
	$r = execute_requete(" SELECT a.id_annonce, a.titre, a.description_courte, a.description_longue, a.prix, a.photo, a.pays, a.ville, a.adresse, a.cp, 
	c.titreCat, a.date_enregistrement 
	FROM annonce a, membre m, categorie c
	WHERE 1 = 1
    AND a.membre_id_membre = $id_membre
	AND a.membre_id_membre = m.id_membre 
	AND a.categorie_id_categorie = c.id_categorie 
	ORDER BY a.id_annonce DESC");

	$content .= '<h2>Liste de vos annonces</h2>';
	$content .= '<p>Vous avez publié '. $r->rowCount() .' annonce(s).</p>';

	// On affiche le tableau des annonces
	$content .= '<table border="2" cellpadding="5" id="table_id">';
		$content .= '<thead><tr>';
			for( $i = 0; $i < $r->columnCount(); $i++ ){
				$colonne = $r->getColumnMeta( $i );
				// debug($colonne['name']);
				if($colonne['name'] == 'membre_id_membre'){
					$content .= "<th>Membre auteur</th>";
                }
                elseif($colonne['name'] == 'id_annonce'){
					$content .= "";
				}
				elseif($colonne['name'] == 'titreCat'){
					$content .= "<th>Catégorie</th>";
				}
				elseif($colonne['name'] == 'photo_id_photo'){
					$content .= "<th>ID photo</th>";
				}
				else{
					
					//debug($colonne);
					$content .= "<th>$colonne[name]</th>";
				}
			}
			$content .= '<th>Actions</th>';
			// $content .= '<th>Modification</th>';
		$content .= '</tr></thead>';

		while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
			$content .= '<tbody><tr>';
				//debug( $ligne );

				// On affiche les informations et la photo
				foreach( $ligne as $indice => $valeur ){
					//Si l'index du tableau '$ligne' est égal à 'photo', on affiche une cellule avec une balise <img>
					if( $indice == 'photo' ){ 
						$content .= "<td><img src='$valeur' width='50'></td>";
                    }
                    elseif($indice == 'id_annonce'){
                        $content .= "";
                    }
					
					//Sinon, on affiche juste la valeur
					else{ 

						$content .= "<td> $valeur </td>";
					}
				}
				$content .= '<td class="text-center">
								<a href="deposer_annonce.php?action=suppression&id_annonce='. $ligne['id_annonce'] .'" onclick="return( confirm(\'En etes vous certain ?\') )" title="Supprimer">
									<i class="far fa-trash-alt"></i>
								</a> 
								<a href="deposer_annonce.php?action=modification&id_annonce='. $ligne['id_annonce'] .'" title="Modifier">
									<i class="far fa-edit"></i>
								</a><br>
								<a href="fiche_annonce.php?id_annonce='. $ligne['id_annonce'] .'">
									<i class="fas fa-search"></i>
								</a>	
							</td>';
			$content .= '</tr></tbody>';
		}
    $content .= '</table>';
    
}

?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Profil | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "inc/nav.inc.php"; ?>

    <!-- <main class="container"> -->
        <h1>Profil</h1>
        <?= $error; ?>
        <?= $content; ?>
        
      
       






    <?php require_once "inc/footer.inc.php"; ?>