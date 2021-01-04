<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="<?= URL ?>index1.php">LOGO</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="<?= URL ?>index1.php">Accueil</a>
      </li>

      <?php if(userConnect()) : //Si l'internaute est connecté, on affiche les liens profil, déposer une annonce et déconnexion
      ?>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Espace membre
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="nav-link" href="<?= URL ?>profil.php">Profil</a>
          <a class="nav-link" href="<?= URL ?>deposer_annonce.php?action=ajout">Déposer une annonce</a>
        </div>
      </li>

      <?php else : //Sinon, c'est qu'on n'est pas connecté, on affiche le lien inscription. ?>


        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Espace membre
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="nav-link" href="<?php echo URL ?>inscription.php">Inscription</a>
          <a class="nav-link" href="<?= URL ?>connexion.php">Connexion</a>
        </div>
      </li>

        

      <?php endif; ?>
      
      
          <!-- *** Back office *** -->

      <?php if(adminConnect()) : //Si l'admin est connecté :
      ?>
        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          BackOffice
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="<?= URL ?>admin/gestion_annonces_admin.php?action=affichage">Gestion des annonces</a>
          <a class="dropdown-item" href="<?= URL ?>admin/gestion_membres.php">Gestion des membres</a>
          <a class="dropdown-item" href="<?= URL ?>admin/gestion_categories.php?action=affichage">Gestion des catégories</a>
          <a class="dropdown-item" href="<?= URL ?>admin/gestion_commentaires_admin.php?action=affichage">Gestion des commentaires</a>
          <a class="dropdown-item" href="<?= URL ?>admin/statistiques.php">Statistiques</a>          
        </div> 
        </li> 

      <?php endif; ?>
       

      
      <?php if( adminConnect() || userConnect() ) : //Si un admin ou un utilisateur est connecté :
      ?>
      <li class="nav-item">
          <a class="nav-link" href="<?= URL ?>connexion.php?action=deconnexion">Déconnexion</a>
      </li>
      <?php endif; ?>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Valider</button>
    </form>
  </div>
</nav>



<main class="container-fluid">