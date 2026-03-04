# Déploiement — Teranga Business Hub

## URLs de production
- **Backend** : https://terangabusiness.onrender.com
- **Frontend** : https://teranga-business.vercel.app

---

## ÉTAPE 1 — Base de données MySQL (Aiven)

1. Créer un compte sur https://aiven.io
2. New Service > MySQL > Free tier
3. Récupérer les infos de connexion :
   - Host, Port, Database, Username, Password
   - SSL : REQUIS (utiliser `/etc/ssl/certs/ca-certificates.crt` sur Render)

---

## ÉTAPE 2 — Backend sur Render

1. Compte sur https://render.com
2. New > Web Service > connecter le repo GitHub
3. Settings :
   - **Root Directory** : `terangabusiness/teranga_backend`
   - **Runtime** : Docker
   - **Branch** : master
4. Environment > ajouter toutes les variables ci-dessous
5. Cliquer Deploy

### Variables d'environnement Render

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

> Générer APP_KEY : `php artisan key:generate --show`

---

## ÉTAPE 3 — Frontend sur Vercel

1. Compte sur https://vercel.com
2. New Project > importer le repo GitHub
3. Settings :
   - **Root Directory** : `terangabusiness/teranga_frontend`
   - **Framework Preset** : Nuxt.js
4. Environment Variables > ajouter :

```
NUXT_PUBLIC_API_BASE=https://terangabusiness.onrender.com
```

5. Deploy

---

## ÉTAPE 4 — Après déploiement

Si l'URL Vercel change (nouveau déploiement, domaine custom), mettre à jour sur Render :
- `CORS_ALLOWED_ORIGINS` = nouvelle URL Vercel
- `SANCTUM_STATEFUL_DOMAINS` = nouvelle URL Vercel (sans https://)

Render redéploie automatiquement après Save Changes.

---

## Points d'attention

- **Render free tier** : le service dort après 15 min d'inactivité → la 1ère requête prend ~30s
- **SSL Aiven** : vérification du certificat désactivée dans `config/database.php` (connexion reste chiffrée)
- **php artisan migrate** : s'exécute automatiquement au démarrage du conteneur (CMD dans Dockerfile)
- **php artisan db:seed** : s'exécute aussi au démarrage → crée le compte demo@teranga.sn / Demo@2026




