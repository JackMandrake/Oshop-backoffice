<div class="container my-4">
    <a href="<?= $router->generate('product-list') ?>" class="btn btn-success float-right">Retour</a>
    <h2>Edition d'un produit</h2>

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
            <input type="text" class="form-control" id="name" placeholder="Nom du produit" value="<?= $product->getName() ?>" name="name">
        </div>
        <div class="form-group">
            <label for="price">Prix</label>
            <input type="number" class="form-control" id="price" placeholder="Combien ça coute ?" value="<?= $product->getPrice() ?>" name="price">
            <small id="priceHelpBlock" class="form-text text-muted">
                Le prix du produit
            </small>
        </div>
        <div class="form-group">
            <label for="status">Statut</label>
            <select class="custom-select" id="status" name="status" aria-describedby="statusHelpBlock">
                <?php 
                    $optionStatus1 = '';
                    $optionStatus2 = '';
                    $statusValue = $product->getStatus();

                    if ($statusValue === '1') {
                        $optionStatus1 = 'selected';
                    }
                    if ($statusValue === '2') {
                        $optionStatus2 = 'selected';
                    }

                ?>
                <option value="1" <?= $optionStatus1 ?>>Disponible</option>
                <option value="2" <?= $optionStatus2 ?>>Non disponible</option>
            </select>
            <small id="statusHelpBlock" class="form-text text-muted">
                Le statut du produit
            </small>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" placeholder="Décrire le produit" aria-describedby="descriptionHelpBlock" value="<?= $product->getDescription() ?>" name="description">
            <small id="descriptionHelpBlock" class="form-text text-muted">
                La description du produit
            </small>
        </div>
        <div class="form-group">
            <label for="picture">Image</label>
            <input type="text" class="form-control" id="picture" placeholder="image jpg, gif, svg, png" aria-describedby="pictureHelpBlock" value="<?= $product->getPicture() ?>" name="picture">
            <small id="pictureHelpBlock" class="form-text text-muted">
                URL relative d'une image (jpg, gif, svg ou png) fournie sur <a href="https://benoclock.github.io/S06-images/" target="_blank">cette page</a>
            </small>
        </div>

        <div class="form-group">
            <label for="category">Catégorie</label>
            <select class="custom-select" id="category" name="category_id" aria-describedby="categoryHelpBlock" value="<?= $product->getCategoryId() ?>">
                <option value="1" <?= $product->getCategoryId() === '1' ? 'selected':'' ?>>Détente</option>
                <option value="2" <?= $product->getCategoryId() === '2' ? 'selected':'' ?>>Au travail</option>
                <option value="3" <?= $product->getCategoryId() === '3' ? 'selected':'' ?>>Cérémonie</option>
            </select>
            <small id="categoryHelpBlock" class="form-text text-muted">
                La catégorie du produit
            </small>
        </div>
        <div class="form-group">
            <label for="brand">Marque</label>
            <select class="custom-select" id="brand" name="brand_id" aria-describedby="brandHelpBlock" value="<?= $product->getBrandId() ?>">
                <option value="1" <?= $product->getBrandId() === '1' ? 'selected':'' ?>>oCirage</option>
                <option value="2" <?= $product->getBrandId() === '2' ? 'selected':'' ?>>BOOTstrap</option>
                <option value="3" <?= $product->getBrandId() === '3' ? 'selected':'' ?>>Talonette</option>
            </select>
            <small id="brandHelpBlock" class="form-text text-muted">
                La marque du produit
            </small>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select class="custom-select" id="type" name="type_id" aria-describedby="typeHelpBlock" value="<?= $product->getTypeId() ?>">
                <option value="1">Chaussures de ville</option>
                <option value="2">Chaussures de sport</option>
                <option value="3">Tongs</option>
            </select>
            <small id="typeHelpBlock" class="form-text text-muted">
                Le type de produit
            </small>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>
    </form>
</div>