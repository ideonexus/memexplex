<?php
/**
 * Functions for strings.
 *
 * @package Framework
 * @subpackage Control
 * @author Adam Lyons, buffalo bill
 */
class String
{
    /**
     * Takes a string in ISO-8859-1 character set from
     * the Ingres Database and converts it to UTF-8 for
     * RFC3629 compliance.
     *
     * @param string $string
     * @return string
     */
    public static function convertSpecialCharacters($string)
    {
        // First, replace UTF-8 characters.
        $string = str_replace(
            array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6")
            ,array("'", "'", '"', '"', '-', '--', '...')
            ,$string
        );
        // Next, replace their Windows-1252 equivalents.
        $string = str_replace(
            array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133))
            ,array("'", "'", '"', '"', '-', '--', '...')
            ,$string
        );

        return  iconv("CP1252", "UTF-8", $string);
    }

    /**
     * <todo:description>
     *
     * @param string $string <todo:description>
     * @param array $delimiters <todo:description>
     * @param array $exceptions <todo:description>
     * @return string <todo:description>
     * @link http://us.php.net/manual/en/function.mb-convert-case.php#90165
     */
    public static function titleCase
    (
        $string,
        $delimiters = array
        (
            ' ',
            '-',
            'O\''
        ),
        $exceptions = array
        (
            'to',
            'a',
            'the',
            'of',
            'by',
            'and',
            'with',
            'II',
            'III',
            'IV',
            'V',
            'VI',
            'VII',
            'VIII',
            'IX',
            'X'
        )
    )
    {
        // Exceptions in lower case are words you don't want converted.
        // Exceptions all in upper case are any words you don't want
        // converted to title case but should be converted to upper case,
        // e.g.: king henry viii or king henry Viii -> King Henry VIII
        $string = strtolower($string);
        foreach ($delimiters as $delimiter)
        {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $word)
            {
                if (in_array(strtoupper($word), $exceptions))
                {
                    // check exceptions list for any words
                    // that should be in upper case
                    $word = strtoupper($word);
                }
                else if (!in_array($word, $exceptions))
                {
                    // convert to uppercase
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
        }

        return $string;
    }

    /**
     * Takes a string and replaces a special character in the order of the
     * param array.  Similar to SQL Params.
     *
     * @param string array $replaceParams
     * @param string $subject
     * @param string $replaceCharacters
     * @return string
     */
    public static function replaceByParam($replaceParams, $subject, $replaceCharacters = '?')
    {

        foreach ($replaceParams as $param)
        {
            $subject = self::replaceOnce($replaceCharacters, $param, $subject);

        }
        return  $subject;
    }


    /**
     * Replaces only the first instance of the searched phrase.
     *
     * @param string $string
     * @param string array $params
     * @param string $replaceCharacters
     * @return string
     */
    public static function replaceOnce($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);
        if ($pos === false)
        {
            return $subject;
        }
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
}
