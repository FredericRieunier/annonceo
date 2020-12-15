<?php require_once "../inc/header.inc.php"; ?>

<?php
//Restriction de l'accès
if( !adminConnect() ){
	header('location:../connexion.php');
	exit();
}

//Gestion de la SUPPRESSION :

if( isset( $_GET['action'] ) && $_GET['action'] == 'suppression' ){ 	

    execute_requete(" DELETE FROM categorie WHERE id_categorie = '$_GET[id_categorie]' ");
    header('location:?action=affichage');
    exit();
}

//---------------------------------------------
//INSERTION et MODIFICATION :
if( !empty( $_POST ) ){

	foreach( $_POST as $key => $value ){
		$_POST[$key] = htmlentities( addslashes( $value ) );
	}

	

	//---------------------------------------------
	//INSERTION  ou MODIFICATION d'une catégorie :
	if( isset($_GET['action']) && $_GET['action'] == 'modification' ){

		execute_requete(" UPDATE categorie SET 	titre = '$_POST[titre]',
												motscles = '$_POST[motscles]'
							WHERE id_categorie = '$_GET[id_categorie]'
					 ");

		//redirection vers l'affichage :
        header('location:?action=affichage');
        exit();

	}
	elseif( empty( $error) ) { //SI la variable $error est vide, je fais mon insertion:

		execute_requete(" INSERT INTO categorie( titre, motscles ) 

						VALUES(
								'$_POST[titre]',
								'$_POST[motscles]'
							)
						");
		//redirection vers l'affichage :

        header('location:?action=affichage');
        exit();

	}
}

//Affichage des catégories :
if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
	//S'il existe une 'action' dans mons URL ET que cette 'action' est égale à 'affichage', alors on affiche la liste des categories ;

	//Je récupère les categories en bdd:
	$r = execute_requete(" SELECT * FROM categorie ");

	$content .= '<h2>Liste des catégories</h2>';
	$content .= '<p>Nombre de catégories répertoriées : '. $r->rowCount() .'</p>';

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

				//EXERCICE : affichez les informations ET la photo !
				foreach( $ligne as $indice => $valeur ){
						$content .= "<td> $valeur </td>";
				}
				$content .= '<td class="text-center">
								<a href="?action=suppression&id_categorie='. $ligne['id_categorie'] .'" onclick="return( confirm(\'En êtes-vous certain ?\') )">
									<i class="far fa-trash-alt"></i>
								</a>	
							</td>';
				$content .= '<td class="text-center">
								<a href="?action=modification&id_categorie='. $ligne['id_categorie'] .'">
									<i class="far fa-edit"></i>
								</a>	
							</td>';
			$content .= '</tr>';
		}
	$content .= '</table>';
}

?>


    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Admin - Gestion des catégories | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "../inc/nav.inc.php"; ?>

    <main class="container">
        <h1>Gestion des catégories</h1>

        <a href="?action=ajout">Ajout d'une catégorie</a><br>
        <a href="?action=affichage">Afficher toutes les catégories</a><hr>
        <?= $error; ?>
        <?= $content; ?>



<?php 
if( isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')  ) : //S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'ajout' OU à 'modification', alors on affiche le formulaire 

    if( isset( $_GET['id_categorie']) ){ //S'il existe 'id_produit' dans l'URL, c'est que je suis dans le cadre d'une modification

        //récupération des infos à modifier :
        $r = execute_requete(" SELECT * FROM categorie WHERE id_categorie = '$_GET[id_categorie]' ");
        //exploitation des données :
        $categorie_actuelle = $r->fetch( PDO::FETCH_ASSOC );
            // debug( $categorie_actuelle );
    }

    //conditions pour vérifier l'existence des variables :
    $titre = ( isset($categorie_actuelle['titre']) ) ? $categorie_actuelle['titre'] : '';
    $motscles = ( isset($categorie_actuelle['motscles']) ) ? $categorie_actuelle['motscles'] : '';

    

?>

<form method="post" enctype="multipart/form-data">
<label>Titre de la catégorie</label><br>
<input type="text" name="titre" class="form-control" value="<?= $titre ?>"><br>


<label>Mots-clés (veillez à séparer les mots-clés ou expressions-clés par des virgules)</label><br>
<input type="text" name="motscles" class="form-control" value="<?= $motscles ?>"><br>

<input type="submit" value="<?= ucfirst($_GET['action']) ?>" class="btn btn-secondary">

</form>

<?php 
endif; 
?>

    <?php require_once "../inc/footer.inc.php"; ?>