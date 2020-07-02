<?php

namespace App\Controllers;

use App\Models\Type;

class TypeController extends CoreController
{

    /**
     * Page permettant d'afficher la liste des catégories
     *
     * @return void
     */
    public function list()
    {

        // On va vérifier les authorisations : Admin et Catalogue manager
        $this->checkAuthorisation(['admin', 'catalog-manager']);

        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller


        /**
         * C'est dans le controller que l'on va pouvoir via les Models récupérer des données
         * à transmettre à nos templates (vues)
         */

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
        $types = Type::findAll();

        $this->show('type/list', [
            'types' => $types
        ]);
    }


    /**
     * Page permettant d'afficher un formulaire de type
     * 
     * URL : /type/add
     * Méthode HTTP : GET
     *
     * @return void
     */
    public function add()
    {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        // On va appeler (require) le fichier views/type/add.tpl.php
        /**
         * Les méthodes add et create utilisent le même template (add.tpl.php)
         * 
         * Or dans la méthode create on passe en argument un objet $type, ce qui déclenchait une erreur lorsqu'on essayait d'afficher la page d'ajout en GET.
         * 
         * Solutions : 
         * 
         * 1) Créer des vues (templates) différents pour les méthodes
         * 2) Faire un test d'existence (isset :: is Set ? Est ce définit) de la variable $type dans le template add.tpl.php.
         * 
         * 3) Transmettre un objet vide (new Type()) à la vue depuis la méthode add (voir ci-dessous)
         * 
         */
        $this->show('type/add', [
            'type' => new Type(),

            // On transmet ici Token pour le mettre à dispos dans la vue courante 
            // celui-ci est aléatoire et propre à celle-ci
            'tokenCSRF' => $this->generateToken()
        ]);
    }

    /**
     * Page permettant de mettre à jour une catégorie
     *
     * @param $type_id
     * @return void
     */
    public function update($type_id)
    {
        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller

        $type = Type::find($type_id);

        // On va appeler (require) le fichier views/type/add.tpl.php
        $this->show('type/add', [
            'type' => $type,

            // On transmet ici Token pour le mettre à dispos dans la vue courante 
            // celui-ci est aléatoire et propre à celle-ci
            'tokenCSRF' => $this->generateToken()
        ]);
    }


    /**
     * Page permettant d'ajouter un nouveau produit ou de le mettre à jour
     * 
     * URL : /type/update
     * Méthode HTTP : GET et POST
     *
     * 
     * @param [$type_id] : 
     * - Dans le cas d'un ajout de produit (add) : $type_id égal à null
     * - Dans le cas d'une mise à jour de produit (add) : $type_id non null
     * @return void
     */
    public function createOrUpdate($type_id = null)
    {
        /**
         * Récupération et nettoyage de données
         */
        $name = filter_input(INPUT_POST, 'type_name', FILTER_SANITIZE_STRING);
        $footer_order = filter_input(INPUT_POST, 'footer_order', FILTER_VALIDATE_INT);


        if (is_null($type_id)) {
            // Si $type_id == null alors on dans le cas d'un ajout de produit
            // 1) On crée un objet produit vide
            $type = new Type();
        } else {
            // Si $type_id est non null alors on dans le cas d'une MAJ de produit
            // 1.bis) On récupère un objet produit déjà existant en BDD
            $type = Type::find($type_id);
        }

        // 2) On vient renseigner les données à sauvegarder dans notre objet
        $type->setName($name);
        $type->setFooterOrder($footer_order);


        // dd($type_id, $name, $price, $status, $description, $picture, $brandId, $categoryId, $typeId);

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

        if (empty($footer_order)) {
            $errorList[] = 'Vous devez saisir une description';
        }

        // dump($_POST, $name, $subtitle, $picture, $errorList);

        // S'il n'y a aucune erreur dans les données...
        // Si le tableau d'erreur est vide
        if (empty($errorList)) {
            // ...On va donc créer ou mettre à jour nos données

            // 3) On appelle la méthode save du CoreModel

            $queryExecuted = $type->save();

            /// 4) Si mes données ont correctement été ajoutées/sauvegardées en base...
            if ($queryExecuted) {
                // On va rediriger l'utilisateur vers une autre page, pour éviter
                // une double soumission du formulaire (Ex : double virement bancaire...)
                global $router;
                if (is_null($type_id)) {
                    // Dans le cas d'un ajout de produit, on est redirigé vers la liste de tous les 
                    // produits
                    $urlLocation = $router->generate('type-list');
                } else {
                    // Dans le cas d'une mise à jour de produit, on est redirigé vers 
                    // le formulaire de mise à jour du produit courant
                    $urlLocation = $router->generate('type-update', ['type_id' => $type_id]);
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

            // On appelle la méthode show() de l'objet courant
            // En argument, on fournit le fichier de Vue
            // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller
            $this->show('type/add', [
                'errorList' => $errorList,
                'type' => $type
            ]);
        }
    }

    /**
     * Méthode permettant la suppression d'une entrée en base de données
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id)
    {
        $newProduct = Type::find($id);
        $queryExecuted = $newProduct->delete();

        if ($queryExecuted) {
            // Si aucune erreur dans la suppresion, on redirige vers la liste de produits
            global $router;
            $urlLocation = $router->generate('type-list');
            header('Location: ' . $urlLocation);
        }
    }
}
