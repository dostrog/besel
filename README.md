# Besel

Simple, lightweight console utility to work with imported data

_This is a test assigment project (B1)_ [Description on Google Docs](https://docs.google.com/document/d/1dC0PrfmIbhP3EtG-3gwdto5vrv83m1DAmjSiNOSCAaQ/edit?usp=sharing)

Used technologies:

- [PHP 7.4](https://www.php.net)
- [Laravel Zero - Micro-framework for console applications](https://laravel-zero.com)

------
## Installation

Project is written on 100% PHP and may be started from within project folder (`php besel`) or from PHAR archive as a standalone application.

- Copy PHAR archive to the localhost from [https://github.com/dostrog/besel/tree/develop/builds](https://github.com/dostrog/besel/tree/develop/builds)
- Create `.env` file in the same folder where PHAR archive is

Example of .env file
```shell
#DB_CONNECTION=sqlite
#DB_DATABASE="/Absolute/path/to/database.sqlite"

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=besel
DB_USERNAME=besel
DB_PASSWORD=besel
DB_SOCKET=/tmp/mysql.sock

DATA_URL="https://xxxxx/test/project"
DATA_CACHE_FILENAME="besel-data.dat"
FAKER_LOCALE="sl_SI"
```

### Demo prepare

- Import data. **NB! existing data may be overwritten!**
    ```shell
    $ php besel import
    ```
    It will populate test data in DB (according to `.env`). It is possible to populate data from cache with option `--fromCache`

```shell
$ php besel import

                              Import (with parse) data to DB

Use source for input: phar:///Users/dostrog/projects/preskok/besel/builds/besel/database/besel-data.dat
Received 355745 byte(s).
Normalize header (make snake_cased).
There are 8416 rows in received data (wo header). Parsing...

 8416/8416 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

Successfully parsed 8415 of 8416 rows (1 empty row(s)). See log for details.
Create (with clear) main table: `trades`
Create (with clear) table: `buyers`
Seeding `trades` by 8415 row(s). Chunked by 512 bytes.

 8415/8415 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

Seeding `buyers` by 55 row(s) (with fake Name, Surname)

 55/55 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

```

## Using

### Best selling model

To get best selling model per client run 

```shell
$ php besel best:model

                               Best selling model per customer

┌────┬───────┬──────────┬──────────┬───────────┬─────────────┐
│ #  │ Model │ Quantity │ Buyer ID │ Name      │ Surname     │
├────┼───────┼──────────┼──────────┼───────────┼─────────────┤
│ 1  │ 84    │ 705      │ 40       │ Hirthe    │ Jr.         │
│ 2  │ 84    │ 289      │ 37       │ Lorena    │ Koss        │
│ 3  │ 102   │ 272      │ 71       │ Daron     │ Orn         │
│ 4  │ 186   │ 158      │ 16       │ Donavon   │ Cruickshank │
│ 5  │ 84    │ 108      │ 108      │ Adam      │ Schaefer    │
│ 6  │ 84    │ 100      │ 374      │ Geovany   │ Wunsch      │
│ 7  │ 47    │ 80       │ 285      │ Gabrielle │ Baumbach    │
│ 8  │ 29    │ 72       │ 277      │ Wyman     │ I           │
│ 9  │ 84    │ 63       │ 11       │ Sheridan  │ Harber      │
... 
```
Or for specific buyer:

```shell
$ php besel best:model 40

 Best selling model per customer

┌───┬───────┬──────────┬──────────┬────────┬─────────┐
│ # │ Model │ Quantity │ Buyer ID │ Name   │ Surname │
├───┼───────┼──────────┼──────────┼────────┼─────────┤
│ 1 │ 84    │ 705      │ 40       │ Hirthe │ Jr.     │
└───┴───────┴──────────┴──────────┴────────┴─────────┘
```
### Best months for model

```shell
$ php besel best:months 84

                          Best 3 months for model 84

┌───────┬──────────┬───────┬──────┐
│ Model │ Quantity │ Month │ Year │
├───────┼──────────┼───────┼──────┤
│ 84    │ 237      │ 04    │ 2014 │
│ 84    │ 403      │ 07    │ 2014 │
│ 84    │ 282      │ 12    │ 2014 │
└───────┴──────────┴───────┴──────┘
```
Or for other period:

```shell
$ php besel best:months 84 6

                          Best 6 months for model 84

┌───────┬──────────┬───────┬──────┐
│ Model │ Quantity │ Month │ Year │
├───────┼──────────┼───────┼──────┤
│ 84    │ 127      │ 01    │ 2014 │
│ 84    │ 237      │ 04    │ 2014 │
│ 84    │ 403      │ 07    │ 2014 │
│ 84    │ 123      │ 10    │ 2014 │
│ 84    │ 100      │ 11    │ 2014 │
│ 84    │ 282      │ 12    │ 2014 │
└───────┴──────────┴───────┴──────┘
```
## Help

```shell
$ php besel

  Besel  1.0.0

  USAGE: besel <command> [options] [arguments]

  import           Import test data from provided URL or from cache (see .env).
  migrate          Run the database migrations

  best:model       Best selling model per client
  best:months      Command description
```
