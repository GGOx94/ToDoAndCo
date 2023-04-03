
# Contribuer à [ToDo & Co](https://github.com/GGOx94/ToDoAndCo)

La dernière version de cette application a été développée sur Symfony **6.2** et PHP **8.2**.

Pour contribuer au projet, veuillez suivre les étapes suivantes : 

## 1. Les issues
Qu'il s'agisse de développer de nouvelles **fonctionnalités** ou de corriger des **anomalies**, ces dernières seront à déclarer dans un premier temps sur les [issues du projet](https://github.com/GGOx94/ToDoAndCo/issues).

Si vous créez une issue, n'hésitez pas à utiliser les **labels** (ou tags) afin de définir sa nature.
Par exemple, les labels "**bug**" (anomalie) ou "**enhancement**" (amélioration / fonctionnalité) sont disponibles.

`Prenez le temps de vérifier que cette issue n'existe pas déjà !`

## 2. Les branches

Pour travailler sur une issue, une nouvelle branche correspondante doit être créée.

Clonez le projet sur votre machine, et créez une nouvelle branche :
>git clone https://github.com/GGOx94/ToDoAndCo.git
>git checkout -b **ma-branche-exemple**

## 3. Les standards de code

Afin d'assurer une uniformité du code sur le projet et l'application des bonnes pratiques, il est demandé de respecter les PSR (*PHP Standards Recommendations) suivants :
- PSR-1 : [Basic Coding Standard](https://www.php-fig.org/psr/psr-1/)
- PSR-4 : [Autoloading Standard](https://www.php-fig.org/psr/psr-4/)
- PSR-12 : [Extended Coding Style Guide](https://www.php-fig.org/psr/psr-12/)

Il est aussi important de respecter les [bonnes pratiques](https://symfony.com/doc/6.2/best_practices.html) de Symfony **6.2**.

## 4. Tester le code
Après avoir fait vos modifications, et avant de pousser le code, il est important de lancer la suite de tests présente dans le projet.

Si vous avez développé de nouvelles fonctionnalités, il est bien vu d'**écrire des tests** pour valider leur fonctionnement.

Ces tests sont développés à l'aide de **PHPUnit** et sont placés dans le dossier *tests/* du projet.
En vous plaçant à la racine du projet, vous pouvez lancer ces tests avec la commande :
>.\vendor\bin\phpunit

Si tous les tests passent, vous pouvez continuer vers la dernière étape !
## 5. Pousser le code

Si vous avez respecté les standards de code et passé la suite de tests, vous êtes prêts à faire votre **pull request**.

Vous pouvez pousser votre travail depuis votre machine vers notre projet :
>git push -u origin **ma-branche-exemple**

Rendez-vous ensuite sur la page de [pull request du projet](https://github.com/GGOx94/ToDoAndCo/pulls) et créer-en une nouvelle.

Un administrateur verifiera votre code avant de le **merger** dans la base de code commune.

**Merci !**
