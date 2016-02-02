<?php

use \YepSQL\Builder;
use \YepSQL\BuilderException;
use \PDO as PDO_instance;
use \PDOException as PDO_exception;

class BuilderWorkTest extends \PHPUnit_Framework_TestCase {

    public function testWork()
    {
        // work_test
        $pdo = new PDO_instance('sqlite::memory:', null, null, [\PDO::ATTR_ERRMODE  => \PDO::ERRMODE_EXCEPTION]);
        $pdo->query('CREATE TABLE IF NOT EXISTS  `work_test` (
            `key` varchar(128) DEFAULT NULL,
            `value` varchar(256) DEFAULT NULL
        );');
        $pdo->query(" DELETE FROM `work_test`;"); // clear table data

        $pdo->query("INSERT INTO `work_test` (`key`, `value`) VALUES ('key1', 'val1');");
        $pdo->query("INSERT INTO `work_test` (`key`, `value`) VALUES ('key2', 'val2');");
        $pdo->query("INSERT INTO `work_test` (`key`, `value`) VALUES ('key3', 'val3');");

        $count = $pdo->query('SELECT count(*) FROM `work_test`;')->fetch();
        $count = $count[0];
        $builder = new Builder($pdo);

        $this->assertInstanceOf('\YepSQL\Builder', $builder); #

        $queries = $builder->loadFromFile(__DIR__.'/true.sql');

        $this->assertEquals(6, count($queries)); #
        $this->assertEquals($count, $builder->workTest()->fetchColumn()); #
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

    public function testLongWork()
    {
        // work_test
        $pdo = new PDO_instance('sqlite::memory:', null, null, [\PDO::ATTR_ERRMODE  => \PDO::ERRMODE_EXCEPTION]);
        $pdo->query('CREATE TABLE IF NOT EXISTS `work_test` (
            `key` varchar(128) DEFAULT NULL,
            `value` varchar(256) DEFAULT NULL
        );');
        $pdo->query(" DELETE FROM `work_test`;"); // clear table data

        for ($i = 1; $i <= 100; $i++) {
            $pdo->query("INSERT INTO `work_test` (`key`, `value`) VALUES ('key". $i ."', '". mt_rand(0, 50) ."');");
        }

        $builder = new Builder($pdo);

        $this->assertInstanceOf('\YepSQL\Builder', $builder); #

        $this->assertEquals(3230, count( $builder->loadFromFile(__DIR__.'/true_long.sql') ));

        $count = $pdo->query('SELECT count(*) FROM `work_test` WHERE `value` > 32;')->fetch();
        $count = $count[0];

        $re = $builder->workLongMore([':more' => 32])
        ->fetchColumn();
        print_r($re);
        $this->assertEquals($count, $re); #

        $count = $pdo->query('SELECT count(*) FROM `work_test` WHERE `value` < 32;')->fetch();
        $count = $count[0];
        $re = $builder->workLongLess([':less' => 32])->fetchColumn();
        print_r($re);
        $this->assertEquals($count, $re); #

    }
}
