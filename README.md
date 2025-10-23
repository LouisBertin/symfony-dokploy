# Symfony App avec Dokploy

## Architecture

### Application Web (Dockerfile principal)
- Serveur Apache + PHP 8.4
- Routes web Symfony
- Templates Twig
- Port 80

### Worker Messenger (Dockerfile.worker)
- PHP 8.3 CLI uniquement
- Exécute les tâches de fond
- Consomme les messages de la file d'attente
- Transport: Doctrine (database)

## Déploiement

### Application Web
- **Dockerfile Path**: `Dockerfile`
- **Container Port**: `80`
- **Internal Path**: `/`
- **Variables d'environnement**:
  - `APP_ENV=prod`
  - `APP_SECRET=votre-clé-secrète`

### Worker Messenger
- **Dockerfile Path**: `Dockerfile.worker`
- **Container Port**: `80` (peut être n'importe quel port, pas utilisé)
- **Internal Path**: `/`
- **Variables d'environnement**:
  - `APP_ENV=prod`
  - `APP_SECRET=votre-clé-secrète`
  - `DATABASE_URL=database://...` (si nécessaire)

## Routes

- `/` - Page d'accueil (Hello World)
- `/hello/{name}` - Page dynamique avec paramètre

## Scheduler

Le scheduler Symfony envoie des messages toutes les 30 secondes via le transport `async`.
Le worker consomme ces messages et exécute les tâches correspondantes.