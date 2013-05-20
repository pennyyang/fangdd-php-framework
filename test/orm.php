<?php

Model::config('mysql:host=localhost;dbname=lazyartest');
Model::config('username', 'root');
Model::config('password', '');

$tf = new Testify("ORM Test Suite");

$tf->beforeEach(function($tf){
});

$tf->test("Testing the config() method", function($tf){
    ORM::config(array(
        'logging' => true,
        'debug' => true,
    ));
});

$tf->test("Testing the findOne(id) method", function($tf){
    $personId = 1;
    $person = ORM::forTable('person')->findOne($personId);
    $tf->assertEqual($person->name, "bill");
});

$tf->test("Testing the where(k, v) method", function($tf){
    $persons = ORM::forTable('person')->where('name', 'bill')->findMany();
    $person = current($persons);
    $tf->assertEqual($person->name, "bill");
});

$tf->test("Testing the whereLike(k, v) method", function($tf){
    $persons = ORM::forTable('person')
        ->whereLike('name', '%ro%')
        ->whereLt('age', 20)
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
        ->selectExpr('gender')
        ->selectExpr('count(id) as c')
        ->groupBy('gender')
        ->findMany();
    $tf->assertEqual(count($genders), 2);
});

$tf->test("Testing the getLastSql() method", function($tf){
    ORM::config('logging', true);
    $personId = 1;
    $person = ORM::forTable('person')->findOne($personId);
    $tf->assertEqual(strlen(ORM::getLastSql()), 54);
});

$tf->run();