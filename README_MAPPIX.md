# Mappix

> Repérez-vous en un instant et découvrez tout ce dont vous avez besoin pendant votre séjour grâce à notre carte interactive.

Plateforme de conciergerie touristique interactive permettant de découvrir les lieux notables, événements et services d'une ville.

---

## Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Démarrage](#-démarrage)
- [Utilisation du GPS](#-utilisation-du-gps)
- [Structure du projet](#-structure-du-projet)
- [Contributeurs](#-contributeurs)

---

## Fonctionnalités

- **Carte interactive** : Centralise les lieux notables de Rouen (scalable pour d'autres villes/régions/pays)
- **Calendrier d'événements** : Recense tous les événements locaux
- **Informations détaillées** : Adresses, numéros de téléphone, sites web et réseaux sociaux des lieux
- **Système de réservation** : Réservation possible pour les événements listés sur le site
- **Géolocalisation** : Localisation en temps réel pour faciliter la navigation

---

## Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP** : `>= 8.3.28`
- **Symfony CLI** : [Installation](https://symfony.com/download)
- **Composer** : [Installation](https://getcomposer.org/download/)
- **Node.js** : `>= 20.20.0`
- **npm** : Inclus avec Node.js
- **MySQL** : `>= 8.4.7`
- **phpMyAdmin** : `>= 5.2.3` (optionnel, pour la gestion de la base de données)

### Vérifier les versions installées

```bash
php -v
symfony -v
composer -v
node -v
npm -v
mysql --version
```

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/projet-conciergerie/mappix.git
cd mappix
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
```

### 4. Créer la base de données

```bash
# Créer le fichier .env.local (si inexistant)
cp .env .env.local

# Éditer .env.local et configurer DATABASE_URL
# Exemple : DATABASE_URL="mysql://root:@127.0.0.1:3306/mappix?serverVersion=8.4.7&charset=utf8mb4"

# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

---

## Configuration

### Variables d'environnement

Éditez le fichier `.env.local` pour configurer votre environnement :

```env
# Base de données
DATABASE_URL="mysql://root:password@127.0.0.1:3306/mappix?serverVersion=8.4.7&charset=utf8mb4"

# Environnement (dev, prod, test)
APP_ENV=dev
APP_SECRET=votre_secret_généré
```

## Démarrage

### 1. Compiler les assets

En mode développement avec watch :

```bash
npm run watch
```

Ou pour une compilation unique :

```bash
npm run build
```

### 2. Démarrer le serveur Symfony

```bash
symfony server:start
```

Le serveur sera accessible sur `http://127.0.0.1:8000`


## Utilisation du GPS

Des restrictions d'usage s'appliquent à la localisation par GPS. L'utilisateur doit donner son consentement et la connexion doit être sécurisée (HTTPS).

### Création d'un certificat auto-signé

Pour activer la géolocalisation en développement local, vous devez créer un certificat SSL :

```bash
# Créer le dossier pour les certificats
mkdir -p cert

# Générer le certificat SSL
openssl req -x509 -out ./cert/localhost.crt -keyout ./cert/localhost.key \
  -newkey rsa:2048 -nodes -sha256 \
  -subj '/CN=localhost' -extensions EXT \
  -config <( \
  printf "[dn]\nCN=localhost\n[req]\ndistinguished_name = dn\n[EXT]\nsubjectAltName=DNS:localhost\nkeyUsage=digitalSignature\nextendedKeyUsage=serverAuth")

# Créer le certificat PKCS12
openssl pkcs12 -export \
  -out ./cert/localhost.p12 \
  -inkey ./cert/localhost.key \
  -in ./cert/localhost.crt \
  -passout pass:
```

### Configuration pour HTTPS

#### 1. Modifier `.env.local`

Ajoutez ou modifiez la ligne suivante :

```env
VITE_DEV_SERVER_URL="https://127.0.0.1:5173"
```

#### 2. Démarrer les serveurs avec HTTPS

```bash
# Terminal 1 : Démarrer le serveur npm avec HTTPS
npm run dev

# Terminal 2 : Démarrer Symfony avec le certificat
symfony server:start --allow-all-ip --p12=./cert/localhost.p12
```

### Test sur mobile

1. Connectez votre appareil mobile au **même réseau Wi-Fi** que votre ordinateur
2. Trouvez l'adresse IP de votre ordinateur :
   ```bash
   # Linux/Mac
   ifconfig | grep "inet "
   
   # Windows
   ipconfig
   ```
3. Accédez depuis votre mobile à : `https://VOTRE_IP:8000`
4. Acceptez le certificat auto-signé (avertissement de sécurité normal en développement)
5. Autorisez la géolocalisation quand le navigateur le demande

---

## Structure du projet

```
mappix
├── .editorconfig
├── .env
├── .env.dev
├── .env.test
├── .gitignore
├── assets
│   ├── app.js
│   ├── bootstrap.js
│   ├── controllers
│   │   ├── csrf_protection_controller.js
│   │   ├── geolocate_controller.js
│   │   ├── hello_controller.js
│   │   ├── map_controller.js
│   │   └── return_home_controller.js
│   ├── icons
│   │   └── symfony.svg
│   └── styles
│       ├── app.css
│       └── map.css
├── compose.override.yaml
├── compose.yaml
├── composer.json
├── composer.lock
├── package-lock.json
├── package.json
├── phpunit.dist.xml
├── postcss.config.mjs
├── public
│   ├── build
│   │   ├── .vite
│   │   │   ├── entrypoints.json
│   │   │   └── manifest.json
│   │   └── assets
│   │       ├── app-B-vXRS40.js
│   │       └── app-DwqLNfSD.css
│   ├── bundles
│   │   └── easyadmin
│   │       ├── app.81f6af73.css
│   │       ├── app.8f681b52.js
│   │       ├── app.8f681b52.js.LICENSE.txt
│   │       ├── entrypoints.json
│   │       ├── field-boolean.adeeab81.js
│   │       ├── field-code-editor.877c61fa.js
│   │       ├── field-code-editor.cdcf15eb.css
│   │       ├── field-collection.b4d3688b.js
│   │       ├── field-file-upload.5c32db38.js
│   │       ├── field-image.c338d2ad.js
│   │       ├── field-slug.ba7fb8e5.js
│   │       ├── field-text-editor.3e768b9b.js
│   │       ├── field-text-editor.3e768b9b.js.LICENSE.txt
│   │       ├── field-text-editor.d426785c.css
│   │       ├── field-textarea.98322d83.js
│   │       ├── fonts
│   │       │   ├── fa-brands-400.26b80c88.ttf
│   │       │   ├── fa-brands-400.fdbb5585.woff2
│   │       │   ├── fa-regular-400.05fdd87b.ttf
│   │       │   ├── fa-regular-400.4f6a2dab.woff2
│   │       │   ├── fa-solid-900.83a538a0.woff2
│   │       │   ├── fa-solid-900.ad1782c7.ttf
│   │       │   ├── fa-v4compatibility.c3ea317a.woff2
│   │       │   └── fa-v4compatibility.fa86b3c8.ttf
│   │       ├── form.5bccac01.js
│   │       ├── login.7259f5de.js
│   │       ├── manifest.json
│   │       ├── page-color-scheme.30cb23c2.js
│   │       └── page-layout.6e9fe55d.js
│   ├── data
│   │   ├── overpass_Rouen_attractions.json
│   │   ├── overpass_Rouen_bars.json
│   │   ├── overpass_Rouen_fontaines.json
│   │   ├── overpass_Rouen_hotels.json
│   │   ├── overpass_Rouen_monuments.json
│   │   ├── overpass_Rouen_monuments_historiques.json
│   │   ├── overpass_Rouen_musees.json
│   │   ├── overpass_Rouen_parcs.json
│   │   ├── overpass_Rouen_pubs.json
│   │   ├── overpass_Rouen_restaurants.json
│   │   └── overpass_Rouen_toilettes.json
│   ├── icons
│   │   ├── center_map.png
│   │   ├── layers_map.png
│   │   ├── marker_attractions.png
│   │   ├── marker_bars.png
│   │   ├── marker_fontaines.png
│   │   ├── marker_fontains.png
│   │   ├── marker_hotels.png
│   │   ├── marker_ici.png
│   │   ├── marker_monuments.png
│   │   ├── marker_monuments_historiques.png
│   │   ├── marker_musees.png
│   │   ├── marker_parcs.png
│   │   ├── marker_pubs.png
│   │   ├── marker_restaurants.png
│   │   ├── marker_toilets.png
│   │   └── marker_toilettes.png
│   ├── images
│   │   ├── img_hero_homepage.jpg
│   │   ├── logo1.png
│   │   ├── logo2.png
│   │   ├── logo3.png
│   │   └── logo4.png
│   └── index.php
├── README.md
├── src
│   ├── Command
│   ├── Controller
│   │   ├── .gitignore
│   │   ├── Admin
│   │   │   ├── AvisCrudController.php
│   │   │   ├── CategoryCrudController.php
│   │   │   ├── ContactCrudController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── EvenementCrudController.php
│   │   │   ├── LocalisationCrudController.php
│   │   │   ├── ReservationCrudController.php
│   │   │   ├── ServiceCrudController.php
│   │   │   └── UserCrudController.php
│   │   ├── ContactController.php
│   │   ├── EvenementController.php
│   │   ├── HomeController.php
│   │   ├── MapController.php
│   │   ├── ProfileController.php
│   │   ├── RegistrationController.php
│   │   ├── ReservationController.php
│   │   ├── SecurityController.php
│   │   └── Tourisme
│   │       └── TourismeDashboardController.php
│   ├── Entity
│   │   ├── .gitignore
│   │   ├── Avis.php
│   │   ├── Category.php
│   │   ├── Contact.php
│   │   ├── Evenement.php
│   │   ├── Favoris.php
│   │   ├── Localisation.php
│   │   ├── Reservation.php
│   │   ├── Service.php
│   │   └── User.php
│   ├── EventListener
│   │   ├── LoginListener.php
│   │   └── SessionDirectoryListener.php
│   ├── Form
│   │   ├── ContactType.php
│   │   ├── ProfileType.php
│   │   └── RegistrationFormType.php
│   ├── Kernel.php
│   ├── Repository
│   │   ├── .gitignore
│   │   ├── AvisRepository.php
│   │   ├── CategoryRepository.php
│   │   ├── ContactRepository.php
│   │   ├── EvenementRepository.php
│   │   ├── FavorisRepository.php
│   │   ├── LocalisationRepository.php
│   │   ├── ReservationRepository.php
│   │   ├── ServiceRepository.php
│   │   └── UserRepository.php
│   └── Service
│       ├── OpeningHoursParser.php
│       └── Overpass.php
├── symfony.lock
├── templates
│   ├── admin
│   │   ├── dashboard.html.twig
│   │   └── layout.html.twig
│   ├── base.html.twig
│   ├── bundles
│   │   └── TwigBundle
│   │       └── Exception
│   │           ├── error403.html.twig
│   │           ├── error404.html.twig
│   │           └── error500.html.twig
│   ├── contact
│   │   └── index.html.twig
│   ├── evenement
│   │   ├── calendar.html.twig
│   │   ├── index.html.twig
│   │   └── show.html.twig
│   ├── home
│   │   └── index.html.twig
│   ├── map
│   │   ├── index.html.twig
│   │   ├── _map_details.html.twig
│   │   └── _map_details_none.html.twig
│   ├── profile
│   │   └── index.html.twig
│   ├── registration
│   │   └── register.html.twig
│   ├── reservation
│   │   └── index.html.twig
│   ├── security
│   │   └── login.html.twig
│   └── tourisme_dashboard
│       ├── dashboard.html.twig
│       └── layout.html.twig
└── vite.config.js
```

---

## Commandes utiles

### Base de données

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Créer une migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Réinitialiser la base de données (ATTENTION : supprime toutes les données)
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Assets

```bash
# Compiler les assets (développement)
npm run dev

# Compiler les assets en mode watch
npm run watch

# Compiler les assets (production)
npm run build
```

### Symfony

```bash
# Vider le cache
php bin/console cache:clear

# Lister les routes
php bin/console debug:router

# Créer une entité
php bin/console make:entity

# Créer un controller
php bin/console make:controller
```

### Tests

```bash
# Lancer les tests PHPUnit
php bin/phpunit

# Lancer les tests avec couverture
php bin/phpunit --coverage-html coverage
```

---

## Résolution de problèmes

### Erreur "Table doesn't exist"

```bash
php bin/console doctrine:migrations:migrate
```

### Erreur "npm ERR! Webpack compilation failed"

```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Erreur "Access denied for user"

Vérifiez votre configuration `DATABASE_URL` dans `.env.local`

### La géolocalisation ne fonctionne pas

1. Vérifiez que vous utilisez HTTPS (certificat SSL)
2. Vérifiez que le navigateur a l'autorisation d'accéder à la localisation
3. Sur mobile, vérifiez que le GPS est activé

---

## Contributeurs

Ce projet a été développé par :

- **Olivier**
- **Victor**
- **Cédric**

---

## Support

Pour toute question ou problème :

Créez une [issue](https://github.com/votre-organisation/mappix/issues) sur GitHub

---

## Notes de développement

### Technologies utilisées

- **Backend** : Symfony 8.0, PHP 8.3.28
- **Frontend** : Tailwind CSS, Stimulus, Webpack Encore
- **Base de données** : MySQL 8.4.7
- **API externe** : Overpass API (données OpenStreetMap)

### Environnement recommandé

- **IDE** : PhpStorm, VS Code avec extensions PHP/Symfony
- **Outils** : Symfony CLI, Composer, Git
- **Navigateur** : Chrome/Firefox (avec outils de développement)

---