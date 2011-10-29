<?php
/**
 * Horde Coding Standard for PHP_CodeSniffer parser.
 *
 * Requires PHP_CodeSnifer 1.2.0RC3+
 * Usage: phpcs --standard=[path_to_this file_directory] [files]
 *
 * @package   maintainer_tools
 * @author    Michael Slusarz <slusarz@horde.org>
 * @license   LGPLv2
 */
class PHP_CodeSniffer_Standards_Horde_HordeCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * External locations can be single sniffs, a whole directory of sniffs, or
     * an entire coding standard. Locations start with the standard name. For
     * example:
     *  PEAR                              => include all sniffs in this standard
     *  PEAR/Sniffs/Files                 => include all sniffs in this dir
     *  PEAR/Sniffs/Files/LineLengthSniff => include this single sniff
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
            'Generic/Sniffs/Classes',
            'Generic/Sniffs/CodeAnalysis',
            'Generic/Sniffs/Commenting',
            'Generic/Sniffs/Files/LineEndingsSniff.php',
            'Generic/Sniffs/Formatting/NoSpaceAfterCastSniff.php',
            'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
            'Generic/Sniffs/Metrics',
            'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
            'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
            'Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php',
            'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',
            'Generic/Sniffs/Strings',
            'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php',
            'PEAR/Sniffs/Classes',
            'PEAR/Sniffs/Commenting/InlineCommentSniff.php',
            'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php',
            'PEAR/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php',
            'PEAR/Sniffs/Functions/ValidDefaultValueSniff.php',
            'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php',
            'PEAR/Sniffs/WhiteSpace',
            'Squiz/Sniffs/Arrays/ArrayBracketSpacingSniff.php',
            'Squiz/Sniffs/Classes/DuplicatePropertySniff.php',
            'Squiz/Sniffs/Classes/SelfMemberReferenceSniff.php',
            'Squiz/Sniffs/ControlStructures/ForLoopDeclarationSniff.php',
            'Squiz/Sniffs/ControlStructures/LowercaseDeclarationSniff.php',
            'Squiz/Sniffs/Functions/FunctionDeclarationSniff.php',
            'Squiz/Sniffs/Functions/FunctionDuplicateArgumentSniff.php',
            'Squiz/Sniffs/Functions/LowercaseFunctionKeywordsSniff.php',
            'Squiz/Sniffs/Operators/IncrementDecrementUsageSniff.php',
            'Squiz/Sniffs/Operators/ValidLogicalOperatorsSniff.php',
            'Squiz/Sniffs/PHP/CommentedOutCodeSniff.php',
            'Squiz/Sniffs/PHP/DisallowSizeFunctionsInLoopsSniff.php',
            'Squiz/Sniffs/PHP/EvalSniff.php',
            'Squiz/Sniffs/PHP/InnerFunctionsSniff.php',
            'Squiz/Sniffs/PHP/LowercasePHPFunctionsSniff.php',
            'Squiz/Sniffs/PHP/NonExecutableCodeSniff.php',
            'Squiz/Sniffs/Scope',
//            'Squiz/Sniffs/Strings/DoubleQuoteUsageSniff.php',
            'Squiz/Sniffs/Strings/EchoedStringsSniff.php',
            'Squiz/Sniffs/WhiteSpace/SemicolonSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/SuperfluousWhitespaceSniff.php',
        );
    }

}
