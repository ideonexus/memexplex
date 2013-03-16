<?php

/**
 * This puts all the HTML for the page together, calling all the appropriate
 * objects in order. BAM!
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 08/21/2008
 */
class Page extends Html
{

    public function setSource()
    {

        //IF THIS IS AN AJAX CALL, JUST GET THE HTML CONTENT
        if (!Constants::getConstant('AJAX_METHOD'))
        {
            /**
             * Check it out! Decorator pattern!!! Each Html object
             * accepts and an Html object as an argument, gets its source,
             * and appends its content to it. Nifty, right?
             * No wonder my mom says I'm cool!
             */
            $pageContent =
                new HeaderFooter(
                    new JavaScript(
                        new MenuApplication(
                            new MenuPage(
                                new ErrorDisplay(
                                    new HtmlContentMain()
                                )
                            )
                        )
                    )
                );
        }
        else
        {
            $pageContent = new HtmlContentMain();
        }

        $pageContent->setSource();
        $this->source = $pageContent->getSource();

    }

}
