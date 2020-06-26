<?php 

namespace App\Controllers;

use App\Models\Category;

class CategoryController extends CoreController {
    
    /**
     * Page permettant d'afficher la liste des catégories
     *
     * @return void
     */
    public function list() {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller


        /**
         * C'est dans le controller que l'on va pouvoir via les Models récupérer des données
         * à transmettre à nos templates (vues)
         */

        // $categoryModel = new Category();

        /**
         * On peut directement appeler la méthode findAll (static), puisque celle-ci
         * n'est pas directement lié à un objet ($this).
         * 
         * NomClasseModel::methodeStatique
         * 
         * Comment savoir si ma méthode doit être statique ou non : 
         * - Si ma méthode a besoin d'appeler un $this, alors méthode de classe (non statique)
         * - Sinon, on peut la rendre statique
         */
        $categories = Category::findAll();

        dump($categories);

        // On va appeler (require) le fichier views/category/list.tpl.php
        // Et transmettre un tableau d'argument qui sera accessible depuis
        // nos templates html. 
        // La fonction extract va ensuite transformer la clé 'categories' en variable
        // $categories
        // Version 2 (courte)
        $dataToDisplay = [
            'categories' => $categories
        ];
        $this->show('category/list', $dataToDisplay);

        // Version 2 (courte)
        // $this->show('category/list', [
        //     'categories' => $categories
        // ]);
    }


    /**
     * Page permettant d'afficher un formulaire de category
     *
     * @return void
     */
    public function add() {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        // On va appeler (require) le fichier views/category/add.tpl.php
        $this->show('category/add');
    }


    /**
     * Page permettant de mettre à jour une catégorie
     *
     * @param $category_id
     * @return void
     */
    public function update($category_id) {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        $category = Category::find($category_id);

        $dataToDisplay = ['category' => $category];
        // On va appeler (require) le fichier views/category/update.tpl.php
        $this->show('category/update', $dataToDisplay);
    }
}