<?php

include_once 'HTML/Template/PHPLIB.php';
include_once 'Benchmark/Timer.php';

/**
* "keep" = keep unknown template variables,
* "remove" = remove 'em,
* "comment" = put them into comments
*
* use keep if you have stylesheets or JavaScript
* in your HTML code since those are also using
* { and } which will then get removed.
*/
$t =& new HTML_Template_PHPLIB("./", "keep");
$timer =& new Benchmark_Timer();
$timer->start();

$t->setFile(array(
    "main" => "tpl1.ihtml"
));

$welcome = "Welcome to the real world";

$t->setVar(array(
    "TITLE" => "This is my title",
    "BGCOLOR" => "#cccccc",
    "CONTENT" => $welcome
));

/**
* or, use: $t->pparse("out", array("main"));
*/
$t->parse("out", array("main"));
$t->p("out");
$timer->stop();
$timer->display();
?>
