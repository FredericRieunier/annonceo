<?php


// Version en ligne
// Ouverture de session
session_start();

// Connexion à la bdd
$pdo = new PDO('mysql:host=cl1-sql11;dbname=sjh26701', 'sjh26701', 'poupou12', array(  PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES UTF8" ));

// URL en constante
define('URL', 'https://annonceo.fredericrieunier.fr/');

// 
define ('DOSSIER_PHOTO_LOCAL', URL . "img/");

// Variables
$content = '';
$error = '';

// Inclusion des fonctions
require_once 'fonction.inc.php';


/* // Version locale
// Ouverture de session
session_start();

// Connexion à la bdd
$pdo = new PDO('mysql:host=localhost;dbname=sjh26701', 'root', 'root', array(  PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES UTF8" ));

// URL en constante
define('URL', 'http://localhost/annonceo/');

// 
define ('DOSSIER_PHOTO_LOCAL', $_SERVER['DOCUMENT_ROOT'] . "/annonceo/img/" );

// Variables
$content = '';
$error = '';

// Inclusion des fonctions
require_once 'fonction.inc.php'; */