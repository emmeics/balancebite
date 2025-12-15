# 🍽️ BalanceBite

> **Every bite in balance** — Personal nutrition assistant for dietary management

[![CI](https://github.com/emmeics/balancebite/actions/workflows/ci.yml/badge.svg)](https://github.com/emmeics/balancebite/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![Symfony](https://img.shields.io/badge/Symfony-7.2-000000?logo=symfony)](https://symfony.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?logo=postgresql&logoColor=white)](https://postgresql.org)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

---

## 📖 About

BalanceBite is a personal nutrition assistant that helps users follow dietary plans and manage food restrictions with ease. Built as a portfolio project demonstrating modern PHP development with **Domain-Driven Design (DDD)** architecture.

### ✨ Key Features

- 🔐 **Secure Authentication** — JWT-based auth with Google OAuth support
- 👤 **Health Profile** — Track health conditions, allergies, and dietary goals
- 🥗 **Smart Food Search** — Search across Open Food Facts and USDA databases
- ⚠️ **Restriction Alerts** — Automatic warnings for foods that violate your restrictions
- 🔄 **Alternative Suggestions** — Get safe food alternatives when needed
- 📅 **Meal Planning** — Create and manage weekly meal plans

---

## 🏗️ Architecture

The project follows **Domain-Driven Design (DDD)** with **Clean Architecture** principles:

```
src/
├── Domain/                 # 💎 Business logic (pure PHP, no framework)
│   ├── User/              # User bounded context
│   ├── Nutrition/         # Nutrition bounded context
│   └── Meal/              # Meal bounded context
│
├── Application/           # 🎯 Use cases (commands, queries)
│
├── Infrastructure/        # 🔧 Technical implementations
│   ├── Persistence/       # Doctrine repositories
│   ├── ExternalService/   # External APIs
│   └── Security/          # JWT, OAuth
│
└── Presentation/          # 🌐 API controllers
```

### Design Patterns Used

- **Repository Pattern** — Abstract data access
- **CQRS** — Separate read/write operations
- **Strategy Pattern** — Multiple nutrition data sources
- **Value Objects** — Immutable domain concepts

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.3+
- Composer
- Docker & Docker Compose
- Symfony CLI

### Installation

```bash
# Clone the repository
git clone https://github.com/emmeics/balancebite.git
cd balancebite

# Start Docker services (PostgreSQL, Redis)
make start

# Install dependencies and setup
make init

# Start the development server
make serve
```

The API will be available at: **https://localhost:8000**

### Available Commands

```bash
make help         # Show all available commands
make start        # Start Docker containers
make serve        # Start Symfony dev server
make test         # Run tests
make qa           # Run all quality checks (lint + stan + test)
```

---

## 🛠️ Tech Stack

### Backend

| Technology | Purpose |
|------------|---------|
| PHP 8.3 | Language |
| Symfony 7.2 | Framework |
| Doctrine ORM | Database abstraction |
| PostgreSQL 16 | Database |
| Redis 7 | Cache |

### Infrastructure

| Technology | Purpose |
|------------|---------|
| Docker | Containerization |
| GitHub Actions | CI/CD |
| JWT | Authentication |

### External APIs

| API | Purpose |
|-----|---------|
| [Open Food Facts](https://openfoodfacts.org) | Packaged food data |
| [USDA FoodData Central](https://fdc.nal.usda.gov) | Nutritional data |

---

## 📁 Project Structure

```
balancebite/
├── backend/               # Symfony application
│   ├── src/              # Source code (DDD structure)
│   ├── tests/            # PHPUnit tests
│   ├── config/           # Symfony configuration
│   └── migrations/       # Database migrations
│
├── docker/               # Docker configurations
│   ├── php/             # PHP Dockerfile (production)
│   ├── nginx/           # Nginx configuration
│   └── postgres/        # PostgreSQL init scripts
│
├── .github/workflows/    # CI/CD pipelines
├── docker-compose.yml    # Development services
└── Makefile             # Common commands
```

---

## 🧪 Testing

```bash
# Run all tests
make test

# Run only unit tests
make test-unit

# Run only integration tests
make test-integration

# Generate coverage report
make test-coverage
```

---

## 📊 Code Quality

```bash
# Check code style
make lint

# Fix code style
make fix

# Run static analysis
make stan

# Run all checks
make qa
```

---

## 📝 API Documentation

API documentation is available at `/api/doc` when running the development server.

---

## 🤝 Contributing

This is a portfolio project, but feedback and suggestions are welcome! Feel free to open an issue.

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👤 Author

**Emmeics** — Portfolio project for Senior Backend Developer position

---

<p align="center">
  Built with ❤️ using Symfony, DDD, and Clean Architecture
</p>
