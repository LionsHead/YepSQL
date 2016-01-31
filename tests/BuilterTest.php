<?php

use \YepSQL\Builder;
use \YepSQL\BuilderException;
use \PDO as PDO_instance;
use \PDOException as PDO_exception;

class BuilterTest extends \PHPUnit_Framework_TestCase {

    public function testParseTrueFile()
    {
        $file = __DIR__.'/true.sql';
        $builder = new Builder(new PDO_instance('sqlite::memory:'), $file);
        $this->assertInstanceOf('\YepSQL\Builder', $builder); #

        $this->assertContains('SQL REQUEST', $builder->getQuery('user_request')); #
        $this->assertContains('WHERE user_id = :user_id', $builder->getQuery('select_all')); #
        $this->assertContains('UPDATE REQUEST FROM TABLE', $builder->getQuery('update')); #
        $this->assertContains('FROM `work_test`', $builder->getQuery('work_test')); #
    }

    public function testInvalidQueryName()
    {

        $file = __DIR__.'/invalid_name.sql';
        $builder = new Builder(new PDO_instance('sqlite::memory:'));
        $this->assertInstanceOf('\YepSQL\Builder', $builder); #

        /* Exception */
        $this->setExpectedException('Exception'); #
        $builder->loadFromFile($file);

    }

    public function testInvalidQueryEmpty()
    {
        /* Exception */
        $this->setExpectedException('\YepSQL\BuilderException', 'Query "select_all" is empty!');

        $file = __DIR__.'/invalid_empty.sql';
        $builder = new Builder(new PDO_instance('sqlite::memory:'), $file);
    }

    public function testInvalidQueryUnknow()
    {
        $file = __DIR__.'/true.sql';
        $builder = new Builder(new PDO_instance('sqlite::memory:'), $file);
        $this->assertInstanceOf('\YepSQL\Builder', $builder); #

        /* Exception */
        $this->setExpectedException('\YepSQL\BuilderException', 'Query "query_unknow" does not exist'); #
        $builder->query_unknow();
    }

    public function testWork()
    {
        // work_test
        $pdo = new PDO_instance('sqlite::memory:', null, null, [\PDO::ATTR_ERRMODE  => \PDO::ERRMODE_EXCEPTION]);
        $pdo->query('CREATE TABLE `work_test` (
            `key` varchar(128) DEFAULT NULL,
            `value` varchar(256) DEFAULT NULL
        );');

        $pdo->query("INSERT INTO `work_test` (`key`, `value`) VALUES ('key1', 'val1');");
        $pdo->query("INSERT INTO `work_test` (`key`, `value`) VALUES ('key2', 'val2');");
        $pdo->query("INSERT INTO `work_test` (`key`, `value`) VALUES ('key3', 'val3');");

        $count = $pdo->query('SELECT count(*) FROM `work_test`;');
        $count = $count->fetch();
        $builder = new Builder($pdo);

        $this->assertInstanceOf('\YepSQL\Builder', $builder); #

        $queries = $builder->loadFromFile(__DIR__.'/true.sql');

        $this->assertEquals(5, count($queries)); #
        $this->assertEquals($count[0], $builder->work_test()->fetchColumn()); #
    }

    public function testWorkFail()
    {
        // work_test
        $pdo = new PDO_instance('sqlite::memory:', null, null, [\PDO::ATTR_ERRMODE  => \PDO::ERRMODE_EXCEPTION]);
        $builder = new Builder($pdo);
        $this->assertInstanceOf('\YepSQL\Builder', $builder); #

        $builder->loadFromFile(__DIR__.'/true.sql');
        /* Exception */
        $this->setExpectedException('PDOException'); #

        $builder->work_fail_test();
    }
}
