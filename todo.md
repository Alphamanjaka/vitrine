📅 Planning de la semaine
Jour 1 : Installation et Architecture

    Objectif : Environnement propre et premières routes.

    Actions : * Installer Laravel via Laragon.

        Créer le Layout principal (app.blade.php) avec un menu (Accueil, Services, Contact).

        Configurer les routes de base dans web.php.

Jour 2 : Base de données et Migrations

    Objectif : Préparer le stockage des données.

    Actions :

        Créer une migration pour les "Services" (titre, description, icône).

        Créer une migration pour les "Messages" du formulaire de contact.

        Lancer php artisan migrate.

Jour 3 : Les Modèles et le "Seeding"

    Objectif : Remplir le site avec de fausses données.

    Actions :

        Créer le modèle Service.

        Utiliser les Factories et Seeders pour générer 6 services automatiquement en base de données.

        Afficher ces services sur la page d'accueil avec une boucle @foreach.

Jour 4 : Formulaire de Contact et Validation

    Objectif : Rendre le site interactif.

    Actions :

        Créer un formulaire HTML/Blade.

        Créer un ContactController pour gérer l'envoi.

        Ajouter la validation (ex: l'email doit être valide, le message est obligatoire).

        Enregistrer le message en base de données et afficher un message de succès ("Flash message").

Jour 5 : Authentification et Administration (Début)

    Objectif : Créer un espace privé pour l'entreprise.

    Actions :

        Installer Laravel Breeze (le kit d'authentification le plus simple) : composer require laravel/breeze --dev puis php artisan breeze:install.

        Découvrir l'espace "Dashboard" créé automatiquement.

Jour 6 : CRUD des Services

    Objectif : Pouvoir modifier le site sans toucher au code.

    Actions :

        Créer une interface pour Créer, Read (lire), Update (modifier), Delete (supprimer) les services.

        Sécuriser ces pages pour que seul l'administrateur connecté y accède (Middleware auth).

Jour 7 : Finitions et SEO

    Objectif : Rendre le site professionnel.

    Actions :

        Ajouter des balises Meta dynamiques pour le référencement.

        Optimiser les images.

        Tester le site sur mobile.

🛠️ Outils à utiliser dans VS Code

Pour réussir ce projet en une semaine, utilise ces commandes artisan dans ton terminal :

    php artisan make:model Service -m (Crée le modèle + la migration en une fois).

    php artisan make:controller ServiceController --resource (Crée un contrôleur avec toutes les méthodes CRUD déjà prêtes).

    php artisan route:list (Pour voir toutes tes URLs actives).
