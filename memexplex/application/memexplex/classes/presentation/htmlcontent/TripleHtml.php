<?php

/**
 * A collection of functions to build HTML displays for triples.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Rom
 */
class TripleHtml
{

    /**
     * Builds a displayable Triple.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     * @param boolean $modal Modal window display or not.
     * @param integer $memeId Database Id for the meme association.
     */
    public static function getBlock($formArray,$pageObjectsXml,$modal=null,$memeId=null)
    {
        $tripleArray = $pageObjectsXml->xpath(
            $formArray
                ->TripleBlock
                    ->recordDataXpath
        );

        if ($pageObjectsXml->TripleList->Triple->Id)
        {
            if (!$memeId)
            {
                //Set Page Header
                HeaderFooter::$title = 'Triple: '.str_replace('"','',strip_tags($pageObjectsXml->TripleList->Triple->Title));
                HeaderFooter::$description = str_replace('"','',strip_tags($pageObjectsXml->TripleList->Triple->Description));
            }
            
            if (count($tripleArray) == 0)
            {
                $tripleArray[0] = new SimpleXMLElement("<base></base>");
            }
            $modalClass = "";
            if ($modal)
            {
                $modalClass = "modal";
            }
            $TripleDisplay = '<h1>'.$pageObjectsXml->TripleList->Triple->Title.'</h1>'
                .'<div class="'.$modalClass.'tripledivlistitem">'.$pageObjectsXml->TripleList->Triple->Description.'</div>';
            $formConfig = new SimpleXMLElement("<base></base>");

            $formField = new FormFieldTaxonomy();
            $formField->setData(
                $formConfig
                ,$tripleArray[0]
                ,$pageObjectsXml
            );
            $formField->setDestination("TripleList/");
            $formField->setDefaultValue("");
            $formField->setSource("");
            if ($formField->getSource(true) != "None")
            {
                $TripleDisplay .= "<br/><b>Folksonomies:</b> " . $formField->getSource(true);
                HeaderFooter::$keywords .= $formField->getTaxonomyString().",";
            }

            if ($memeId)
            {
                $TripleDisplay = '<h2><a href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'Triple/'
                    . 'id='
                    . $pageObjectsXml->TripleList->Triple->Id
                    . '">'
                    . '<b>Triple</b>'
                	. '</a></h2>'
                    . $TripleDisplay;
            }
        }
        else
        {
            if ($memeId)
            {
                $TripleDisplay =
                    '<link rel="stylesheet" type="text/css" href="'.ROOT_FOLDER.'framework/css/subModal.css" />'
                	. '<a class="submodal-600-525"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'TripleModal/'
                    . 'memeid='
                    . $memeId
                    . '">'
                    . 'Add Triple'
                	. '</a>';
                JavaScript::addJavaScriptInclude("subModal");
                CascadingStyleSheets::addCascadingStyleSheetsInclude("subModal");
            }
        }
        return $TripleDisplay;
    }

    /**
     * Builds an editable Triple Form.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     */
    public static function getForm($formArray,$pageObjectsXml)
    {
        $tripleArray = $pageObjectsXml->xpath(
            $formArray
                ->TripleBlock
                    ->recordDataXpath
        );
        if (count($tripleArray) == 0)
        {
            $tripleArray[0] = new SimpleXMLElement("<base></base>");
        }
        //BEGIN REFERENCE TABLE CONSTRUCTION
        $TripleFieldSet = new HtmlFormFieldSet("Triple","Triple");
        //REGISTER TABLE CONFIG WITH FORMTABLE OBJECT
        $TripleFieldSet->setFormConfiguration($formArray->TripleBlock);
        $TripleFieldSet->setFormData($tripleArray[0]);
        $TripleFieldSet->setPageObjectsXml($pageObjectsXml);
        //BUILDS THE VIEW/EDIT TABLES
        $TripleFieldSet->appendFormFieldSet();
        // END REFERENCE TABLE CONSTRUCTION
        return $TripleFieldSet->getFieldSetSource();
    }

    /**
     * Builds a list of Memes in Div blocks.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     */
    public static function getList($formArray,$pageObjectsXml)
    {
        $listSource = "";
        $setdata = $pageObjectsXml->xpath(
            $formArray
                ->TripleListTable
                    ->recordDataXpath
        );
        //LOOP THROUGH ROWS
        foreach($setdata as $rowdata)
        {
            $listSource .= "<div class=\"tripledivlistitem\">";
            $datePublished = null;
            //LOOP THOUGH FORM ELEMENTS
            foreach($formArray->TripleListTable->formfield as $formfield)
            {
                $htmlFormField = FormFieldFactory::setFormfield($formfield,$rowdata,$pageObjectsXml);
                $htmlFormField->setSource();
                switch ((string) $formfield->label)
                {
                    case "Date Published":
                        $datePublishedSource = $htmlFormField->getSource(true);
                        if ($datePublishedSource != "31 DEC 1969")
                        {
                            $datePublished = true;
                        }
                        break;
                    case "Curator":
                        if (ApplicationSession::getValue('DOMAIN') != 'curator'
                            && !isset($_GET['id']))
                        {
                            $by = "";
                            if ($datePublished)
                            {
                                $by = $datePublishedSource."&nbsp;by&nbsp;";
                            }
                            $listSource .= '<div class="divListItemDate">'
                                .$by.$htmlFormField->getSource(true)."</div>";
                        }
                        elseif ($datePublished)
                        {
                            $listSource .= '<div class="divListItemDate">'
                                .$datePublishedSource."</div>";
                        }
                        break;
                    case "Title":
                        $listSource .= 
                            '<h2><img src="'.ROOT_FOLDER.'framework/images/triple.png" class="tripleListIcon" />' 
                        	."&nbsp;".$htmlFormField->getSource(true)."</h2>";
                        break;
                    case "Description":
                        $listSource .= "<br/>".$htmlFormField->getSource(true);
                        break;
                    case "Subject":
                        $listSource .= "<b>".$htmlFormField->getSource(true)." &gt; ";
                        break;
                    case "Predicate":
                        $listSource .= $htmlFormField->getSource(true)." &gt; ";
                        break;
                    case "Object":
                        $listSource .= $htmlFormField->getSource(true)."</b>";
                        break;
                    case "Folksonomies":
                        $source = $htmlFormField->getSource(true);
                        if (trim($source) != "None")
                        {
                            $listSource .= "<div class=\"folksonomies\"><b>Folksonomies:</b> ".$source."</div>";
                            //An invislbe placeholder div for absolute-positioned folksonomies.
                            $listSource .= "<div class=\"folksonomiesHeight\"><b>Folksonomies:</b> ".$source."</div>";
                        }
                        break;
                    default:
                        $listSource .= $htmlFormField->getSource(true);
                }
            }
            $listSource .= "</div>";
        }
        return $listSource;
    }

}
