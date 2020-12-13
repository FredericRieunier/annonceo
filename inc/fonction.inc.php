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
function champ_facultatif($champ){
    if(empty($champ)){
        $champ = '';
    }
}

// Copier une photo si elle est présente dans le formulaire (à ne pas utiliser pour la photo 1, qui est obligatoire)
function copy_photo($photo, $destination){
    if( !empty($photo) ){
        copy($photo, $destination);
    }
}