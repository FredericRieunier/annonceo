<?php require_once "inc/header.inc.php"; ?>

<?php
//affichage des informations du annonce concerné :
//debug( $_GET );

if( isset($_GET['id_annonce']) ){ //S'il existe 'id_annonce' dans l'URL, c'est que l'on a choisi délibérément d'afficher la fiche d'une annonce en particulier, donc ici, je vais récupérer toutes les infos de l'annonce concernée

		$r = execute_requete(" SELECT a.id_annonce, a.titre, a.description_courte, a.prix, a.photo, a.pays, a.ville, a.adresse, a.cp, 
			m.pseudo, c.titreCat, a.date_enregistrement 
			FROM annonce a, membre m, categorie c
			WHERE a.id_annonce = '$_GET[id_annonce]'
			AND a.membre_id_membre = m.id_membre 
			AND a.categorie_id_categorie = c.id_categorie ");

		//Pour exploiter les données :
		$annonce = $r->fetch( PDO::FETCH_ASSOC );

		foreach($annonce as $indice => $valeur){
			$annonce[$indice] = stripcslashes($valeur);
		}

		extract($annonce);
		// debug($annonce);

		if(empty($titre)){
			header('location:erreur_404.php');
			exit();
		}
		else{
			$content .= "<h2><a href=''>$titreCat</a></h2>";
			$content .= "<p><img src='$photo' width='200'></p>";
			$content .= "<h3>$prix €</h3>";
			if( !empty($description_longue) ){
				$content .= "<p><strong>Description :</strong><br> $description_longue</p>";
			}
			elseif( !empty($description_courte) ){
				$content .= "<p><strong>Description :</strong><br> $description_courte</p>";
			}
			$content .= "<p><strong>Vendeur/Vendeuse :</strong><br>$pseudo</p>";
			$content .= "<p><strong>Adresse :</strong><br>$adresse <br>$cp $ville <br> $pays</p>";

			$content .= "<p>Ajouter date de publication (faire fonction pour mettre au bon format), note du vendeur, affichage des commentaires, ajout de lightbox ou autre syst (sous réserve que l'user soit connecté : formulaire, sinon : 'se connecter pour noter, commenter, contacter' pour noter le vendeur, commenter l'annonce, bouton vert pour le contacter.</p>";

			// Affichage des commentaires
			$comments = '';
			$r = execute_requete(" SELECT co.commentaire, m.pseudo, co.date_enregistrement
								FROM annonce a, commentaire co, membre m
								WHERE co.membre_id_membre = m.id_membre
								AND a.id_annonce = '$_GET[id_annonce]'
								");

			$comments .= "<p>" . $r->rowCount() . " commentaires</p>";
			
			while($tab_comments = $r->fetch(PDO::FETCH_ASSOC)){

				$comments .= "<p><strong>$tab_comments[pseudo]</strong></p>";
				$comments .= "<p>" . stripcslashes($tab_comments['commentaire']) . "</p>";
				// debug($tab_comments);
			}
			

			
		}

}
else{ //SINON, on le redirige vers une page d'erreur 
	header('location:erreur_404.php');
	exit();
}
//---------------------------------------------------



?>
<!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

<title><?= $annonce['titre'] . ' | ' . $titreCat ?> | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "inc/nav.inc.php"; ?>

<h1><?= $annonce['titre'] ?></h1>

<?= $error; ?>
<?= $content; ?>

<p><a href="deposer_commentaire_note.php?id_annonce=<?= $_GET['id_annonce'] ?>">Déposer un commentaire ou une note</a></p>

<?= $comments; ?>
<p>Une super chemise à col en v! Je recommande!!!</p>
<p>Poupoudu18</p>

<br>

<?php require_once 'inc/footer.inc.php'; ?>