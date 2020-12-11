<?php require_once "inc/header.inc.php"; ?>
<!-- Page 15 dans le CDC -->

<?php
if(!userConnect()){
    header('location:connexion.php');  
    exit();  
}

if(adminConnect()){
    $content .= '<h2 style="color: darkred;">Administrateur</h2>';
}

extract($_SESSION['membre']);
debug($id_membre);

$content .= "<h2>Vos informations personnelles</h2>
        <p><strong>Pseudo&nbsp;:</strong> $pseudo</p>
        <p><strong>Prénom&nbsp;: </strong> $prenom</p>
        <p><strong>Nom&nbsp;: </strong> $nom</p>
        <p><strong>Téléphone&nbsp;: </strong> $telephone</p>
        <p><strong>E-mail&nbsp;: </strong> $email</p>
        <p><strong>Civilité&nbsp;: </strong>" . civilite() . "</p>";


?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Profil | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "inc/nav.inc.php"; ?>

    <main class="container">
        <h1>Profil</h1>
        <?= $error; ?>
        <?= $content; ?>
        
      <form action="inscription.php?action=modification&id_membre= <?= $id_membre; ?> ">
        <input type="submit" value="Modifier" class="btn btn-secondary">
      </form>  
      <a href="inscription.php?action=modification">Lien</a>






    <?php require_once "inc/footer.inc.php"; ?>