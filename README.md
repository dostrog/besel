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

### Add record

Use command as described in help:

```shell
$ php besel help add
Description:
  Add record to database

Usage:
  add [options] [--] <VehicleId> <InhouseSellerId> <BuyerId> <ModelId> <SaleDate> <BuyDate>

Arguments:
  VehicleId             Vehicle Id (integer)
  InhouseSellerId       Seller Id (integer)
  BuyerId               Buyer Id (integer)
  ModelId               Model Id (integer)
  SaleDate              Sale Date (YYYY-MM-DD)
  BuyDate               Buy Date (YYYY-MM-DD)
```
Example:

```shell
$ php besel add 324 2124 3123 4123 2020-11-11 2020-11-01


                            Add records to test database


Adding record to trades with Id 324:  added with ID 8416
Adding record to buyers with Id 3123 : done (Blanka Klemenčič).
```
### Test inserting (for the presence of a record in the DBMS after insert)

For the last inserting record, use:

```shell
$ php besel test

                                Check for record in Trades table

Last inserted record:

┌──────┬────────────┬───────────┬──────────┬──────────┬─────────────────────┬─────────────────────┐
│ ID   │ Vehicle ID │ Seller ID │ Buyer ID │ Model ID │ Sale Date           │ Buy Date            │
├──────┼────────────┼───────────┼──────────┼──────────┼─────────────────────┼─────────────────────┤
│ 8416 │ 324        │ 2124      │ 3123     │ 4123     │ 2020-11-11 00:00:00 │ 2020-11-01 00:00:00 │
└──────┴────────────┴───────────┴──────────┴──────────┴─────────────────────┴─────────────────────┘
```
Or by record (primary) ID:

```shell
$ php besel test --id=1

                                Check for record in Trades table

There is record(s) with parameters. 1 latest :

┌────┬────────────┬───────────┬──────────┬──────────┬─────────────────────┬─────────────────────┐
│ ID │ Vehicle ID │ Seller ID │ Buyer ID │ Model ID │ Sale Date           │ Buy Date            │
├────┼────────────┼───────────┼──────────┼──────────┼─────────────────────┼─────────────────────┤
│ 1  │ 306687     │ 82        │ 245      │ 84       │ 2014-01-24 00:00:00 │ 2013-05-16 00:00:00 │
└────┴────────────┴───────────┴──────────┴──────────┴─────────────────────┴─────────────────────┘
```

Or by any of title, ie seller id:

```shell
$ php besel test --seller=82

                                Check for record in Trades table

There is record(s) with parameters. 5 latest :

┌────┬────────────┬───────────┬──────────┬──────────┬─────────────────────┬─────────────────────┐
│ ID │ Vehicle ID │ Seller ID │ Buyer ID │ Model ID │ Sale Date           │ Buy Date            │
├────┼────────────┼───────────┼──────────┼──────────┼─────────────────────┼─────────────────────┤
│ 1  │ 306687     │ 82        │ 245      │ 84       │ 2014-01-24 00:00:00 │ 2013-05-16 00:00:00 │
│ 2  │ 306689     │ 82        │ 245      │ 84       │ 2014-01-24 00:00:00 │ 2013-05-16 00:00:00 │
│ 72 │ 312306     │ 82        │ 40       │ 8        │ 2014-01-07 00:00:00 │ 2013-10-22 00:00:00 │
│ 75 │ 312321     │ 82        │ 40       │ 8        │ 2014-01-07 00:00:00 │ 2013-10-22 00:00:00 │
│ 76 │ 312322     │ 82        │ 40       │ 8        │ 2014-01-07 00:00:00 │ 2013-10-22 00:00:00 │
└────┴────────────┴───────────┴──────────┴──────────┴─────────────────────┴─────────────────────┘
```

Correct inserting (and some other features) of scripts tested by PhpUnit:

```shell
$ phpunit --verbose --coverage-text
PHPUnit 9.5.2 by Sebastian Bergmann and contributors.

Runtime:       PHP 7.4.15 with PCOV 1.0.6
Configuration: /Users/dostrog/projects/preskok/besel/phpunit.xml

..........................                                        26 / 26 (100%)

Time: 00:07.300, Memory: 50.00 MB

OK (26 tests, 65 assertions)


Code Coverage Report:
  2021-03-03 21:34:40

 Summary:
  Classes: 46.15% (6/13)
  Methods: 78.95% (30/38)
  Lines:   92.31% (312/338)

App\Commands\AddCommand
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% (  8/  8)
App\Commands\BestModelCommand
  Methods:   0.00% ( 0/ 1)   Lines:  75.00% (  6/  8)
App\Commands\BestMonthsCommand
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% ( 10/ 10)
App\Commands\EnsureEnvironment
  Methods:   0.00% ( 0/ 1)   Lines:  33.33% (  5/ 15)
App\Commands\ImportCommand
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% ( 11/ 11)
App\Commands\TestCommand
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% (  9/  9)
App\Providers\AppServiceProvider
  Methods:  50.00% ( 1/ 2)   Lines:  80.00% (  4/  5)
App\Services\BestModel
  Methods:  50.00% ( 1/ 2)   Lines:  90.00% (  9/ 10)
App\Services\BestMonths
  Methods:  33.33% ( 1/ 3)   Lines:  46.15% (  6/ 13)
App\Services\Importer
  Methods:  87.50% ( 7/ 8)   Lines:  96.15% ( 75/ 78)
App\Services\Recorder
  Methods: 100.00% ( 4/ 4)   Lines: 100.00% ( 43/ 43)
App\Services\Seeder
  Methods:  85.71% ( 6/ 7)   Lines:  97.10% ( 67/ 69)
App\Services\Tester
  Methods: 100.00% ( 6/ 6)   Lines: 100.00% ( 59/ 59)
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
