-- =============================================================================
-- INIZIALIZZAZIONE DATABASE - BalanceBite
-- =============================================================================
-- Questo script viene eseguito SOLO al primo avvio del container
-- quando il volume è vuoto. Se il DB esiste già, viene ignorato.

-- -----------------------------------------------------------------------------
-- ESTENSIONI POSTGRESQL
-- -----------------------------------------------------------------------------
-- uuid-ossp: genera UUID (useremo UUID v4 per gli ID delle entità)
-- Alternativa moderna: gen_random_uuid() in PostgreSQL 13+
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- pg_trgm: trigram matching per ricerca fuzzy
-- Utile per: "cerca 'chiken' e trova 'chicken'"
CREATE EXTENSION IF NOT EXISTS "pg_trgm";

-- -----------------------------------------------------------------------------
-- DATABASE DI TEST
-- -----------------------------------------------------------------------------
-- Creiamo un DB separato per i test (PHPUnit)
-- Così i test non sporcano i dati di sviluppo
CREATE DATABASE balancebite_test;

-- Diamo i permessi all'utente
GRANT ALL PRIVILEGES ON DATABASE balancebite_test TO balancebite;

-- -----------------------------------------------------------------------------
-- LOG
-- -----------------------------------------------------------------------------
\echo '✓ BalanceBite database initialized successfully!'
\echo '  - Extension uuid-ossp enabled'
\echo '  - Extension pg_trgm enabled'  
\echo '  - Test database created'
