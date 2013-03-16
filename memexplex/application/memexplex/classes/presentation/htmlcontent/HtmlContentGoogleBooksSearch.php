<?php

/**
 * Builds the XHTML content for the Google Books Search pop up window.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Zap Brannigan
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentGoogleBooksSearch extends HtmlContent
implements HtmlContentInterface
{

    /**
     * Builds a list of results with images to display to the Curator. 
     */
    public function setSource()
    {

        $searchParameters = new MemexPlexObjectSearchParameters();
        $searchParameters->setPropertiesFromGetAndPost();

        $filterSource = "";
        $resultsSource = "";

        try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsGoogleBooksSearch;
            $pageObjects->setSearchParameters($searchParameters);
            $pageObjectsXml = $pageObjects->getData();
        }
        catch (GeneralException $e)
        {
            //DON'T DISPLAY EDIT LINK ON ERROR
            $this->source = "<input type=\"hidden\""
                          . " id=\"hidEditFooBarsMenu\""
                          . " value=\"false\" />"
						  . "<div class=\"largeBlue\">"
                          . "<p>An Error Occurred in Retrieving Meme List.</p>"
                          . "</div>";
            return;
        }

        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        $filterSource = SearchFilterHtml::getSearchFilter($formArray,$pageObjectsXml);

        if ($searchParameters->getSearchString())
        {
//Uncomment this to see all the data populating the page.
//            echo "<pre>";
//            print_r($pageObjectsXml);
//            echo "</pre>";

            $listSource = "";
            $currentRow = 1;
            //LOOP THROUGH ROWS
            foreach($pageObjectsXml->xpath($formArray->booklist->recordDataXpath) as $rowdata)
            {
                $listSource .= 
                    "<div class=\"modalreferencedivlistitem\">";
                //LOOP THOUGH FORM ELEMENTS
                foreach($formArray->booklist->formfield as $formfield)
                {
                    $htmlFormField = FormFieldFactory::setFormfield($formfield,$rowdata,$pageObjectsXml,(string)$currentRow);
                    $htmlFormField->setSource();
                    switch ((string) $formfield->label)
                    {
                        case "SmallImage":
                            $listSource .= "<span style=\"float:right;\">"
                                ."<a href=\"javascript:void(0);\""
                                ." onclick=\"populateReference({$currentRow});\""
                                .">"
                                ."Add Reference</span>"
                                ."</a>&nbsp;"
                                ."<span style=\"float:left;padding:3px;\">"
                                .$htmlFormField->getSource(true)
                                ."</span>";
                            break;
                        case "Author":
                            $listSource .= "<h4>".$htmlFormField->getSource(true)."&nbsp;</h4>";
                            break;
                        case "Title":
                            $listSource .= "<h3>".$htmlFormField->getSource(true)."&nbsp;</h3><br/><br/>";
                            break;
                        default:
                            $listSource .= $htmlFormField->getSource();
                    }
                }
                $listSource .= "</div>";
                $currentRow++;
            }
// Uncomment the following to see all attributes for the reference.
//            foreach ($pageObjectsXml->ItemSearchResponse->Items->Item as $item)
//            {
//        	    $resultsSource .=
//        	        "<div class=\"modaldivlistitempadding\">"
//                    ."<div class=\"modaldivlistitem\">"
//        	        ."<img src=\"{$item->SmallImage->URL}\" style=\"float:right;\" width=\"{$item->SmallImage->Width}\" height=\"{$item->SmallImage->Height}\" />"
//        	        //."<br><img src=\"" . $item->LargeImage->URL . "\" width=\"{$item->ItemAttributes->LargeImage->Width}\" height=\"{$item->ItemAttributes->LargeImage->Height}\" align=\"left\" /><br>"
//            	    ."<h3>{$item->ItemAttributes->Title}&nbsp;</h3>"
//        	        ."<h4>{$item->ItemAttributes->Author}&nbsp;</h4>"
//            	    ."ISBN : {$item->ItemAttributes->ISBN}<br>"
//            	    ."EAN : {$item->ItemAttributes->EAN}<br>"
//            	    ."UPC : {$item->ItemAttributes->UPC}<br>"
//            	    ."ASIN : {$item->ItemAttributes->ASIN}<br>"
//            	    ."Publication Date : {$item->ItemAttributes->PublicationDate}<br>"
//            	    ."Publisher : {$item->ItemAttributes->Publisher}<br>"
//        	        ."</div></div>";
//            }
//        }
        }
        $this->source =
            $filterSource
            ."<span id=\"amzresult\">"
            .$listSource
            . "</span>"
            . "<script type=\"text/javascript\" defer=\"defer\">"
            . "addLoadEvent(function()"
            . "{"
            // OVERIDE AJAX CONTENT TARGET
            .     "setTimeout('setAjaxContentTarget(\'amzresult\',\'loadingDisplay\')',500);"
            . "});"
            . "</script>";

    }

}
