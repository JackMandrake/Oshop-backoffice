<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

/**
 * Une instance de Product = un produit dans la base de données
 * Product hérite de CoreModel
 */
class Product extends CoreModel {
    
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $picture;
    /**
     * @var float
     */
    private $price;
    /**
     * @var int
     */
    private $rate;
    /**
     * @var int
     */
    private $status;
    /**
     * @var int
     */
    private $brand_id;
    /**
     * @var int
     */
    private $category_id;
    /**
     * @var int
     */
    private $type_id;
    
    /**
     * Méthode permettant de récupérer un enregistrement de la table Product en fonction d'un id donné
     * 
     * @param int $productId ID du produit
     * @return Product
     */
    public static function find($productId)
    {
        // récupérer un objet PDO = connexion à la BDD
        $pdo = Database::getPDO();

        // on écrit la requête SQL pour récupérer le produit
        $sql = '
            SELECT *
            FROM product
            WHERE id = ' . $productId;

        // query ? exec ?
        // On fait de la LECTURE = une récupration => query()
        // si on avait fait une modification, suppression, ou un ajout => exec
        $pdoStatement = $pdo->query($sql);

        // fetchObject() pour récupérer un seul résultat
        // si j'en avais eu plusieurs => fetchAll
        $result = $pdoStatement->fetchObject('App\Models\Product');
        
        return $result;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table product
     * 
     * @return Product[]
     */
    public static function findAll()
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `product`';
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Product');
        
        return $results;
    }

    /**
     * Récupérer les 5 catégories mises en avant sur la home
     * 
     * @return Product[]
     */
    public static function findAllHomepage()
    {
        $pdo = Database::getPDO();
        $sql = '
            SELECT *
            FROM product
            ORDER BY created_at DESC
            LIMIT 5
        ';
        $pdoStatement = $pdo->query($sql);
        $categories = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Category');
        
        return $categories;
    }


    /**
     * Méthode permettant d'ajouter un enregistrement dans la table product
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
            INSERT INTO `product` (name, price, status, description, picture, category_id, brand_id, type_id)
            VALUES (:name, 
                    :price, 
                    :status, 
                    :description, 
                    :picture, 
                    :category_id, 
                    :brand_id, 
                    :type_id)
        ";
        /**
         * On va déléguer le traitement des données du formulaire à PDO, pour
         * éviter les injections SQL.
         * 
         * La méthode PDO::prepare — Prépare une requête à l'exécution et retourne un objet
         */
        $query = $pdo->prepare($sql);


        // Execution de la requête d'insertion (exec, pas query)
        // On peut utiliser la méthode bindValue pour chaque input 
        // qui va préparer nos données : Associer une valeur à un paramètre (Ex :name ==> $this->name)
        // Voir la documentation ici : https://www.php.net/manual/fr/pdostatement.bindvalue.php
        // $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        // $query->bindValue(':price', $this->price, PDO::PARAM_INT);
        // Le 3e argument permet de préciser "valeur numérique" (PDO::PARAM_INT) ou "autre" (PDO::PARAM_STR)

        // La méthode binValue nous obligerait quand même à appeler plus tard la méthode execute.
        // Exemple complet : 
        // $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        // $query->bindValue(':price', $this->price, PDO::PARAM_INT);
        // ...
        // $query->execute(); // j'execute la requete
        // ...
        // ...On peut faire ça (en deux étapes : binValue + execute)....Ou alors

        /**
         * On peut envoyer les données « brutes (parce que provenant du client, dont on a pas confiance) » 
         * à execute() en arguments, qui va les "sanitize" pour SQL, tout en executant la requete. 
         * 
         * C'est la méthode TOUT en UN (couteau suisse)
         */
        $query->execute([
            ':name' => $this->name,
            ':price' => $this->price,
            ':status' => $this->status,
            ':description' => $this->description,
            ':picture' => $this->picture,
            ':category_id' => $this->category_id,
            ':brand_id' => $this->brand_id,
            ':type_id' => $this->type_id
        ]);
        
        // On récupère le nombre d'élements affectés par la requete. 
        // Puisqu'on qu'on insert q'une seule
        // données à la fois, on aura toujours $insertedRows = 1.
        $insertedRows = $query->rowCount();

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
     * Méthode permettant de mettre à jour un produit en base de données
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
            UPDATE `product`
            SET
                name = :name,
                price = :price, 
                status = :status,
                description = :description,
                picture = :picture, 
                category_id = :category_id,
                brand_id = :brand_id, 
                type_id = :type_id,
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


        // Execution de la requête d'insertion (exec, pas query)
        // On peut utiliser la méthode bindValue pour chaque input 
        // qui va préparer nos données : Associer une valeur à un paramètre (Ex :name ==> $this->name)
        // Voir la documentation ici : https://www.php.net/manual/fr/pdostatement.bindvalue.php
        // $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        // $query->bindValue(':price', $this->price, PDO::PARAM_INT);
        // Le 3e argument permet de préciser "valeur numérique" (PDO::PARAM_INT) ou "autre" (PDO::PARAM_STR)

        // La méthode binValue nous obligerait quand même à appeler plus tard la méthode execute.
        // Exemple complet : 
        // $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        // $query->bindValue(':price', $this->price, PDO::PARAM_INT);
        // ...
        // $query->execute(); // j'execute la requete
        // ...
        // ...On peut faire ça (en deux étapes : binValue + execute)....Ou alors

        /**
         * On peut envoyer les données « brutes (parce que provenant du client, dont on a pas confiance) » 
         * à execute() en arguments, qui va les "sanitize" pour SQL, tout en executant la requete. 
         * 
         * C'est la méthode TOUT en UN (couteau suisse)
         */
        $query->execute([
            ':name' => $this->name,
            ':price' => $this->price,
            ':status' => $this->status,
            ':description' => $this->description,
            ':picture' => $this->picture,
            ':category_id' => $this->category_id,
            ':brand_id' => $this->brand_id,
            ':type_id' => $this->type_id,
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
     * Methode permettant de supprimer des données en BDD
     *
     * @return void
     */
    public function delete() {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête DELETE : avec des alias pour empécher les injections SQL
        $sql = "
            DELETE FROM `product`
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
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     */ 
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the value of description
     *
     * @return  string
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     */ 
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Get the value of picture
     *
     * @return  string
     */ 
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the value of picture
     *
     * @param  string  $picture
     */ 
    public function setPicture(string $picture)
    {
        $this->picture = $picture;
    }

    /**
     * Get the value of price
     *
     * @return  float
     */ 
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @param  float  $price
     */ 
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * Get the value of rate
     *
     * @return  int
     */ 
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set the value of rate
     *
     * @param  int  $rate
     */ 
    public function setRate(int $rate)
    {
        $this->rate = $rate;
    }

    /**
     * Get the value of status
     *
     * @return  int
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  int  $status
     */ 
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * Get the value of brand_id
     *
     * @return  int
     */ 
    public function getBrandId()
    {
        return $this->brand_id;
    }

    /**
     * Set the value of brand_id
     *
     * @param  int  $brand_id
     */ 
    public function setBrandId(int $brand_id)
    {
        $this->brand_id = $brand_id;
    }

    /**
     * Get the value of category_id
     *
     * @return  int
     */ 
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id
     *
     * @param  int  $category_id
     */ 
    public function setCategoryId(int $category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * Get the value of type_id
     *
     * @return  int
     */ 
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * Set the value of type_id
     *
     * @param  int  $type_id
     */ 
    public function setTypeId(int $type_id)
    {
        $this->type_id = $type_id;
    }
}
