@echo off
setlocal enabledelayedexpansion

REM HYDE PVP - Docker Development Helper Script for Windows CMD
cd /d "%~dp0"

if "%1"=="" goto help
if "%1"=="help" goto help
if "%1"=="setup" goto setup
if "%1"=="up" goto up
if "%1"=="down" goto down
if "%1"=="restart" goto restart
if "%1"=="status" goto status
if "%1"=="logs" goto logs
if "%1"=="bash" goto bash
if "%1"=="artisan" goto artisan
if "%1"=="composer" goto composer
if "%1"=="npm" goto npm
if "%1"=="migrate" goto migrate
if "%1"=="seed" goto seed
if "%1"=="test" goto test
if "%1"=="clean" goto clean

:help
echo.
echo ========================================
echo   HYDE PVP - Docker Development CLI
echo ========================================
echo Usage: docker-dev.bat [command]
echo.
echo Available Commands:
echo   setup         Full first-time setup (env, containers, composer, migrate, seed)
echo   up            Start all containers in detached mode
echo   down          Stop containers
echo   restart       Restart all containers
echo   status        Show container status
echo   logs          Follow real-time logs
echo   bash          Open bash shell inside app container
echo   artisan [...] Run artisan command (e.g. docker-dev.bat artisan route:list)
echo   composer [...]Run composer command (e.g. docker-dev.bat composer update)
echo   npm [...]     Run npm command (e.g. docker-dev.bat npm run build)
echo   migrate       Run database migrations
echo   seed          Seed initial database data
echo   test          Run PHPUnit test suite
echo   clean         Stop containers and remove volumes (wipes database)
echo.
goto end

:setup
echo [HYDE PVP] Starting Development Environment Setup...
if not exist .env (
    echo [HYDE PVP] Copying .env.docker.example to .env...
    copy .env.docker.example .env >nul
)
echo [HYDE PVP] Building and starting Docker containers...
docker compose up -d --build
echo [HYDE PVP] Installing Composer dependencies...
docker compose exec -T app composer install
echo [HYDE PVP] Generating Application Key...
docker compose exec -T app php artisan key:generate --force
echo [HYDE PVP] Running Migrations and Seeders...
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan db:seed --force
echo.
echo ========================================
echo  Setup Complete!
echo  Web App:       http://localhost:8000
echo  Vite HMR:      http://localhost:5173
echo  MySQL:         localhost:3306
echo  Redis:         localhost:6379
echo ========================================
goto end

:up
docker compose up -d
echo [HYDE PVP] Containers started. Access at http://localhost:8000
goto end

:down
docker compose down
echo [HYDE PVP] Containers stopped.
goto end

:restart
docker compose down
docker compose up -d
echo [HYDE PVP] Containers restarted.
goto end

:status
docker compose ps
goto end

:logs
shift
docker compose logs -f %1 %2 %3 %4 %5
goto end

:bash
docker compose exec app bash
goto end

:artisan
shift
docker compose exec app php artisan %1 %2 %3 %4 %5 %6 %7 %8 %9
goto end

:composer
shift
docker compose exec app composer %1 %2 %3 %4 %5 %6 %7 %8 %9
goto end

:npm
shift
docker compose exec vite npm %1 %2 %3 %4 %5 %6 %7 %8 %9
goto end

:migrate
shift
docker compose exec app php artisan migrate %1 %2 %3 %4 %5
goto end

:seed
shift
docker compose exec app php artisan db:seed %1 %2 %3 %4 %5
goto end

:test
shift
docker compose exec app php ./vendor/bin/phpunit %1 %2 %3 %4 %5
goto end

:clean
echo WARNING: This will delete database volumes and stop containers.
set /p confirm="Are you sure? (y/N): "
if /i "%confirm%"=="y" (
    docker compose down -v
    echo [HYDE PVP] Environment wiped clean.
)
goto end

:end
