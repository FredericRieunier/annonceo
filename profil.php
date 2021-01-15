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
		foreach($tab_infos_membre as $indice => $valeur){
			$tab_infos_membre[$indice] = stripcslashes($valeur);
		}
		
		extract($tab_infos_membre);
		
		// Calcul de la note moyenne du vendeur de l'annonce affichée
		$r = execute_requete(" SELECT ROUND(AVG(n.note), 1) 
		FROM note n, annonce a, membre m
		WHERE a.membre_id_membre = m.id_membre
		AND m.id_membre = n.membre_id_membre1
		AND m.id_membre = '$_GET[id_membre]'
		");
		$requete_note_moyenne = $r->fetch(PDO::FETCH_ASSOC);
		$note_moyenne = $requete_note_moyenne['ROUND(AVG(n.note), 1)'];
		// debug($note_moyenne);

        $content .= "<h2>Informations personnelles du membre</h2>
		<p><strong>ID&nbsp;:</strong> $id_membre</p>
		<p><strong>Note moyenne&nbsp;:</strong> $note_moyenne/5</p>
        <p><strong>Pseudo&nbsp;:</strong> $pseudo</p>
        <p><strong>Prénom&nbsp;: </strong> $prenom</p>
        <p><strong>Nom&nbsp;: </strong> $nom</p>
        <p><strong>Téléphone&nbsp;: </strong> $telephone</p>
        <p><strong>E-mail&nbsp;: </strong> $email</p>
		<p><strong>Civilité&nbsp;: </strong>" . civilite() . "</p>";

		$r = execute_requete(" SELECT avis FROM note WHERE membre_id_membre1 = $id_membre ");
		if($r->rowCount() >= 1){
			$content .= "<p><strong>Avis sur le membre&nbsp;: </strong></p>";
			while($avis_sur_le_membre = $r->fetch(PDO::FETCH_ASSOC)){
				$content .= "<p>$avis_sur_le_membre[avis]</p>";
	
			}
		}
		
		
    }

    /* Fin de l'affichage des différents profils pour l'admin */

    // Ajout au profil de l'admin la mention "Administrateur" s'il a juste cliqué sur profil :
    if( !$_GET ){
        $content .= '<h2 style="color: darkred;">Administrateur</h2>';
	}
	elseif( !empty($_GET) && isset($_GET['id_membre']) ){
		// Affichage de la liste des annonces publiées par le membre

		//On récupère les annonces en bdd:
		$r = execute_requete(" SELECT a.id_annonce, a.titre, a.description_courte, a.description_longue, a.prix, a.photo, a.pays, a.ville, a.adresse, a.cp, 
		c.titreCat, a.date_enregistrement 
		FROM annonce a, membre m, categorie c
		WHERE 1 = 1
		AND a.membre_id_membre = '$_GET[id_membre]'
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

						list($date, $time) = explode(" ", $ligne['date_enregistrement']);
						list($year, $month, $day) = explode("-", $date);
						$date_enregistrement = "$day/$month/$year";
						//Si l'index du tableau '$ligne' est égal à 'photo', on affiche une cellule avec une balise <img>
						$valeur = stripcslashes($valeur);
						// debug($valeur);
						
						if( $indice == 'photo' ){ 
							$content .= "<td><img src='$valeur' width='50' alt='" . $ligne['titre'] . " - Annonceo'></td>";
						}
						elseif($indice == 'id_annonce'){
							$content .= "";
						}
						elseif($indice == 'date_enregistrement'){

							$content .= "<td>$date_enregistrement</td>";
						}
						
						
						//Sinon, on affiche juste la valeur
						else{ 

							$content .= "<td> $valeur </td>";
						}
					}
					$content .= '<td class="text-center">
									<a href="deposer_annonce.php?action=suppression&id_annonce='. $ligne['id_annonce'] .'" onclick="return( confirm(\'En êtes-vous certain ?\') )" title="Supprimer">
										<i class="far fa-trash-alt"></i>
									</a> 
									<a href="deposer_annonce.php?action=modification&id_annonce='. $ligne['id_annonce'] .'" title="Modifier">
										<i class="far fa-edit"></i>
									</a><br>
									<a href="fiche_annonce.php?id_annonce='. $ligne['id_annonce'] .'" title="Afficher">
										<i class="fas fa-search"></i>
									</a>	
								</td>';
				$content .= '</tr></tbody>';
			}
		$content .= '</table>';
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
	
	// Calcul de la note moyenne du vendeur de l'annonce affichée
	$r = execute_requete(" SELECT ROUND(AVG(n.note), 1) 
	FROM note n, annonce a, membre m
	WHERE a.membre_id_membre = m.id_membre
	AND m.id_membre = n.membre_id_membre1
	AND m.id_membre = '$id_membre'
	");
	$requete_note_moyenne = $r->fetch(PDO::FETCH_ASSOC);
	$note_moyenne = $requete_note_moyenne['ROUND(AVG(n.note), 1)'];

    $content .= "<h2>Vos informations personnelles</h2>
			<p><strong>Pseudo&nbsp;:</strong> $pseudo</p>
			<p><strong>Note moyenne&nbsp;:</strong> $note_moyenne/5</p>
            <p><strong>Prénom&nbsp;: </strong> $prenom</p>
            <p><strong>Nom&nbsp;: </strong> $nom</p>
            <p><strong>Téléphone&nbsp;: </strong> $telephone</p>
            <p><strong>E-mail&nbsp;: </strong> $email</p>
			<p><strong>Civilité&nbsp;: </strong>" . civilite() . "</p>";
			
	// Affichage des avis
	$id_membre_en_ligne = $_SESSION['membre']['id_membre'];

	$r = execute_requete(" SELECT avis FROM note WHERE membre_id_membre1 = $id_membre_en_ligne ");
	if($r->rowCount() >= 1){
		$content .= "<p><strong>Avis sur le membre&nbsp;: </strong></p>";
		while($avis_sur_le_membre = $r->fetch(PDO::FETCH_ASSOC)){
			$content .= "<p>$avis_sur_le_membre[avis]</p>";

		}
	}


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
				// debug($date_enregistrement);
				
				foreach( $ligne as $indice => $valeur ){
					
					//Si l'index du tableau '$ligne' est égal à 'photo', on affiche une cellule avec une balise <img>
					if( $indice == 'photo' ){ 
						$content .= "<td><img src='$valeur' width='50' alt='" . $ligne['titre'] . " - Annonceo'></td>";
                    }
                    elseif($indice == 'id_annonce'){
                        $content .= "";
					}
					
					elseif($indice == 'date_enregistrement'){
						// On met la date à un format correct
						list($date, $time) = explode(" ", $valeur);
						list($year, $month, $day) = explode("-", $date);
						$date_enregistrement = "$day/$month/$year";
                        $content .= "<td>$date_enregistrement</td>";
                    }
					//Sinon, on affiche juste la valeur
					else{ 

						$content .= "<td>" . stripcslashes($valeur) . "</td>";
					}
				}
				$content .= '<td class="text-center">
								<a href="deposer_annonce.php?action=suppression&id_annonce='. $ligne['id_annonce'] .'" onclick="return( confirm(\'En êtes-vous certain ?\') )" title="Supprimer">
									<i class="far fa-trash-alt"></i>
								</a> 
								<a href="deposer_annonce.php?action=modification&id_annonce='. $ligne['id_annonce'] .'" title="Modifier">
									<i class="far fa-edit"></i>
								</a><br>
								<a href="fiche_annonce.php?id_annonce='. $ligne['id_annonce'] .'" title="Afficher">
									<i class="fas fa-search"></i>
								</a>	
							</td>';
			$content .= '</tr></tbody>';
		}
	$content .= '</table>';

	// Affichage des commentaires auxquels le membre connecté n'a pas répondu.

	// On récupère son id
	$id_membre_en_ligne = $_SESSION['membre']['id_membre'];
	// debug($id_membre_en_ligne);

	// On voit d'abord la date du dernier message posté par le membre en ligne sur une de ses annonces
	$s = execute_requete(" SELECT c.date_enregistrement AS date_commentaire
		FROM commentaire c, annonce a
		WHERE c.membre_id_membre = '$id_membre_en_ligne'
		AND c.annonce_id_annonce = a.id_annonce
		AND a.membre_id_membre = c.membre_id_membre
		ORDER BY date_commentaire DESC LIMIT 1
	");
	// Notice: Trying to access array offset on value of type bool in C:\MAMP\htdocs\PHP\00-Annonceo_V1.0\profil.php on line 268

	// echo 'coucou';
	// if( 1= 1 ){
		// S'il y a au moins une annonce, on recherche les commentaires des annonces dont il est l'auteur publiés après son dernier message
		// echo 'coucou';
		
		$date_dernier_commentaire_membre_en_ligne = $s->fetch(PDO::FETCH_ASSOC)['date_commentaire'];
		// debug($date_dernier_commentaire_membre_en_ligne);

		// On cherche les commentaires des autres membres postés après son dernier message
		$r = execute_requete(" SELECT DISTINCT commentaire
		FROM commentaire c, annonce a
		WHERE c.membre_id_membre != '$id_membre_en_ligne'
		AND c.date_enregistrement > '$date_dernier_commentaire_membre_en_ligne'
		AND a.membre_id_membre = '$id_membre_en_ligne'
		AND a.id_annonce = c.annonce_id_annonce
		");

		if( $r->rowCount() >=1 ){
			

			// L'affichage
			$content .= '<br><table border="2" cellpadding="5" id="table_id">';
			$content .= '<thead><tr><th>Commentaires auxquels vous n\'avez pas répondu</th></tr>';
			while( $tab_derniers_commentaires_autres_membres = $r->fetch(PDO::FETCH_ASSOC) ){
				
				foreach($tab_derniers_commentaires_autres_membres as $indice => $value){
					
					$content .= "<tr>";
					$content .=  "<td>" . stripcslashes($value) ."</td>";
					$content .= "</tr>";
				}
				
			}

			$content .= "</table>";
		}
	// }

	
    
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