# Agency - Plateforme de gestion immobilière

*Une solution complète pour la gestion de rendez-vous et de biens immobiliers.*

## 📌 Description
**Agency** est une application web conçue pour simplifier la gestion des biens immobiliers et des rendez-vous entre clients et particulier.  

### 👥 Rôles et fonctionnalités
#### **Pour les clients** :
- 📅 Prendre rendez-vous en ligne pour visiter un bien publié.
- 🔍 Parcourir les biens disponibles avec filtres (localisation, prix, etc.).

#### **Pour les particuliers** :
- 🏠 **Gestion des biens** : Ajouter, modifier, supprimer ou archiver un bien.
- 🗓️ **Gestion des rendez-vous** : Valider/annuler les demandes de visite, consulter l'agenda.
- 📊 Tableau de bord analytique (statistiques des biens, rendez-vous, etc.).

## 🌟 Fonctionnalités clés
- **Interface intuitive** : Navigation fluide pour les clients et les particuliers.
- **Notifications** : Alertes par email/SMS pour les nouveaux rendez-vous.
- **Archivage intelligent** : Conservation des biens vendus/loués sans encombrer l’affichage principal.

## 🚀 Technologies utilisées
- **Frontend** : HTML/CSS, JavaScript, Twig (Symfony)
- **Backend** : PHP 8+, Symfony 6+
- **Base de données** : MySQL
- **Outils** : Git, Composer

## 🔧 Installation
1. **Cloner le dépôt** :
   ```bash
   git clone https://github.com/marouamechri/agency.git
   cd agency
   ```
2. **Installer les dépendances Symfony** :
   ```bash
   composer install
   ```
3. **Configurer l'environnement** :
   - Copier le fichier .env et l'adapter 
      ```bash
      cp .env .env.local
      ```
   - Modifier les variables dans .env.local :
     ```bash
      DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/agency?serverVersion=8.0"
     ```
4. **Créer la base de données** :
    ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
5. **Lancer le serveur Symfony**:
    ```bash
   symfony server:start
   ```
L'application sera accessible sur : http://localhost:8000


📧 Contact
Auteur : Maroua Mechri

Email : votre-email@exemple.com

LinkedIn : www.linkedin.com/in/maroua-mechri
