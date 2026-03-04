# Guide de déploiement — Teranga Business Hub

## URLs de production

| Service | URL |
|---------|-----|
| Frontend | https://teranga-business.vercel.app |
| Backend API | https://terangabusiness.onrender.com |

---

## Prérequis

- Compte [GitHub](https://github.com) avec le repo `TerangaBusiness` poussé
- Compte [Aiven](https://aiven.io) (base de données)
- Compte [Render](https://render.com) (backend)
- Compte [Vercel](https://vercel.com) (frontend)

---

## ÉTAPE 1 — Base de données MySQL sur Aiven

1. Se connecter sur https://aiven.io
2. **Create Service** > MySQL > Free tier > choisir une région
3. Attendre que le service soit `Running`
4. Dans l'onglet **Overview**, récupérer :
   - `Host` (ex: `mysql-xxxx.aivencloud.com`)
   - `Port` (ex: `19919`)
   - `Database` : `defaultdb`
   - `Username` : `avnadmin`
   - `Password` : cliquer sur l'icône œil pour révéler
5. SSL est **obligatoire** — noter le chemin CA : `/etc/ssl/certs/ca-certificates.crt` (déjà présent sur Render)

---

## ÉTAPE 2 — Backend sur Render

### Créer le service

1. Se connecter sur https://render.com
2. **New** > **Web Service**
3. Connecter le repo GitHub `TerangaBusiness`
4. Configurer :
   - **Name** : `terangabusiness`
   - **Root Directory** : `terangabusiness/teranga_backend`
   - **Runtime** : Docker
   - **Branch** : `master`
   - **Instance Type** : Free

### Générer l'APP_KEY

Depuis le terminal local dans `teranga_backend/` :
```bash
php artisan key:generate --show
```
Copier la valeur `base64:XXXX...`

### Ajouter les variables d'environnement

Dans Render > **Environment** > **Add Environment Variable**, saisir :

```
APP_NAME=Teranga Business Hub
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_URL=https://terangabusiness.onrender.com

DB_CONNECTION=mysql
DB_HOST=mysql-3cf800f-djibyloum35-ed68.d.aivencloud.com
DB_PORT=19919
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=VOTRE_MOT_DE_PASSE_AIVEN
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=sync

LOG_CHANNEL=stderr
LOG_LEVEL=error

CORS_ALLOWED_ORIGINS=https://teranga-business.vercel.app
SANCTUM_STATEFUL_DOMAINS=teranga-business.vercel.app
```

### Déployer

Cliquer **Save Changes** puis **Deploy Latest Commit**.

Le démarrage du conteneur exécute automatiquement :
- `php artisan migrate --force` → crée les tables
- `php artisan db:seed --force` → crée le compte demo
- `php artisan serve` → démarre le serveur sur le port assigné

---

## ÉTAPE 3 — Frontend sur Vercel

### Créer le projet

1. Se connecter sur https://vercel.com
2. **New Project** > importer le repo GitHub `TerangaBusiness`
3. Configurer :
   - **Root Directory** : `terangabusiness/teranga_frontend`
   - **Framework Preset** : Nuxt.js

### Ajouter la variable d'environnement

Dans **Environment Variables** :

```
NUXT_PUBLIC_API_BASE=https://terangabusiness.onrender.com
```

### Déployer

Cliquer **Deploy**.

Une fois déployé, récupérer l'URL de production dans **Settings > Domains**
(ex: `https://teranga-business.vercel.app`)

---

## ÉTAPE 4 — Connecter frontend et backend

Après le déploiement Vercel, mettre à jour sur **Render > Environment** :

```
CORS_ALLOWED_ORIGINS=https://teranga-business.vercel.app
SANCTUM_STATEFUL_DOMAINS=teranga-business.vercel.app
```

Cliquer **Save Changes** → Render redéploie automatiquement.

Sur **Vercel**, aller dans **Deployments** > **Redeploy** pour que la variable `NUXT_PUBLIC_API_BASE` soit prise en compte.

---

## Vérification

Tester la connexion sur https://teranga-business.vercel.app avec :
- Email : `demo@teranga.sn`
- Mot de passe : `Demo@2026`

---

## Points d'attention

| Problème | Cause | Solution |
|----------|-------|----------|
| 1ère requête lente (~30s) | Render free tier dort après 15 min d'inactivité | Normal, attendre |
| Erreur CORS | URL Vercel non déclarée dans Render | Mettre à jour `CORS_ALLOWED_ORIGINS` |
| Erreur SSL MySQL | Certificat Aiven non reconnu | Déjà corrigé dans `config/database.php` |
| Migrations non exécutées | Conteneur ne démarre pas | Vérifier les logs Render |
