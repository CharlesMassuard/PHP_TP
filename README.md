# MASSUARD Charles - INSA ICy FISA 3 - PHP

## Fonctionnalités

Il y existe 3 catégories d'utilisateurs : **MEMBRE**, **LIBRAIRE** et **ADMIN**, respectivement représentés par **ROLE_USER**, **ROLE_LIBRARIAN** et **ROLE_ADMIN**. Le *libraire* hérite des permissions des *membres* et l'administrateur des permissions du *libraire*.

Un **membre** peut :

- Faire des recherches parmis les ouvrages
- Voir les détails d'un ouvrage
- Voir les exemplaires d'un ouvrage
- Réserver et/ou emprunté un ouvrage
- Voir ses réservations/emprunts
- Annuler une réservation
- Retourner un emprunt
- Recevoir des mails

Un **libraire** peut :

- Créer/modifier un ouvrage
- Créer/modifier un exemplaire

Un **administrateur** peut :

- Paramétrer les emprunts par catégorie (durée, nombre max par membre, pénalité par jour de retard)
- Accéder au tableau de bord


## Lancement de l'application

Afin de lancer l'application, exécuter `symfony server:start` dans le repertoire courant.

Pour utiliser les mails en local *(si une erreur Mail Server not found apparait)* : `docker run -d -p 1025:1025 -p 8025:8025 mailhog/mailhog`

## Commandes

L'application contient plusieurs commandes :

`symfony console app:envoi-rappels` permet d'envoyer des rappels par *mail* aux membres ayant un emprunt en cours :
    - rappel envoyé si la date de retour est dans **3 jours**
    - rappel envoyé si la date de retour est **aujourd'hui**
    - rappel envoyé si la date de retour était **il y a 7 jours**
`symfony console app:clear-logs` permet de clear les logs présentes depuis 30 jours ou plus
`symfony console app:create-librarian` permet de créer un **libraire**
`symfony console app:create-admin` permet de créer un **administrateur**

## Tests

Afin de lancer les différents tests : `php bin/phpunit`

Pour lancer un fichier test en particulier : `php bin/phpunit tests/{nom_du_fichier}`. Par exemple : `php bin/phpunit tests/EmpruntControllerTest.php`

## Fixtures

Afin de lancer les fixtures pour peupler la base de données : `php bin/console doctrine:fixtures:load`

## Logs

Les logs sont ajoutés à la base de données, dans la table **audit_log**.

