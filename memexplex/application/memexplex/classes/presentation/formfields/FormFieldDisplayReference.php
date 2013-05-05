<?php

/**
 * The idea here was to take a reference and display it in the appropriate
 * APA format; however, APA formats are a confusing mess of silly rules and
 * ill-conceived standards. In fact, at the time of my writing this, the sixth
 * edition of the Publication Manual of the American Psychological Association
 * has just come out, which was quickly followed by eight, count 'em, EIGHT
 * pages of corrections, resulting in college students nationwide recieving
 * lower marks for poorly-formated citations, which leads to crying in the
 * short term and a distrust of authority in the long term.
 *
 * And what's the point of getting it exactly right anyways? If the APA's just
 * going to change it all again in willy-nilly fashion so they can force another
 * generation of students to buy the next edition of their stupid, error-filled
 * book, why bother? It shouldn't take a 30-page chapter in a reference book
 * to explain how to properly format a citation; there should be a single-page
 * of simple, common sense rules that define how to layout these 100 to 300
 * character entries. This kind of B.S. is why most people think academia is
 * pedantic nonsense.
 *
 * @package Framework
 * @subpackage Presentation
 * @see FormField
 * @see FormFieldInterface
 * @author Lisbeth Salander
 */
class FormFieldDisplayReference extends FormField
implements FormFieldInterface
{
    /**
     * @var string The fully-assembled reference display.
     */
    protected $referenceText;

    /**
     * @var string <todo:description>
     */
    protected $authorsDestination = '';

    /**
     * <todo:description>
     *
     */
    public function setAuthorsDestination($authorsDestination='')
    {
        $this->authorsDestination = $authorsDestination;
    }

    /**
     * Takes the xpaths defined in the formfield xml configuration, and uses
     * them to access the values for the various properties of the reference.
     *
     * @param SimpleXMLElement $formfield XML Configuration defining the formfield.
     * @param SimpleXMLElement $formData XML data informing the formfield.
     * @param SimpleXMLElement $pageObjectsXml All XML data for the page.
     */
    public function setData(
        SimpleXMLElement $formfield
        ,SimpleXMLElement $formData
        ,SimpleXMLElement $pageObjectsXml
    )
    {
        $referenceText = "";

        $referenceSuperTypeDescription = "";
        if ($formData->ReferenceSuperTypeDescription)
        {
            $referenceSuperTypeDescription = "<b>".$formData->ReferenceSuperTypeDescription."&gt;</b>";
        }

        $referenceTypeDescription = "";
        if ($formData->ReferenceTypeDescription)
        {
            $referenceTypeDescription = "<b>".$formData->ReferenceTypeDescription.":</b>&nbsp;&nbsp;";
        }
        
        $authorsDestination = 'ReferenceList/';
        if ($formfield->authorsDestination)
        {
            $authorsDestination = $formfield->authorsDestination;
        }
        
        $authors = "";
        if ($formData->Authors)
        {
            $separator = "";
            $openHref = '<a href="'
                .ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                .$authorsDestination
                .'author=';
            $midHref = '">';
            $closeHref = '</a>';
            if (count($formData->Authors->Author) == 1)
            {
                $authors .= $openHref.$formData->Authors->Author->Id.$midHref
                    .$formData->Authors->Author->FullName.$closeHref;
            }
            elseif (count($formData->Authors->Author) == 2)
            {
                foreach ($formData->Authors->Author as $author)
                {
                    $authors .= $separator.$openHref.$author->Id.$midHref
                        .$author->FullName.$closeHref;
                    $separator = " and ";
                }
            }
            elseif (count($formData->Authors->Author) > 2)
            {
                foreach ($formData->Authors->Author as $author)
                {
                    $authors .= $separator.$openHref.$author->Id.$midHref
                        .$author->LastName.$closeHref;
                    $separator = ", ";
                }
            }
        }

        $date = "";
        if
        (
            (string) $formData->ReferenceDate != ""
            && (string) $formData->ReferenceDate != "0000-00-00 00:00:00"
        )
        {
            $date = "(" . $formData->ReferenceDate . ")";
        }

        $title = "";
        $titleEmOpen = "";
        $titleEmClose = "";
        if ((string) $formData->Title != "")
        {
            $titleEmOpen = "<em>";
            $titleEmClose = "</em>";
            if ($authors != "")
            {
                $title = ", " . $formData->Title;
            }
            else
            {
                $title = $formData->Title;
            }
        }

        $publicationLocation = "";
        if ((string) $formData->PublicationLocation != "")
        {
            $publicationLocation = ", " . $formData->PublicationLocation;
        }

        $volumePages = "";
        if ((string) $formData->VolumePages != "")
        {
            $volumePages = ", " . $formData->VolumePages;
        }

        $publisherPeriodical = "";
        if ((string) $formData->PublisherPeriodical != "")
        {
            $publisherPeriodical = ", " . $formData->PublisherPeriodical;
        }

        $dateRetrieved = "";
        if
        (
            (string) $formData->DateRetrieved != ""
            && (string) $formData->DateRetrieved != "0000-00-00 00:00:00"
        )
        {
            $dateRetrieved = ", Retrieved on " . str_replace(' 00:00:00','',$formData->DateRetrieved);
        }

        $url = "";
        if ((string) $formData->Url != "")
        {
        	$parsedUrl = parse_url($formData->Url);
//            if ($dateRetrieved != "")
//            {
//                $url = "<li>from "
//                . "[<a href=\"" . $formData->Url . "\">" . $parsedUrl['host'] . "</a>]</li>";
//            }
//            else
//            {
                $url = "<li>"
                . "<a href=\"" . $formData->Url . "\" target=\"_blank\">Source Material</a>"
                . " [" . $parsedUrl['host'] . "]</li>";
//            }
        }

        $referenceService = "";
        if ((string) $formData->ReferenceService != "")
        {
            if ($dateRetrieved != "" || $url != "")
            {
                $referenceService = " from " . $formData->ReferenceService;
            }
            else
            {
                $referenceService = ", Retrieved from " . $formData->ReferenceService;
            }
        }

        $this->referenceText =
        	$referenceSuperTypeDescription
            . $referenceTypeDescription
            . $authors
            . " " . $date
            . $titleEmOpen . $title . $titleEmClose
            . $publisherPeriodical
            . $volumePages
            . $publicationLocation
            . $dateRetrieved
            . $url;
    }

    /**
     * Default value is the display-only version of the formelement
     */
    public function setDefaultValue()
    {
        $this->defaultValue = '<div class="reference">'.$this->referenceText."</div>";
    }

    /**
     * Source is the editable version of the formelement, in this case it is
     * the same as the view only.
     */
    public function setSource()
    {
        $this->source = $this->defaultValue;
    }

}
