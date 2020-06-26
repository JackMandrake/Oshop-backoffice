<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

class Category extends CoreModel {

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $subtitle;
    /**
     * @var string
     */
    private $picture;
    /**
     * @var int
     */
    private $home_order;

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
    public function setName(string $name='')
    {
        $this->name = $name;
    }

    /**
     * Get the value of subtitle
     */ 
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set the value of subtitle
     */ 
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * Get the value of picture
     */ 
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the value of picture
     */ 
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * Get the value of home_order
     */ 
    public function getHomeOrder()
    {
        return $this->home_order;
    }

    /**
     * Set the value of home_order
     */ 
    public function setHomeOrder($home_order)
    {
        $this->home_order = $home_order;
    }

    /**
     * Méthode permettant de récupérer un enregistrement de la table Category en fonction d'un id donné
     * 
     * @param int $categoryId ID de la catégorie
     * @return Category
     */
    public static function find($categoryId)
    {
        // se connecter à la BDD
        $pdo = Database::getPDO();

        // écrire notre requête
        $sql = 'SELECT * FROM `category` WHERE `id` =' . $categoryId;

        // exécuter notre requête
        $pdoStatement = $pdo->query($sql);

        // un seul résultat => fetchObject
        $category = $pdoStatement->fetchObject('App\Models\Category');

        // retourner le résultat
        return $category;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table category.
     * On la met en static, car elle n'est pas directement lié à un objet.
     * 
     * @return Category[]
     */
    public static function findAll()
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `category`';
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Category');
        
        return $results;
    }

    /**
     * Récupérer les 5 catégories mises en avant sur la home
     * 
     * @return Category[]
     */
    public static function findAllHomepage()
    {
        $pdo = Database::getPDO();
        $sql = '
            SELECT *
            FROM category
            WHERE home_order > 0
            ORDER BY home_order ASC
            LIMIT 5
        ';
        $pdoStatement = $pdo->query($sql);
        $categories = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Category');
        
        return $categories;
    }



    /**
     * Méthode permettant d'ajouter un enregistrement dans la table category
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     * 
     * @return bool
     */
    public function insert()
    {
        // 1) Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // 2) Ecriture de la requête INSERT INTO
        $sql = "
            INSERT INTO `category` (name, subtitle, picture)
            VALUES (:name, :subtitle, :picture)
        ";

        /**
         * 3) On va déléguer le traitement des données du formulaire à PDO, pour
         * éviter les injections SQL.
         * 
         * La méthode PDO::prepare — Prépare une requête à l'exécution et retourne un objet
         */
        $query = $pdo->prepare($sql);

        // 4) Execution de la requête d'insertion
        // On peut utiliser la méthode bindValue pour chaque input 
        // qui va préparer nos données : Associer une valeur à un paramètre (Ex :name ==> $this->name)
        // Voir la documentation ici : https://www.php.net/manual/fr/pdostatement.bindvalue.php
        // $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        // $query->bindValue(':price', $this->price, PDO::PARAM_INT);

        // La méthode binValue nous obligerait quand même à appeler plus tard la méthode execute.
        // (Voir la déclaration de la méthode insert du modèle Product)
        // Exemple complet
        // $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        // $query->bindValue(':subtitle', $this->price, PDO::PARAM_STR);
        // ...
        // $query->execute();
        // ...
        // ...On peut faire ça (en deux étapes : binValue + execute)....Ou alors

        /**
         * On envoie les données « brutes (parce que provenant du client, dont on a pas confiance) » 
         * à execute() en arguments, qui va les "sanitize" pour SQL, tout en executant la requete. 
         * 
         * C'est la méthode TOUT en UN (couteau suisse)
         */

        $query->execute([
            ':name' => $this->name,
            ':subtitle' => $this->subtitle,
            ':picture' => $this->picture
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
    public function update()
    {
        // 1) Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // 2) Ecriture de la requête INSERT INTO
        $sql = "
        UPDATE `category`
        SET
            name = '{$this->name}',
            subtitle = '{$this->subtitle}',
            picture = '{$this->picture}',
            updated_at = NOW()
        WHERE id = {$this->id}
    ";
        //dump($sql); // POURQUOI IL MARCHE PAS CE PUTAIN DE DUMP
        $querry = $pdo->prepare($sql);

        $querry->bindValue(':name', $this->name, PDO::PARAM_STR);
        $querry->bindValue(':subtitle', $this->subtitle, PDO::PARAM_STR);
        $querry->bindValue(':picture', $this->picture, PDO::PARAM_STR);

        // $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        // $query->bindValue(':price', $this->price, PDO::PARAM_INT);
        $querry->execute();
        
        
        // On récupère le nombre d'élements affectés par la requete. 
        // Puisqu'on qu'on insert q'une seule
        // données à la fois, on aura toujours $insertedRows = 1.
        $insertedRows = $querry->rowCount();

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
}
