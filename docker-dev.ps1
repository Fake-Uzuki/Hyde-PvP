<#
.SYNOPSIS
    HYDE PVP - Docker Development Helper for Windows PowerShell
.EXAMPLE
    .\docker-dev.ps1 setup
    .\docker-dev.ps1 up
    .\docker-dev.ps1 artisan migrate
#>

param(
    [Parameter(Position=0)]
    [string]$Command = "help",

    [Parameter(ValueFromRemainingArguments=$true)]
    [string[]]$ExtraArgs
)

$ErrorActionPreference = "Stop"

function Show-Help {
    Write-Host ""
    Write-Host "🎮 HYDE PVP — Docker Development CLI (PowerShell)" -ForegroundColor Cyan
    Write-Host "=================================================" -ForegroundColor DarkGray
    Write-Host "Usage: .\docker-dev.ps1 <command> [arguments]"
    Write-Host ""
    Write-Host "Commands:" -ForegroundColor Yellow
    Write-Host "  setup         Full first-time setup (env, containers, composer, migrate, seed)"
    Write-Host "  up            Start containers in detached mode"
    Write-Host "  down          Stop all containers"
    Write-Host "  restart       Restart all containers"
    Write-Host "  status        Check container statuses"
    Write-Host "  logs          Follow real-time logs"
    Write-Host "  bash          Open an interactive shell in app container"
    Write-Host "  artisan [...] Run Laravel artisan commands"
    Write-Host "  composer [...]Run composer commands"
    Write-Host "  npm [...]     Run npm commands"
    Write-Host "  migrate       Run database migrations"
    Write-Host "  seed          Run database seeders"
    Write-Host "  test          Run PHPUnit test suite"
    Write-Host "  clean         Stop containers and wipe volumes"
    Write-Host ""
}

switch ($Command.ToLower()) {
    "setup" {
        Write-Host "🚀 Starting HYDE PVP Development Setup..." -ForegroundColor Cyan
        if (-not (Test-Path ".env")) {
            Write-Host "📄 Copying .env.docker.example to .env..." -ForegroundColor Yellow
            Copy-Item ".env.docker.example" ".env"
        }
        Write-Host "🐳 Building and starting Docker containers..." -ForegroundColor Cyan
        docker compose up -d --build
        Write-Host "📦 Installing Composer dependencies..." -ForegroundColor Cyan
        docker compose exec -T app composer install
        Write-Host "🔑 Generating Application Key..." -ForegroundColor Cyan
        docker compose exec -T app php artisan key:generate --force
        Write-Host "🗄️ Running Migrations and Seeders..." -ForegroundColor Cyan
        docker compose exec -T app php artisan migrate --force
        docker compose exec -T app php artisan db:seed --force
        Write-Host ""
        Write-Host "✅ Setup Complete!" -ForegroundColor Green
        Write-Host "🌐 Web App:  http://localhost:8000" -ForegroundColor White
        Write-Host "⚡ Vite HMR: http://localhost:5173" -ForegroundColor White
        Write-Host "🗄️ MySQL:    localhost:3306" -ForegroundColor White
        Write-Host "🔴 Redis:    localhost:6379" -ForegroundColor White
    }
    "up" {
        docker compose up -d
        Write-Host "✅ Containers started: http://localhost:8000" -ForegroundColor Green
    }
    "down" {
        docker compose down
        Write-Host "🛑 Containers stopped." -ForegroundColor Yellow
    }
    "restart" {
        docker compose down
        docker compose up -d
        Write-Host "🔄 Containers restarted." -ForegroundColor Green
    }
    "status" {
        docker compose ps
    }
    "logs" {
        docker compose logs -f @ExtraArgs
    }
    "bash" {
        docker compose exec app bash
    }
    "artisan" {
        docker compose exec app php artisan @ExtraArgs
    }
    "composer" {
        docker compose exec app composer @ExtraArgs
    }
    "npm" {
        docker compose exec vite npm @ExtraArgs
    }
    "migrate" {
        docker compose exec app php artisan migrate @ExtraArgs
    }
    "seed" {
        docker compose exec app php artisan db:seed @ExtraArgs
    }
    "test" {
        docker compose exec app php ./vendor/bin/phpunit @ExtraArgs
    }
    "clean" {
        $confirm = Read-Host "⚠️ Wipe database volumes and stop containers? (y/N)"
        if ($confirm -eq "y" -or $confirm -eq "Y") {
            docker compose down -v
            Write-Host "🧹 Environment wiped clean." -ForegroundColor Yellow
        }
    }
    Default {
        Show-Help
    }
}
