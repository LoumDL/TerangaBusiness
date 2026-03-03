# Teranga Business Hub — Backend API

Laravel REST API pour la plateforme fintech de finance solidaire Teranga Business Hub.

---

## Prérequis

- PHP 8.2+ avec extensions : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer 2.x
- MySQL 8.x ou MariaDB 10.6+

---

## Installation locale

```bash
# 1. Cloner et entrer dans le dossier
git clone <URL_DU_REPO> teranga-backend && cd teranga-backend

# 2. Installer les dépendances
composer install

# 3. Copier .env
cp .env.example .env

# 4. Générer la clé
php artisan key:generate

# 5. Créer la base de données
mysql -u root -e "CREATE DATABASE IF NOT EXISTS teranga_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Configurer .env (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 7. Migrations + seed
php artisan migrate && php artisan db:seed

# 8. Lien de stockage
php artisan storage:link

# 9. Démarrer
php artisan serve
```

---

## Variables d'environnement

| Variable | Défaut | Description |
|---|---|---|
| `APP_NAME` | `Teranga Business Hub` | Nom de l'app |
| `APP_ENV` | `local` | Environnement |
| `APP_URL` | `http://localhost:8000` | URL de l'API |
| `DB_CONNECTION` | `mysql` | Driver BDD |
| `DB_HOST` | `127.0.0.1` | Hôte MySQL |
| `DB_PORT` | `3306` | Port MySQL |
| `DB_DATABASE` | `teranga_db` | Nom de la base |
| `DB_USERNAME` | `root` | Utilisateur MySQL |
| `DB_PASSWORD` | `` | Mot de passe MySQL |
| `FILESYSTEM_DISK` | `local` | Stockage (`s3` en prod) |
| `SANCTUM_STATEFUL_DOMAINS` | `localhost:3000` | Domaines Sanctum |
| `CORS_ALLOWED_ORIGINS` | `http://localhost:3000` | Origines CORS |

---

## Compte de démo

| | |
|---|---|
| **Email** | `demo@teranga.sn` |
| **Mot de passe** | `Demo@2026` |

Données créées : 6 cotisations (jan-juin 2026), 2 paiements, historique complet.

---

## Routes API

```
POST /api/v1/auth/register    — Inscription
POST /api/v1/auth/login       — Connexion
POST /api/v1/auth/logout      — Déconnexion (auth)
GET  /api/v1/dashboard        — Tableau de bord (auth)
GET  /api/v1/cotisations      — Liste cotisations (auth)
POST /api/v1/paiements        — Nouveau paiement multipart (auth)
GET  /api/v1/paiements/{id}   — Détail paiement (auth)
GET  /api/v1/historique       — Historique (auth)
```

---

## Déploiement en production (Render)

1. Créer un service Web sur [render.com](https://render.com)
2. **Build Command** : `composer install --no-dev && php artisan migrate --force && php artisan db:seed --force && php artisan storage:link`
3. **Start Command** : `php artisan serve --host=0.0.0.0 --port=$PORT`
4. Variables d'env Render :
```
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
CORS_ALLOWED_ORIGINS=https://teranga-hub.vercel.app
SANCTUM_STATEFUL_DOMAINS=teranga-hub.vercel.app
```

---

## Schéma de base de données

```
users          — id, name, email, password, timestamps
cotisations    — id, user_id(FK), montant DECIMAL, statut ENUM, periode VARCHAR(7)
paiements      — id, user_id(FK), description TEXT, montant DECIMAL, statut ENUM
justificatifs  — id, paiement_id(FK), file_url, file_type, original_name, uploaded_at
historique     — id, user_id(FK), type ENUM, description, montant, statut, date
```

---

## Lien déployé

- API Production : `https://teranga-backend.onrender.com`
- Frontend : `https://teranga-hub.vercel.app`
