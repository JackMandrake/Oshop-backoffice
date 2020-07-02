<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

// Si j'ai besoin du Model Category
// use App\Models\Category;

class MainController extends CoreController
{

    /**
     * Méthode s'occupant de la page d'accueil
     *
     * @return void
     */
    public function home()
    {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        // On récupère 5 catégories triées par home_order
        $categories = Category::findAllHomepage();

        // On récupère 5 produits triées par created_at (date de création)
        $products = Product::findAllHomepage();

        $dataToDisplay = [
            'categories' => $categories,
            'products' => $products
        ];

        $this->show('main/home', $dataToDisplay);
    }
}
