<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HTML_Template_PHPLIB_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';


chdir(dirname(__FILE__));
require_once 'HTML_Template_PHPLIBTest.php';


class HTML_Template_PHPLIB_AllTests
{
    public static function main()
    {

        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HTML_Template_PHPLIB Tests');
        /** Add testsuites, if there is. */
        $suite->addTestSuite('HTML_Template_PHPLIBTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'HTML_Template_PHPLIB_AllTests::main') {
    HTML_Template_PHPLIB_AllTests::main();
}
?>