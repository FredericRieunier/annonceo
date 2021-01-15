<?php require_once "../inc/header.inc.php"; ?>

<?php
//Restriction de l'accès à la page administrative : 
if( !adminConnect() ){
	header('location:../connexion.php');
	exit();
}

// Suppression de commentaire
if( isset($_GET['action']) && $_GET['action'] == 'suppression' ){
    execute_requete(" DELETE FROM commentaire WHERE id_commentaire = '$_GET[id_commentaire]' ");
    header('location:?action=affichage');
    exit();
}

// Modification de commentaire
// Affichage du textarea pour modifier le commentaire
if( isset($_GET['action']) && $_GET['action'] == 'modification' && empty($_POST) ){
    $comment_value = '';  
    if( isset($_GET['id_commentaire']) ){
        $r = execute_requete(" SELECT commentaire FROM commentaire WHERE id_commentaire = '$_GET[id_commentaire]' ");      
        $comment_value = $r->fetch(PDO::FETCH_ASSOC)['commentaire'];
        $comment_value = stripcslashes($comment_value);
    }
    // debug($comment_value);

    $content = "<p><a href='?action=affichage'>Retourner à la liste des commentaires</a></p>
                <form method='post'>
                <label><h2>Modifier le commentaire</h2></label><br>
                <textarea name='commentaire' cols='40' rows='5'>$comment_value</textarea><br>
                <input type='submit' value='Valider' class='btn btn-secondary'>
                </form>
                ";
}

// Envoi du contenu du textarea en bdd
elseif( isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_POST) ){
    // debug($_POST);
    execute_requete(" UPDATE commentaire SET commentaire = '$_POST[commentaire]' WHERE id_commentaire = $_GET[id_commentaire] ");
    header('location:?action=affichage');
    exit();
}

// Affichage des commentaires
if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
    if( empty($_POST) ){
        $r = execute_requete(" SELECT co.id_commentaire, m.id_membre, m.email, a.id_annonce, a.titre, co.commentaire, co.date_enregistrement
                                FROM annonce a, commentaire co, membre m
                                WHERE co.annonce_id_annonce = a.id_annonce
                                AND co.membre_id_membre = m.id_membre
                                ORDER BY co.date_enregistrement DESC
        ");

        // Voir pourquoi n.avis conduit à afficher les avis correctement d'abord puis après les avoir tous passés en revue à en afficher 7 en boucle

        $content .= '<h2>Liste des commentaires</h2>';
        $content .= '<p>Nombre de commentaires en ligne : '. $r->rowCount() .'</p>';

        // On affiche le tableau des commentaires
        $content .= '<table border="2" cellpadding="5" id="table_id">';
            $content .= '<thead><tr>';
                for( $i = 0; $i < $r->columnCount(); $i++ ){
                    $colonne = $r->getColumnMeta( $i );
                    // debug($colonne['name']);
                    if($colonne['name'] == 'id_commentaire'){
                        $content .= "<th>ID commentaire</th>";
                    }
                    elseif($colonne['name'] == 'id_membre'){
                        $content .= "<th>Membre</th>";
                    }
                    elseif($colonne['name'] == 'email' || $colonne['name'] == 'titre'){
                        // On n'affiche pas de colonne dédiée à ces champs
                    }
                    elseif($colonne['name'] == 'id_annonce'){
                        $content .= "<th>ID annonce</th>";
                    }
                    elseif($colonne['name'] == 'date_enregistrement'){
                        $content .= "<th>Date d'enregistrement</th>";
                    }
                    else{                        
                        $content .= "<th>" . ucfirst($colonne['name']) . "</th>";
                    }
                }
                $content .= '<th>Actions</th>';
                // $content .= '<th>Modification</th>';
            $content .= '</tr></thead>';

            while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
                foreach($ligne as $indice => $valeur){
                    $ligne[$indice] = stripcslashes($valeur);
                }
                $content .= '<tbody><tr>';
                    //debug( $ligne );

                    // On affiche les informations
                    foreach( $ligne as $indice => $valeur ){
                        if( ($indice == 'id_membre') ){ 
                            $content .= "<td><a href='../profil.php?action=affichage&id_membre=" .$ligne['id_membre'] . "'>" . $valeur. " - ";
                        }			
                        elseif( ($indice == 'email') ){ 
                            $content .= "$valeur</a></td>";
                        }
                        elseif( $indice == 'id_annonce' ){
                            $content .= "<td> <a href='../fiche_annonce.php?id_annonce=" .$ligne['id_annonce'] . "'>" . $valeur . " - ";
                        }
                        elseif( $indice == 'titre' ){
                            $content .= $valeur . "</a></td>";
                        }
                        elseif( $indice == 'avis' ){
                            $content .= "<td> $valeur </td>";;
                        }
                        elseif($indice == "date_enregistrement"){
                            // On met la date à un format correct
                            list($date, $time) = explode(" ", $valeur);
                            list($year, $month, $day) = explode("-", $date);
                            $date_enregistrement = "$day/$month/$year";
                            $content .= "<td>$date_enregistrement</td>";
                        }
                        //Sinon, on affiche juste la valeur
                        else{ 
                            $content .= "<td> $valeur </td>";
                        }
                    }
                    $content .= '<td class="text-center">
                                    <a href="?action=suppression&id_commentaire='. $ligne['id_commentaire'] .'" onclick="return( confirm(\'En êtes-vous certain ?\') )" title="Supprimer">
                                        <i class="far fa-trash-alt"></i>
                                    </a> 
                                    <a href="?action=modification&id_commentaire='. $ligne['id_commentaire'] .'" title="Modifier">
                                        <i class="far fa-edit"></i>
                                    </a><br>
                                    <a href="../fiche_annonce.php?id_annonce='. $ligne['id_annonce'] .'" title="Afficher">
                                        <i class="fas fa-search"></i>
                                    </a>	
                                </td>';
                $content .= '</tr></tbody>';
            }
        $content .= '</table>';
    }
}

?>

    <!-- Pour gagner du temps, je n'ai pas mis en place de balises meta SEO ailleurs que sur la page d'accueil  -->

    <title>Admin - Gestion des commentaires | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "../inc/nav.inc.php"; ?>

        <h1>Admin - Gestion des commentaires</h1>
        <?= $error; ?>
        <?= $content; ?>





    <?php require_once "../inc/footer.inc.php"; ?>