# my-data-api

A quick API to serve a simple data set from SQLite DB.

I've created few tools and scripts here for quick API access to a data set. These are great for application testing and data analysis.

All data are stored in `crud.db` file. You may make a copy from `crud_template.db` to get started.

Project Home: https://github.com/zemian/my-data-api

## How To Run

```
cd <project>
php -S localhost:3000
open http://localhost:3000
```

## Available API Endpoints

* http://localhost:3000/index.php - Just give a status that this app is working.
* http://localhost:3000/crud.php - A PHP CRUD API powered by [`api.include.php`](https://github.com/mevdschee/php-crud-api). See `crud.php` source for usage examples.
* http://localhost:3000/crud-sql.php - A simple UI to manipulate `crud.db` DB with raw SQL.
* http://localhost:3000/crud-query.php?sql=<input> - A API that runs a raw SQL query to give you back JSON data.
* http://localhost:3000/crud-create-locales.php - A API that populate locales data set in `crud.db`.
* http://localhost:3000/locales.php - A standalone API to give you list of Locales. NOTE: These data are not from `crud.db` file! See `locales.php` source comment for example usages.
* http://localhost:3000/data.php?file=<input> - A API to fetch static files from a data directory.
