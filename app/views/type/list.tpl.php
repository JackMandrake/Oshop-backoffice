<div class="container my-4">
    <a href="<?= $router->generate('type-add') ?>" class="btn btn-success float-right">Ajouter</a>
    <h2>Liste des types</h2>
    <table class="table table-hover mt-4">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nom</th>
                <th scope="col">Ordre dans le Footer</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($types as $type): ?>
            <tr>
                <th scope="row"><?= $type->getId() ?></th>
                <td><?= $type->getName() ?></td>
                <td><?= $type->getFooterOrder() ?></td>
                <td class="text-right">
                    <a href="<?=$router->generate('type-update', ['type_id'=>$type->getId()]) ?>" class="btn btn-sm btn-warning">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    </a>
                    <!-- Example single danger button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?=$router->generate('type-delete', ['type_id'=>$type->getId()]) ?>">Oui, je veux supprimer</a>
                            <a class="dropdown-item" href="#" data-toggle="dropdown">Oups !</a>
                        </div>
                    </div>
                </td>
            </tr> 
            <?php endforeach; ?>           
        </tbody>
    </table>
</div>