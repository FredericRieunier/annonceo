<?php require_once "inc/header.inc.php"; ?>

<?php



// Affichage dans le select des catégories
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

// Affichage dans le select des départements
$r = execute_requete(" SELECT DISTINCT cp FROM annonce ORDER BY cp ASC ");

// On parcourt la requête, à chaque fois on extrait l'indice de département du CP (93 pour 93100) et on l'ajoute à un tableau
$departements_tab = array();
while( $list_departements = $r->fetch(PDO::FETCH_ASSOC) ){
  // debug($list_departements);
  foreach($list_departements as $indice => $valeur){
    array_push($departements_tab, substr($valeur, 0, 2));    
  }
}

// Dans le tableau obtenu, on élimine les doublons
$departements_tab = array_unique($departements_tab);

// On initialise la liste du select
$list_id_departements = '<option value="0"></option>';

// On affiche dans ce select le tableau obtenu précédemment
    foreach($departements_tab as $indice => $valeur){
        // debug($valeur);
        if( !empty($valeur) ){     
              // debug($list_departements['cp']);
              $list_id_departements .= "<option value='" . $valeur . "'>";
              $list_id_departements .= $valeur . "</option>";            
        }
    }

// Affichage dans le select des membres 
$pdostatement = execute_requete(" SELECT id_membre, pseudo FROM membre ");

// On initialise la liste des membres qui s'affichera dans un select
$list_id_membre = '<option value="0"></option>';

