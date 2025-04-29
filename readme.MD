# Application de Gestion de Projet - Environnement Docker

Ce projet utilise Docker pour mettre en place un environnement de développement complet avec PHP, MySQL, Nginx et PhpMyAdmin. Il fournit une infrastructure flexible pour le développement d'applications PHP avec gestion de la base de données et interface d'administration.

## Lancement rapide

### 1. Cloner le dépôt
Clonez le projet depuis GitHub et accédez au dossier du projet :

```bash
git clone https://github.com/ton-utilisateur/ton-projet.git
cd ton-projet
```
### 2. Configurer les variables d’environnement
Avant de démarrer les containers, créez un fichier .env à la racine du projet, en utilisant le fichier .env.example comme modèle. Ce fichier contient des informations sensibles (comme les mots de passe pour MySQL) et ne doit pas être commité dans Git. Ajoutez le fichier .env à votre .gitignore.

Exemple de contenu pour .env :
```
MYSQL_ROOT_PASSWORD=root_password
MYSQL_DATABASE=db_name
MYSQL_USER=user_name
MYSQL_PASSWORD=user_password
```

### 3. Démarrer les containers
Lancez les containers Docker avec la commande suivante :

```bash
docker-compose up --build -d
```
Cela va télécharger les images Docker nécessaires et démarrer les services PHP, MySQL, Nginx et PhpMyAdmin en arrière-plan.

## Accès aux interfaces
Une fois les containers démarrés, vous pouvez accéder à votre environnement via les URLs suivantes :

Site principal (Nginx + PHP) : http://localhost:8080

PhpMyAdmin (gestion de la BDD) : http://localhost:8081

## 🗃️ Base de données
La base de données est initialisée automatiquement à partir du fichier dump.sql au premier démarrage. Si le fichier SQL existe et contient des tables et des données, elles seront chargées dans la base de données.

Réinitialisation de la base de données :
Si vous souhaitez réinitialiser la base de données (supprimer les données actuelles et recréer les tables), vous pouvez le faire en supprimant les volumes Docker et en redémarrant les containers :
```bash
docker-compose down -v
docker-compose up -d
```
## Notes
Sécurisation des informations sensibles : N'oubliez pas d'ajouter le fichier .env à .gitignore pour éviter de commettre des informations sensibles dans Git.

Variables d'environnement : Le fichier .env contient des informations sensibles, comme les mots de passe de la base de données. Ces variables sont utilisées pour la connexion à MySQL, PhpMyAdmin et d'autres services.

PhpMyAdmin : Vous pouvez gérer votre base de données via PhpMyAdmin en accédant à http://localhost:8081. Les informations de connexion par défaut sont :

Utilisateur : user_name
Mot de passe : user_password
Hôte : mysql

Docker Compose : Ce projet utilise Docker Compose pour gérer les différents services. Si vous souhaitez modifier les configurations des containers, vous pouvez le faire dans les fichiers docker-compose.yaml et Dockerfile.

## Configuration Nginx
Le fichier nginx.conf permet de configurer Nginx pour gérer les requêtes HTTP et les rediriger vers le serveur PHP-FPM. Il est configuré pour gérer les requêtes PHP et les rediriger vers le container PHP à l'adresse php-fpm:9000.

Voici un aperçu de la configuration de base dans nginx.conf :
```nginx
server {
    listen 80;
    server_name localhost;

    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass php-fpm:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## Licence
Ce projet est sous licence libre MIT.
