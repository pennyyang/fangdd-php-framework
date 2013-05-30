<?php

require_once APP_ROOT.'common/Model.php';

ORM::config('mysql:host=localhost;dbname=test');
ORM::config('username', 'root');
ORM::config('password', '');

$tf = new Testify("Model, Logic and Controller Test Suite");

$tf->before(function($tf){
    ORM::config(array(
        'logging' => true,
        'debug' => true,
    ));
    foreach (explode(';', file_get_contents(TEST_ROOT.'orm.sql')) as $sql) {
        if (trim($sql)) {
            $stmt = ORM::forTable()->query(trim($sql))->execute();
        }
    }
});

class PersonModel extends Model
{
    public static $table = 'person';
    public static $pkey = 'id';
    public static $fields = array(
        'name' => false
    );
}
$tf->test("Testing the get(id) method", function($tf){
    $personId = 1;
    $p = new PersonModel();
    $person = $p->get($personId);
    $tf->assertEqual($person->name, "bill");
});

$tf->test("Testing the edit(id, data) method", function($tf){
    $personId = 1;
    $p = new PersonModel();
    $p->edit($personId, array('name' => 'jack'));
    $person = $p->get($personId);
    $tf->assertEqual($person->name, "jack");
});

$tf->test("Testing the add(data) method", function($tf){
    $p = new PersonModel();
    $personId = $p->add(array('name' => 'shit'));
    $person = $p->get($personId);
    $tf->assertEqual($person->name, "shit");
});

$tf->test("Testing the delete(id) method", function($tf){
    $personId = 1;
    $p = new PersonModel();
    $p->delete($personId);
    $person = $p->get($personId);
    $tf->assertEqual($person, false);
});

$tf->run();