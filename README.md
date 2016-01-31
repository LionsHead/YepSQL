# YepSQL ![ScreenShot](https://api.travis-ci.org/LionsHead/YepSQL.svg)
SQL templating helper for PHP inspired by yesql.

Requirements:
  php >= 5.4;
  pdo_extension;

## Usage
Create a file containing your SQL queries
### file example:
````sql
-- name: sql-query-name
-- query annotation
SELECT count(*)
FROM `work_test`

-- name: selectAll
-- request annotation 1
-- request annotation 2
SELECT *
FROM table
WHERE user_id = ?
````

Notice: "query-name" is converted to "query_name", php does not support this name methods.

### example:

````php
  $filepath = '/path/to/file.sql';
  $pdo_instance = new PDO_instance('sqlite::memory:');
  $user_id = 128;

  $builder = new \YepSQL\Builder($pdo_instance, $filepath);

  $user_data = $builder->selectAll($user_id)->fetch(PDO::FETCH_ASSOC);
````
