-- name: user-request
-- request invalid
SQL REQUEST;


-- name: select-all
-- request annotation 1
-- request annotation 2
SELECT *
FROM table
WHERE user_id = :user_id;
-- request annotation 1511411

 -- name: workTest
SELECT count(*) FROM `work_test`;






-- name: update
 -- request invalid
UPDATE REQUEST FROM TABLE;

-- name: work-test
SELECT count(*)
FROM `work_test`;

-- name: work-fail-test
SELECT count(*)
FROM `work_test_incorreasdksald`;
