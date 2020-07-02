<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= $router->generate('main-home') ?>">oShop</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item<?= $currentPage == 'main/home' ? ' active' : '' ?>">
          <a class="nav-link" href="<?= $router->generate('main-home') ?>">Accueil <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item<?= in_array($currentPage, ['category/list', 'category/add', 'category/update']) ? ' active' : '' ?>">
          <a class="nav-link" href="<?= $router->generate('category-list') ?>">Catégories</a>
        </li>
        <li class="nav-item<?= in_array($currentPage, ['product/list', 'product/add']) ? ' active' : '' ?>">
          <a class="nav-link" href="<?= $router->generate('product-list') ?>">Produits</a>
        </li>
        <li class="nav-item<?= in_array($currentPage, ['type/list', 'type/add']) ? ' active' : '' ?>">
          <a class="nav-link" href="<?= $router->generate('type-list') ?>">Types</a>
        </li>
        <li class="nav-item<?= in_array($currentPage, ['brand/list', 'brand/add']) ? ' active' : '' ?>">
          <a class="nav-link" href="<?= $router->generate('brand-list') ?>">Marques</a>
        </li>
        <li class="nav-item<?= in_array($currentPage, ['tag/list', 'tag/add']) ? ' active' : '' ?>">
          <a class="nav-link" href="#">Tags</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $router->generate('manage-category') ?>">Sélections Accueil &amp; Footer</a>
        </li>
          
        <?php if (isset($_SESSION['userId'])) : ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?= $_SESSION['userObject']->getFirstName() ?>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="<?= $router->generate('user-list') ?>">Liste des utilisateurs</a>
              <a class="dropdown-item" href="<?= $router->generate('manage-category') ?>">Gestion des catégories</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?= $router->generate('user-logout') ?>">Déconnexion</a>
            </div>
          </li>
        <?php else: ?>
          <li class="nav-item">
              <a class="nav-link" href="<?= $router->generate('user-login') ?>">Connexion</a>
          </li>
        <?php endif; ?>

      </ul>
      <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Rechercher">
        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">Rechercher</button>
      </form>
    </div>
  </div>
</nav>