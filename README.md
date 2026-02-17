# laravel-my-media-library

Eine Laravel 12 Anwendung mit Filament 5 Admin-Panel für die Verwaltung von Medieninhalten.

## 1. Installation

### Voraussetzungen

- Docker
- Docker-Compose
- Git

### Erstellen der .env-Datei
```bash
cp .env.example .env
```

### Initialisierung ausführen
```bash
sh init.sh laravel-my-media-library
```

## 2. Entwicklungsumgebung (Development)

### Docker-Container starten

```bash
docker-compose up -d
```

### Container-Status überprüfen

```bash
docker-compose ps
```

### In den Laravel-Container wechseln

```bash
docker-compose exec -it -u sail laravel bash
```

### Wichtige Befehle im Container ausführen

#### Composer-Pakete installieren/aktualisieren

```bash
docker-compose exec -it -u sail laravel bash
composer install
# oder
composer update
```

#### Laravel-Befehle ausführen

```bash
docker-compose exec -it -u sail laravel bash
php artisan migrate
php artisan db:seed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### NPM-Pakete installieren und Vite starten

```bash
docker-compose exec -it -u sail laravel bash
npm install
npm run dev
```

### Container stoppen

```bash
docker-compose down
```

### Container stoppen und Volumes löschen (Datenbank zurücksetzen)

```bash
docker-compose down -v
```

### Logs anzeigen

```bash
# Alle Container
docker-compose logs -f

# Nur Laravel-Container
docker-compose logs -f laravel
```

## 3. Produktionsumgebung (Production)

### Docker-Container starten (Produktion)

```bash
docker-compose -f docker-compose-prod.yml up -d
```

### Container-Status überprüfen

```bash
docker-compose -f docker-compose-prod.yml ps
```

### In den Laravel-Container wechseln

```bash
docker-compose -f docker-compose-prod.yml exec -it -u sail laravel bash
```

### Produktions-Setup im Container

#### Abhängigkeiten installieren (ohne Dev-Pakete)

```bash
docker-compose -f docker-compose-prod.yml exec -it -u sail laravel bash
composer install --optimize-autoloader --no-dev
```

#### Anwendung optimieren

```bash
docker-compose -f docker-compose-prod.yml exec -it -u sail laravel bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

#### Assets für Produktion bauen

```bash
docker-compose -f docker-compose-prod.yml exec -it -u sail laravel bash
npm install
npm run build
```

#### Datenbank migrieren

```bash
docker-compose -f docker-compose-prod.yml exec -it -u sail laravel bash
php artisan migrate --force
```

### Container stoppen (Produktion)

```bash
docker-compose -f docker-compose-prod.yml down
```

### Logs anzeigen (Produktion)

```bash
# Alle Container
docker-compose -f docker-compose-prod.yml logs -f

# Nur Laravel-Container
docker-compose -f docker-compose-prod.yml logs -f laravel
```

## 4. Admin Dashboard

### Zugriff auf das Admin-Panel

- **URL:** http://localhost/admin/login
- **E-Mail:** admin@example.com
- **Passwort:** secret

### Neuen Admin-Benutzer erstellen

```bash
docker-compose exec -it -u sail laravel bash
php artisan make:filament-user
```

## 5. Nützliche Docker-Befehle

### Container neu starten

```bash
# Development
docker-compose restart

# Production
docker-compose -f docker-compose-prod.yml restart
```

### Container neu bauen (nach Dockerfile-Änderungen)

```bash
# Development
docker-compose up -d --build

# Production
docker-compose -f docker-compose-prod.yml up -d --build
```

### Datenbank-Backup erstellen

```bash
docker-compose exec mysql mysqldump -u root -p${MYSQL_ROOT_PASSWORD} ${MYSQL_DATABASE} > backup.sql
```

### Datenbank-Backup wiederherstellen

```bash
docker-compose exec -T mysql mysql -u root -p${MYSQL_ROOT_PASSWORD} ${MYSQL_DATABASE} < backup.sql
```

## 6. Troubleshooting

### Permission-Probleme

```bash
docker-compose exec -it -u sail laravel bash
chmod -R 775 storage bootstrap/cache
chown -R sail:sail storage bootstrap/cache
```

### Cache komplett löschen

```bash
docker-compose exec -it -u sail laravel bash
php artisan optimize:clear
```

### Container komplett neu aufsetzen

```bash
docker-compose down -v
docker-compose up -d
docker-compose exec -it -u sail laravel bash
composer install
php artisan migrate:fresh --seed
npm install
npm run dev
```

## 7. Technologie-Stack

- **Framework:** [Laravel 12](https://laravel.com/)
- **Admin-Panel:** [Filament 5](https://filamentphp.com/)
- **Containerisierung:** [Docker](https://www.docker.com/) & [Docker-Compose](https://docs.docker.com/compose/)
- **Datenbank:** [MySQL 8.0](https://hub.docker.com/r/mysql/mysql-server)
- **Cache:** [Redis](https://hub.docker.com/_/redis)
- **Mail-Testing:** [Mailpit](https://hub.docker.com/r/axllent/mailpit)
