<?php require_once "inc/header.inc.php"; ?>

<?php

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

//On récupère les annonces en bdd:
$r = execute_requete(" SELECT *
FROM annonce a, membre m
WHERE a.membre_id_membre = m.id_membre

 ");

while( $annonces_en_stock = $r->fetch( PDO::FETCH_ASSOC ) ){
  $content .= '<div><a href="">';
    $content .= '<div class="row encart_annonce">';
      $content .= '<div class="col-6 col-sm-3 pl-0 pr-0">';
        $content .= "<img src='" . $annonces_en_stock['photo'] . "' class='' alt=''>";
      $content .= '</div>';
      $content .= '<div class="col-6 col-sm-9">';
        $content .= "<h3>$annonces_en_stock[titre]</h3>";
        $content .= "<h4>$annonces_en_stock[prix]</h4>";
        $content .= "<p>$annonces_en_stock[description_courte]</p>";
        $content .= "<p>$annonces_en_stock[ville] - $annonces_en_stock[cp]</p>";
        $content .= "<div class='row'><p class='col-6'>$annonces_en_stock[pseudo]</p>";  
        $content .= "<p class='text-right col-6'><strong>$annonces_en_stock[prix]&nbsp;€</strong></p></div>";
      $content .= "</div>";
    $content .= "</div>";
  $content .= '</a></div>';

  // debug($annonces_en_stock);

}
// <div class="row encart_annonce">
//   <div style="border:1px red solid;" class="col-6 col-sm-3 pl-0 pr-0">
//     <img src="img/208_bottes-2.jpg" style="border:1px green solid;"  class="" alt="">
//   </div>
//   <div class="col-6 col-sm-9">
//     <h3>Titre de l'annonce</h3>
//     <h4>Prix</h4>
//     <p>Catégorie</p>
//     <p>Ville Code Postal</p>
//     <p>Date enregistrement</p>
//   </div>
// </div>

?>

<!-- A MODIFIER -->
    <meta
      name="description"
      content="En plein Paris, au 1, avenue Montaigne, dans le 8e arrondissement, le Parimis propose une nouvelle idée du chic à la française."
    />
    <meta
      name="keywords"
      content="hôtel de luxe Paris, Paris 8e arrondissement, spa 8e arrondissement, Paris 8e, Parimis"
    />

    <!-- BALISES OG RÉSEAUX SOCIAUX -->
    <meta property="og:site_name" content="Hôtel cinq étoiles Parimis Paris" />
    <meta
      property="og:title"
      content="Accès | Hôtel cinq étoiles Paris | spa | Parimis Paris"
    />
    <meta property="og:type" content="website" />
    <meta
      property="og:url"
      content="https://hotel-parimis.fredericrieunier.fr/acces.html"
    />
    <meta
      property="og:image"
      content="https://hotel-parimis.fredericrieunier.fr/img/toits-paris-2-500.jpg"
    />
    <meta
      property="og:description"
      content="En plein Paris, au 1, avenue Montaigne, dans le 8e arrondissement, le Parimis propose une nouvelle idée du chic à la française."
    />

    <title>Accueil | Annonceo, le meilleur des annonces en ligne</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
<?php require_once "inc/nav.inc.php"; ?>
<main class="container-fluid">
<h1>Accueil</h1>

<form action="">
  <label>Catégorie</label><br>
	<select name="categorie_id_categorie" id="" class="form-control">
		<?= $list_id_categorie; ?>
	</select>

  <label>Département</label><br>
	<select name="departement" id="" class="form-control">
		
	</select>
  <!-- <?= $list_id_categorie; ?> -->

  <label for="categorie">Membre</label> <br>
  <select name="membre" id="">
    <?= $list_id_membre ?>
  </select><br>

  <label for="prix">Prix</label><br>
  <input type="range" name="" id=""><br>

</form>
<br>

<div>
<a href="">
<div class="row encart_annonce">
  <div style="border:1px red solid;" class="col-6 col-sm-3 pl-0 pr-0">
    <img src="img/208_bottes-2.jpg" style="border:1px green solid;"  class="" alt="">
  </div>
  <div class="col-6 col-sm-9">
    <h3>Titre de l'annonce</h3>
    <h4>Prix</h4>
    <p>Catégorie</p>
    <p>Ville Code Postal</p>
    <p>Date enregistrement</p>
  </div>
</div>
</a>
</div>

<?= $content; ?>

    


<?php require_once "inc/footer.inc.php"; ?>