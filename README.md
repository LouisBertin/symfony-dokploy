# Symfony App avec Dokploy

## Architecture

### Application Web (Dockerfile principal)
- Serveur Apache + PHP 8.4
- Routes web Symfony
- Templates Twig
- Mysql 8.0
- Port 80

### Worker Messenger (Dockerfile.worker)
- PHP 8.4 CLI uniquement
- Exécute les tâches de fond
- Consomme les messages de la file d'attente
- Transport: Doctrine (database)

ℹ️ Voir le dossier `.dokploy`

## Déploiement

### Application Web
- **Dockerfile Path**: `.dokploy/Dockerfile`
- **Dockerfile Context**: `.`
- **Container Port**: `80`
- **Internal Path**: `/`
- **Variables d'environnement**:
  - `APP_ENV=prod`
  - `APP_SECRET=votre-clé-secrète`
  - `DATABASE_URL=database://...` (si nécessaire)

### Worker Messenger
- **Dockerfile Path**: `.dokploy/Dockerfile.worker`
- - **Dockerfile Context**: `.`
- **Variables d'environnement**:
  - `APP_ENV=prod`
  - `APP_SECRET=votre-clé-secrète`
  - `DATABASE_URL=database://...` (si nécessaire)

## Routes

- `/` - Page d'accueil (Hello World)
- `/hello/{name}` - Page dynamique avec paramètre

## Scheduler

Le scheduler Symfony envoie des messages
Le worker consomme ces messages et exécute les tâches correspondantes.

## Local build

```
docker build -t symfony-test -f .dokploy/Dockerfile .
docker run -p 8080:80 symfony-test
```
