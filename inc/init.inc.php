<?php

// Ouverture de session
session_start();

// Connexion à la bdd
$pdo = new PDO('mysql:host=localhost;dbname=annonceo', 'root', 'root', array(  PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES UTF8" ));

// URL en constante
define('URL', 'http://localhost/PHP/00-Annonceo_V1.0/');

// 
define ('DOSSIER_PHOTO_LOCAL', $_SERVER['DOCUMENT_ROOT'] . "/PHP/00-Annonceo_V1.0/img/" );

// Variables
$content = '';
$error = '';

// Inclusion des fonctions
require_once 'fonction.inc.php';