while( $id_en_bdd = $pdostatement->fetch(PDO::FETCH_ASSOC) ){    
    $list_id_membre .= "<option value='";
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

// Affichage dans le select des prix max
$r = execute_requete(" SELECT DISTINCT prix FROM annonce ORDER BY prix ASC ");
$list_prix_max = '<option value="0"></option>';
while( $prix_en_bdd = $r->fetch(PDO::FETCH_ASSOC) ){    


  foreach($prix_en_bdd as $indice => $valeur){
          $list_prix_max .= "<option value='" . $valeur . "'>";
          $list_prix_max .= $valeur . " € </option>";            
  }

}

// Affichage dans le select des prix min
$r = execute_requete(" SELECT DISTINCT prix FROM annonce ORDER BY prix ASC ");
$list_prix_min = '<option value="0"></option>';
while( $prix_en_bdd = $r->fetch(PDO::FETCH_ASSOC) ){    


  foreach($prix_en_bdd as $indice => $valeur){
          $list_prix_min .= "<option value='" . $valeur . "'>";
          $list_prix_min .= $valeur . " € </option>";            
  }

}


if(empty($_POST)){     //Affichage standard d'index.php
//On récupère les annonces en bdd:
  $r = execute_requete(" SELECT *
  FROM annonce
  ORDER BY date_enregistrement DESC
  ");

  while( $annonces_en_stock = $r->fetch( PDO::FETCH_ASSOC ) ){
    foreach($annonces_en_stock as $indice => $valeur){
			$annonces_en_stock[$indice] = stripcslashes($valeur);
    }
    
    // Recherche du pseudo
    $pseudo_search = execute_requete(" SELECT * FROM membre WHERE id_membre = '$annonces_en_stock[membre_id_membre]' ");
    $pseudo_tab = $pseudo_search->fetch(PDO::FETCH_ASSOC);
    
    // Calcul de la note moyenne du vendeur de l'annonce affichée
    $pdostatement = execute_requete(" SELECT ROUND(AVG(n.note), 1) 
    FROM note n, annonce a, membre m
    WHERE a.id_annonce = '$annonces_en_stock[id_annonce]'
    AND a.membre_id_membre = m.id_membre
    AND m.id_membre = n.membre_id_membre1
    ");
    $requete_note_moyenne = $pdostatement->fetch(PDO::FETCH_ASSOC);
    $note_moyenne = $requete_note_moyenne['ROUND(AVG(n.note), 1)'];
    
    // debug($note_moyenne);
    $affichage_note_moyenne = '';
    if($note_moyenne != NULL && $note_moyenne != ''){ 
      $affichage_note_moyenne = " - " . $note_moyenne . "/5";
    }

    $content .= '<div><a href="fiche_annonce.php?id_annonce=' . $annonces_en_stock['id_annonce'] . '">';
      $content .= '<div class="row encart_annonce">';
        $content .= '<div class="col-6 col-sm-3 pl-0 pr-0">';
          $content .= "<img src='" . $annonces_en_stock['photo'] . "' class='' alt=''>";
        $content .= '</div>';
        $content .= '<div class="col-6 col-sm-9">';
          $content .= "<h3>$annonces_en_stock[titre]</h3>";
          $content .= "<p>$annonces_en_stock[description_courte]</p>";
          $content .= "<p>$annonces_en_stock[ville] - $annonces_en_stock[cp]</p>";          
          $pseudo_membre = $pseudo_tab['pseudo'];
          $content .= "<div class='row'><p class='col-6'>$pseudo_membre $affichage_note_moyenne</p>";  
          $content .= "<p class='text-right col-6'><strong>$annonces_en_stock[prix]&nbsp;€</strong></p></div>";
        $content .= "</div>";
      $content .= "</div>";
    $content .= '</a></div>';

  }
}
else{ //Il y a qqch dans le $_POST
    // Mettre cette requête sd une fonction, ac en paramètre id_annonce (), extraire les 3 valeurs voulues
  // Dans les 3 tables utilisées pour les AND, faire un fetch de la colonne des id_annonce, fair eun array avec
  // Vérifier que l'id récupéré est bn dans l'array que je viens de faire. S'il existe pas : header vers une page d'erreur.
  // Pour chacune : vérifier si c'est un entier (isinteger()) et vérifier q le numéro d'id existe dans la base
 

  extract($_POST);
  debug($_POST);

  $search_categorie = add_AND_in_request($categorie_id_categorie, 'categorie_id_categorie');
  $search_membre = add_AND_in_request($membre, 'membre_id_membre');
  

  // Traitement particulier pour les départements :
  if(empty($departement)){
    $search_departement = '';
  }
  else{
    $search_departement = "AND cp LIKE '$departement%' ";
  }  

  // Et pour les prix :
  if(empty($prix_maximum)){
    $search_prix_maximum = '';
  }
  else{
    $search_prix_maximum = "AND prix <= '$prix_maximum' ";
  }

  if(empty($prix_minimum)){
    $search_prix_minimum = '';
  }
  else{
    $search_prix_minimum = "AND prix >= '$prix_minimum' ";
  }

  // ET POUR le tri par prix
  // Min vers max
  if(empty($order_prix)){
    $order_prix_min_vers_max = '';
  }
  elseif($order_prix == 'min_au_max' ){
    $order_prix_min_vers_max = "ORDER BY prix ASC";
  }
  elseif($order_prix == 'max_au_min' ){
    $order_prix_min_vers_max = "ORDER BY prix DESC";
  }
  
  if(is_numeric($categorie_id_categorie) && is_numeric($membre)){  //L'id récupéré est bien numérique
    // On vérifie que cet id existe dans la bdd
    $r = execute_requete(" SELECT * FROM annonce WHERE 1 = 1 $search_categorie $search_membre $search_departement $search_prix_maximum $search_prix_minimum $order_prix_min_vers_max ");
      if($r->rowCount() >=1){
        // On a un id bien numérique et qui existe dans la bdd, on peut donc afficher toutes les annonces correspondant à cette requête :      

        while( $annonces_en_stock = $r->fetch( PDO::FETCH_ASSOC ) ){
          $content .= '<div><a href="">';
            $content .= '<div class="row encart_annonce">';
              $content .= '<div class="col-6 col-sm-3 pl-0 pr-0">';
                $content .= "<img src='" . $annonces_en_stock['photo'] . "' class='' alt=''>";
              $content .= '</div>';
              $content .= '<div class="col-6 col-sm-9">';
                $content .= "<h3>$annonces_en_stock[titre]</h3>";
                $content .= "<p>$annonces_en_stock[description_courte]</p>";
                $content .= "<p>$annonces_en_stock[ville] - $annonces_en_stock[cp]</p>";
                $pseudo_search = execute_requete(" SELECT * FROM membre WHERE id_membre = '$annonces_en_stock[membre_id_membre]' ");
                $pseudo_tab = $pseudo_search->fetch(PDO::FETCH_ASSOC);
                $pseudo_membre = $pseudo_tab['pseudo'];
                $content .= "<div class='row'><p class='col-6'>$pseudo_membre </p>";  
                $content .= "<p class='text-right col-6'><strong>$annonces_en_stock[prix]&nbsp;€</strong></p></div>";
              $content .= "</div>";
            $content .= "</div>";
          $content .= '</a></div>';

        }



      }
      else{
        // Redirection vers une page d'erreur
      }


    

  }
  else{
    // Redirection vers une page d'erreur
  }
  // debug($search_categorie);


}   // Fin du else suivant if(empty($_POST))

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
<!-- <main class="container-fluid"> -->
<h1>Accueil</h1>

<form action="" method="post">

  <label>Trier par prix</label><br>
	<select name="order_prix" id="" class="form-control">
    <option value=""></option>
		<option value="min_au_max">Du moins cher au plus cher</option>
    <option value="max_au_min">Du plus cher au moins cher</option>
	</select>

  <label>Catégorie</label><br>
	<select name="categorie_id_categorie" id="" class="form-control">
		<?= $list_id_categorie; ?>
	</select>


  <label>Département</label><br>
	<select name="departement" id="" class="form-control">
    <?= $list_id_departements; ?>
	</select>

  <label for="" >Membre</label> <br>
  <select name="membre" id="" class="form-control">
    <?= $list_id_membre; ?>
  </select>

  <label for="">Prix maximum</label><br>
  <select name="prix_maximum" id="" class="form-control">
    <?= $list_prix_max; ?>
  </select><br>

  <label for="">Prix minimum</label><br>
  <select name="prix_minimum" id="" class="form-control">
    <?= $list_prix_min; ?>
  </select><br>


  <input type="submit" value="Valider">

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