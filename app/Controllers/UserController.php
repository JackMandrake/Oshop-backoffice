<?php

namespace App\Controllers;

use App\Models\AppUser;

class UserController extends CoreController
{

    /**
     * Affiche un formulaire de connexion
     * 
     * Méthode HTTP : GET
     *
     * @return void
     */
    public function login()
    {

        /**
         * Affiche le template views/user/login.tpl.php
         */
        $this->show('user/login', [
            // On transmet ici Token pour le mettre à dispos dans la vue courante 
            // celui-ci est aléatoire et propre à celle-ci
            'tokenCSRF' => $this->generateToken()
        ]);
    }

    /**
     * Méthode permettant le traitement des données issues du formulaire de connexion
     *
     * @return void
     */
    public function validate()
    {
        // dd($_POST);
        /**
         * On récupère les données du formulaire de connexion
         * et on en profite pour les nettoyer (sanitize).
         */
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        // dump($email, $password);

        $errorList = [];
        if (empty($email)) {
            $errorList[] = 'Merci de saisir un Email';
        }
        if (empty($password)) {
            $errorList[] = 'Merci de saisir un mot de passe';
        }

        /**
         * Si je n'ai pas d'erreur...
         */
        if (empty($errorList)) {
            // ... alors je peux récupérer les données de l'utilisateur...
            // on va pouvoir chercher s'il y a un enregistrement dans la table app_user 
            // pour l'email fourni
            $currentUser = AppUser::findByEmail($email);
            // dd($currentUser);
            if ($currentUser) {
                // Un utilisateur (avec cet Email) existe en base de données

                global $router;

                // ... Mais a t-il saisi le bon mot de passe ?
                // if ($currentUser->getPassword() === $password) {
                if (password_verify($password, $currentUser->getPassword())) {
                    // dd('OK !!!');
                    /**
                     * Tout utilisateur authentifié n'a pas le droit de TOUT faire !!
                     * 
                     * Solution : on va mettre en place un système d'Authorisation en se
                     * basant sur le rôle de chaque utilisateur
                     */
                    $_SESSION['userId'] = $currentUser->getId();
                    $_SESSION['userObject'] = $currentUser;

                    // Redirect vers la Home
                    header('Location: ' . $router->generate('main-home'));
                } else {
                    $errorList[] = 'Email ou mot de passe invalide';
                }
            } else {
                // Aucun utilisateur (avec cet Email) n'existe en base de données
                $errorList[] = 'Email ou mot de passe invalide';
            }
        }

        if (!empty($errorList)) {
            // Sinon...on affiche un message d'erreur dans le formulaire de connexoin
            $this->show('user/login', [
                'errorList' => $errorList
            ]);
        }
    }

    /**
     * Une méthode permettant d'afficher la liste des utilisateurs
     *
     * @return void
     */
    public function list()
    {

        // On restreint l'affichage de la page aux admins uniquement
        // On va effectuer la vérification dans le CoreController grâce aux ACL
        // $this->checkAuthorisation(['admin']);

        // On récupère les utilisateurs en appellant la méthode findAll du modèle AppUser
        // Les Models sont dans le dossiers == app/Models
        $users = AppUser::findAll();

        // On affiche le template views/user/list.tpl.php
        $this->show('user/list', [
            'users' => $users
        ]);
    }

    public function add()
    {

        $this->show('user/add', [
            'user' => new AppUser(),

            // On transmet ici Token pour le mettre à dispos dans la vue courante 
            // celui-ci est aléatoire et propre à celle-ci
            'tokenCSRF' => $this->generateToken()
        ]);
    }

    public function update($user_id)
    {

        $this->show('user/add', [
            'user' => AppUser::find($user_id),

            // On transmet ici Token pour le mettre à dispos dans la vue courante 
            // celui-ci est aléatoire et propre à celle-ci
            'tokenCSRF' => $this->generateToken()
        ]);
    }

    /**
     * Methode permettant de gérer l'ajout ou la mise à jour d'un utilisateur
     *
     * @param int $user_id
     * @return void
     */
    public function createOrUpdate($user_id = null)
    {

        // Si mon user_id est null ==> je suis dans le cas d'un ajout (add)
        if (is_null($user_id)) {
            $user = new AppUser(); // un objet user vide
        } else {
            // Sinon, je suis dans le cas d'une mise à jour d'un utilisateur
            $user = AppUser::find($user_id);
        }

        // Filtrage de données
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);

        // dd : dump + die
        // dd($email, $password, $firstname, $lastname, $role, $status);

        // Je peux à présent pré-remplir mon objet
        // $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setRole($role);
        $user->setStatus($status);

        $errorList = [];
        if (empty($email)) {
            $errorList[] = 'Vous devez saisir un email';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorList[] = 'Vous devez saisir un email valide';
        }

        if (empty($password)) {
            $errorList[] = 'Vous devez saisir un mot de passe';
        }

        if (!preg_match("/@/i", $password)) {
            $errorList[] = 'Le mot de passe doit contenir un arobase';
        }

        if (empty($firstname)) {
            $errorList[] = 'Vous devez saisir un prénom';
        }

        if (empty($lastname)) {
            $errorList[] = 'Vous devez saisir un nom';
        }

        if (empty($role)) {
            $errorList[] = 'Vous devez saisir un role';
        }

        if (empty($status)) {
            $errorList[] = 'Vous devez saisir un status';
        }

        // S'il n'y a pas d'erreur...
        if (empty($errorList)) {
            // On va pouvoir sauvegarder les données de mon utilisateur
            $queryExecuted = $user->save();
            /// 4) Si mes données ont correctement été ajoutées/sauvegardées en base...
            if ($queryExecuted) {
                // On va rediriger l'utilisateur vers une autre page, pour éviter
                // une double soumission du formulaire (Ex : double virement bancaire...)
                global $router;
                if (is_null($user_id)) {
                    // Dans le cas d'un ajout de produit, on est redirigé vers la liste de tous les 
                    // produits
                    $urlLocation = $router->generate('user-list');
                } else {
                    // Dans le cas d'une mise à jour de produit, on est redirigé vers 
                    // le formulaire de mise à jour du produit courant
                    $urlLocation = $router->generate('user-update', ['user_id' => $user_id]);
                }
                header('Location: ' . $urlLocation);
                return; // Le return permet un arrêt plus cordial du script. exit = Claquer la porte au nez...Pas cool;
            } else {
                $errorList[] = 'L\'insertion des données s\'est mal passée';
            }
        }

        // Si on a des erreurs, on les affiche dans la vue
        if (!empty($errorList)) {
            $this->show('user/add', [
                'errorList' => $errorList,
                'user' => $user
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
        $user = AppUser::find($id);

        $queryExecuted = $user->delete();

        if ($queryExecuted) {
            // Si aucune erreur dans la suppresion, on redirige vers la liste de produits
            global $router;
            $urlLocation = $router->generate('user-list');
            header('Location: ' . $urlLocation);
        }
    }

    /**
     * Méthode permettant de déconnecter l'utilisateur courant
     *
     * @return void
     */
    public function logout()
    {
        // dd('Logout');

        // On supprime les données utilisateur
        unset($_SESSION['userId']);
        unset($_SESSION['userObject']);

        //.. avant de faire une redirection vers la page d'accueil
        // Redirect vers la Home
        global $router;
        header('Location: ' . $router->generate('user-login'));
        exit();
    }
}
