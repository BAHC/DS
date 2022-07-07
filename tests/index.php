<style>
    .color-OK, .color-FAIL { padding: 10px; }
    .color-OK { background-color: lime; }
    .color-FAIL { background-color: red; }
</style>
<?php
include_once 'autoloader.php';

const DEBUG = 1 ? false: true;

$DS = new BAHC\DS\DS;

$DS::preserveCase(true);

echo '<h3>Put 1 '. $DS::norm('A/A->A') .'</h3>';
$_assertion = 'FAIL';
$_key = 'A/a->B';
$_value = 'AA_B';
$DS::put($_key, $_value);
$_res = $DS::get($_key);
if (@\assert('AA_B' == $_res)){
    $_assertion = 'OK';
}
if (DEBUG) { echo $_value, ' = ', $_res, '<br />'; }
echo '<p class="color-', $_assertion ,'">put() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Put Many ', $DS::norm('A/C->[a/c->[one, two, [0, 1, [rabbits, cows, whales]]]]'), '</h3>';

$DS::preserveCase(false);

$_assertion = 'FAIL';
$_values = [
        'a/c->one'=>1,
        'a/c->two'=>2,
        'a/c'=>[
            0=>null, 
            1=>'once', 
            'rabbits'=>[
                'rabbit'=>[
                    1=>'big',
                    2=>'huge',
                    3=>'enormous',
                ],
            ],
            'cows'=>[
                'cow'=>[
                    1=>'big',
                    2=>'huge',
                    'rabbit'=>'Bunny',
                ],
            ],
            'whales'=>[
                'whale'=>[
                    1=>'small',
                    2=>'little',
                    'cow'=>'Moo',
                ],
            ],
        ],
        
];
$DS::putMany($_values);
$_value1 = 'big';
$_value2 = 'Bunny';
$_string1 = 'a/c->rabbits->rabbit->1';
$_string2 = 'a/c->cows->cow->Rabbit';
$_res1 = $DS::get($_string1);
$_res2 = $DS::get($_string2);
if (@\assert($_value1 == $_res1) && 
        @\assert($_value2 == $DS::get($_string2))){
    $_assertion = 'OK';
}
if (DEBUG) {
    echo 'value1 = ', $_value1,'; '. $DS::norm($_string1) . ' = ', $_res1, '<br />';
    echo 'value2 = ', $_value2,'; '. $DS::norm($_string2). ' = ', $_res2, '<br />';
}
echo '<p class="color-', $_assertion ,'">many() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>One Tag</h3>';
$_assertion = 'FAIL';
$_value = 'Bunny';
$_tag = $DS::norm('a/c->cows->cow->Rabbit');
$_tags = 'rabbit';

$a_tags = $DS::tags($_tags);

if (@\assert($_value == $a_tags[ $_tag ])) {
    echo 'value = ', $_value,'; tag value = ', $a_tags[$_tag], '<br />';
    $_assertion = 'OK';
}
if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">tags() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Many Tags</h3>';

echo '<p style="padding: 6px; background-color: #EEE;">', 
    json_encode($DS::toArray($DS::tags(['Rabbit', 'Cow']))), '</p>';

/* --------------------------------------- */

echo '<h3>Increment A/D</h3>';
$_assertion = 'FAIL';
$_key = 'a/d';
$_value = 10;
$DS::put($_key, 1);
$DS::increment($_key, $_value);

$_res = $DS::get($_key);

if (@\assert(11 == $_res)){
    echo 'value = ', $_value,'; ', $_key, ' = ', $_res, '<br />';
    $_assertion = 'OK';
}
if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">increment() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Decrement A/E</h3>';
$_assertion = 'FAIL';
$_key = 'a/e';
$_value = 1000;
$DS::put($_key, 0);
$DS::decrement($_key, $_value); //key 'a/e' = -1000;

$_res = $DS::get($_key);

if (@\assert(-1000 == $_res)){
    echo 'value = ', $_value,'; ', $_key,' = ', $_res, '<br />';
    $_assertion = 'OK';
}
if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">decrement() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Forget Key</h3>';
$_assertion = 'FAIL';
$_value = null;
$_key = 'a/e';
$DS::put($_key, 'abc');
$DS::forget($_key);
$_res = $DS::get('a/e');
if (@\assert($_value === $_res)){
    echo 'value = ', $_value,'; ', $_key,' = ', $_res, '<br />';
    var_dump($DS::get('a/e'));
    $_assertion = 'OK';
}
if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">forget() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Forget Keys</h3>';

$_assertion = 'FAIL';
$_keys = ['a/e->1'=>'abc', 'a/e->2'=>'def', 'a/e->3'=>'ghi'];
$DS::putMany($_keys);
$DS::forget(['a/e->2', 'a/e->3', 'Santa']);
$_res = $DS::tags(['a/e->2', 'a/e->3']);

if (@\assert(empty($_res))){
    $_assertion = 'OK';
}

var_dump($_res);

if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">forget($keys) ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Flush</h3>';
$_assertion = 'FAIL';
$_value = count($DS::getAll());
$DS::flush();
$count_ds = count($DS::getAll());

if (@\assert(0 == $count_ds)){
    echo 'value = ', $_value, '; ', 'DS count = ', $count_ds, '<br />'; 
    $_assertion = 'OK';
}

if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">flush() ', $_assertion, '<br />';

/* NEXT, CURRENT, PREVIOUS --------------- */

