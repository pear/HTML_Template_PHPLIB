<?php
// Call HTML_Template_PHPLIB_HelperTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HTML_Template_PHPLIB_HelperTest::main');
}

require_once dirname(__FILE__) . '/helper.inc';

require_once 'HTML/Template/PHPLIB/Helper.php';

/**
 * Test class for HTML_Template_PHPLIB_Helper.
 * Generated by PHPUnit on 2007-10-03 at 23:46:21.
 */
class HTML_Template_PHPLIB_HelperTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('HTML_Template_PHPLIB_HelperTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    /**
     */
    public function testGetLines() {
        //test content
        $strContent = <<<EOT
1
22
333
44
5
EOT;
        $this->assertEquals(
            array('1', '22', '333', '44', '5'),
            HTML_Template_PHPLIB_Helper::getLines(null, $strContent)
        );

        //TODO: test file loading
    }

    /**
     */
    public function testSplitLines() {
        $strContent = <<<EOT
1
22
333
44
5
EOT;
        $this->assertEquals(
            array('1', '22', '333', '44', '5'),
            HTML_Template_PHPLIB_Helper::splitLines($strContent)
        );
    }
}

// Call HTML_Template_PHPLIB_HelperTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'HTML_Template_PHPLIB_HelperTest::main') {
    HTML_Template_PHPLIB_HelperTest::main();
}
?>
