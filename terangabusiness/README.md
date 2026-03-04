# Teranga Business Hub

Plateforme fintech de finance solidaire (tontine digitale) pour entrepreneurs africains.

## Liens de production

| Service | URL |
|---------|-----|
| **Frontend** | https://teranga-business.vercel.app |
| **Backend API** | https://terangabusiness.onrender.com |

## Stack technique

- **Frontend** : NuxtJS 4 + Vue 3 + TailwindCSS v4 + Pinia + @nuxt/ui
- **Backend** : Laravel 12 + PHP 8.4 + MySQL (Aiven) + Laravel Sanctum
- **Déploiement** : Vercel (frontend) + Render Docker (backend)

## Structure

```
terangabusiness/
  teranga_backend/    — API REST Laravel
  teranga_frontend/   — Application NuxtJS
```

## Compte de démo

- Email : `demo@teranga.sn`
- Mot de passe : `Demo@2026`

## Développement local

### Backend
```bash
cd teranga_backend
php artisan serve --port=8000
```

### Frontend
```bash
cd teranga_frontend
npm run dev
```
