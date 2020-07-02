<div class="container my-4">
    <a href="<?= $router->generate('brand-list') ?>" class="btn btn-success float-right">Retour</a>
    <h2>Edition d'une marque</h2>

    <?php if (isset($errorList)) : ?>
        <?php foreach ($errorList as $error) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="" method="POST" class="mt-5">
        <div class="form-group">
            <label for="name">Nom</label>
            <input brand="text" class="form-control" id="name" placeholder="Nom de la catégorie" name="brand_name" , value="<?= $brand->getName() ?>">
        </div>
        <div class="form-group">
            <label for="footer_order">Footer order</label>
            <input brand="number" class="form-control" id="footer_order" placeholder="Ordre dans le Footer" aria-describedby="footerOrderHelpBlock" name="footer_order" value="<?= $brand->getFooterOrder() ?>">
            <small id="footerOrderHelpBlock" class="form-text text-muted">
                Ordre d'affichage dans le footer
            </small>
        </div>
        <button brand="submit" class="btn btn-primary btn-block mt-5">Valider</button>
    </form>
</div>