# Recap Authentification

1. Import du script sql 

2. déclarer le Model AppUser correspondant
    - Coder les propriétés (1 Propriété = 1 champ de la base : email, password, ...)
    - Code les setters / getters

3. Créer une nouvelle page pour "afficher" le formulaire de connexion
    - Déclarer une route dans index.php
    - Créer si nécessaire un controller/méthod permettant d'afficher le formulaire
    - Créer le template permettant l'affichage du formulaire (view/nomcontroller/method.tpl.php)

4. Soumettre les données du formulaire

:warning: : L'attribut name permet la transmission de données à PHP :wink

5. Controller les données soumises :
    - Filter input
    - Est-ce que l'Email saisi existe ? (findByEmail ?)
        - si ok, on conserve l'état "connecté" pour la suite (**$_SESSION['connectedUser']**)
        - sinon, message d'erreur  => tableau d'erreur $errorList à transmettre à la View

Au final, comme oLogin (S03) mais avec les users en DB :wink:



