--TEST--
Bigint Lua 5.3
--SKIPIF--
<?php
if (!extension_loaded("lua")) print "skip lua extension missing";
if (\Lua::getVersionInt() < 503) print "skip works only with Lua >= 5.3";
if (PHP_INT_SIZE < 8) print "skip for x64 builds";
?>
--FILE--
<?php

(new lua)->eval("print('int: ', (1 << 62) + 1, '\\n')");
(new lua)->eval("print('str: ', tostring((1 << 62) + 1), '\\n')");
echo PHP_EOL;

(new lua)->eval("print('int: ', (1 << 62) - 10, '\\n')");
(new lua)->eval("print('str: ', tostring((1 << 62) - 10), '\\n')");
echo PHP_EOL;

(new lua)->eval("print('int: ', (1 << 62) + 10, '\\n')");
(new lua)->eval("print('str: ', tostring((1 << 62) + 10), '\\n')");
echo PHP_EOL;

?>
--EXPECT--
int: 4611686018427387905
str: 4611686018427387905

int: 4611686018427387894
str: 4611686018427387894

int: 4611686018427387914
str: 4611686018427387914