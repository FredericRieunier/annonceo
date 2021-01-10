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
    

    
    if(!empty( $_FILES) ){
        copy($_FILES['photo1']['tmp_name'], '/home/users9/s/sjh2670/www/annonceo/img/nom_de_photo.jpg');
    }

    
?>

    <title>XXXXXX | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "inc/nav.inc.php"; ?>

    <main class="container">
        <h1>TITRE PRINCIPAL</h1>
        <?= $error; ?>
        <?= $content; ?>

<form method="post" enctype="multipart/form-data">
        <label>Photo 1</label><br>
	    <input type="file" name="photo1">
        <input type="submit" class="btn btn-secondary">
</form>




    <?php require_once "inc/footer.inc.php"; ?>