<?php

/**
* Class to validate templates (syntax checks)
*
* @category HTML
* @package HTML_Template_PHPLIB
* @author Christian Weiske <cweiske@php.net>
*/
class HTML_Template_PHPLIB_Validator
{
    /**
    * Check if all block definitions have a closing counterpart
    * and if the block comments have all the required spaces
    *
    * @param string $strContent Template content
    *
    * @return array Array of errors/warnings. An error/warning is an array
    *                of several keys: message, line
    *
    * @static
    */
    function checkBlockDefinitions($strContent)
    {
         $arLines = explode("\n",
                        str_replace(
                            array("\r\n", "\r"),
                            "\n",
                             $strContent
                        )
         );
         //Array of block definitions found.
         // key is the block name, value is an array of line numbers
         $arBlockOpen  = array();
         $arBlockClose = array();
         $arErrors     = array();
         
         foreach ($arLines as $nLine => $strLine) {
             if (preg_match('/<!--(\s*)(BEGIN|END)(\s*)([a-zA-Z0-9_]*)(\s*)-->/', $strLine, $arMatches)) {
                 //code line numbers start with 1, not 0
                 $nLine = $nLine + 1;

                 $strType      = $arMatches[2];
                 $strBlockName = $arMatches[4];
                 $strArName    = $strType == 'BEGIN' ? 'arBlockOpen' : 'arBlockClose';

                 if ($arMatches[1] == '') {
                     //space missing between <!-- and BEGIN|END
                     $arErrors[] = array(
                         'short'   => 'MISSING_SPACE',
                         'message' => 'Space missing between HTML comment opening marker and ' . $strType,
                         'line'    => $nLine,
                         'code'    => $strLine
                     );
                 }
                 if ($arMatches[3] == '') {
                     //space missing between BEGIN and block name
                     $arErrors[] = array(
                         'short'   => 'MISSING_SPACE',
                         'message' => 'Space missing between ' . $strType . ' and block name',
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
                 }
                 if ($arMatches[5] == '') {
                     //space missing between block name and -->
                     $arErrors[] = array(
                         'short'   => 'MISSING_SPACE',
                         'message' => 'Space missing between block name and HTML comment end marker',
                         'line'    => $nLine,
                         'code'    => $strLine
                     );
                 }
             }
         }
         
         
         /**
         * Check if all open blocks have a close counterpart
         */

         
         return $arErrors;
    }//function checkBlockDefinitions($strContent)

}//class HTML_Template_PHPLIB_Validator

?>