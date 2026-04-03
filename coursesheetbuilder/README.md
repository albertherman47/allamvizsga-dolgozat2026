# CourseSheet Builder

Laravel-alapú tantervi adatlapkezelő rendszer Filament adminisztrációs felülettel.

## Telepítés

**Követelmények:** PHP 8.2+, Composer, Node.js/NPM

```bash
# 1. Függőségek telepítése és alap konfiguráció
composer install
cp .env.example .env
php artisan key:generate

# 2. Adatbázis létrehozása és feltöltése (SQLite by default)
php artisan migrate --seed

# 3. Frontend eszközök fordítása
npm install && npm run build
```

> Alternatívaként az összes fenti lépés egyben futtatható: `composer setup`

**Admin belépési adatok** (seeder után):
- URL: `/admin`
- Email: `admin@admin.com`
- Jelszó: `password`

## Fejlesztői mód indítása

```bash
composer dev
```

Ez párhuzamosan indítja a Laravel szervert, queue workert, log viewert és Vite dev szervert.
