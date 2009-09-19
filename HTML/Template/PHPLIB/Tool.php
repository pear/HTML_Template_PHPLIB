<?php
/**
 * Additional tools for HTML_Template_PHPLIB
 *
 * PHP Versions 4 and 5
 *
 * @category HTML
 * @package  HTML_Template_PHPLIB
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/HTML_Template_PHPLIB
 */

/**
* Command line tool to use the HTML_Template_PHPLIB validator and generator.
*
* @category HTML
* @package  HTML_Template_PHPLIB
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
* @link     http://pear.php.net/package/HTML_Template_PHPLIB
*/
class HTML_Template_PHPLIB_Tool
{
    /**
    * Constructor
    *
    * @param array $args Cmdline arguments
    */
    function HTML_Template_PHPLIB_Tool($args)
    {
        $strAction = $this->getAction($args);
        $this->{'do' . ucfirst($strAction)}($args);
    }//function HTML_Template_PHPLIB_Tool($args)



    /**
    * Start the tool
    *
    * @return void
    * @static
    */
    function run()
    {
        $args = $GLOBALS['argv'];
        array_shift($args);
        new HTML_Template_PHPLIB_Tool($args);
    }//function run()



    /**
    * Returns the action to execute
    *
    * @param array &$args Array of command line arguments
    *
    * @return string Action to execute
    */
    function getAction(&$args)
    {
        if (count($args) == 0) {
            return 'help';
        }
        $arg = array_shift($args);
        switch ($arg) {
        case 'v':
        case 'validate':
        case '-v':
        case '--validate':
            return 'validate';
        case 'g':
        case 'generate':
        case '-g':
        case '--generate':
            return 'generate';
        default:
            return 'help';
        }
    }//function getAction(&$args)



    /**
    * Echoes the message and exist php with the given
    *  exit code
    *
    * @param string $strMessage Message to display
    * @param int    $nExitCode  Exit code
    *
    * @return void
    */
    function dieHard($strMessage, $nExitCode)
    {
        echo $strMessage;
        exit($nExitCode);
    }//function dieHard($strMessage, $nExitCode)



    /**
    * Prints the help message to stdout
    *
    * @return void
    */
    function doHelp()
    {
        echo <<<EOT
Usage: html_template_phplibtool action parameters

Tool to validate and work with HTML templates

mode: (- and -- are optional)
 h,  help      Show this help screen
 g,  generate  Generate PHP code for the template
 v,  validate  Validate a template file

EOT;
    }//function doHelp()



    /**
    * Validates the files given on the cmdline
    *
    * @param array $args Command line arguments (files)
    *
    * @return void
    */
    function doValidate($args)
    {
        if (count($args) == 0) {
            $this->dieHard("No template files to validate\n", 1);
        }

        include_once 'HTML/Template/PHPLIB/Validator.php';
        $nError = 0;
        foreach ($args as $file) {
            if (file_exists($file)) {
                $arErrors = HTML_Template_PHPLIB_Validator::validate($file);
                if ($arErrors === true) {
                    echo 'No errors found in ' . $file . "\n";
                    $nError =  0;
                } else if ($arErrors === false) {
                    echo 'Some unexpected error in ' . $file . "\n";
                    $nError =  3;
                } else {
                    echo count($arErrors) . ' errors in ' . $file . "\n";
                    foreach ($arErrors as $arError) {
                        echo ' Line #' . $arError['line'] . ': '
                            . $arError['message'] . "\n";
                    }
                    $nError = 10;
                }
            } else {
                echo 'File does not exist: ' . $file . "\n";
                $nError = 4;
            }
        }
        $this->dieHard('', $nError);
    }//function doValidate($args)



    /**
    * Generates PHP code for the given template file
    *
    * @param array $args Command line arguments
    *
    * @return void
    */
    function doGenerate($args)
    {
        if (count($args) == 0) {
            $this->dieHard("No template file given\n", 1);
        }

        $strFile = $args[0];
        include_once 'HTML/Template/PHPLIB/Generator.php';
        $strCode = HTML_Template_PHPLIB_Generator::getCodeBlockDefinition(
            $strFile
        );

        $this->dieHard($strCode, 0);
    }//function doGenerate($args)

}//class HTML_Template_PHPLIB_Tool

?>