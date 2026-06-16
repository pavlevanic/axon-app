# Axon - Web Shop Platforma za Računarske Komponente

Axon je veb aplikacija za elektronsku trgovinu (e-commerce) specijalizovana za katalogizaciju, pretragu i onlajn prodaju računarskog hardvera i prateće opreme. Projekat je razvijen kao seminarski rad na Fakultetu prirodno-matematičkih nauka (PMF), smer Informatika.

Sistem pokriva celokupan proces jedne moderne internet prodavnice – od naprednog upravljanja proizvodima i CMS sadržajem kroz administratorski panel, do korisničkog dela sa korpom, narudžbenicama i modulom za konfigurisanje računara.

---

## Glavne Funkcionalnosti

### 1. E-Commerce i Korisnički Modul
* Katalog hardvera: Detaljan pregled, filtriranje i pretraga artikala po kategorijama, brendovima i specifikacijama.
* Autentifikacija i autorizacija: Kastomizovan sistem registracije i prijave korisnika, razvijen ručno (bez korišćenja gotovih starter kitova), sa definisanim ulogama za klijente i administratore.
* Sistem korpe: Dinamičko dodavanje, izmena i brisanje proizvoda u korpi pre završetka kupovine.
* Procesiranje porudžbina: Kreiranje narudžbenica sa istorijom kupovine za registrovane korisnike.

### 2. Modul za Sklapanje Konfiguracija (PC Builder)
* Interaktivni konfigurator: Vođeni proces selekcije ključnih komponenti računara u realnom vremenu.
* Validacija kompatibilnosti: Logika koja na osnovu izabranih hardverskih parametara (poput ležišta procesora, tipa radne memorije i form faktora) filtrira i prikazuje isključivo kompatibilne komponente.
* Kalkulator potrošnje: Automatsko računanje ukupne snage (TDP) i provera adekvatnosti izabranog napajanja.

### 3. Napredni Administratorski Panel (CMS)
* Upravljanje proizvodima: Kompletan CRUD sistem za administraciju artikala, zaliha i tehničkih specifikacija hardvera.
* Upravljanje marketing sadržajem: Modul za kreiranje i izmenu vesti, akcija i promotivnih banera na početnoj stranici.
* Dinamički Hero Carousel: Opcija postavljanja važnih vesti ili banera u glavnu sekciju (slider) uz mogućnost preusmeravanja korisnika na prilagođene URL rute.
* Kontrola estetike banera: Mogućnost definisanja svetle ili tamne pozadine učitanog banera kroz formu, što sistem koristi za automatsko prilagođavanje kontrasta teksta i dugmadi radi optimalne čitljivosti.

---

## Tehnološki Stek

* Backend: Laravel 13.x (PHP 8.4)
* Baza podataka: MySQL
* Frontend: Blade templating engine, Bootstrap 5.x, JavaScript (Vanilla ES6)
* Razvojno okruženje: Laragon / Windows

---

## Struktura Baze Podataka

Baza podataka je projektovana u trećoj normalnoj formi (3NF) sa optimizovanim relacijama za potrebe e-commerce platforme.

### Ključni Modeli:
* User - Upravljanje korisnicima, sesijama i administratorskim pravima.
* Product - Centralni entitet za sve artikle, cene, opise i stanja na zalihama.
* ComponentSpecification - Tabela sa specifičnim hardverskim atributima koji se koriste za filtriranje i validaciju u konfiguratoru.
* News - Model za upravljanje dinamičkim banerima i vestima (custom_url, dark_image).

---

## Instalacija i Pokretanje u Lokalnom Okruženju

1. Klonirajte repozitorijum sa GitHub-a:
   git clone https://github.com/pavlevanic/axon-app

2. Instalirajte zavisnosti (Composer):
   composer install
   npm install

3. Kreirajte konfiguracioni fajl:
   Kopirajte .env.example u .env i podesite parametre Vaše lokalne MySQL baze podataka (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
   cp .env.example .env

4. Generišite ključ aplikacije:
   php artisan key:generate

5. Pokrenite migracije i seed-ere:
   php artisan migrate --seed

6. Kreirajte simbolički link za skladište fajlova:
   Neophodno za ispravno učitavanje i prikaz slika proizvoda i banera iz admin panela.
   php artisan storage:link