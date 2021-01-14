<?php require_once "../inc/header.inc.php"; ?>

<?php
//Restriction de l'accès à la page administrative : 
if( !adminConnect() ){
	header('location:../connexion.php');
	exit();
}

// Redirection vers l'affichage des notes par défaut
if(empty($_GET)){
    header('location:?action=affichage');
	exit();
}

// Suppression de note
if( isset($_GET['action']) && $_GET['action'] == 'suppression' ){
    execute_requete(" DELETE FROM note WHERE id_note = '$_GET[id_note]' ");
    header('location:?action=affichage');
    exit();
}

// Modification de note
// Affichage du select pour modifier la note
if( isset($_GET['action']) && $_GET['action'] == 'modification' && empty($_POST) ){
    $note_value = '';  
    if( isset($_GET['id_note']) ){
        $r = execute_requete(" SELECT note, avis FROM note WHERE id_note = '$_GET[id_note]' ");      
        $tab_notes = $r->fetch(PDO::FETCH_ASSOC);

        $note_value = $tab_notes['note'];
        $note_value = stripcslashes($note_value);
        $avis_value = $tab_notes['avis'];
        $avis_value = stripcslashes($avis_value);
    }
    // debug($note_value);

    $content = "<p><a href='?action=affichage'>Retourner à la liste des notes</a></p>
                <form method='post'>
                <label><h2>Modifier la note ou l'avis</h2></label><br>
                <input type='text' name='note' cols='40' rows='5' value='$note_value'><br>
                <label>Avis</label>
                <input type='text' name='avis' class='form-control' value='$avis_value'><br>
                <input type='submit' value='Valider' class='btn btn-secondary'>
                </form>
                ";
}

// Envoi du contenu du textarea en bdd
elseif( isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_POST) ){
    // debug($_POST);
    execute_requete(" UPDATE note SET note = '$_POST[note]' WHERE id_note = $_GET[id_note] ");
    header('location:?action=affichage');
    exit();
}

// Affichage des notes
// Je n'ai pas réussi à linker vers le profil du membre 2 ni à afficher son adresse.
// J'ai tenté avec une seule requête en jointure, puis avec deux requêtes distinctes, mais j'obtenais bien plus de résultats que je n'avais de lignes de notes en base de données.
if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
    if( empty($_POST) ){
        $r = execute_requete(" SELECT n.id_note, n.membre_id_membre1, m.email AS email_membre1, n.membre_id_membre2, n.note, n.avis, m.id_membre AS id_membre1
                                FROM note n, membre m
                                WHERE n.membre_id_membre1 = m.id_membre
                                ORDER BY n.date_enregistrement DESC
        ");

        /* $s = execute_requete(" SELECT n.membre_id_membre2, m.email AS email_membre2 
                                FROM note n, membre m
                                WHERE m.id_membre = n.membre_id_membre2
                                ORDER BY n.date_enregistrement DESC
        "); */

        $content .= '<h2>Liste des notes ou avis</h2>';
        $content .= '<p>Nombre de notes ou avis en ligne : '. $r->rowCount() .'</p>';

        // On affiche le tableau des notes
        $content .= '<table border="2" cellpadding="5" id="table_id">';
            $content .= '<thead><tr>';
                for( $i = 0; $i < $r->columnCount(); $i++ ){
                    $colonne = $r->getColumnMeta( $i );
                    // debug($colonne['name']);
                    if($colonne['name'] == 'id_note'){
                        $content .= "<th>ID note</th>";
                    }
                    elseif($colonne['name'] == 'membre_id_membre1'){
                        $content .= "<th>Membre 1</th>";
                    }
                    elseif($colonne['name'] == 'membre_id_membre2'){
                        $content .= "<th>ID membre 2</th>";
                    }
                    elseif($colonne['name'] == 'email' || $colonne['name'] == 'email_membre1' || $colonne['name'] == 'id_membre1'){
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
            $content .= '</tr></thead>';

            while( ($ligne = $r->fetch( PDO::FETCH_ASSOC )) ){
                // && ($mail_membre2 = $s->fetch(PDO::FETCH_ASSOC))
                foreach($ligne as $indice => $valeur){
                    $ligne[$indice] = stripcslashes($valeur);
                }
                // debug($mail_membre2);
                
                $content .= '<tbody><tr>';
                    //debug( $ligne );

                    // On affiche les informations
                    foreach( $ligne as $indice => $valeur ){
                        if( ($indice == 'membre_id_membre1') ){ 
                            $content .= "<td><a href='../profil.php?action=affichage&id_membre=" .$ligne['id_membre1'] . "'>" . $valeur. " - ";
                        }			
                        elseif( ($indice == 'email_membre1') ){ 
                            $content .= "$valeur</a></td>";
                        }
                        elseif( ($indice == 'membre_id_membre2') ){ 
                            $content .= "<td> $valeur </td>";
                        }
                        elseif( $indice == 'id_annonce' ){
                            $content .= "<td> <a href='../fiche_annonce.php?id_annonce=" .$ligne['id_annonce'] . "'>" . $valeur . " - ";
                        }
                        elseif( $indice == 'titre' ){
                            $content .= $valeur . "</a></td>";
                        }
                        elseif( $indice == 'id_membre1' ){
                            // On n'affiche rien
                        }
                        elseif( $indice == 'avis' ){
                            $content .= "<td> $valeur </td>";
                        }
                        //Sinon, on affiche juste la valeur
                        else{ 
                            $content .= "<td> $valeur </td>";
                        }
                    }
                    $content .= '<td class="text-center">
                                    <a href="?action=suppression&id_note='. $ligne['id_note'] .'" onclick="return( confirm(\'En êtes-vous certain ?\') )" title="Supprimer">
                                        <i class="far fa-trash-alt"></i>
                                    </a> 
                                    <a href="?action=modification&id_note='. $ligne['id_note'] .'" title="Modifier">
                                        <i class="far fa-edit"></i>
                                    </a><br>
                                    <a href="../profil.php?action=affichage&id_membre='. $ligne['membre_id_membre1'] .'" title="Afficher">
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

    <title>Admin - Gestion des notes et avis | Annonceo, le meilleur des annonces en ligne</title>
</head>
<body>
    <?php require_once "../inc/nav.inc.php"; ?>

        <h1>Admin - Gestion des notes et avis</h1>
        <?= $error; ?>
        <?= $content; ?>





    <?php require_once "../inc/footer.inc.php"; ?>