<?php
namespace App\Models;

use App\Models\CoreModel;
use App\Utils\Database;

use PDO;

/**
 * Pour chaque model, on met en place un système de CRUD (Create Read Update Delete) :
 * - Create : insert
 * - Read : find et FindAll
 * - Update : update
 * - Delete : delete
 */
class AppUser extends CoreModel {
    /**
     * On définit les propriétés de la classe
     */

    private $email;
    private $password;
    private $firstname;
    private $lastname;
    private $role;
    private $status;

    /**
     * Méthode permettant de récupérer un enregistrement de la table Product en fonction d'un id donné
     * 
     * @param int $productId ID du produit
     * @return Product
     */
    public static function find($userId)
    {
        // récupérer un objet PDO = connexion à la BDD
        $pdo = Database::getPDO();

        // on écrit la requête SQL pour récupérer le produit
        $sql = '
            SELECT *
            FROM `app_user`
            WHERE id = :id';

        // Je prépare la requete de select
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute([
            ':id'=>$userId
        ]);

        // fetchObject() pour récupérer un seul résultat
        // si j'en avais eu plusieurs => fetchAll
        $result = $pdoStatement->fetchObject(self::class);
        
        return $result;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table app_user
     * 
     * @return AppUser[]
     */
    public static function findAll()
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `app_user`';
        $pdoStatement = $pdo->query($sql);
        // $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\AppUser');
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);
        
        return $results;
    }

    /**
     * Méthode permettant de rechercher un utilisateur en fonction de son Email.
     *
     * @param [type] $email
     * @return AppUser ou Bool
     */
    public static function findByEmail($email) {
        $sql = 'SELECT * from app_user WHERE email=:email';

        $pdo = Database::getPDO();

        /**
         * On prépare la requete SQL pour permette 
         * un "select" propre (sans injection SQL)
         */
        $pdoStatement = $pdo->prepare($sql);

        /**
         * bindValue permet de faire la correspondance entre l'alias (:email)
         * et la valeur réelle ($email)
         */
        $pdoStatement->bindValue(':email', $email);

        /**
         * J'execute la requete
         */
        $pdoStatement->execute();

        // return $pdoStatement->fetchObject('App\Models\AppUser');
        return $pdoStatement->fetchObject(self::class);

    }

    /**
     * Methode permettant de supprimer des données en BDD
     *
     * @return void
     */
    public function delete() {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête DELETE : avec des alias pour empécher les injections SQL
        $sql = "
            DELETE FROM `app_user`
            WHERE id = :id
        ";

        $query = $pdo->prepare($sql);

        $query->execute([
            ':id' => $this->id,
        ]);


        $deletedRows = $query->rowCount();

        // On retourne VRAI, si au moins une ligne ajoutée
        return ($deletedRows > 0);    
    }

    /**
     * Méthode permettant d'ajouter un enregistrement en base de données
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     * 
     * @return bool
     */
    public function insert()
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête INSERT INTO
        $sql = "
            INSERT INTO `app_user` (email, password, firstname, lastname, status, role)
            VALUES (:email, :password, :firstname, :lastname, :status, :role)
        ";
        /**
         * On va déléguer le traitement des données du formulaire à PDO, pour
         * éviter les injections SQL.
         * 
         * La méthode PDO::prepare — Prépare une requête à l'exécution et retourne un objet
         */
        $pdoStatement = $pdo->prepare($sql);

        /**
         * On peut envoyer les données « brutes (parce que provenant du client, dont on a pas confiance) » 
         * à execute() en arguments, qui va les "sanitize" pour SQL, tout en executant la requete. 
         * 
         * C'est la méthode TOUT en UN (couteau suisse)
         */
        $pdoStatement->execute([
            ':email' => $this->email,
            ':password' => $this->password,
            ':firstname' => $this->firstname,
            ':lastname' => $this->lastname,
            ':status' => $this->status,
            ':role' => $this->role,
        ]);
        
        // On récupère le nombre d'élements affectés par la requete. 
        // Puisqu'on qu'on insert q'une seule
        // données à la fois, on aura toujours $insertedRows = 1.
        $insertedRows = $pdoStatement->rowCount();

        // Si au moins une ligne ajoutée
        if ($insertedRows === 1) {
            // Alors on récupère l'id auto-incrémenté généré par MySQL
            $this->id = $pdo->lastInsertId();

            // On retourne VRAI car l'ajout a parfaitement fonctionné
            return true;
            // => l'interpréteur PHP sort de cette fonction car on a retourné une donnée
        }
        
        // Si on arrive ici, c'est que quelque chose n'a pas bien fonctionné => FAUX
        return false;
    }


    /**
     * Méthode permettant de mettre à jour les données en base
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     * 
     * @return bool
     */
    public function update()
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête UPDATE
        $sql = "
            UPDATE `app_user`
            SET
                email= :email,
                password=:password, 
                firstname=:firstname, 
                lastname=:lastname, 
                status=:status, 
                role=:role,
                updated_at = NOW()
            WHERE id = :id
        ";
        /**
         * On va déléguer le traitement des données du formulaire à PDO, pour
         * éviter les injections SQL.
         * 
         * La méthode PDO::prepare — Prépare une requête à l'exécution et retourne un objet
         */
        $query = $pdo->prepare($sql);

        /**
         * On peut envoyer les données « brutes (parce que provenant du client, dont on a pas confiance) » 
         * à execute() en arguments, qui va les "sanitize" pour SQL, tout en executant la requete. 
         * 
         * C'est la méthode TOUT en UN (couteau suisse)
         */
        $query->execute([
            ':email' => $this->email,
            ':password' => $this->password,
            ':firstname' => $this->firstname,
            ':lastname' => $this->lastname,
            ':status' => $this->status,
            ':role' => $this->role,
            ':id' => $this->id
        ]);
        
        // On récupère le nombre d'élements affectés par la requete. 
        // Puisqu'on qu'on update q'une seule
        // données à la fois, on aura toujours $updatedRows = 1.

        $updatedRows = $query->rowCount();

        // On retourne VRAI, si au moins une ligne ajoutée
        return ($updatedRows > 0);
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    /**
     * Get the value of firstname
     */ 
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */ 
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of lastname
     */ 
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @return  self
     */ 
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of role
     */ 
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */ 
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}