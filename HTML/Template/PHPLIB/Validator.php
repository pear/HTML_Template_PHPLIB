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
require_once 'HTML/Template/PHPLIB/Helper.php';

/**
* Class to validate templates (syntax checks)
*
* @category HTML
* @package  HTML_Template_PHPLIB
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
* @link     http://pear.php.net/package/HTML_Template_PHPLIB
*/
class HTML_Template_PHPLIB_Validator
{
    /**
    * Validates a template file.
    * You can pass either a file name, or the file content. One of the parameters
    *  needs to be !== null.
    *
    * @param string $strFile    Template file name to check
    * @param string $strContent Template content to check
    *
    * @return mixed Boolean true if no errors have been found, array of
    *                errors otherwise. An error is an array with keys
    *                - 'short' (short error code, string)
    *                - 'message' (readable message)
    *                - 'line'    (line number)
    *                - 'code'    (code that caused the error)
    *                false if no file and content is given
    *
    * @static
    */
    function validate($strFile = null, $strContent = null)
    {
        $arLines = HTML_Template_PHPLIB_Helper::getLines($strFile, $strContent);
        if ($arLines === false) {
            return false;
        }
        $arErrors = HTML_Template_PHPLIB_Validator::checkBlockDefinitions($arLines);
        $arErrors = array_merge(
            $arErrors,
            HTML_Template_PHPLIB_Validator::checkVariables($arLines)
        );

        HTML_Template_PHPLIB_Validator::sortByLine($arErrors);

        return count($arErrors) == 0 ? true : $arErrors;
    }//function validate($strFile = null, $strContent = null)



    /**
    * Check if all block definitions have a closing counterpart
    * and if the block comments have all the required spaces
    *
    * @param array $arLines Array of template code lines
    *
    * @return array Array of errors/warnings. An error/warning is an array
    *                of several keys: message, line
    *
    * @static
    */
    function checkBlockDefinitions($arLines)
    {
        //Array of block definitions found.
        // key is the block name, value is an array of line numbers
        $arBlocks     = array();
        $arBlockOpen  = array();
        $arBlockClose = array();
        $arErrors     = array();

        $strRegex = '/<!--(\s*)(BEGIN|END)(\s*)([a-zA-Z0-9_]*)(\s*)-->/';
        foreach ($arLines as $nLine => $strLine) {
            if (preg_match($strRegex, $strLine, $arMatches)) {
                //code line numbers start with 1, not 0
                $nLine = $nLine + 1;

                $strType      = $arMatches[2];
                $strBlockName = $arMatches[4];
                $strArName    = $strType == 'BEGIN'
                    ? 'arBlockOpen' : 'arBlockClose';
                if ($arMatches[1] == '') {
                    //space missing between <!-- and BEGIN|END
                    $arErrors[] = array(
                        'short'   => 'MISSING_SPACE',
                        'message' => 'Space missing between HTML comment'
                            . ' opening marker and ' . $strType,
                        'line'    => $nLine,
                        'code'    => $strLine
                    );
                }
                if ($arMatches[3] == '') {
                    //space missing between BEGIN and block name
                    $arErrors[] = array(
                        'short'   => 'MISSING_SPACE',
                        'message' => 'Space missing between ' . $strType
                            . ' and block name',
                        'line'    => $nLine,
                        'code'    => $strLine
                    );
                }
                if ($arMatches[4] == '') {
                    //block name missing
                    $arErrors[] = array(
                        'short'   => 'MISSING_BLOCK_NAME',
                        'message' => 'Block name missing',
                        'line'    => $nLine,
                        'code'    => $strLine
                    );
                } else {
                    ${$strArName}[$strBlockName][] = $nLine;
                    $arBlocks[] = array(
                        'line' => $nLine,
                        'code' => $strLine,
                        'name' => $strBlockName,
                        'open' => $strType == 'BEGIN'
                    );
                }
                if ($arMatches[5] == '') {
                    //space missing between block name and -->
                    $arErrors[] = array(
                        'short'   => 'MISSING_SPACE',
                        'message' => 'Space missing between block name and'
                            . ' HTML comment end marker',
                        'line'    => $nLine,
                        'code'    => $strLine
                    );
                }
            }
        }


        /**
        * Check if all open blocks have a close counterpart
        */
        foreach ($arBlockOpen as $strBlockName => $arLines) {
            if (count($arLines) > 1) {
                $arErrors[] = array(
                    'short'   => 'DUPLICATE_BLOCK',
                    'message' => 'Block "' . $strBlockName . '" is opened'
                        . ' several times on lines '
                        . implode(', ', $arLines),
                    'line'    => $arLines[0],
                    'code'    => $strBlockName
                );
            }
            if (!isset($arBlockClose[$strBlockName])) {
                $arErrors[] = array(
                    'short'   => 'UNFINISHED_BLOCK',
                    'message' => 'Block "' . $strBlockName . '" is not closed.',
                    'line'    => $arLines[0],
                    'code'    => $strBlockName
                );
            }
        }
        foreach ($arBlockClose as $strBlockName => $arLines) {
            if (count($arLines) > 1) {
                $arErrors[] = array(
                    'short'   => 'DUPLICATE_BLOCK',
                    'message' => 'Block "' . $strBlockName . '" is closed'
                        . ' several times on lines '
                        . implode(', ', $arLines),
                    'line'    => $arLines[0],
                    'code'    => $strBlockName
                );
            }
            if (!isset($arBlockOpen[$strBlockName])) {
                $arErrors[] = array(
                    'short'   => 'UNFINISHED_BLOCK',
                    'message' => 'Block "' . $strBlockName . '" is closed'
                        . ' but not opened.',
                    'line'    => $arLines[0],
                    'code'    => $strBlockName
                );
            }
        }


        /**
        * Check correct order (that begin comes before end)
        */
        foreach ($arBlockOpen as $strBlockName => $arLines) {
            if (isset($arBlockClose[$strBlockName])
                && $arLines[0] > $arBlockClose[$strBlockName][0]
            ) {
                $arErrors[] = array(
                    'short'   => 'WRONG_ORDER',
                    'message' => 'BEGIN of block "' . $strBlockName . '"'
                        . ' defined after END.',
                    'line'    => $arLines[0],
                    'code'    => $strBlockName
                );
            }
        }


        /**
        * Check proper nesting
        */
        $arStack = array();
        foreach ($arBlocks as $arBlock) {
            if ($arBlock['open']) {
                $arStack[] = $arBlock['name'];
            } else {
                //closing block
                if (end($arStack) == $arBlock['name']) {
                    //all fine
                    array_pop($arStack);
                } else {
                    //closing block is not the expected one
                    $strStackName = end($arStack);
                    if ($strStackName === null) {
                        //no open block defined -> we already handle this
                    } else {
                        $arErrors[] = array(
                            'short'   => 'WRONG_NESTING',
                            'message' => 'Block "' . $arBlock['name']
                                . '" closed,'
                                . ' but  "' . $strStackName
                                . '" is still open.',
                            'line'    => $arBlock['line'],
                            'code'    => $arBlock['code']
                        );
                    }
                }
            }
        }

        return $arErrors;
    }//function checkBlockDefinitions($arLines)



