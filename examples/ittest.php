<?php
include_once 'HTML/Template/IT.php';
include_once 'Benchmark/Timer.php';

$articles = array(
        "ISBN345624345" => "Matrix 2",
        "ISBN45df32342" => "Roger Rabbit",
        "ISBN305632232" => "foo bar",
        "ISBN674589123" => "Joe user's adventures"
);


$t =& new HTML_Template_IT("./");
$timer =& new Benchmark_Timer();
$timer->start();

$t->loadTemplateFile("block.ihtml",true,true);
$t->setVariable(array(
        "TITLE" => "This is a block integrated into another template",
        "BGCOLOR" => "#cccccc",
        "BLOCKER" => "block"
));

foreach ($articles as $k => $v) {
    $t->setCurrentBlock("articlerow");
    $t->setVariable("ID", $k);
    $t->setVariable("ANAME", $v);
    $t->parseCurrentBlock("articlerow");
}
$t->show();
$timer->stop();
$timer->display();
?>
