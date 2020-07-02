<form action="" method="POST" class="mt-5">
    <input type="hidden" value="<?=$tokenCSRF ?>" name="tokenCSRF">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="emplacement1">Emplacement #1</label>
                <select class="form-control" id="emplacement1" name="emplacement[]">
                    <option value="">choisissez :</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?= $category->getId() ?>"
                                <?= $category->getHomeOrder() == 1 ? 'selected': '' ?>
                                >
                            <?= $category->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="emplacement2">Emplacement #2</label>
                <select class="form-control" id="emplacement2" name="emplacement[]">
                    <option value="">choisissez :</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?= $category->getId() ?>"
                                <?= $category->getHomeOrder() == 2 ? 'selected': '' ?>>
                            <?= $category->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="emplacement3">Emplacement #3</label>
                <select class="form-control" id="emplacement3" name="emplacement[]">
                    <option value="">choisissez :</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?= $category->getId() ?>"
                                <?= $category->getHomeOrder() == 3 ? 'selected': '' ?>>
                            <?= $category->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="emplacement4">Emplacement #4</label>
                <select class="form-control" id="emplacement4" name="emplacement[]">
                    <option value="">choisissez :</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?= $category->getId() ?>"
                                <?= $category->getHomeOrder() == 4 ? 'selected': '' ?>>
                            <?= $category->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="emplacement5">Emplacement #5</label>
                <select class="form-control" id="emplacement5" name="emplacement[]">
                    <option value="">choisissez :</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?= $category->getId() ?>"
                            <?= $category->getHomeOrder() == 5 ? 'selected': '' ?>>
                            <?= $category->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>
</form>