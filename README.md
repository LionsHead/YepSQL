# YepSQL [![Build Status](https://travis-ci.org/LionsHead/YepSQL.svg?branch=master)](https://travis-ci.org/LionsHead/YepSQL)
SQL templating helper for PHP inspired by [yesql](https://github.com/krisajenkins/yesql).

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

  $builder = new \YepSQL\Builder(
    new PDO_instance('sqlite::memory:'), // instance of PDO
    '/path/to/file.sql' // file with queries
  );

  $user_id = 128; // request arguments
  $user_data = $builder->selectAll($user_id)->fetch(PDO::FETCH_ASSOC);
````
