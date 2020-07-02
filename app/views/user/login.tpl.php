<div class="container my-4">
    <h3>Connexion</h3>
    <?php if (isset($errorList)) : ?>
        <?php foreach ($errorList as $error) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <form action="" method="post">
        <input type="hidden" name="tokenCSRF" value="<?= $tokenCSRF?>" >
        <div class="form-group">
            <label for="exampleInputEmail1">Email</label>
            <input type="email" 
                   class="form-control" 
                   id="exampleInputEmail1" 
                   aria-describedby="emailHelp" 
                   placeholder="Saisir un email valide"
                   name="email">
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Mot de passe</label>
            <input type="password" 
                   class="form-control" 
                   id="exampleInputPassword1" 
                   placeholder="Saisir un mot de passe"
                   name="password">
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>