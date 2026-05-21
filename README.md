# HappyCulteur 🐝

Une application Symfony pour les apiculteurs permettant de gérer efficacement leurs exploitations apicoles.

## 📋 À propos

HappyCulteur est une application web développée avec Symfony 8 qui aide les apiculteurs à organiser et suivre :
- **Les ruches** : Gestion des différents types de ruches (Dadant, Langstroth, Warré, Ruchette, etc.)
- **Les ruchers** : Organisation par apiaire/emplacement
- **Les récoltes** : Suivi et documentation des récoltes de miel
- **Les données** : Enregistrement des visites, traitements, et états sanitaires
- **Les hausses** : Gestion des cadres supplémentaires selon les besoins saisonniers

L'application offre une interface intuitive pour visualiser vos données et générer des rapports.

## 🔧 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP** >= 8.4
- **Composer** (gestionnaire de dépendances PHP)
- **Docker** et **Docker Compose** (pour la base de données PostgreSQL)
- **Git** (pour le contrôle de version)
- **Apache** (ou Nginx) pour la production

### Vérification des installations

```bash
php --version
composer --version
docker --version
docker-compose --version
```

## 📦 Installation

### 1. Cloner le projet

```bash
git clone <repository-url> happyculteur
cd happyculteur
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer les variables d'environnement

Copiez le fichier `.env` en `.env.local` et configurez vos paramètres :

```bash
cp .env .env.local
```

Éditez `.env.local` et configurez :

```env
# Application
APP_ENV=dev
APP_SECRET=<générez-une-clé-secrète>
APP_SHARE_DIR=var/share

# Base de données (PostgreSQL)
DATABASE_URL="postgresql://app:app@127.0.0.1:5432/app?serverVersion=16&charset=utf8"

# URL par défaut pour les commandes CLI
DEFAULT_URI=http://localhost

# Messenger
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0

# Mailer
MAILER_DSN=null://null
```

Pour générer une clé APP_SECRET secrète :

```bash
php -r 'echo bin2hex(random_bytes(16));'
```

### 4. Lancer la base de données

Utilisez Docker Compose pour démarrer PostgreSQL :

```bash
docker-compose up -d database
```

Vérifiez que la base est prête :

```bash
docker-compose ps
```

### 5. Créer la base de données

```bash
php bin/console doctrine:database:create
```

### 6. Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

## 👤 Initialisation de l'application

Après l'installation classique, plusieurs étapes sont nécessaires pour initialiser l'application :

### Étape 1 : Compiler les assets

L'application utilise Symfony Asset Mapper pour gérer les assets (CSS, JavaScript) sans dépendances Node.js :

```bash
php bin/console asset-map:compile
```

Cette commande compile les assets et crée les fichiers nécessaires dans le répertoire `public/`.

**Remarque** : En environnement de développement, les assets peuvent être recompilés automatiquement. Consultez la [documentation Asset Mapper de Symfony](https://symfony.com/doc/current/frontend/asset_mapper.html) pour plus de détails.

### Étape 2 : Ajouter les types de ruches

```bash
php bin/console app:add-hive-type
```

Cette commande populate la base de données avec les types de ruches standards :
- Autre
- Dadant
- Ruchette
- Warré
- Langstroth

⚠️ Si des données existent déjà, la commande n'ajoutera rien.

### Étape 3 : Ajouter les types de hausses

```bash
php bin/console app:add-hive-rise
```

Cette commande ajoute les types de hausses standards :
- Hausse 01 cadre
- Hausse 02 cadres
- ...
- Hausse 12 cadres

⚠️ Si des données existent déjà, la commande n'ajoutera rien.

## 🚀 Lancer l'application

### Démarrage du serveur de développement

**Option 1** : Serveur PHP intégré

```bash
php -S localhost:8000 -t public
```

**Option 2** : Symfony CLI

```bash
symfony serve
```

L'application est accessible à [http://localhost:8000](http://localhost:8000)

### Étape 4 : Créer un utilisateur administrateur

1. **Créer un utilisateur** via l'interface web :
   - Accédez à l'application
   - Cliquez sur le lien d'inscription
   - Remplissez le formulaire d'enregistrement

2. **Promouvoir l'utilisateur en administrateur** :

```bash
php bin/console app:promote-user
```

Sélectionnez l'utilisateur créé pour lui attribuer le rôle d'administrateur.

### Configuration pour Apache

Pour déployer sur Apache en production :

1. **Configurez un Virtual Host** :

```apache
<VirtualHost *:80>
    ServerName happyculteur.example.com
    DocumentRoot /var/www/happyculteur/public

    <Directory /var/www/happyculteur/public>
        AllowOverride All
        Order Allow,Deny
        Allow from all
        
        # Enable mod_rewrite
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php [QSA,L]
        </IfModule>
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/happyculteur_error.log
    CustomLog ${APACHE_LOG_DIR}/happyculteur_access.log combined