$DS::flush();
$DS::putMany([
    'a/a' => 'AA',
    'a/b' => 'AB',
    'a/c' => 'AC',
    'a/c->a' => 'AC_A',
    'a/c->b' => 'AC_B',
    'a/c->c' => 'AC_C',
]);

$_ds = $DS::getAll();
$count_ds = count($_ds);

/* NEXT ---------------------------------- */

echo '<h3>Next</h3>';
$_assertion = 'FAIL';
$_value = 'AC_B';

$_index = 'a/c->a';
echo 'Set index: ', $_index, '<br />';
$DS::setIndex($_index);

$index_value = $DS::next();

if (@\assert($_value == $index_value)){
    $_assertion = 'OK';
    echo 'value = ', $_value, '; ', 
    'index_value = ', $DS::getIndex(), '<br />';
    
    $_res = [];
    for ($i=0; $i< $count_ds; $i++) {
        $_res[] = $DS::next();
    }
    echo '<p>', implode(', ', $_res), '<br />';
}
if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">next() ', $_assertion, '<br />';

$DS::reset();

/* --------------------------------------- */

echo '<h3>Current</h3>';

$_value = 'AA';
$_current = $DS::current();
echo '<p>', $_current;
echo ' : ', $DS::getIndex();

if (@\assert($_value === $_current)) {
    $_assertion = 'OK';
}

if (DEBUG) {
    echo 'value = ', $_value, '; ', 
    'current = ', $_current, '<br />';

    $_res = [];
    for ($i=0; $i<3; $i++) {
        $_res[] = $DS::next() .' : '. $DS::current();
    }
    echo '<p>', implode(', ', $_res);    
}
echo '<p class="color-', $_assertion ,'">current() ', $_assertion, '<br />';
/* --------------------------------------- */

echo '<h3>Previous</h3>';

$_index = 'a/b';
echo 'Set index: ', $_index, '<br />';
$DS::setIndex($_index);

if (DEBUG) {
    echo '<p>', $DS::current();
    echo ' : ', $DS::getIndex();
}

$_res = [];
for ($i=0; $i<3; $i++) {
    $_res[] = $DS::prev();
}
$_value = 'AA, AC_C, AC_B';
$_result = implode(', ', $_res);

if (@\assert($_value === $_result)) {
    $_assertion = 'OK';
}

if (DEBUG) {
    echo '<p>', $_value;
    echo '<p>', $_result;
}

echo '<p class="color-', $_assertion ,'">prev() ', $_assertion, '<br />';
/* --------------------------------------- */

echo '<h3>toArray</h3>';
$DS::flush();
$_set['value'] = 'ABC';
$_set['assertion'] = 'FAIL';
$_set['data'] = [
    'a' => 'A',
    'a->b' => 'AB',
    'a->b->c' => 'ABC',
    null => '0',
];

$DS::putMany($_set['data']);
$_set['to_array'] = $DS::toArray();
$_set['result'] = $_set['to_array']['a']['b']['c'];
if (@\assert($_set['value'] == $_set['result'])) {
    $_set['assertion'] = 'OK';
}

if (DEBUG) {
    echo '<p>value = ', $_set['value'], ' : result = ', $_set['result'];
    echo '<p>JSON: ', json_encode($_set['to_array']), '<br />';
}
echo '<p class="color-', $_set['assertion'] ,'">toArray() ', $_set['assertion'], '<br />';
unset($_set);

/*---------------------------------------------*/

echo '<h3>toJson</h3>';
$DS::flush();


$_value = '{"a":{"b":{"c":true,"d":false}},"e":{"f":{"g":"h","i":"j"}}}';
$DS::put('response', $_value);

$_assertion = 'FAIL';

$_data = '{
    "a->b->c":true,
    "a->b->d":false,
    "e->f->g":"h",
    "e->f->i":"j"
}';

$_data = json_decode($_data, true);
$DS::putMany($_data, 'json');

$_tgs = $DS::tags('json');
$_ds = $DS::toArray($_tgs);

$_rs = $_ds['json'];
$_result = $DS::toJson( $_rs );

if (@assert($_value === $_result)) {
    $_assertion = 'OK';
}

if (DEBUG) {
    echo '<p>value = ', $_value;
    echo '<p>result = ', $_result;
    
    var_dump($_tgs);
    var_dump($_rs);
}
echo '<p class="color-', $_assertion ,'">toJson() ', $_assertion, '<br />';

/*---------------------------------------------*/

echo '<h3>Sum and SumAll</h3>';

$DS::flush();
$DS::put('one->red', 1);
$DS::put('two->blue', 2);
$DS::put('three->green', 3);
$DS::put('four->blue->green', 4);
$DS::put('five-red', 5);

echo '<p>one + two + three = ', $DS::sum(['one', 'two', 'three']);
echo '<p>reds: ', $DS::sum('red');
echo '<p>greens: ', $DS::sum('green');
echo '<p>blues: ', $DS::sum('blue');
echo '<p>all: ', $DS::sumAll();

/*---------------------------------------------*/

echo '<h3>Slice & Dice</h3>';

$DS::flush();
$DS::sortKeys(false);
$DS::preserveCase(true);

$data = ['one'=>1, 'Two'=>2, 'three'=>3, 'Four'=>4, 'five'=>5,];
$DS::putMany($data, $key = 'data');
$res = $DS::slice($DS::getAll(), 2);

var_dump($res);