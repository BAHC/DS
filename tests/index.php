<style>
    .color-OK, .color-FAIL { padding: 10px; }
    .color-OK { background-color: lime; }
    .color-FAIL { background-color: red; }
</style>
<?php
include_once 'autoloader.php';
//const DEBUG = true;
const DEBUG = false;

echo '<h3>Put 1 '. BAHC\DS::norm('A/A->A') .'</h3>';
$_assertion = 'FAIL';
$_key = 'a/a->a';
$_value = 'AA_A';
BAHC\DS::put($_key, $_value);
$_res = BAHC\DS::get($_key);
if (@\assert('AA_A' == $_res)){
    $_assertion = 'OK';
}
if (DEBUG) { echo $_value, ' = ', $_res, '<br />'; }
echo '<p class="color-', $_assertion ,'">put() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Get A/B</h3>';
$_assertion = 'FAIL';
$_key = 'a/b';
$_value = 123;
BAHC\DS::put($_key, $_value);
$_res = BAHC\DS::get($_key);
if (@\assert($_value == $_res)){
    $_assertion = 'OK';
}
if (DEBUG) { echo '<p>value = ', $_value,'; ', $_key, ' = ', $_res, '<br />'; }
echo '<p class="color-', $_assertion ,'">get() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Put Many A/C</h3>';
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
BAHC\DS::putMany($_values);
$_value1 = 'big';
$_value2 = 'Bunny';
$_string1 = 'a/c->rabbits->rabbit->1';
$_string2 = 'a/c->cows->cow->Rabbit';
$_res1 = BAHC\DS::get($_string1);
$_res2 = BAHC\DS::get($_string2);
if (@\assert($_value1 == $_res1) && 
        @\assert($_value2 == BAHC\DS::get($_string2))){
    $_assertion = 'OK';
}
if (DEBUG) {
    echo 'value1 = ', $_value1,'; '. BAHC\DS::norm($_string1) . ' = ', $_res1, '<br />';
    echo 'value2 = ', $_value2,'; '. BAHC\DS::norm($_string2). ' = ', $_res2, '<br />';
}
echo '<p class="color-', $_assertion ,'">many() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>One Tag</h3>';
$_assertion = 'FAIL';
$_value = 'Bunny';
$_tag = BAHC\DS::norm('a/c->cows->cow->Rabbit');
$_tags = 'rabbit';

$a_tags = BAHC\DS::tags($_tags);

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
    json_encode(BAHC\DS::toArray(BAHC\DS::tags(['Rabbit', 'Cow']))), '</p>';

/* --------------------------------------- */

echo '<h3>Increment A/D</h3>';
$_assertion = 'FAIL';
$_key = 'a/d';
$_value = 10;
BAHC\DS::put($_key, 1);
BAHC\DS::increment($_key, $_value);

$_res = BAHC\DS::get($_key);

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
BAHC\DS::put($_key, 0);
BAHC\DS::decrement($_key, $_value); //key 'a/e' = -1000;

$_res = BAHC\DS::get($_key);

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
BAHC\DS::put($_key, 'abc');
BAHC\DS::forget($_key);
$_res = BAHC\DS::get('a/e');
if (@\assert($_value === $_res)){
    echo 'value = ', $_value,'; ', $_key,' = ', $_res, '<br />';
    var_dump(BAHC\DS::get('a/e'));
    $_assertion = 'OK';
}
if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">forget() ', $_assertion, '<br />';

/* --------------------------------------- */

echo '<h3>Forget Keys</h3>';

$_assertion = 'FAIL';
$_keys = ['a/e->1'=>'abc', 'a/e->2'=>'def', 'a/e->3'=>'ghi'];
BAHC\DS::putMany($_keys);
BAHC\DS::forget(['a/e->2', 'a/e->3', 'Santa']);
$_res = BAHC\DS::tags(['a/e->2', 'a/e->3']);

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
$_value = count(BAHC\DS::getAll());
BAHC\DS::flush();
$count_ds = count(BAHC\DS::getAll());

if (@\assert(0 == $count_ds)){
    echo 'value = ', $_value, '; ', 'DS count = ', $count_ds, '<br />'; 
    $_assertion = 'OK';
}

