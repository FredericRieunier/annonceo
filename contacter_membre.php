<?php require_once "inc/header.inc.php"; ?>

<?php
if( isset($_GET['id_membre']) ){
    $r = execute_requete(" SELECT telephone, email, pseudo
                            FROM membre
                            WHERE id_membre = '$_GET[id_membre]' 
    ");
    $coordonnees_membre = $r->fetch(PDO::FETCH_ASSOC);

    $content .= "<p><strong>Téléphone :</strong> $coordonnees_membre[telephone]</p>";
    
    if( $_POST ){
        mail("$coordonnees_membre[email]", 'Annonceo - ' . $_POST['sujet'], $_POST['message'], 'Expéditeur : ' . $_POST['expediteur']);
        $content .= "<div class='alert alert-success'>Votre message a été envoyé correctement.</div>";
    }

}
else{ //SINON, on le redirige vers une page d'erreur 
	header('location:erreur_404.php');
	exit();
}

?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Contacter un membre | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "inc/nav.inc.php"; ?>

        <h1>Contacter <?= $coordonnees_membre['pseudo'] ?></h1>
        <?= $error; ?>
        <?= $content; ?>

        <form method="post">
        <label for="expediteur">Votre mail :</label><br>
        <input type="text" name="expediteur" class="form-control"><br><br>

        <label for="sujet">Sujet</label><br>
        <input type="text" name="sujet" class="form-control"><br><br>

        <label for="message">Message</label><br>
        <textarea name="message" cols="40" rows="5" class="form-control"></textarea><br><br>

        <input type="submit">
        
        </form>



    <?php require_once "inc/footer.inc.php"; ?>