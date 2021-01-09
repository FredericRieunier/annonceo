<?php require_once "../inc/header.inc.php"; ?>
<?php
if( !adminConnect() ){
	header('location:../connexion.php');
	exit();
}

// Membres les mieux notés
$r = execute_requete(" SELECT ROUND(AVG(n.note), 1) AS note_avg, COUNT(n.note) AS note_count, m.id_membre, m.prenom, m.nom
                        FROM membre m, note n
                        WHERE n.membre_id_membre1 = m.id_membre
                        GROUP BY m.id_membre
                        ORDER BY note_avg DESC LIMIT 5
");

$afficher_meilleures_notes = ''; 
$i = 0;
while( $meilleures_notes = $r->fetch(PDO::FETCH_ASSOC) ){
    $afficher_meilleures_notes .= "<p><div>" . ($i+1) . " - $meilleures_notes[prenom] $meilleures_notes[nom]</div> <div style='text-align: right;'>$meilleures_notes[note_avg]/5 pour $meilleures_notes[note_count] avis</div></p>";
    $i = $i+1;
}

// Membres les plus actifs, c'est-à-dire ayant publié le plus d'annonces
$r = execute_requete(" SELECT m.prenom, m.nom, COUNT(a.id_annonce) AS nombre_annonces
                        FROM membre m, annonce a
                        WHERE a.membre_id_membre = m.id_membre
                        GROUP BY m.id_membre
                        ORDER BY nombre_annonces DESC LIMIT 5            
");

$afficher_membres_actifs = '';
$i = 0;
while( $membres_actifs = $r->fetch(PDO::FETCH_ASSOC) ){
    $afficher_membres_actifs .= "<p><div>" . ($i+1) . " - $membres_actifs[prenom] $membres_actifs[nom] </div><div style='text-align:right;'> $membres_actifs[nombre_annonces] annonce(s) publiée(s) </div>";
    $i = $i+1;
}


// Annonces les plus anciennes
$r = execute_requete(" SELECT titre, date_enregistrement FROM annonce ORDER BY date_enregistrement LIMIT 5 ");

$afficher_annonces_anciennes = '';
$i = 0;
while( $annonces_anciennes = $r->fetch(PDO::FETCH_ASSOC) ){
    $afficher_annonces_anciennes .= "<p><div>" . ($i+1) . " - $annonces_anciennes[titre]</div> <div style='text-align: right;'>$annonces_anciennes[date_enregistrement]</div></p>";
    $i = $i+1;
}

// Catégories avec le plus d'annonces
$r = execute_requete(" SELECT COUNT(a.id_annonce) AS nombre_annonces, c.titreCat
                        FROM annonce a, categorie c
                        WHERE a.categorie_id_categorie = c.id_categorie
                        GROUP BY c.titreCat
                        ORDER BY nombre_annonces DESC 
");

$afficher_nombre_annonces_par_categorie = '';
$i= 0;
while( $nombre_annonces_par_categorie = $r->fetch(PDO::FETCH_ASSOC) ){
    $afficher_nombre_annonces_par_categorie .= "<p><div>" . ($i+1) . " - $nombre_annonces_par_categorie[titreCat]</div> <div style='text-align: right;'>$nombre_annonces_par_categorie[nombre_annonces] annonce(s)</div></p>";
    $i = $i+1;
}

?>


    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Admin - Statistiques | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "../inc/nav.inc.php"; ?>

        <h1>Admin - Statistiques</h1>
        <?= $error; ?>
        <?= $content; ?>


    <section class="top5">
        <h3>Top 5 des membres les mieux notés</h3>
        <?= $afficher_meilleures_notes; ?>
    </section>

    <section class="top5">
        <h3>Top 5 des membres les plus actifs (ayant posté le plus d'annonces)</h3>
        <?= $afficher_membres_actifs; ?>
    </section>

    <section class="top5">
        <h3>Top 5 des annonces les plus anciennes</h3>
        <?= $afficher_annonces_anciennes; ?>
    </section>

    <section class="top5">
        <h3>Top 5 des des catégories contenant le plus d'annonces</h3>
        <?= $afficher_nombre_annonces_par_categorie; ?>
    </section>



    <?php require_once "../inc/footer.inc.php"; ?>