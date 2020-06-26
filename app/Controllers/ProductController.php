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
