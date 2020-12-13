<?php require_once "inc/header.inc.php"; ?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

<?php
    /* $nomValue = '';
    $prenomValue = '';
    debug($_POST);
    if($_POST){
    champ_facultatif($_POST['nom']);
    } */

    /* if(empty($_POST)){
        
        $nomValue = '';
    } */

    // $pdostatement = execute_requete(" INSERT INTO photo(photo1) VALUES('124') ");
    $id_de_photo = $pdo->lastInsertId();
    debug($id_de_photo);



    
?>

    <title>XXXXXX | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "inc/nav.inc.php"; ?>

    <main class="container">
        <h1>TITRE PRINCIPAL</h1>
        <?= $error; ?>
        <?= $content; ?>

<form action="" method="post">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= $nomValue; ?>"><br>
        <label>Prénom</label>
        <input type="text" name="prenom" class="form-control" value="<?= $prenomValue; ?>"><br>
        <input type="submit" class="btn btn-secondary">
</form>




    <?php require_once "inc/footer.inc.php"; ?>