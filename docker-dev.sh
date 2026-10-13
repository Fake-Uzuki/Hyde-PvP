#!/usr/bin/env bash

# HYDE PVP - Docker Development Helper Script (Linux, macOS, WSL)
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)"
cd "$DIR"

function print_help() {
    echo ""
    echo "🎮 HYDE PVP — Docker Development CLI"
    echo "======================================"
    echo "Usage: ./docker-dev.sh [command]"
    echo ""
    echo "Available Commands:"
    echo "  setup         Full first-time setup (env, containers, composer, migrate, seed, npm)"
    echo "  up            Start all containers in detached mode"
    echo "  down          Stop and remove containers"
    echo "  restart       Restart all containers"
    echo "  status        Check container statuses"
    echo "  logs          Follow real-time container logs"
    echo "  bash          Open an interactive shell inside the app container"
    echo "  artisan [...] Run a Laravel artisan command (e.g., ./docker-dev.sh artisan route:list)"
    echo "  composer [...]Run composer commands (e.g., ./docker-dev.sh composer update)"
    echo "  npm [...]     Run npm commands (e.g., ./docker-dev.sh npm run build)"
    echo "  migrate       Run database migrations"
    echo "  seed          Seed initial database data"
    echo "  test          Run PHPUnit test suite"
    echo "  clean         Stop containers and wipe volumes (caution: resets database)"
    echo ""
}

case "$1" in
    setup)
        echo "🚀 Starting HYDE PVP Development Environment Setup..."
        if [ ! -f .env ]; then
            echo "📄 Copying .env.docker.example to .env..."
            cp .env.docker.example .env
        fi
        echo "🐳 Building and starting Docker containers..."
        docker compose up -d --build
        echo "📦 Installing Composer dependencies..."
        docker compose exec -T app composer install
        echo "🔑 Generating Application Key..."
        docker compose exec -T app php artisan key:generate --force
        echo "🗄️ Running Migrations and Seeders..."
        docker compose exec -T app php artisan migrate --force
        docker compose exec -T app php artisan db:seed --force
        echo ""
        echo "✅ Setup Complete!"
        echo "🌐 Web Application: http://localhost:8000"
        echo "⚡ Vite HMR Server:  http://localhost:5173"
        echo "🗄️ MySQL Database:   localhost:3306 (user: laravel, pass: secret, db: p5db)"
        echo "🔴 Redis:            localhost:6379"
        ;;
    up)
        docker compose up -d
        echo "✅ Containers are running! Access at http://localhost:8000"
        ;;
    down)
        docker compose down
        echo "🛑 Containers stopped."
        ;;
    restart)
        docker compose down
        docker compose up -d
        echo "🔄 Containers restarted."
        ;;
    status)
        docker compose ps
        ;;
    logs)
        docker compose logs -f "${@:2}"
        ;;
    bash)
        docker compose exec app bash
        ;;
    artisan)
        docker compose exec app php artisan "${@:2}"
        ;;
    composer)
        docker compose exec app composer "${@:2}"
        ;;
    npm)
        docker compose exec vite npm "${@:2}"
        ;;
    migrate)
        docker compose exec app php artisan migrate "${@:2}"
        ;;
    seed)
        docker compose exec app php artisan db:seed "${@:2}"
        ;;
    test)
        docker compose exec app php ./vendor/bin/phpunit "${@:2}"
        ;;
    clean)
        read -p "⚠️  Are you sure you want to stop containers and wipe database volumes? (y/N): " confirm
        if [[ $confirm == [yY] || $confirm == [yY][eE][sS] ]]; then
            docker compose down -v
            echo "🧹 Environment wiped clean."
        fi
        ;;
    *)
        print_help
        ;;
esac
