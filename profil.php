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
        
      
        <?php 
    //   debug($id_membre);
        if( adminConnect() && !empty($_GET) ){
        ?>
            <a href="admin/gestion_membres.php?action=modification&id_membre= <?= $id_membre ?>"><input type="submit" value="Modifier" class="btn btn-secondary"></a>
        <?php
        }
        else{
        ?>
        <a href="inscription.php?action=modification"><input type="submit" value="Modifier" class="btn btn-secondary"></a>
        
        <?php
        }
        ?>






    <?php require_once "inc/footer.inc.php"; ?>