if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">flush() ', $_assertion, '<br />';

/* NEXT, CURRENT, PREVIOUS --------------- */

BAHC\DS::flush();
BAHC\DS::putMany([
    'a/a' => 'AA',
    'a/b' => 'AB',
    'a/c' => 'AC',
    'a/c->a' => 'AC_A',
    'a/c->b' => 'AC_B',
    'a/c->c' => 'AC_C',
]);

$ds = BAHC\DS::getAll();
$count_ds = count($ds);

/* NEXT ---------------------------------- */

echo '<h3>Next</h3>';
$_assertion = 'FAIL';
$_value = 'AC_B';

$_index = 'a/c->a';
echo 'Set index: ', $_index, '<br />';
BAHC\DS::setIndex($_index);

$index_value = BAHC\DS::next();

if (@\assert($_value == $index_value)){
    $_assertion = 'OK';
    echo 'value = ', $_value, '; ', 
    'index_value = ', BAHC\DS::getIndex(), '<br />';
    
    $_res = [];
    for ($i=0; $i< $count_ds; $i++) {
        $_res[] = BAHC\DS::next();
    }
    echo '<p>', implode(', ', $_res), '<br />';
}
if (DEBUG) {
    
}
echo '<p class="color-', $_assertion ,'">next() ', $_assertion, '<br />';

BAHC\DS::reset();

/* --------------------------------------- */

echo '<h3>Current</h3>';

$_value = 'AA';
$_current = BAHC\DS::current();
echo '<p>', $_current;
echo ' : ', BAHC\DS::getIndex();

if (@\assert($_value === $_current)) {
    $_assertion = 'OK';
}

if (DEBUG) {
    echo 'value = ', $_value, '; ', 
    'current = ', $_current, '<br />';

    $_res = [];
    for ($i=0; $i<3; $i++) {
        $_res[] = BAHC\DS::next() .' : '. BAHC\DS::current();
    }
    echo '<p>', implode(', ', $_res);    
}
echo '<p class="color-', $_assertion ,'">current() ', $_assertion, '<br />';
/* --------------------------------------- */

echo '<h3>Previous</h3>';

$_index = 'a/b';
echo 'Set index: ', $_index, '<br />';
BAHC\DS::setIndex($_index);

if (DEBUG) {
    echo '<p>', BAHC\DS::current();
    echo ' : ', BAHC\DS::getIndex();
}

$_res = [];
for ($i=0; $i<3; $i++) {
    $_res[] = BAHC\DS::prev();
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
function toArray() {
    echo '<h3>toArray</h3>';
    BAHC\DS::flush();
    $_value = 'ABC';
    $_assertion = 'FAIL';
    $_data = [
        'a' => 'A',
        'a->b' => 'AB',
        'a->b->c' => 'ABC',
        null => '0',
    
    ];

    BAHC\DS::putMany($_data);
    $_array = BAHC\DS::toArray();
    $_result = $_array['a']['b']['c'];
    if (@\assert($_value == $_result)) {
        $_assertion = 'OK';
    }

    if (DEBUG) {
        echo '<p>value = ', $_value, ' : result = ', $_result;
        echo '<p>JSON: ', json_encode($_array), '<br />';
    }
    echo '<p class="color-', $_assertion ,'">toArray() ', $_assertion, '<br />';
}

toArray();
/*---------------------------------------------*/

echo '<h3>toJson</h3>';
BAHC\DS::flush();


$_value = '{"a":{"b":{"c":true,"d":false}},"e":{"f":{"g":"h","i":"j"}}}';
BAHC\DS::put('response', $_value);

$_assertion = 'FAIL';

$_data = '{
    "a->b->c":true,
    "a->b->d":false,
    "e->f->g":"h",
    "e->f->i":"j"
}';

$_data = json_decode($_data, true);
BAHC\DS::putMany($_data, 'json');

$_tgs = BAHC\DS::tags('json');
$_ds = BAHC\DS::toArray($_tgs);

$_rs = $_ds['json'];
$_result = BAHC\DS::toJson( $_rs );

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
