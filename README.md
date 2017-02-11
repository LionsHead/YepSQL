# YepSQL [![Build Status](https://travis-ci.org/LionsHead/YepSQL.svg?branch=master)](https://travis-ci.org/LionsHead/YepSQL)
SQL templating helper for PHP inspired by [yesql](https://github.com/krisajenkins/yesql).

Requirements:
  php >= 5.6;
  pdo_extension;

Install:
  composer require yepsql/yepsql

## Usage
Create a file containing your SQL queries
### file example:
````sql
-- name: sqlQueryName
-- query annotation
SELECT count(*) FROM `table`;

-- name: getUsersInfo
-- request annotation 1 ...
-- request annotation 2 ...
SELECT *
FROM `table`
WHERE `user_id` = ? ;

-- name: updateUserName
UPDATE `table`
SET `user_name` = :user_name
WHERE `user_id` = :user_id ;
````

And call them in your code.
Notice: "query-name" is converted to "query_name", php does not support this name methods.

### example:

````php

  $sql_template = new \YepSQL\Builder(
    new PDO_instance('sqlite::memory:'),    // you instance of PDO
    '/path/to/file.sql'                     // file with queries
  );


  // prepare SELECT * FROM `table` WHERE `user_id` = ? ;
  // and send query "getUsersInfo" = SELECT * FROM `table`  WHERE `user_id` = 128;
  $user_id = 128; // request arguments
  $stmt = $sql_template->getUsersInfo($user_id);
  // returned PDOStatement instance
  $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

  // send query "updateUserName" = UPDATE `table` SET `user_name` = 'NewUSerName' WHERE `user_id` = '128';
  $sql_template->updateUserName([
     ':user_name' => 'NewUSerName',
     ':user_id' => 128
  ]);

````

Enjoy.
