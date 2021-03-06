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
        
        // On va vérifier les authorisations : Admin et Catalogue manager
        $this->checkAuthorisation(['admin', 'catalog-manager']);

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
     * URL : /category/add
     * Méthode HTTP : GET
     *
     * @return void
     */
    public function add() {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        // On va appeler (require) le fichier views/category/add.tpl.php
        /**
         * Les méthodes add et create utilisent le même template (add.tpl.php)
         * 
         * Or dans la méthode create on passe en argument un objet $category, ce qui déclenchait une erreur lorsqu'on essayait d'afficher la page d'ajout en GET.
         * 
         * Solutions : 
         * 
         * 1) Créer des vues (templates) différents pour les méthodes
         * 2) Faire un test d'existence (isset :: is Set ? Est ce définit) de la variable $category dans le template add.tpl.php.
         * 
         * 3) Transmettre un objet vide (new Category()) à la vue depuis la méthode add (voir ci-dessous)
         * 
         */
        $this->show('category/add', [
            'category' => new Category(),

            // On transmet ici Token pour le mettre à dispos dans la vue courante 
            // celui-ci est aléatoire et propre à celle-ci
            'tokenCSRF'=> $this->generateToken()
        ]);
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

        $dataToDisplay = [
            'category' => $category,
            // On transmet ici Token pour le mettre à dispos dans la vue courante 
            // celui-ci est aléatoire et propre à celle-ci
            'tokenCSRF'=> $this->generateToken()
        ];
        // On va appeler (require) le fichier views/category/update.tpl.php
        $this->show('category/update', $dataToDisplay);
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
        
        $name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);
        $subtitle = filter_input(INPUT_POST, 'category_subtitle', FILTER_SANITIZE_STRING);
        $picture = filter_input(INPUT_POST, 'category_picture', FILTER_SANITIZE_URL);
        $homeOrder = filter_input(INPUT_POST, 'home_order', FILTER_SANITIZE_NUMBER_INT);

        // Vérification csrf unitaire 
        // ==> Une vérification plus globale est faite dans le CoreController (méthode : checkTokenCsrfByRouteName)
        // $tokenCSRF = filter_input(INPUT_POST, 'tokenCSRF', FILTER_SANITIZE_STRING);
        // $this->checkTokenCSRF($tokenCSRF);

        
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
                
        if (empty($subtitle)) {
            $errorList[] = 'Vous devez saisir un sous-titre';
        }
        
        if (empty($picture)) {
            $errorList[] = 'Vous devez saisir une image sous forme d\'url';
        }

        if (empty($homeOrder)) {
            $errorList[] = 'Vous devez saisir un Home order';
        }
        
        // dump($_POST, $name, $subtitle, $picture, $errorList);

        // S'il n'y a aucune erreur dans les données...
        // Si le tableau d'erreur est vide
        if (empty($errorList)) {
            // On va créer notre catégorie


            // 1) On crée un objet categorie vide
            $newCategory = new Category();

            // 2) On vient renseigner les données à sauvegarder dans notre objet
            $newCategory->setName($name);
            $newCategory->setSubtitle($subtitle);
            $newCategory->setPicture($picture);
            $newCategory->setHomeOrder($homeOrder);

            // J'appelle la méthode insert qui va créer une requete d'insertion
            // de mes données en Base de données.
            $inserted = $newCategory->insert();

            /// Si mes données ont correctement été ajoutées en base...
            if ($inserted) {
                // On va rediriger l'utilisateur vers une autre page, pour éviter
                // une double soumission du formulaire (Ex : double virement bancaire...)
                global $router;
                // header('Location: /category/list');
                header('Location: '. $router->generate('category-list'));
                return; // Le return permet un arrêt plus cordial du script PHP. exit = Claquer la porte au nez...Pas cool;
            } else {
                $errorList[] = 'L\'insertion des données s\'est mal passée';
            }
        }

        // S'il y a des erreurs dans les données ou l'insert...
        // Si le tableau d'erreur n'est pas (!) vide
        if (!empty($errorList)) {
            // On affiche les erreurs dans le formulaire de saisie d'une catégorie
            // On va appeler (require) le fichier views/category/add.tpl.php
            $category = new Category();
            $category->setName($name);
            $category->setSubtitle($subtitle);
            $category->setPicture($picture);
            $category->setHomeOrder($homeOrder);

            $this->show('category/add', [
                'errorList' => $errorList,
                'category' => $category
            ]);
        }

    }

    /**
     * Méthode permettant de mettre à jour une catégorie de produit
     *
     * @return void
     */
    public function edit($category_id) {
        
        
        $name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);
        $subtitle = filter_input(INPUT_POST, 'category_subtitle', FILTER_SANITIZE_STRING);
        $picture = filter_input(INPUT_POST, 'category_picture', FILTER_SANITIZE_URL);
        $homeOrder = filter_input(INPUT_POST, 'home_order', FILTER_SANITIZE_NUMBER_INT);

        // 1) On récupère les données de la catégorie dont l'identifiant est category_id
        $category = Category::find($category_id);

        // 2) On vient mettre à jour les données dans notre objet
        $category->setName($name);
        $category->setSubtitle($subtitle);
        $category->setPicture($picture);
        $category->setHomeOrder($homeOrder);

        // dd($name, $subtitle, $picture, $_POST);
        
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
                
        if (empty($subtitle)) {
            $errorList[] = 'Vous devez saisir un sous-titre';
        }
        
        if (empty($picture)) {
            $errorList[] = 'Vous devez saisir une image sous forme d\'url';
        }

        if (empty($homeOrder)) {
            $errorList[] = 'Vous devez saisir un Home order';
        }
        
        // dump($_POST, $name, $subtitle, $picture, $errorList);

        // S'il n'y a aucune erreur dans les données...
        // Si le tableau d'erreur est vide
        if (empty($errorList)) {
            // On va mettre à jour notre catégorie

            // J'appelle la méthode update qui va créer une requete de mise à jour
            // de mes données en Base de données.
            // updated est un booléen qui est vrai si la requete s'est bien passée
            // et faux si la req s'est mal passée.
            $updated = $category->update();

            /// Si mes données ont correctement été mises à jour en base...
            if ($updated) {
                // On va rediriger l'utilisateur vers le formulaire d'édition de la catégorie
                global $router;
                $urlRedirect = $router->generate('category-update', 
                                                ['category_id'=>$category_id]);
                header('Location: '. $urlRedirect);
                return; // Le return permet un arrêt plus cordial du script PHP. exit = Claquer la porte au nez...Pas cool;
            } else {
                $errorList[] = 'L\'insertion des données s\'est mal passée';
            }
        }

        // S'il y a des erreurs dans les données ou l'insert...
        // Si le tableau d'erreur n'est pas (!) vide
        if (!empty($errorList)) {
            // On affiche les erreurs dans le formulaire de saisie d'une catégorie
            // On va appeler (require) le fichier views/category/update.tpl.php

            $this->show('category/update', [
                'errorList' => $errorList,
                'category' => $category
            ]);
        }

    }


    /**
     * Méthode permettant la suppression d'une entrée en base de données
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id) {
        $category = Category::find($id);
        $queryExecuted = $category->delete();

        if ($queryExecuted) {
            // Si aucune erreur dans la suppresion, on redirige vers la liste de produits
            global $router;
            $urlLocation = $router->generate('category-list');
            header('Location: '.$urlLocation );
        }
    }

}