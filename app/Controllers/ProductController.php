<?php

namespace App\Controllers;

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
     * Page permettant d'ajouter une nouvelle categorie
     * 
     * URL : /category/add
     * Méthode HTTP : POST
     *
     * @return void
     */
    public function create() {
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


        // dd($name, $price, $status, $description, $picture, $brandId, $categoryId, $typeId);
        
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


            // 1) On crée un objet produit vide
            $newProduct = new Product();

            // 2) On vient renseigner les données à sauvegarder dans notre objet
            $newProduct->setName($name);
            $newProduct->setStatus($status);
            $newProduct->setPrice($price);
            $newProduct->setDescription($description);
            $newProduct->setPicture($picture);
            $newProduct->setCategoryId($categoryId);
            $newProduct->setTypeId($typeId);
            $newProduct->setBrandId($brandId);

            // 3) J'appelle la méthode insert qui va créer une requete d'insertion
            // de mes données en Base de données.
            $inserted = $newProduct->insert();

            /// 4) Si mes données ont correctement été ajoutées en base...
            if ($inserted) {
                // On va rediriger l'utilisateur vers une autre page, pour éviter
                // une double soumission du formulaire (Ex : double virement bancaire...)
                global $router;
                // header('Location: /product/list');
                header('Location: '. $router->generate('product-list'));
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
            $emptyProduct = new Product();
            $emptyProduct->setName($name);
            $emptyProduct->setStatus($status);
            $emptyProduct->setPrice($price);
            $emptyProduct->setDescription($description);
            $emptyProduct->setPicture($picture);
            $emptyProduct->setCategoryId($categoryId);
            $emptyProduct->setTypeId($typeId);
            $emptyProduct->setBrandId($brandId);

            $this->show('category/add', [
                'errorList' => $errorList,
                'product' => $emptyProduct
            ]);
        }

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
            'product' => $product
        ]);
    }
}
