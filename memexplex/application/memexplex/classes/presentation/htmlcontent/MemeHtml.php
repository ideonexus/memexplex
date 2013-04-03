<?php

/**
 * A collection of functions to build HTML displays for memes.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Stewie Griffin
 */
class MemeHtml
{

    /**
     * Builds a displayable Meme.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     * @param boolean $modal Modal window display or not.
     */
    public static function getBlock($formArray,$pageObjectsXml,$modal=null,$tripleid=null,$linkTitle=null)
    {
        $MemeDisplay = "";
        if (isset($pageObjectsXml->MemeList->Meme))
        {
            $memeArray = $pageObjectsXml->xpath
            (
                $formArray
                    ->MemeBlock
                        ->recordDataXpath
            );
            
            if (count($memeArray) == 0)
            {
                $memeArray[0] = new SimpleXMLElement("<base></base>");
            }
            
            $modalClass = "";
            if ($modal)
            {
                $modalClass = "modal";
            }
            
            if (!$tripleid)
            {
                //Set Page Title
                HeaderFooter::$title = 'Meme: '.str_replace('"','',strip_tags($pageObjectsXml->MemeList->Meme->Title));
                HeaderFooter::$description = str_replace('"','',strip_tags($pageObjectsXml->MemeList->Meme->Text));
            }
            
            if ($linkTitle)
            {
            	$MemeDisplay .= '<div class="titleblock"><h1><a href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'Meme/'
                    . $pageObjectsXml->MemeList->Meme->Id . '/'
                    . '">'.$pageObjectsXml->MemeList->Meme->Title.'</a></h1></div>';
            }
            else
            {
            	$MemeDisplay .= '<div class="titleblock"><h1>'.$pageObjectsXml->MemeList->Meme->Title.'</h1></div>';
            }
            $MemeDisplay .= '<div class="'.$modalClass.'memedivlistitem">'.$pageObjectsXml->MemeList->Meme->Text.'</div>'
                .'<h2>Direct Quote:</h2>'
                .'<div class="'.$modalClass.'memedivlistitem">'.$pageObjectsXml->MemeList->Meme->Quote.'</div>';
            $formConfig = new SimpleXMLElement("<base></base>");
            $formField = new FormFieldTaxonomy();
            $formField->setData
            (
                $formConfig
                ,$memeArray[0]
                ,$pageObjectsXml
            );
            $formField->setDestination("MemeList/");
            $formField->setDefaultValue("");
            $formField->setSource("");
            if ($formField->getSource(true) != "None"
                && !$modal)
            {
                $MemeDisplay .='<p><b>Folksonomies:</b> ' . $formField->getSource(true) . '</p>';
                HeaderFooter::$keywords .= $formField->getTaxonomyString().",";
            }
        }

        return '<span id="memeblock">'.$MemeDisplay."</span>";
    }

    /**
     * Builds an editable Meme Form.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     * @param string $modifier Subject, Predicate or other distinguishing string.
     */
    public static function getForm($formArray,$pageObjectsXml,$modifier=null)
    {
        $memeArray = $pageObjectsXml->xpath
        (
            $formArray
                ->MemeBlock
                    ->recordDataXpath
        );
        if (count($memeArray) == 0)
        {
            $memeArray[0] = new SimpleXMLElement("<base></base>");
        }
        //BEGIN MEME TABLE CONSTRUCTION
        $formName = "Meme";
        $formId   = "Meme";
        if ($modifier)
        {
            $formName = $modifier." Meme";
            $formId   = $modifier."Meme";
        }
        $MemeFormFieldSet = new HtmlFormFieldSet($formName,$formId);
        $MemeFormFieldSet->setFormConfiguration($formArray->MemeBlock);
        $MemeFormFieldSet->setFormData($memeArray[0]);
        $MemeFormFieldSet->setPageObjectsXml($pageObjectsXml);
        if ($modifier)
        {
            $MemeFormFieldSet->setModifier($modifier);
        }
        $MemeFormFieldSet->appendFormFieldSet();
        //END MEME TABLE CONSTRUCTION
        return $MemeFormFieldSet->getFieldSetSource();
    }