</VirtualHost>
```

2. **Activez les modules nécessaires** :

```bash
sudo a2enmod rewrite
sudo a2enmod headers
```

3. **Configurez les permissions** :

```bash
chown -R www-data:www-data /var/www/happyculteur
chmod -R 755 /var/www/happyculteur
chmod -R 777 /var/www/happyculteur/var
chmod -R 777 /var/www/happyculteur/public/uploads
```

4. **Environnement de production** :

Éditez `.env.local` :

```env
APP_ENV=prod
APP_DEBUG=0
```

5. **Redémarrez Apache** :

```bash
sudo systemctl restart apache2
```


## 📝 Commandes utiles

### Gestion des utilisateurs

```bash
# Promouvoir un utilisateur administrateur
php bin/console app:promote-user


### Gestion de la base de données

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Supprimer la base de données
php bin/console doctrine:database:drop --force

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Afficher le statut des migrations
php bin/console doctrine:migrations:status

# Créer une nouvelle migration
php bin/console make:migration
```

### Initialisation des données

```bash
# Ajouter les types de ruches
php bin/console app:add-hive-type

# Ajouter les types de hausses
php bin/console app:add-hive-rise
```

### Tests et validation

```bash
# Exécuter les tests PHPUnit
php bin/phpunit

# Valider la configuration
php bin/console lint:yaml config/

# Valider les templates Twig
php bin/console lint:twig templates/
```

### Gestion des assets

```bash
# Compiler les assets
php bin/console asset-map:compile
```

## 🐳 Gestion de Docker

```bash
# Démarrer tous les services
docker-compose up -d

# Arrêter tous les services
docker-compose down

# Voir les logs du conteneur database
docker-compose logs -f database

# Accéder à la base de données PostgreSQL
docker-compose exec database psql -U app -d app
```

## 🔍 Dépannage

### Erreur de connexion à la base de données

1. Vérifiez que Docker est lancé : `docker ps`
2. Vérifiez les variables DATABASE_URL dans `.env.local`
3. Vérifiez que le conteneur PostgreSQL est en bonne santé : `docker-compose ps`
4. Consultez les logs : `docker-compose logs database`

### Migration échouée

```bash
# Afficher le statut des migrations
php bin/console doctrine:migrations:status

# Annuler la dernière migration (si nécessaire)
php bin/console doctrine:migrations:execute --down Version20260521120000
```

### Permissions manquantes

Assurez-vous que PHP peut écrire dans les répertoires :

```bash
chmod -R 777 var/
chmod -R 777 public/uploads/
```

## 📚 Documentation supplémentaire

- [Documentation Symfony 8](https://symfony.com/doc/current/index.html)
- [Documentation Doctrine ORM](https://www.doctrine-project.org/projects/doctrine-orm/en/current/index.html)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)

## 📧 Support

Pour des questions ou des signalements de bugs, veuillez créer une issue dans le projet.

---

**Version** : 1.0.0  
**Dernière mise à jour** : Mai 2026  
**Licence** : MIT

---

# HappyCulteur 🐝 (English Version)

A Symfony application for beekeepers to efficiently manage their beekeeping operations.

## 📋 About

HappyCulteur is a web application developed with Symfony 8 that helps beekeepers organize and track:
- **Hives**: Management of different hive types (Dadant, Langstroth, Warré, Nuc box, etc.)
- **Apiaries**: Organization by location/apiary
- **Harvests**: Tracking and documentation of honey harvests
- **Data**: Recording visits, treatments, and health status
- **Supers**: Management of additional frames according to seasonal needs

The application provides an intuitive interface for visualizing your data and generating reports.

## 🔧 Requirements

Before getting started, make sure you have installed:

- **PHP** >= 8.4
- **Composer** (PHP dependency manager)
- **Docker** and **Docker Compose** (for PostgreSQL database)
- **Git** (for version control)
- **Apache** (or Nginx) for production

### Verify installations

```bash
php --version
composer --version
docker --version
docker-compose --version
```

## 📦 Installation

### 1. Clone the project

```bash
git clone <repository-url> happyculteur
cd happyculteur
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment variables

Copy the `.env` file to `.env.local` and configure your settings:

```bash
cp .env .env.local
```

Edit `.env.local` and configure:

```env
# Application
APP_ENV=dev
APP_SECRET=<generate-a-secret-key>
APP_SHARE_DIR=var/share

# Database (PostgreSQL)
DATABASE_URL="postgresql://app:app@127.0.0.1:5432/app?serverVersion=16&charset=utf8"

# Default URL for CLI commands
DEFAULT_URI=http://localhost

# Messenger
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0

# Mailer
MAILER_DSN=null://null
```

To generate a secret APP_SECRET key:

```bash
php -r 'echo bin2hex(random_bytes(16));'
```

### 4. Start the database

