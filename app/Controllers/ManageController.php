<?php 

namespace App\Controllers;

use App\Models\Category;

/**
 * Cette classe va permettre la gestion générale de la page d'accueil :
 * - Ordre des catégories
 * - Ordre des marques dans le footer (potentiellement)
 * - ...
 */
class ManageController extends CoreController {

    /**
     * Une méthode qui va afficher le formulaire 
     * de gestion des catégories en page d'accueil
     *
     * @return void
     */
    public function category() {

        // On veut rendre dynamique l'affichage de la gestion des catégories
        // On va donc récupérer les catégories présentes en BDD
        $categories = Category::findAll();

        dump($categories);

        // On appelle la vue views/manage/category.tpl.php
        $this->show('manage/category', [
            'categories' => $categories,
            'tokenCSRF' => $this->generateToken()
        ]);
    }

    public function categoryHome() {
        // $emplacement = filter_input_array(INPUT_POST);
        // Filtrage des données récupérées dans le formulaire

        $tokenCSRF = filter_input(INPUT_POST, 'tokenCSRF');

        /**
         * Je m'assure que le token (jeton) présent dans le formulaire est conforme
         * à celui qui est présent en Session.
         */
        $this->checkTokenCSRF($tokenCSRF);

        $emplacement = filter_input(INPUT_POST, 'emplacement', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        /**
         * emplacement est un tableau qui fait correspondre :
         * 1) le numéro de l'emplacement (home order-1) ==> à Gauche du tableau (index)
         * 2) La catégorie sélectionnée (son ID ==> getId) ==> à Droit du tableau (value)
         */
        // array:5 [▼
        //     0 => "1"
        //     1 => "2"
        //     2 => "3"
        //     3 => "4"
        //     4 => "15"
        // ]

        // 0) Reset des catégories : pour éviter des doublons dans les valeurs du Home order
        Category::resetHomeOrder();

        /**
         * Pour chaque (boucle) emplacement, je viens modifier le Home order
         * avec la valeur recupérée dans le tableau $emplacement
         */
        // $emplacement1 = $emplacement[0];
        // $emplacement2 = $emplacement[1];
        // ...
        // $emplacement5 = $emplacement[4];
        foreach($emplacement as $indexTableauEmplacement => $categoryId) {
            // dd($emplacement);

            // 1) On incrémente de 1 pour avoir la valeur du Home order
            $homeOrder = $indexTableauEmplacement + 1;

            // 2) On peut désormais récupérer l'objet catégorie avec le bon identifiants
            $currentCategory = Category::find($categoryId);

            // 3) Mise à jour du Home order de la catégorie
            $currentCategory->setHomeOrder($homeOrder);

            // On sauvegarde le nouvel emplacement de la catégorie
            $currentCategory->save();
        }
        

        // Redirection vers le formulaire de gestion des catégories 
        global $router;
        header('Location:' .$router->generate('manage-category'));
        exit();
    }
}