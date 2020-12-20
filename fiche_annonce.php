<?php require_once "inc/header.inc.php"; ?>

<?php
//affichage des informations du annonce concerné :
//debug( $_GET );

if( isset($_GET['id_annonce']) ){ //S'il existe 'id_annonce' dans l'URL, c'est que l'on a choisi délibérément d'afficher la fiche d'un annonce en particulier, donc ici, je vais récupérer toutes les infos du annonce concerné

	$r = execute_requete(" SELECT * FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");

}
else{ //SINON, on le rediriger vers la page d'accueil (Si jamais on essaie de forcer l'accès à cette page via l'URL)

	header('location:index1.php');
	exit();
}
//---------------------------------------------------

//Pour exploiter les données :
$annonce = $r->fetch( PDO::FETCH_ASSOC );
	//debug( $annonce );

$content .= '<a href="index1.php">Retour vers l\'accueil</a><br>';
debug($annonce);

/* $content .= "<a href='index1.php?categorie=$annonce[categorie]' >

				Retour vers la catégorie $annonce[categorie]

			</a><hr>"; */

foreach( $annonce as $cle => $valeur ){


    switch($cle){
        case 'prix' :
            $content .= "<p>$valeur&nbsp;€</p>";
        break;

        case 'description_longue' :
            if(!empty($valeur )){
            $content .= "<p><strong>Description :</strong>$valeur</p>";
            }
        break;

        


        case 'photo' :
            $content .= "<p><img src='$valeur' width='200'></p>";
        break;



    }

	/* if( $cle == 'photo'){ //SI l'indice est égal à "photo", on affiche une balise <img>

		$content .= "<p><img src='$valeur' width='200'></p>";
	}
	else{ //SINON, on affiche la valeur dans des balises <p>

		if( $cle != 'id_annonce' && $cle != 'photo_id_photo'){ //SI l'indice est différent de 'id_annonce', on affiche les valeurs

			$content .= "<p><strong>$cle :</strong> $valeur</p>";
		}
	} */
}

//---------------------------------------------------------------------
//gestion du panier :
/* if( $annonce['stock'] > 0 ){ //si le stock est supérieur à zero 

	$content .= "<p>Nombre dannonces disponibles : $annonce[stock]</p>";

	$content .= '<form method="post" action="panier.php">';

		$content .= "<input type='hidden' name='id_annonce' value='$annonce[id_annonce]' >";

		$content .= '<label>Quantite</label>';
		$content .= '<select name="quantite">';

			for( $i = 1; $i <= $annonce['stock']; $i++ ){

				$content .= "<option> $i </option>";
			}

		$content .= '</select><br><br>';

		$content .= '<input type="submit" name="ajout_panier" value="Ajouter au panier" class="btn btn-secondary">';

	$content .= '</form>';
}
else{ //SINON, on indique "rupture de stock"

	$content .= '<p>Rupture de stock</p>';
} */


//---------------------------------------------------------------------
?>
<!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

<title>XXXXXX | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "inc/nav.inc.php"; ?>

<h1><?= $annonce['titre'] ?></h1>

<?= $error; ?>
<?= $content; ?>

<br>

<?php require_once 'inc/footer.inc.php'; ?>