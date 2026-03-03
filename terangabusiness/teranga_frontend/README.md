# Teranga Business Hub — Frontend

Application NuxtJS 4 (Vue 3) pour la plateforme fintech de finance solidaire Teranga Business Hub.

---

## Prérequis

- Node.js 18+
- npm 9+
- Backend Laravel démarré sur `http://localhost:8000`

---

## Installation locale

```bash
# 1. Cloner et entrer dans le dossier
git clone <URL_DU_REPO> teranga-frontend && cd teranga-frontend

# 2. Installer les dépendances
npm install

# 3. Copier le .env
cp .env.example .env

# 4. Configurer l'URL du backend dans .env
# NUXT_PUBLIC_API_BASE=http://localhost:8000

# 5. Démarrer en développement
npm run dev
```

L'application est accessible sur `http://localhost:3000`

---

## Variables d'environnement

| Variable | Défaut | Description |
|---|---|---|
| `NUXT_PUBLIC_API_BASE` | `http://localhost:8000` | URL du backend Laravel |
| `NUXT_PUBLIC_APP_NAME` | `Teranga Business Hub` | Nom affiché |

---

## Lancer en développement

```bash
npm run dev        # → http://localhost:3000
npm run build      # Build de production
npm run preview    # Prévisualiser le build
```

---

## Compte de démo

| | |
|---|---|
| **Email** | `demo@teranga.sn` |
| **Mot de passe** | `Demo@2026` |

---

## Pages et fonctionnalités

| Route | Description |
|---|---|
| `/login` | Connexion email + mot de passe |
| `/register` | Inscription nouveau membre |
| `/dashboard` | Solde global + liste des cotisations |
| `/paiement` | Formulaire de paiement avec upload justificatif |
| `/historique` | Historique filtrable par jour / mois / année |

---

## Architecture

```
app/
  pages/          — Routes NuxtJS (login, register, dashboard, paiement, historique)
  components/     — Composants réutilisables (AppHeader, SoldeCard, PaiementForm, etc.)
  stores/         — État Pinia (auth, cotisation, paiement, historique)
  composables/    — Logique réutilisable (useAuth, usePaiement, useHistorique, etc.)
  middleware/     — auth.ts (protection des routes)
  types/          — Interfaces TypeScript
  utils/          — api.ts (fetch configuré avec intercepteur token)
  assets/css/     — main.css (Tailwind v4 + thème Teranga)
```

---

## Déploiement en production (Vercel)

1. Connecter le repo GitHub à [vercel.com](https://vercel.com)
2. **Framework Preset** : Nuxt.js
3. Variables d'environnement :
```
NUXT_PUBLIC_API_BASE=https://teranga-backend.onrender.com
```
4. Deploy automatique sur chaque push `main`

---

## CI/CD GitHub Actions

Le workflow `.github/workflows/deploy.yml` :
- Build + test automatique sur chaque PR
- Déploiement auto sur `main` → Vercel

---

## Lien déployé

- Frontend : `https://teranga-hub.vercel.app`
- API Backend : `https://teranga-backend.onrender.com`
