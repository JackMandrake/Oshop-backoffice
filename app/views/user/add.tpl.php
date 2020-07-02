<div class="container my-4">
    <a href="<?= $router->generate('user-list') ?>" class="btn btn-success float-right">Retour</a>
    <h2>Edition d'un utilisateur</h2>

    <?php if (isset($errorList)) : ?>
        <?php foreach ($errorList as $error) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="" method="POST" class="mt-5">
        <input type="hidden" name="tokenCSRF" value="<?= $tokenCSRF?>" >
        <div class="form-group">
            <label for="lastname">Nom</label>
            <input type="text" class="form-control" id="lastname" placeholder="Saisir le Nom" name="lastname" , value="<?= $user->getLastName() ?>">
        </div>
        <div class="form-group">
            <label for="firstname">Prénom</label>
            <input type="text" class="form-control" id="firstname" placeholder="saisir le Prénom" name="firstname" , value="<?= $user->getFirstName() ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Saisir un email valide" name="email" value="<?= $user->getEmail() ?>">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" placeholder="Saisir le mot de passe" name="password">
        </div>

        <div class="form-group">
            <label for="status">Statut</label>
            <select class="custom-select" id="status" name="status" aria-describedby="statusHelpBlock" value="<?= $user->getStatus() ?>">
                <option value="1" <?= $user->getStatus() === '1' ? 'selected':'' ?>>Actif</option>
                <option value="2" <?= $user->getStatus() === '2' ? 'selected':'' ?>>Désactivé</option>                
            </select>
            <small id="statusHelpBlock" class="form-text text-muted">
                Le statut de l'utilisateur
            </small>
        </div>


        <div class="form-group">
            <label for="role">Role</label>
            <select class="custom-select" id="role" name="role" aria-describedby="roleHelpBlock" value="<?= $user->getRole() ?>">
            <option value="">-</option>
                <option value="admin" <?= $user->getRole() === 'admin' ? 'selected':'' ?>>Administrateur</option>
                <option value="catalog-manager" <?= $user->getRole() === 'catalog-manager' ? 'selected':'' ?>>Catalogue manager</option>                
            </select>
            <small id="roleHelpBlock" class="form-text text-muted">
                Le role de l'utilisateur
            </small>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>
    </form>
</div>