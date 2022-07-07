# Alumni-Back

## Présentation du projet
Ce projet est l’api du projet de fin d’année de 2022 de l’école Normandie Web School. Le projet est de faire un site des “Alumnis” de l’établissement.
L’application doit comporter (a minima) les fonctionnalités suivantes :

-  annuaires des anciens (avec une fiche par personne) avec un affichage public et un affichage plus détaillé pour les personnes connectées,
- possibilité de s’enregistrer en tant qu’ancien après validation de l’inscription par les administrateurs de l’application,
- messagerie interne entre les personnes enregistrées et avec les administrateurs.

## Installation
  Prérequis:
   - Docker
   - Git
   - Composer
   - Php

Comme tout projet sur la plateforme github, vous devez cloner le repo via la commande suivante:

```bash
git clone https://github.com/HenriquesSylvio/Alumni-Back.git
```

Ensuite, à la racine du repo que vous venez de cloner, vous devez lancer la commande suivante : 
```bash
composer install
```
Quand celà est fait vous devez démarrer docker, et lancer la commande : 
```bash
docker-compose up -d
```
Si tout fonctionne, vous devez voir un container nommé alumni-back qui a été créer sur docker, avec 3 autres containers nommé : 
- php
- nginx
- db

Dans le container php, vous devez la commande : 
```bash
php bin/console d:m:m
```
Puis dans le même container : 
```bash
php bin/console d:f:l
```
Et voilà ! Le projet est fin prêt à être utilisé !
