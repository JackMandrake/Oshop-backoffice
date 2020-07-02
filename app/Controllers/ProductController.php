<?php

namespace App\Controllers;

use App\Models\CoreModel;
use App\Models\Product;

class ProductController extends CoreController
{

    /**
     * Page permettant d'afficher la liste des produits
     *
     * @return void
     */
    public function list()
    {
        // On va vérifier les authorisations : Admin only
        $this->checkAuthorisation(['admin']);

        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        $products = Product::findAll();

        $dataToDisplay = [
            'products' => $products
        ];

        // On va appeler (require) le fichier views/category/list.tpl.php
        $this->show('product/list', $dataToDisplay);
    }


    /**
     * Page permettant d'afficher le formulaire d'ajout d'un produit
     *
     * @return void
     */
    public function add()
    {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        // On va appeler (require) le fichier views/category/list.tpl.php
        // en transmettant des données vides (un objet vide)
        // Grâce à cela, on peut utilse le même template (add.tpl.php) à la fois pour
        // la méthode add et la méthode update (ci-dessous)
        $this->show('product/add', [
            'product' => new Product()
        ]);
    }

    /**
     * Page permettant de mettre à jour un produit
     *
     * @param int $product_id
     * @return void
     */
    public function update($product_id)
    {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        $product = Product::find($product_id);

        // On va appeler (require) le fichier views/category/update.tpl.php
        // en transmettant des données à afficher dans la vue
        $this->show('product/add', [
            'product' => $product,
            // On transmet ici Token pour le mettre à dispos dans la vue courante 
            // celui-ci est aléatoire et propre à celle-ci
            'tokenCSRF' => $this->generateToken()
        ]);
    }


    /**
     * Page permettant d'ajouter un nouveau produit ou de le mettre à jour
     * 
     * URL : /product/update
     * Méthode HTTP : GET et POST
     *
     * 
     * @param [$product_id] : 
     * - Dans le cas d'un ajout de produit (add) : $product_id égal à null
     * - Dans le cas d'une mise à jour de produit (add) : $product_id non null
     * @return void
     */
    public function createOrUpdate($product_id = null)
    {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $picture = filter_input(INPUT_POST, 'picture', FILTER_SANITIZE_URL);
        $brandId = filter_input(INPUT_POST, 'brand_id', FILTER_VALIDATE_INT);
        $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $typeId = filter_input(INPUT_POST, 'type_id', FILTER_VALIDATE_INT);

        if (is_null($product_id)) {
            // Si $product_id == null alors on dans le cas d'un ajout de produit
            // 1) On crée un objet produit vide
            $product = new Product();
            $viewName = 'product/add';
        } else {
            // Si $product_id est non null alors on dans le cas d'une MAJ de produit
            // 1.bis) On récupère un objet produit déjà existant en BDD
            $product = Product::find($product_id);
            $viewName = 'product/add';
        }

        // On peut aller récupérer dans un modèle tous les produits
        // $allTypes = Type::findAll()
        $allTypes = [
            [
                'value' => '1',
                'title' => 'Chaussures de ville'
            ],

            [
                'value' => '2',
                'title' => 'Chaussures de sport'
            ],

            [
                'value' => '3',
                'title' => 'Tongs'
            ]
        ];


        // dd($product_id, $name, $price, $status, $description, $picture, $brandId, $categoryId, $typeId);

        /**
         * Par défaut, toute donnée transmise par le client n'est pas fiable.
         * 
         * filter_input permet de s'assurer de la fiabilité des données transmises via
         * le formulaire en les nettoyant (Sanitize) ou en s'assurant de leur conformité (VALIDATE)
         * 
         * Valeurs de retour :
         * - Succès : on récupère la variable demandée
         * - Echec de  validation : on récupère un FALSE
         * - Si variable n'est pas définie : NULL
         */
        $errorList = [];
        if (empty($name)) {
            $errorList[] = 'Vous devez saisir un nom';
        }

        if (empty($description)) {
            $errorList[] = 'Vous devez saisir une description';
        }

        if (empty($picture)) {
            $errorList[] = 'Vous devez saisir une image sous forme d\'url';
        }

        // dump($_POST, $name, $subtitle, $picture, $errorList);

        // S'il n'y a aucune erreur dans les données...
        // Si le tableau d'erreur est vide
        if (empty($errorList)) {
            // ...On va donc créer notre produit

            // 2) On vient renseigner les données à sauvegarder dans notre objet
            $product->setName($name);
            $product->setStatus($status);
            $product->setPrice($price);
            $product->setDescription($description);
            $product->setPicture($picture);
            $product->setCategoryId($categoryId);
            $product->setTypeId($typeId);
            $product->setBrandId($brandId);

            // 3) J'appelle la méthode adapatée au contexte.
            // a) Si $product_id existe, alors on fait une mise à jour de produit
            // b) Si $product_id n'existe, alors on fait un insert de produit
            // if (is_null($product_id)) {
            //     $queryExecuted = $product->insert();
            // } else {
            //     $queryExecuted = $product->update();
            // }
            $queryExecuted = $product->save();

            /// 4) Si mes données ont correctement été ajoutées/sauvegardées en base...
            if ($queryExecuted) {
                // On va rediriger l'utilisateur vers une autre page, pour éviter
                // une double soumission du formulaire (Ex : double virement bancaire...)
                global $router;
                if (is_null($product_id)) {
                    // Dans le cas d'un ajout de produit, on est redirigé vers la liste de tous les 
                    // produits
                    $urlLocation = $router->generate('product-list');
                } else {
                    // Dans le cas d'une mise à jour de produit, on est redirigé vers 
                    // le formulaire de mise à jour du produit courant
                    $urlLocation = $router->generate('product-update', ['product_id' => $product_id]);
                }
                header('Location: ' . $urlLocation);
                return; // Le return permet un arrêt plus cordial du script. exit = Claquer la porte au nez...Pas cool;
            } else {
                $errorList[] = 'L\'insertion des données s\'est mal passée';
            }
        }

        // 5) S'il y a des erreurs dans les données ou l'insert...
        // Si le tableau d'erreur n'est pas (!) vide
        if (!empty($errorList)) {
            // On affiche les erreurs dans le formulaire de saisie d'une catégorie
            // On va appeler (require) le fichier views/category/add.tpl.php
            $product->setName($name);
            $product->setStatus($status);
            $product->setPrice($price);
            $product->setDescription($description);
            $product->setPicture($picture);
            $product->setCategoryId($categoryId);
            $product->setTypeId($typeId);
            $product->setBrandId($brandId);

            $this->show($viewName, [
                'errorList' => $errorList,
                'product' => $product
            ]);
        }
    }

    /**
     * Méthode permettant la suppression d'un produit en base de données
     *
     * @param [type] $product_id
     * @return void
     */
    public function delete($product_id)
    {
        $newProduct = Product::find($product_id);
        $queryExecuted = $newProduct->delete();

        if ($queryExecuted) {
            // Si aucune erreur dans la suppresion, on redirige vers la liste de produits
            global $router;
            $urlLocation = $router->generate('product-list');
            header('Location: ' . $urlLocation);
        }
    }
}
