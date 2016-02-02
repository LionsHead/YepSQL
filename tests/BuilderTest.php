<?php

use \YepSQL\Builder;
use \YepSQL\BuilderException;
use \PDO as PDO_instance;
use \PDOException as PDO_exception;

class BuilderTest extends \PHPUnit_Framework_TestCase {

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

}
