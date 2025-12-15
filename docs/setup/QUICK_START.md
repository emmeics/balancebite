# Quick Start Guide

## Prerequisites

Before starting, make sure you have installed:

- **PHP 8.3+** with extensions: intl, pdo_pgsql, redis
- **Composer 2.x**
- **Docker** and **Docker Compose**
- **Symfony CLI** ([installation guide](https://symfony.com/download))

## Step-by-Step Setup

### 1. Clone the repository

```bash
git clone https://github.com/emmeics/balancebite.git
cd balancebite
```

### 2. Start Docker services

```bash
make start
```

This starts PostgreSQL and Redis containers.

### 3. Install dependencies and configure

```bash
make init
```

This command will:
- Install Composer dependencies
- Generate JWT keys
- Create the database
- Run migrations

### 4. Start the development server

```bash
make serve
```

The API is now available at: **https://localhost:8000**

## Verify Installation

### Check API health

```bash
curl https://localhost:8000/api/v1/health
```

### Check database connection

```bash
cd backend
php bin/console dbal:run-sql "SELECT 1"
```

## Common Issues

### Port already in use

If port 5432 or 6379 is already in use, edit `.env` in the root folder:

```env
DB_PORT=5433
REDIS_PORT=6380
```

### PHP extensions missing

Check required extensions:

```bash
php -m | grep -E "(intl|pdo_pgsql|redis)"
```

Install missing extensions via your package manager or php.ini.

## Next Steps

- Read the [Architecture Overview](architecture/OVERVIEW.md)
- Check the [API Documentation](/api/doc)
- Run tests with `make test`
