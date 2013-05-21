<?php

ORM::config('mysql:host=localhost;dbname=test');
ORM::config('username', 'root');
ORM::config('password', '');


$tf = new Testify("ORM Test Suite");

$tf->beforeEach(function($tf){
});

$tf->test("Testing the config() method", function($tf){
    ORM::config(array(
        'logging' => true,
        'debug' => true,
    ));
});

$tf->test("Testing the query() method", function($tf){
    foreach (explode(';', file_get_contents(TEST_ROOT.'orm.sql')) as $sql) {
        if (trim($sql)) {
            $stmt = ORM::forTable('x')->query(trim($sql));
        }
    }
});

$tf->test("Testing the find(id) method", function($tf){
    $personId = 1;
    $person = ORM::forTable('person')->find($personId);
    $tf->assertEqual($person->name, "bill");
});

$tf->test("Testing the where(k, v) method", function($tf){
    $persons = ORM::forTable('person')->where('name', 'bill')->findMany();
    $person = current($persons);
    $tf->assertEqual($person->name, "bill");
});

$tf->test("Testing the where(k, 'LIKE', v) method", function($tf){
    $persons = ORM::forTable('person')
        ->where('name', 'LIKE', '%ro%')
        ->where('age', '<', 20)
        ->findMany();
    $person = current($persons);
    $tf->assertEqual($person->age, 18);
});

$tf->test("Testing the findMany() method", function($tf){
    $persons = ORM::forTable('person')->where('name', 'rose')->findMany();
    $tf->assertEqual(count($persons), 2);
});

$tf->test("Testing the findArray() method", function($tf){
    $persons = ORM::forTable('person')->where('name', 'bill')->findArray();
    $person = current($persons);
    $tf->assertEqual(is_array($person), true);
});

$tf->test("Testing the count() method", function($tf){
    $n = ORM::forTable('person')->where('name', 'bill')->count();
    $tf->assertEqual($n, 1);
});

$tf->test("Testing the limit() method", function($tf){
    $persons = ORM::forTable('person')->where('name', 'rose')->limit(1)->findMany();
    $tf->assertEqual(count($persons), 1);
});

$tf->test("Testing the orderByDESC() method", function($tf){
    $persons = ORM::forTable('person')->where('name', 'rose')->orderByDESC('age')->findMany();
    $person = current($persons);
    $tf->assertEqual($person->age, 24);
});

$tf->test("Testing the groupBy() method", function($tf){
    $genders = ORM::forTable('person')
        ->column('gender')
        ->column(new Expression('count(id) AS c'))
        ->groupBy('gender')
        ->findMany();
    $tf->assertEqual(count($genders), 2);
});

$tf->test("Testing the getLastSql() method", function($tf){
    ORM::config('logging', true);
    $personId = 1;
    $person = ORM::forTable('person')->find($personId);
    $tf->assertEqual(ORM::getLastSql(), "SELECT * FROM `person` WHERE `id` = '1' LIMIT 1 OFFSET 0");
});

$tf->run();