    /**
     * Builds a list of Memes in Div blocks.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     * @param boolean $modal Modal window or not.
     * @param integer $referenceId Parent reference id.
     * @param integer $schemaId Parent schema id.
     */
    public static function getList(
        $formArray
        ,$pageObjectsXml
        ,$modal=null
        ,$referenceId=null
        ,$schemaId=null
        ,$tripleId=null
    )
    {
        $listSource = "";
        $setdata = $pageObjectsXml->xpath
            (
                $formArray
                    ->MemeListTable
                        ->recordDataXpath
            );
        $modalClass = "";
        if ($modal)
        {
            $modalClass = "modal";
        }
        //LOOP THROUGH ROWS
        foreach($setdata as $rowdata)
        {
            $listSource .= "<div class=\"{$modalClass}memedivlistitem\">";
            $datePublished = null;
            //LOOP THOUGH FORM ELEMENTS
            foreach($formArray->MemeListTable->formfield as $formfield)
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
                            $listSource .= '<span style="float:right;">'
                                .$by.$htmlFormField->getSource(true)."</span>";
                        }
                        elseif ($datePublished)
                        {
                            $listSource .= '<span style="float:right;">'
                                .$datePublishedSource."</span>";
                        }
                        break;
                    case "Title":
                        $listSource .=
                            '<h2><a name="meme'.$rowdata->Id.'" id="meme'.$rowdata->Id.'"></a><img src="'.ROOT_FOLDER.'framework/images/meme.gif" width="13" height="13" border="0"/>' 
                        	."&nbsp;".$htmlFormField->getSource(true)."</h2>".$removeLink;
                        break;
                    case "Summary":
                        $listSource .= $htmlFormField->getSource(true);
                        break;
                    case "Folksonomies":
                        $source = $htmlFormField->getSource(true);
                        if (trim($source) != "None")
                        {
                            $listSource .= "<br/><b>Folksonomies:</b> ".$source;
                        }
                        if ($referenceId || $schemaId)
                        {
                            $dis = "";
                            if (!$modal)
                            {
                                $dis = "dis";
                            }
                            
                            if (trim($source) == "None")
                            {
                                $listSource .= "&nbsp;";
                            }
                            
                            if ($referenceId)
                            {
                                $parentId = "&referenceid=".$referenceId;
                            }
                            elseif ($schemaId)
                            {
                                $parentId = "&schemaid=".$schemaId;
                            }
                            
                            $listSource .= "<span style=\"float:right;position:relative;right:-5px;\" class=\"menulink\">"
                            	."<a href=\"javascript:void(0)\""
                            	." onClick=\""
                            	."getContent('"
                            	. ROOT_FOLDER . "framework/api/processForm.php"
                            	."','"
                                . "application="
                                . Constants::getConstant('CURRENT_APPLICATION')
                                . "&pageCode=ParentChild"
                                . "&function={$dis}associate"
                                . $parentId
                                . "&memeid="
                                . $rowdata->Id
                            	."','processFormCallback'"
                            	.");"
                            	."\">{$dis}associate</a>"
                            	."</span>";
                        }
                        break;
                    case "Expand":
                        $listSource .= "<span  class=\"menulink\"><a href=\"javascript:void(0);\" id=\"expand{$rowdata->Id}\" onclick=\"expandQuote('{$rowdata->Id}');\" style=\"float:right;\">expand</a></span>";
                        break;
                    case "ReferenceCount":
                        $listSource .= "<span style=\"float:right;\">".$htmlFormField->getSource(true)."</span>";
                        break;
                    case "TripleCount":
                        $listSource .= "<span style=\"float:right;\">".$htmlFormField->getSource(true)."</span>";
                        break;
                    case "SchemaCount":
                        $listSource .= "<span style=\"float:right;\">".$htmlFormField->getSource(true)."</span>";
                        break;
                    case "Quote":
                        $listSource .= "</div><span class=\"quoteDisplay\" id=\"quote{$rowdata->Id}\"><div class=\"{$modalClass}memedivlistitemquote\">"
                        	.$htmlFormField->getSource(true) . "</div></span>";
                        break;
                    default:
                        $listSource .= $htmlFormField->getSource(true);
                }
            }
            //$listSource .= "</div>";
        }

        if ($listSource == "")
        {
            $listSource = "<div class=\"largeBlue\">No Memes Found</div><br/>";
        }
        
        return $listSource;
    }

}
