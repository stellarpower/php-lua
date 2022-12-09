--TEST--
Bug #73964 (Segmentation fault (11))
--SKIPIF--
<?php
if (!extension_loaded("lua")) print "skip lua extension missing";
?>
--FILE--
<?php

class LuaTest
{
	function __construct()
	{ 
		$this->lua = new Lua();
		$this->lua->registerCallback("log", array($this, "API_LuaLog")); // creates a cycle reference
	}


	function API_LuaLog( $entry)
	{
		echo("from lua: $entry");
	}   

	public function RunTest()
	{
		$this->lua->eval(<<<CODE
function TestFunc(str)
	 log(str)

end

CODE
	);               
		$GamePackage = $this->lua->RootTable;

		$this->lua->call("TestFunc", array("okey"));
	}
}


$a = new LuaTest();
$a->runtest();

/*
It looks like there is no a memleak but there is a cycled reference: Lua is holding a callback [$this, 'API_LuaLog']
which in turn hols the Lua object itself.

We need to make sure this loop is properly collected before PHP terminates, otherwise we'll get
a memleak report if PHP is built with --enable-debug.

It looks like one call of `gc_collect_cycles` isn't enough (i'm guessing it runs only one step of the mark-sweep at one call)
*/
unset($a);
for ($i = 0; $i < 10000; ++ $i) {
	gc_collect_cycles();
}

?>
--EXPECT--
from lua: okey
