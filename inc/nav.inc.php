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

      <li class="nav-item">
        <a class="nav-link" href="<?= URL ?>panier.php">Panier</a>
      </li>

      <?php if(userConnect()) : //Si l'internaute est connecté, on affiche les liens profil et déconnexion
      ?>

        <li class="nav-item">
          <a class="nav-link" href="<?= URL ?>profil.php">Profil</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= URL ?>connexion.php?action=deconnexion">Déconnexion</a>
        </li>

      <?php else : //Sinon, c'est qu'on n'est pas connecté, on affiche le lien inscription. ?>

        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>inscription.php">Inscription</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= URL ?>connexion.php">Connexion</a>
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
          <a class="dropdown-item" href="<?= URL ?>admin/gestion_annonces_admin.php">Gestion des annonces</a>
          <a class="dropdown-item" href="<?= URL ?>admin/gestion_membres.php">Gestion des membres</a>
          <a class="dropdown-item" href="<?= URL ?>admin/gestion_categories.php">Gestion des catégories</a>
        </div> 

      <?php endif; ?>
       

      </li> 
    </ul>
  </div>
</nav>



<main class="container">