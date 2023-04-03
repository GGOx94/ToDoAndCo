
# ToDo & Co
Projet 8 de la formation **PHP/Symfony** d'OpenClassrooms : *Améliorez une application existante*
  
Ce projet a été mis à jour sur Symfony **6.2** et PHP **8.2**
## Installer le projet localement  
Pour installer le projet sur votre machine, suivez ces étapes :  
- Installez un environnement PHP & MySQL *(par exemple via [XAMPP](https://www.apachefriends.org/))*  
- Installez [Composer](https://getcomposer.org/download/)  
### 1) Clonez le projet et installez les dépendances :  
> git clone https://github.com/GGOx94/ToDoAndCo.git
  
> composer install  
### 3) Changez les variables d'environnement dans le fichier **.env**  
Modifiez le chemin d'accès à la base de données, exemple pour MySQL :  
>DATABASE_URL="mysql://**db_user**:**db_password**@127.0.0.1:3306/**db_name**?serverVersion=5.7&charset=utf8mb4"  
  
### 4) Base de données et jeu de démonstration  
Créez la base de données, initialisez le schéma et chargez les données de démonstration :  
>php bin/console doctrine:database:create  
  
>php bin/console doctrine:schema:up --force  
  
>php bin/console hautelook:fixtures:load
  
## Tout est prêt !  

### Pour tester l'application

Vous pouvez lancer le serveur :  
>symfony server:start  

Les comptes utilisateur et administrateur de test sont :  
>user1 / Secret123
  
>admin / Secret123

### Rapport de couverture des tests

Vous pouvez accéder au dernier rapport de couverture des tests généré, en ouvrant (avec un navigateur) le fichier index.html situé dans le dossier :
> ...\public\test-coverage\index.html
