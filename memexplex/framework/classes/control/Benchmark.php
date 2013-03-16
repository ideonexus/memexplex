<?php
/**
 * Allows for benchmarking PHP code.
 *
 * Create new instances of the benchmark class when benchmarking within
 * classes to avoid conflicts, i.e.,
 *
 * $benchmark = New Benchmark on index.php
 * $pageBenchmark = New Benchmark on Page.class.php
 *
 * @package Framework
 * @subpackage Control
 * @link http://codeigniter.com/user_guide/libraries/benchmark.html
 * @author Ryan Somma 10/06/2008
 */
class Benchmark
{

    /**
     * @var array Array of benchmark points.
     */
    private static $marker = array();

    /**
     * <todo:description>
     *
     */
    public static function initialize()
    {
        self::$marker['initialized'] = microtime(true);
        self::$marker['after'] = microtime(true);
    }

    /**
     * Adds a benchmark point to the array with the elapsed time.
     *
     * @param string $name Marker name.
     */
    public static function mark($name)
    {
        self::$marker[$name] = microtime(true);
    }

    /**
     * Determines the time elapsed between two setBenchmark()'s.
     *
     * @param string $point1 First benchmark point.
     * @param string $point2 Second benchmark point.
     * @return string The difference in time elapsed between two points.
     */
    public static function elapsed_time($point1 = '', $point2 = '')
    {
        if ($point1 == '')
        {
            return '{elapsed_time}';
        }

        if (!isset(self::$marker[$point1]))
        {
            return '';
        }

        if (!isset(self::$marker[$point2]))
        {
            self::$marker[$point2] = microtime(true);
        }

        return number_format((self::$marker[$point2] - self::$marker[$point1]), 4);
    }

    /**
     * Returns the current memory usage.
     *
     * @return int Value returned from PHP function memory_get_usage()
     */
    public function memory_usage()
    {
        return memory_get_usage();
    }

    /**
     * Returns the current memory peak usage.
     *
     * @return int Value returned from PHP function memory_get_peak_usage()
     */
    public function memory_peak_usage()
    {
        return memory_get_peak_usage();
    }

    /**
     * Sets a benchmark point in the code.
     *
     * @param string $codeSection A description of what's going on at the moment
     * in the code.
     * @param string $file Current file (Recommend using the __FILE__ PHP
     * magic constant @link http://php.net/manual/en/language.constants.predefined.php ).
     * @param string $line Current line in code (Recommend using the __LINE__ PHP
     * magic constant @link http://php.net/manual/en/language.constants.predefined.php ).
     */
    public static function setBenchmark
    (
        $codeSection = '',
        $file = '',
        $line = ''
    )
    {
        if (ApplicationSession::getValue('debugFlag'))
        {
            self::$marker['before'] = self::$marker['after'];
            echo self::benchmarkDetails($codeSection,$file,$line);
        }
    }

    /**
     * Spits out the benchmark details.
     *
     * @param string $codeSection A description of what's going on at the moment
     * in the code.
     * @param string $file Current file (Recommend using the __FILE__ PHP
     * magic constant @link http://php.net/manual/en/language.constants.predefined.php ).
     * @param string $line Current line in code (Recommend using the __LINE__ PHP
     * magic constant @link http://php.net/manual/en/language.constants.predefined.php ).
     * @return string Benchmark details.
     */
    public static function benchmarkDetails
    (
        $codeSection = '',
        $file = '',
        $line = ''
    )
    {
        $benchmarkDetails = '<div>';
        self::mark('after');
        $fileAndLine = "";
        if ($line != '' || $file != '')
        {
            $fileAndLine =  "[" . basename($file) . " Line:$line] ";
        }
        $benchmarkDetails .= "<br/><b><u>$fileAndLine $codeSection</u></b><br/>";

        $benchmarkDetails .= "<b>Memory Usage:</b> ".self::$memory_usage()."<br />";
        $benchmarkDetails .= "<b>Memory Peak Usage:</b> ".self::$memory_peak_usage()."<br />";

        if ($codeSection != 'Page Totals')
        {
            if (self::elapsed_time('before', 'after') < 1)
            {
                $benchmarkDetails .= "<b>Elapsed Time:</b> ".self::elapsed_time('before', 'after')."<br/>";
            }
            else
            {
                $benchmarkDetails .= "<span  class=\"ErrorRed\"><b>Elapsed Time: ".self::elapsed_time('before', 'after')."</span></b><br/>";
            }
        }

        if (self::elapsed_time('initialized', 'after') < 5)
        {
            $benchmarkDetails .= "<b>Total Elapsed Time:</b> ".self::elapsed_time('initialized', 'after')."<p>";
        }
        else
        {
            $benchmarkDetails .= "<span  class=\"ErrorRed\"><b>Total Elapsed Time: ".self::elapsed_time('initialized', 'after')."</b></span><br/>";
        }
        $benchmarkDetails .= "</div>";

        return $benchmarkDetails;
    }

}
