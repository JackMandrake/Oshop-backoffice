<?php

namespace App\Controllers;

class CoreController
{

    public function __construct()
    {
        // La variable $match contient les infos sur la route courante
        global $match;

        // On récupère la route courante
        $currentRoute = $match['name'];

        /**
         * 
         * Problématique : On veut regrouper la gestion des permissions dans un tableau plutot que dans
         * chaque controller 
         * 
         * Solution : Mise en place d'un système de vérification des authorization dès la
         * porte d'entrée 
         * (Image : Une soirée privée avec un vigil à l'entrée qui s'assure qu'on a le droit d'être.
         * On pourrait faire la vérification directement dans la salle des fêtes...Mais à l'entrée on gagne du temps ;-) )
         * 
         * En faisant cela, on met en place ce que l'on appelle un ACL (Access List)
         * 
         * ACL = la liste des permissions pour les routes nécessitant 
         * une connexion utilisateur
         */
        $acl =  [
            // 'main-home' => [], => pas besoin, la route est libre d'accès
            // 'user-login' => [], => pas besoin, la route est libre d'accès
            'user-list' => ['admin'],
            'user-add' => ['admin'],
            'user-delete' => ['admin'],
            'user-update' => ['admin'],
            'category-list' => ['admin', 'catalog-manager'],
            'category-add' => ['admin', 'catalog-manager'],
            'category-update' => ['admin', 'catalog-manager'],
            'category-delete' => ['admin', 'catalog-manager']
            // etc
        ];

        // Si la route existe bien dans mon acl (access list)
        if (array_key_exists($currentRoute, $acl)) {
            // ..Alors on récupère les roles autorisés
            $authorisedRoles = $acl[$currentRoute];

            // Et on appele la méthode checkAuthorisation, 
            // qui vérifie que le role de l'utilisateur courant lui donne accès à la route
            // demandée
            $this->checkAuthorisation($authorisedRoles);
        }
        // Sinon, on affiche la page de manière normal


        //============ Gestion des attaques CSRF en fonction de la route courante =====================
        // dd($match, $_POST, $_SESSION);
        $this->checkTokenCsrfByRouteName($currentRoute);
    }

    /**
     * Méthode permettant d'afficher du code HTML en se basant sur les views
     *
     * @param string $viewName Nom du fichier de vue
     * @param array $viewVars Tableau des données à transmettre aux vues
     * @return void
     */
    protected function show(string $viewName, $viewVars = [])
    {
        // On globalise $router car on ne sait pas faire mieux pour l'instant
        global $router;

        // Comme $viewVars est déclarée comme paramètre de la méthode show()
        // les vues y ont accès
        // ici une valeur dont on a besoin sur TOUTES les vues
        // donc on la définit dans show()        
        $viewVars['currentPage'] = $viewName; 

        // définir l'url absolue pour nos assets
        $viewVars['assetsBaseUri'] = $_SERVER['BASE_URI'] . 'assets/';
        // définir l'url absolue pour la racine du site
        // /!\ != racine projet, ici on parle du répertoire public/
        $viewVars['baseUri'] = $_SERVER['BASE_URI'];

        // On veut désormais accéder aux données de $viewVars, mais sans accéder au tableau
        // La fonction extract permet de créer une variable pour chaque élément du tableau passé en argument
        extract($viewVars);
        // => la variable $currentPage existe désormais, et sa valeur est $viewName
        // => la variable $assetsBaseUri existe désormais, et sa valeur est $_SERVER['BASE_URI'] . '/assets/'
        // => la variable $baseUri existe désormais, et sa valeur est $_SERVER['BASE_URI']
        // => il en va de même pour chaque élément du tableau

        // $viewVars est disponible dans chaque fichier de vue
        require_once __DIR__ . '/../views/layout/header.tpl.php';
        require_once __DIR__ . '/../views/' . $viewName . '.tpl.php';
        require_once __DIR__ . '/../views/layout/footer.tpl.php';
    }

    /**
     * Vérifie qu'on le droit d'accéder ou non à une ressource du site
     * Tout utilisateur authentifié n'a pas le droit de TOUT faire !!
     *
     * @param Array $roles
     * @return Bool
     */
    public function checkAuthorisation($listRoles)
    {
        global $router;

        // dd($_SESSION);

        // Si le user est connecté
        if (isset($_SESSION['userId'])) {

            // Alors on récupère l'utilisateur courant
            $currentUser = $_SESSION['userObject'];

            // On récupère le role
            $currentRole = $currentUser->getRole();

            // Si le rôle de l'utilisateur est présent dans la liste des rôles
            // autorisés pour la page courante
            if (in_array($currentRole, $listRoles)) {
                // Alors je retourne vrai
                return true;
            }
            // Sinon le user connecté n'a pas la permission d'accéder à la page (ressource)
            else {
                // ==> on lui renvoie un 403 (Non autorisé) : 403 Forbidden
                http_response_code(403);
                $this->show('error/err403');

                // On arrrête le script pour empecher tout autre action
                exit(); // Coup de pelle !
            }
        }
        // Sinon, l'internaute n'est pas connecté sur le site
        else {
            // On refuse l'accès à la ressource, et on le redirige vers la page de connexion
            header('Location: ' . $router->generate('user-login'));
            exit();
        }
    }

    /**
     * Cette méthode permet une vérification csurf plus intelligente
     * en s'apputant sur un tableau de routes à vérifier
     *
     * @return void
     */
    public function checkTokenCsrfByRouteName($routeName)
    {
        // On défini les routes (index.php ==> Routes en POST) que l'on veut protéger contre les attaques sea-surf
        // ATTENTION : les routes définies ici doivent avoir un token définit dans les formulaires associés (add.tpl.php ou update.tpl.php)
        $csrfTokenRoutesToCheck = [
            'category-add-post',
            'category-update-post',
            'user-add-post',
            'user-update-post'
            // etc.
        ];

        // Si la route actuelle nécessite la vérification d'un token anti-CSRF
        if (in_array($routeName, $csrfTokenRoutesToCheck)) {
            // On récupère le token en POST
            $token = filter_input(INPUT_POST, 'tokenCSRF');

            // dd($_POST, $_SESSION, $token);

            // Comparaison du token soumis en POST avec celui en Session
            $this->checkTokenCSRF($token);

        }
    }

    /**
     * Comparaison du token soumis en POST (formulaire) avec celui en Session
     *
     * @param [type] $token
     * @return void
     */
    public function checkTokenCSRF($token)
    {
        // On récupère le token en SESSION
        $sessionToken = isset($_SESSION['tokenCSRF']) ? $_SESSION['tokenCSRF'] : '';
        if ($token !== $sessionToken || empty($token)) {
            // Si le token soumis dans le formulaire est différent de celui qui est en Session
            // alors on arrete tout ==> On fait une redirection 403
            http_response_code(403);
            $this->show('error/err403');
            exit();
        } else {
            unset($_SESSION['tokenCSRF']);
        }
    }

    public function generateToken()
    {
        // Génération d'un token aléatoire (une clé)
        $_SESSION['tokenCSRF'] = bin2hex(random_bytes(32));

        return $_SESSION['tokenCSRF'];
    }
}
