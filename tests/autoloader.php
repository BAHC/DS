<?php
function custom_autoloader($class) {
  include '../lib/' . $class . '.php';
}
 
spl_autoload_register('custom_autoloader');