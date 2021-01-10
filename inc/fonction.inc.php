<?php
// Réutilisation de la ô combien utile fonction de débugage vue en cours
function debug( $arg ){
    echo '<div style="background:#fda500; z-index: 1000; padding:15px">';

    $trace = debug_backtrace();

    echo "Debug demandé dans le fichier : <strong>" . $trace[0]['file'] . '</strong> à la ligne <strong>' . $trace[0]['line'] . '</strong>';

        print '<pre>';
            print_r($arg);
        print '</pre>';

    echo '</div>';
}

// Réutilisation aussi de la fonction execute_requete pour se simplifier la vie
function execute_requete($req){
    global $pdo;
    $r = $pdo->query($req);
    return $r;
}

// J'ajoute une fonction prepare_requete pour sécuriser davantage
function prepare_requete($req){
    global $pdo;
    $r = $pdo->prepare($req);
    return $r;
}

// Fonction userConnect() : si l'internaute est connecté.

function userConnect(){

    if(!isset($_SESSION['membre'])){
        return false;
    }
    else{
        return true;
    }
}

function adminConnect(){
    if(userConnect() && $_SESSION['membre']['statut'] == 1){ 
        // Si l'internaute est connecté et qu'il est admin (donc que son statut vaut 1)
        return true;
    }
    else{
        return false;
    }
}

// Pour gérer plus proprement l'affichage de la civilité
function civilite(){
    if(userConnect()){
        if($_SESSION['membre']['civilite'] == 'm'){
            return 'homme';
        }
        else{
            return 'femme';
        }
    }
}

// Rendre un champ facultatif lors d'une insertion en base de données
// VOIR COMMENT LA FAIRE MARCHER EVENTUELLEMENT
/* function champ_facultatif($champ_teste, $champ_cible){
    if(!isset($champ_teste)){
        $champ_cible = '';
    }
} */

// Gérer l'ajout d'une photo

function name_photo($numero_photo){
    //Ici, je nomme la photo :
    return $_FILES[$numero_photo]['name'];
}

function add_photo_to_bdd($nom_photo){
		//Chemin pour accéder à la photo (à insérer en BDD) :
        return URL . "img/$nom_photo";
}

function copy_photo($nom_photo, $numero_photo){

    if(!empty($nom_photo)){
    //Où on place le fichier de la photo
    // Version locale :
    // $photo_dossier = DOSSIER_PHOTO_LOCAL . $nom_photo;
    // Version en ligne : 
    $photo_dossier = '/home/users9/s/sjh2670/www/annonceo/img/' . $nom_photo;
    
    //Enregistrement de la photo dans le dossier 'img'
    copy( $_FILES[$numero_photo]['tmp_name'], $photo_dossier );
    }

}

// Ajouter en indice l'id à une photo 
function add_index($nom_photo, $last_id_photo){
		// Nom de photo avec préfixe :
		$nom_photo = $last_id_photo . '_' . $nom_photo;
		// Chemin local complet avec nom de photo avec préfixe
		return DOSSIER_PHOTO_LOCAL . $nom_photo;
}

// Renommer une photo en lui ajoutant l'id en indice
function rename_photo($nom_photo, $new_photo_bdd, $current_photo_bdd){
    if( empty($nom_photo) ){ $new_photo_bdd = '';	}
    else{ rename($current_photo_bdd, $new_photo_bdd); }

    // return str_replace(  $_SERVER['DOCUMENT_ROOT'], 'img/', $new_photo_bdd );
    // Version locale :
    // return str_replace(  $_SERVER['DOCUMENT_ROOT'], 'http://localhost', $new_photo_bdd );
    // Version en ligne :
    return str_replace(  $_SERVER['DOCUMENT_ROOT'], 'img/', $new_photo_bdd );

    
}

// Supprimer un fichier de photo
function delete_photo_file($path_photo_to_delete, $photo_to_delete){
    $path_photo_to_delete = str_replace( 'http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo_to_delete );
}

/* Index */
// Ajouter un AND optionnel dans une requête
function add_AND_in_request($id_champ, $nom_champ){
    if(empty($id_champ)){
        return '';
      }
      else{
        return "AND $nom_champ = '$id_champ' ";
      }
}
