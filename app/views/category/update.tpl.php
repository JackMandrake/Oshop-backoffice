<div class="container my-4">
    <a href="<?= $router->generate('category-list') ?>" class="btn btn-success float-right">Retour</a>
    <h2>Editer une catégorie</h2>

    <?php if (isset($errorList)): ?>
        <?php foreach($errorList as $error): ?>
            <div class="alert alert-danger" role="alert">
                <?=$error ?>            
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="" method="POST" class="mt-5">
        <input type="hidden" value="<?=$category->getId() ?>" name="category_id" />
        <input type="hidden" name="tokenCSRF" value="<?= $tokenCSRF?>" >
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" 
                   id="name" placeholder="Nom de la catégorie"
                   value="<?=$category->getName() ?>"
                   name="category_name">
        </div>
        <div class="form-group">
            <label for="subtitle">Sous-titre</label>
            <input type="text" 
                   class="form-control" id="subtitle" 
                   placeholder="Sous-titre" aria-describedby="subtitleHelpBlock"
                   value="<?=$category->getSubTitle() ?>"
                   name="category_subtitle">
            <small id="subtitleHelpBlock" class="form-text text-muted">
                Sera affiché sur la page d'accueil comme bouton devant l'image
            </small>
        </div>
        <div class="form-group">
            <label for="picture">Image</label>
            <input type="text" 
                   class="form-control" 
                   id="picture" placeholder="image jpg, gif, svg, png" aria-describedby="pictureHelpBlock"
                   value="<?=$category->getPicture() ?>"
                   name="category_picture">
            <small id="pictureHelpBlock" class="form-text text-muted">
                URL relative d'une image (jpg, gif, svg ou png) fournie sur <a href="https://benoclock.github.io/S06-images/" target="_blank">cette page</a>
            </small>
        </div>
        <div class="form-group">
            <label for="home_order">Home order</label>
            <input type="number" 
                   class="form-control" id="home_order" 
                   placeholder="Ordre dans la Home" aria-describedby="homeOrderHelpBlock" name="home_order" 
                   value="<?= $category->getHomeOrder() ?>">
            <small id="homeOrderHelpBlock" class="form-text text-muted">
                Ordre d'affichage dans le footer
            </small>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>
    </form>
</div>