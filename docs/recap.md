# Rappels utiles pour la S06

## Démarage du projet

### Composer

Installation des librairies externes


```bash
composer install
```

Mise en place de l'autoloading


```bash
composer dump-autoload
```

### Config.ini

Renommer le fichier **config.ini.dist** en **config.ini**, puis rajoutez les informatoins de votre base (en metant 127.0.0.1 en DB_HOST) :

```bash
DB_HOST=127.0.0.1
DB_NAME=
DB_USERNAME=
DB_PASSWORD=
```

## Integration d'un nouvelle page
### Créer une nouvelle "url"

1. Créer la route dans `index.php`
   1. `$router->map(...)`
2. Créer le controller si besoin (dossier `app/Controllers`) : ne pas oublier le Namespace (voir la classe MainController)
3. Créer la méthode dans le controller
4. Créer la vue qui reprend le nom du controller en dossier et le nom de la méthode en nom de fichier
   1. `views/controllername/methodname.tpl.php`
5. Tester depuis votre navigateur en lançant la commande (Terminal) :

```bash
php -S 0.0.0.0:8080 -t public
```

### Créer un modele

1. Créer une classe qui porte le nom de la table ciblée
2. Créer une propriété pour chaque champ de la table ciblée
3. Mettre en place les getters/setters
4. Mettre en place l'active record : find, findAll
> Voir la [définition du model Brand en S05](https://github.com/O-clock-Hyperion/S05-projet-oShop-charlesen/blob/master/app/models/Brand.php): 


## Liste de routes

| URL | HTTP Method | Controller | Method | Title | Content | Comment |
|--|--|--|--|--|--|--|
| `/` | `GET` | `MainController` | `home` | Backoffice oShop | Backoffice dashboard | - |
| `/category/list` | `GET`| `CategoryController` | `list` | Liste des catégories | Categories list | - |
| `/category/add` | `GET`| `CategoryController` | `add` | Ajouter une catégorie | Form to add a category | - |
| `/category/update/[i:categoryId]` | `GET`| `CategoryController` | `update` | Éditer une catégorie | Form to update a category | [i:categoryId] is the category to update |
| `/category/delete/[i:categoryId]` | `GET`| `CategoryController` | `delete` | Supprimer une catégorie | Category delete | [i:categoryId] is the category to delete |