# Caritate API – Laravel backend pentru platformă de donații

Acesta este un API REST construit cu Laravel pentru o platformă de donații și abonamente recurente.  
Permite autentificare cu OTP, donații unice și recurente, gestionare cauze, rapoarte financiare și interfață de administrare.

---

## Cerințe

- PHP >= 8.1
- Composer
- MySQL sau MariaDB
- Laravel 12
- Node.js (opțional, doar pentru vite/asset build)

---

## Instalare locală

1. Clonează repository-ul:
```bash
git clone https://github.com/nicolae-zen/caritate-api.git
cd caritate-api
```

2. Instaleaza dependente:
```bash
composer install
```

4. Copiaza fisierul .env.example
```bash
cp .env.example .env
````

6. Genereaza cheia aplicatiei și cheia JWT
```bash
php artisan key:generate
php artisan jwt:secret
````

7. Configureaza conexiunea la baza de date in .env
```bash
DB_DATABASE=caritate_db
DB_USERNAME=root
DB_PASSWORD=
````

8. Ruleaza migratiile
```bash
php artisan migrate
````

## Pornire server local
```bash
php artisan serve
````

## Documentatie API (Scribe)
http://127.0.0.1:8000/docs


## Autentificare
Autentificarea se face pe baza de OTP (cod unic trimis pe telefon)
