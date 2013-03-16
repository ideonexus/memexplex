<?php

/**
 * Really a simple factory. Produces new ProcessForm objects based on the
 * current pagecode.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
class ProcessFormFactory
{

    /**
     * @param string $pageCode <todo:description>
     * @return object <todo:description>
     * @throws {@link ControlExceptionConfigurationError}
     */
    public static function create($pageCode)
    {
        if ($pageCode)
        {
            $pageCodeName = 'ProcessForm' . $pageCode;
            return new $pageCodeName;
        }
        else
        {
            throw new ControlExceptionConfigurationError
            (
                'Page Code required for ProcessFormFactory.'
            );
        }
    }
}