    /**
    * Checks if the variables defined are correct
    *
    * @param array $arLines Array of template content lines
    *
    * @return array Array of error messages.
    */
    function checkVariables($arLines)
    {
        $arErrors      = array();
        $strAllowed    = 'a-zA-Z0-9_-';
        $strRegex      = '/'
            . '(?:'
                . '(\{)'
                . '(['  . $strAllowed . ']+)'
                . '([^' . $strAllowed . ']|$)'
            . '|'
                . '([^' . $strAllowed . ']|^)'
                . '(['  . $strAllowed . ']+)'
                . '(\})'
            . ')'
            . '/';
        $arLineMatches = array();
        foreach ($arLines as $n => $strLine) {
            if (preg_match_all($strRegex, $strLine, $arMatches) > 0) {
                $arLineMatches[$n + 1] = $arMatches;
            }
        }
        foreach ($arLineMatches as $nLine => $arMatches) {
            foreach ($arMatches[0] as $nId => $strFull) {
                $chOpen      = $arMatches[1][$nId];
                $strVariable = $arMatches[2][$nId];
                $chClose     = $arMatches[3][$nId];

                if ($chOpen != '{') {
                    $arErrors[] = array(
                        'short'   => 'OPENING_BRACE_MISSING',
                        'message' => 'Variable "' . $strVariable
                            . '" misses opening brace.',
                        'line'    => $nLine,
                        'code'    => $strFull
                    );
                } else if ($chClose != '}') {
                    $arErrors[] = array(
                        'short'   => 'CLOSING_BRACE_MISSING',
                        'message' => 'Variable "' . $strVariable
                            . '" misses closing brace.',
                        'line'    => $nLine,
                        'code'    => $strFull
                    );
                }
            }
        }

        return $arErrors;
    }//function checkVariables($strContent)



    /**
    * Sorts the given error array by line numbers
    *
    * @param array &$arErrors Error array
    *
    * @return void
    */
    function sortByLine(&$arErrors)
    {
        if (!is_array($arErrors)) {
            return;
        }
        usort($arErrors, array(__CLASS__, 'intcmpLine'));
    }//function sortByLine(&$arErrors)



    /**
    * Compares the two error arrays by line number
    *
    * @param array $arA Error array one
    * @param array $arB Error array two
    *
    * @return integer -1, 0 or 1 if $arA is smaller, equal or bigger than $arB
    */
    function intcmpLine($arA, $arB)
    {
        return $arA['line'] - $arB['line'];
    }//function intcmpLine($arA, $arB)

}//class HTML_Template_PHPLIB_Validator

?>