Use Docker Compose to start PostgreSQL:

```bash
docker-compose up -d database
```

Verify the database is ready:

```bash
docker-compose ps
```

### 5. Create the database

```bash
php bin/console doctrine:database:create
```

### 6. Run migrations

```bash
php bin/console doctrine:migrations:migrate
```

## 👤 Application Initialization

After the standard installation, several steps are necessary to initialize the application:

### Step 1: Compile assets

The application uses Symfony Asset Mapper to manage assets (CSS, JavaScript) without Node.js dependencies:

```bash
php bin/console asset-map:compile
```

This command compiles assets and creates the necessary files in the `public/` directory.

**Note**: In development environment, assets can be recompiled automatically. Consult the [Symfony Asset Mapper documentation](https://symfony.com/doc/current/frontend/asset_mapper.html) for more details.

### Step 2: Add hive types

```bash
php bin/console app:add-hive-type
```

This command populates the database with standard hive types:
- Other
- Dadant
- Nuc box
- Warré
- Langstroth

⚠️ If data already exists, the command will do nothing.

### Step 3: Add super types

```bash
php bin/console app:add-hive-rise
```

This command adds standard super types:
- Super 01 frame
- Super 02 frames
- ...
- Super 12 frames

⚠️ If data already exists, the command will do nothing.

## 🚀 Running the application

### Starting the development server

**Option 1**: Built-in PHP server

```bash
php -S localhost:8000 -t public
```

**Option 2**: Symfony CLI

```bash
symfony serve
```

The application is accessible at [http://localhost:8000](http://localhost:8000)

### Step 4: Create an admin user

1. **Create a user** via the web interface:
   - Access the application
   - Click the registration link
   - Fill in the registration form

2. **Promote the user to administrator**:

```bash
php bin/console app:promote-user
```

Select the created user to assign the administrator role.

### Configuration for Apache

To deploy on Apache in production:

1. **Configure a Virtual Host**:

```apache
<VirtualHost *:80>
    ServerName happyculteur.example.com
    DocumentRoot /var/www/happyculteur/public

    <Directory /var/www/happyculteur/public>
        AllowOverride All
        Order Allow,Deny
        Allow from all
        
        # Enable mod_rewrite
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php [QSA,L]
        </IfModule>
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/happyculteur_error.log
    CustomLog ${APACHE_LOG_DIR}/happyculteur_access.log combined
</VirtualHost>
```

2. **Enable required modules**:

```bash
sudo a2enmod rewrite
sudo a2enmod headers
```

3. **Configure permissions**:

```bash
chown -R www-data:www-data /var/www/happyculteur
chmod -R 755 /var/www/happyculteur
chmod -R 777 /var/www/happyculteur/var
chmod -R 777 /var/www/happyculteur/public/uploads
```

4. **Production environment**:

Edit `.env.local`:

```env
APP_ENV=prod
APP_DEBUG=0
```

5. **Restart Apache**:

```bash
sudo systemctl restart apache2
```

## 📝 Useful Commands

### User management

```bash
# Promote a user to administrator
php bin/console app:promote-user

### Database management

```bash
# Create the database
php bin/console doctrine:database:create

# Drop the database
php bin/console doctrine:database:drop --force

# Run migrations
php bin/console doctrine:migrations:migrate

# Display migration status
php bin/console doctrine:migrations:status

# Create a new migration
php bin/console make:migration
```

### Data initialization

```bash
# Add hive types
php bin/console app:add-hive-type

# Add super types
php bin/console app:add-hive-rise
```

### Testing and validation

```bash
# Run PHPUnit tests
php bin/phpunit

# Validate configuration
php bin/console lint:yaml config/

# Validate Twig templates
php bin/console lint:twig templates/
```

### Asset management

```bash
# Compile assets
php bin/console asset-map:compile
```

## 🐳 Docker Management

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View database container logs
docker-compose logs -f database

# Access PostgreSQL database
docker-compose exec database psql -U app -d app
```

## 🔍 Troubleshooting

### Database connection error

1. Verify Docker is running: `docker ps`
2. Verify DATABASE_URL variables in `.env.local`
3. Verify PostgreSQL container health: `docker-compose ps`
4. Check logs: `docker-compose logs database`

### Migration failed

```bash
# Display migration status
php bin/console doctrine:migrations:status

# Revert last migration (if needed)
php bin/console doctrine:migrations:execute --down Version20260521120000
```

### Missing permissions

Ensure PHP can write to directories:

```bash
chmod -R 777 var/
chmod -R 777 public/uploads/
```

## 📚 Additional Documentation

- [Symfony 8 Documentation](https://symfony.com/doc/current/index.html)
- [Doctrine ORM Documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/current/index.html)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)

## 📧 Support

For questions or bug reports, please create an issue in the project.

---

**Version**: 1.0.0  
**Last updated**: May 2026  
**License**: MIT
