<?php
// Call HTML_Template_PHPLIB_ValidatorTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HTML_Template_PHPLIB_ValidatorTest::main');
}

require_once 'PHPUnit/Framework.php';

require_once 'HTML/Template/PHPLIB/Validator.php';

/**
 * Test class for HTML_Template_PHPLIB_Validator.
 * Generated by PHPUnit on 2007-10-01 at 17:36:02.
 */
class HTML_Template_PHPLIB_ValidatorTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('HTML_Template_PHPLIB_ValidatorTest');
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
    
    
    
    public function testValidate()
    {
        $this->assertFalse(HTML_Template_PHPLIB_Validator::validate());
        $this->assertTrue(HTML_Template_PHPLIB_Validator::validate(null, ''));

        $arErrors = HTML_Template_PHPLIB_Validator::validate(null, '<!-- BEGIN block -->');
        $this->assertType('array', $arErrors);
        $this->assertEquals(1, count($arErrors));
        
        $name = tempnam('/tmp', 'HTML_Template_PHPLIB-test');
        file_put_contents($name, '<!-- BEGIN blo -->');
        $arErrors = HTML_Template_PHPLIB_Validator::validate($name);
        $this->assertType('array', $arErrors);
        $this->assertEquals(1, count($arErrors));
        unlink($name);
    }//public function testValidate()



    /**
     */
    public function testCheckBlockDefinitions()
    {
        $cont = <<<EOT
<!-- BEGIN one -->
<!-- END one -->
<!--BEGIN two -->
<!--END two -->
<!-- BEGINthree -->
<!-- ENDthree -->
<!-- BEGIN four-->
<!-- END four-->
<!--BEGIN five-->
<!--END five-->
<!--BEGINsix-->
<!--ENDsix-->
<!-- BEGIN  -->
EOT;
        $arErrors = HTML_Template_PHPLIB_Validator::checkBlockDefinitions($cont);
        $arErrors = self::stripMessages($arErrors);

        $this->assertEquals(
array (
  array (
    'short' => 'MISSING_SPACE',
    'line' => 3,
    'code' => '<!--BEGIN two -->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 4,
    'code' => '<!--END two -->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 5,
    'code' => '<!-- BEGINthree -->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 6,
    'code' => '<!-- ENDthree -->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 7,
    'code' => '<!-- BEGIN four-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 8,
    'code' => '<!-- END four-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 9,
    'code' => '<!--BEGIN five-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 9,
    'code' => '<!--BEGIN five-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 10,
    'code' => '<!--END five-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 10,
    'code' => '<!--END five-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 11,
    'code' => '<!--BEGINsix-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 11,
    'code' => '<!--BEGINsix-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 11,
    'code' => '<!--BEGINsix-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 12,
    'code' => '<!--ENDsix-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 12,
    'code' => '<!--ENDsix-->',
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 12,
    'code' => '<!--ENDsix-->',
  ),
  array (
    'short' => 'MISSING_BLOCK_NAME',
    'line' => 13,
    'code' => '<!-- BEGIN  -->'
  ),
  array (
    'short' => 'MISSING_SPACE',
    'line' => 13,
    'code' => '<!-- BEGIN  -->',
  )
),
            $arErrors
        );

        //missing opening or closing blocks
        $cont = <<<EOT
<!-- BEGIN one -->
<!-- BEGIN two -->
<!-- END two -->
<!-- END three -->
<!-- BEGIN four -->
<!-- BEGIN four -->
<!-- END four -->
<!-- BEGIN five -->
<!-- END five -->
<!-- END five -->
EOT;
        $arErrors = HTML_Template_PHPLIB_Validator::checkBlockDefinitions($cont);
        $arErrors = self::stripMessages($arErrors);
        $this->assertEquals(
array (
  array (
    'short' => 'UNFINISHED_BLOCK',
    'line' => 1,
    'code' => 'one',
  ),
  array (
    'short' => 'DUPLICATE_BLOCK',
    'line' => 5,
    'code' => 'four',
  ),
  array (
    'short' => 'UNFINISHED_BLOCK',
    'line' => 4,
    'code' => 'three',
  ),
  array (
    'short' => 'DUPLICATE_BLOCK',
    'line' => 9,
    'code' => 'five',
  ),
),
            $arErrors
        );
    }
    
    
    protected static function stripMessages($arErrors)
    {
        foreach ($arErrors as $nId => &$arError) {
            unset($arError['message']);
        }
        return $arErrors;
    }//protected static function stripMessages($arErrors)
}

// Call HTML_Template_PHPLIB_ValidatorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'HTML_Template_PHPLIB_ValidatorTest::main') {
    HTML_Template_PHPLIB_ValidatorTest::main();
}
?>