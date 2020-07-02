<div class="container my-4">
    <a href="<?= $router->generate('type-list') ?>" class="btn btn-success float-right">Retour</a>
    <h2>Edition d'un type</h2>

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
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" placeholder="Nom de la catégorie" name="type_name" , value="<?= $type->getName() ?>">
        </div>
        <div class="form-group">
            <label for="footer_order">Footer order</label>
            <input type="number" class="form-control" id="footer_order" placeholder="Ordre dans le Footer" aria-describedby="footerOrderHelpBlock" name="footer_order" value="<?= $type->getFooterOrder() ?>">
            <small id="footerOrderHelpBlock" class="form-text text-muted">
                Ordre d'affichage dans le footer
            </small>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>
    </form>
</div>