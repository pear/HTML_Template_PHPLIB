<?php

include_once 'HTML/Template/PHPLIB.php';
include_once 'Benchmark/Timer.php';

$articles = array(
    "ISBN345624345" => "Matrix 2",
    "ISBN45df32342" => "Roger Rabbit",
    "ISBN305632232" => "foo bar",
    "ISBN674589123" => "Joe user's adventures"
);

class myT extends HTML_Template_PHPLIB {
    var $file_fallbacks = array("/usr/local/bla","./");
}

$t =& new myT("/usr/local", "keep");

$timer =& new Benchmark_Timer();
$timer->start();

$t->setFile(array(
    "main" => "tpl1.ihtml",
    "block" => "block.ihtml"
));

$t->setBlock("block","articlerow","ar");

$t->setVar(array(
    "TITLE" => "This is a block integrated into another template",
    "BGCOLOR" => "#cccccc",
    "BLOCKER" => "block"
));

foreach ($articles as $isbn => $name) {
    $t->setVar(array(
        "ID" => $isbn,
        "ANAME" => $name
    ));
    /**
    * easy to use rule of thumb: first parameter is the last
    * from setBlock, second is the second from setBlock, third
    * is TRUE in order to append the parsed data to the template var
    * articlerow
    */
    $t->parse("ar", "articlerow", TRUE);
}

$t->parse("CONTENT", "block");
$t->pparse("out",array("main"));
$timer->stop();
$timer->display();
?>
