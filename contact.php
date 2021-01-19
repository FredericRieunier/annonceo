<?php require_once "inc/header.inc.php"; ?>

<?php

if( $_POST ){
    mail('frederic.rieunier@gmail.com', 'Annonceo - ' . $_POST['sujet'], $_POST['message'], 'Expéditeur : ' . $_POST['expediteur']);
    $content .= "<div class='alert alert-success'>Votre message a été envoyé correctement.</div>";
}


?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Contact | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "inc/nav.inc.php"; ?>

        <h1>Contact</h1>
        <?= $error; ?>
        <?= $content; ?>

        <form method="post">
        <label>Votre mail :</label><br>
        <input type="text" name="expediteur" class="form-control"><br><br>

        <label>Sujet</label><br>
        <input type="text" name="sujet" class="form-control"><br><br>

        <label>Message</label><br>
        <textarea name="message" cols="40" rows="5" class="form-control"></textarea><br><br>

        <input type="submit">
        
        </form>





    <?php require_once "inc/footer.inc.php"; ?>