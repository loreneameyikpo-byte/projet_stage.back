# Projetis — Backend

API REST développée avec **Laravel 12**, servant de backend à Projetis, une plateforme de gestion des projets de fin de formation (soumission, encadrement, planification des soutenances, jury, notifications, sauvegardes automatiques).

Frontend associé : [Projetis — Frontend](#) (Nuxt).

---

## Sommaire

- [Stack technique](#stack-technique)
- [Fonctionnalités](#fonctionnalités)
- [Installation en local](#installation-en-local)
- [Variables d'environnement](#variables-denvironnement)
- [Commandes utiles](#commandes-utiles)
- [Déploiement](#déploiement)
- [Sauvegardes automatiques](#sauvegardes-automatiques)
- [Rôles et permissions](#rôles-et-permissions)

---

## Stack technique

| Composant | Technologie |
|---|---|
| Framework | Laravel 12 (PHP 8.4) |
| Base de données | MySQL |
| Authentification | Laravel Sanctum (jetons Bearer) |
| ORM | Eloquent |
| Email | Brevo (API HTTP, via `symfony/brevo-mailer`) |
| Sauvegardes | `spatie/laravel-backup` → Google Drive |
| Historique d'activité | `spatie/laravel-activitylog` |
| Conteneurisation | Docker |

---

## Fonctionnalités

- **Authentification** : connexion par email/mot de passe, limitation à 3 tentatives (blocage 15 min), changement de mot de passe obligatoire à la première connexion, réinitialisation par email.
- **Gestion des utilisateurs** : 5 rôles (étudiant, encadreur, administrateur, super administrateur, jury externe), traçabilité du créateur de chaque compte (`cree_par`).
- **Projets** : soumission, versions, validation/corrections par l'encadreur, changement de statut.
- **Soutenances** : planification avec vérification de disponibilité de salle, composition du jury.
- **Jury** : saisie collégiale des notes avec commentaire obligatoire, calcul automatique de la note finale.
- **Notifications** : en temps réel pour le super administrateur (en base + email).
- **Historique d'activité** : traçabilité de toutes les actions sensibles (créations, modifications), consultable par le super administrateur.
- **Sauvegardes automatiques** : quotidiennes, copiées sur Google Drive.

---

## Installation en local

Prérequis : Docker Desktop.

```bash
git clone <url-du-depot>
cd Backend
cp .env.example .env
docker compose up -d --build
docker exec laravl_app composer install
docker exec laravl_app php artisan key:generate
docker exec laravl_app php artisan migrate
docker exec laravl_app php artisan storage:link
```

L'API est ensuite disponible sur `http://localhost:8000`.

Outils annexes démarrés avec Docker :
- **phpMyAdmin** : `http://localhost:8080` (ou le port configuré)
- **Mailpit** (capture des emails en développement) : `http://localhost:8025`

---

## Variables d'environnement

Voir `.env.example` pour la liste complète. Points d'attention particuliers :

- `DB_*` : connexion MySQL (via Docker en local, via une base gérée type Railway en production).
- `FRONTEND_URL` / `FRONTEND_URL_PROD` : origines autorisées en CORS (local et production).
- `MAIL_MAILER=brevo` + `BREVO_API_KEY` : envoi d'email en production, via l'API Brevo plutôt que le SMTP (certains hébergeurs bloquent les ports SMTP sortants sur leurs offres gratuites — l'API HTTP contourne cette limite).
- `GOOGLE_DRIVE_*` : identifiants pour la destination des sauvegardes automatiques.
- `BACKUP_TRIGGER_SECRET` : secret partagé protégeant les routes système (`/api/system/*`), utilisées pour déclencher migrations/sauvegardes à distance sans accès shell.

---

## Commandes utiles

```bash
# Lancer les migrations
docker exec laravl_app php artisan migrate

# Ouvrir un terminal interactif dans le conteneur
docker exec -it laravl_app bash

# Vider les caches de configuration
docker exec laravl_app php artisan config:clear

# Lancer une sauvegarde manuelle
docker exec laravl_app php artisan backup:run
```

---

## Déploiement

- **Backend** : hébergé sur [Railway](https://railway.app), déployé via `railway up` ou automatiquement depuis GitHub.
- **Base de données** : MySQL managé sur Railway (même projet, connexion via réseau interne).
- **Sauvegardes planifiées** : déclenchées gratuitement via [cron-job.org](https://cron-job.org), qui appelle quotidiennement une route protégée (`/api/system/trigger-backup`), l'hébergeur ne proposant pas de tâche planifiée gratuite.

Le `Dockerfile` utilise `${PORT:-8000}` pour le port d'écoute : certains hébergeurs (Railway) assignent un port dynamique via la variable `PORT`, à la différence d'un environnement Docker local classique.

---

## Sauvegardes automatiques

Sauvegarde quotidienne (base de données + fichiers), conservée localement puis copiée sur Google Drive. Politique de rétention : 7 jours (toutes), 30 jours (quotidiennes), 8 semaines (hebdomadaires), 6 mois (mensuelles), 2 ans (annuelles).

---

## Rôles et permissions

| Rôle | Portée |
|---|---|
| Étudiant | Son propre projet et sa soutenance |
| Encadreur | Les projets qui lui sont affectés, ses évaluations de jury |
| Administrateur | Gestion complète (étudiants, encadreurs, projets, soutenances...) ; tableau de bord personnel limité à ses propres créations |
| Super Administrateur | Tout ce que fait un administrateur, + gestion des rôles/permissions, des autres administrateurs, et de l'historique d'activité |
| Jury externe | Les soutenances où il est désigné, saisie de notes |