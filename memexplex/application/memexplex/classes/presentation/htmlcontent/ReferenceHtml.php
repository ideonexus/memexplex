<?php
/**
 * A collection of functions to build HTML displays for references.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Gaius Baltar
 */
class ReferenceHtml
{

    /**
     * Builds a displayable Reference.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     * @param integer $memeId Database Id for the meme association.
     * @param boolean $modal Modal window display or not.
     */
    public static function getBlock(
        $formArray
        ,$pageObjectsXml
        ,$memeId=null
        ,$modal=null
        ,$parentreferenceid=null
        ,$childreferenceid=null
    )
    {
        $referenceArray = $pageObjectsXml->xpath(
            $formArray
                ->ReferenceBlock
                    ->recordDataXpath
        );

        $ReferenceDisplay = "";
        $ReferenceImageDisplay = "";
        $ReferenceDivWidth = "";
        if ($pageObjectsXml->ReferenceList->Reference->Id)
        {
            
            if (!$memeId && !$parentreferenceid && !$childreferenceid)
            {
                //Set Page Title
                HeaderFooter::$title = 'Reference: '.str_replace('"','',strip_tags($pageObjectsXml->ReferenceList->Reference->Title));
                HeaderFooter::$description = str_replace('"','',strip_tags($pageObjectsXml->ReferenceList->Reference->Title))
                    .", a reference on MemexPlex.";
            }
            
            if ($pageObjectsXml->ReferenceList->Reference->LargeImageUrl
                && trim($pageObjectsXml->ReferenceList->Reference->LargeImageUrl) != ""
            )
            {
                $ReferenceImageDisplay =
                    '<div>'
                    .'<span style="float:left;position:relative;left:20px;"><img'
                    .' src="'.$pageObjectsXml->ReferenceList->Reference->LargeImageUrl.'"'
                    .' height="229"'
                    .' width="150"'
                    .' /></span>';
                $ReferenceDivWidth = ' style="width:530px;float:right;position:relative;top:30px;right:20px;"';
                $ReferenceImageClosingDiv = "</div>";
            }
            
            //Reference Display
            $formField = new FormFieldDisplayReference();
            $formConfig = new SimpleXMLElement("<base></base>");
            $formField->setData(
                $formConfig
                ,$referenceArray[0]
                ,$pageObjectsXml
            );
            $formField->setDefaultValue("");
            $formField->setSource("");
            $ReferenceDisplay .= $formField->getSource();

            //Taxonomy Display
            $formField = new FormFieldTaxonomy();
            $formField->setData(
                $formConfig
                ,$referenceArray[0]
                ,$pageObjectsXml
            );
            $formField->setDestination("ReferenceList/");
            $formField->setDefaultValue("");
            $formField->setSource("");
            if ($formField->getSource(true) != "None")
            {
                $ReferenceDisplay .= "<b>Folksonomies:</b> " . $formField->getSource(true);
                HeaderFooter::$keywords .= $formField->getTaxonomyString().",";
            }
            elseif ($memeId || $parentreferenceid || $childreferenceid)
            {
                $ReferenceDisplay .= "&nbsp;";
            }
                
            $modalClass = "";
            if ($modal)
            {
                $modalClass = "modal";
            }
            
            $titleDisplay = "";
            $disassociateLink = "";
            if ($memeId || $parentreferenceid || $childreferenceid)
            {
                $parentid = null;
                if ($memeId)
                {
                    $parentid = "&memeid=".$memeId;
                }
                elseif ($parentreferenceid)
                {
                    $parentid = "&parentreferenceid=".$parentreferenceid;
                }
                
                $titleDisplay = '<a href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'Reference/'
                    . 'id='
                    . $pageObjectsXml->ReferenceList->Reference->Id
                    . '">'
                    .'<img src="'.ROOT_FOLDER.'framework/images/reference.gif"'
                    .' width="13" height="13" border="0"/>' 
                	."&nbsp;<b>".$pageObjectsXml->ReferenceList->Reference->Title."</b></a><br/>";

                if (CuratorSession::checkEditPrivileges(
                        $pageObjectsXml->ReferenceList->Reference->Curator->Id
                    )
                    && !$modal
                    && $parentid
                )
                {
                    $disassociateLink =
                        '<span style="float:right;" class="menulink">'
                    	."<a href=\"javascript:void(0)\""
                    	." onClick=\""
                    	."getContent('"
                    	. ROOT_FOLDER . "framework/api/processForm.php"
                    	."','"
                        . "application="
                        . Constants::getConstant('CURRENT_APPLICATION')
                        . "&pageCode=ParentChild"
                        . "&function=disassociate"
                        . "&referenceid="
                        . $pageObjectsXml->ReferenceList->Reference->Id
                        . $parentid
                    	."','processFormCallback'"
                    	.");"
                    	."\">disassociate</a>"
                    	."</span><br/>";
                }
            }
            
            $ReferenceDisplay =
                $ReferenceImageDisplay 
            	."<div class=\"{$modalClass}referencedivlistitem\"$ReferenceDivWidth>"
                .$titleDisplay
                .$ReferenceDisplay
                .$disassociateLink
                ."</div>"
                .$ReferenceImageClosingDiv
                .'<div style="clear:both"></div>';
        }
        else
        {
            if ($memeId
                && CuratorSession::checkEditPrivileges($ownerid)
                && !$modal
            )
            {
                $ReferenceDisplay =
                    '<link rel="stylesheet" type="text/css" href="'.ROOT_FOLDER.'framework/css/subModal.css" />'
                	. '<div class="pagination">'
                    . '<a class="submodal-600-525"'
                	.' style="display:none;"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'ReferenceModal/'
                    . 'memeid='
                    . $memeId
                    . '">'
                    . 'Add New Reference'
                	. '</a>'
                    . '<a class="submodal-600-525"'
                	.' style="display:none;"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'ReferenceListModal/'
                    . 'memeid='
                    . $memeId
                    . '">'
                    . 'Add Existing Reference'
                	. '</a>'
                	. '</div><br/>';
                JavaScript::addJavaScriptInclude("subModal");
                CascadingStyleSheets::addCascadingStyleSheetsInclude("subModal");
            }
        }
        return $ReferenceDisplay;
    }

    /**
     * Builds an editable Reference Form.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     */
    public static function getForm($formArray,$pageObjectsXml)
    {
        $referenceArray = $pageObjectsXml->xpath(
            $formArray
                ->ReferenceBlock
                    ->recordDataXpath
        );
        if (count($referenceArray) == 0)
        {
            $referenceArray[0] = new SimpleXMLElement("<base></base>");
        }
        //BEGIN REFERENCE TYPE FIELDSET CONSTRUCTION
        $ReferenceTypeFieldSet = new HtmlFormFieldSet("Reference Type & Date","ReferenceType");
        //REGISTER TABLE CONFIG WITH FORMTABLE OBJECT
        $ReferenceTypeFieldSet->setFormConfiguration($formArray->ReferenceTypeBlock);
        $ReferenceTypeFieldSet->setFormData($referenceArray[0]);
        $ReferenceTypeFieldSet->setPageObjectsXml($pageObjectsXml);
        //BUILDS THE VIEW/EDIT TABLES
        $ReferenceTypeFieldSet->appendFormFieldSet();
        // END REFERENCE TABLE CONSTRUCTION
        
        // Authors Table
        $authorsTable = new HtmlTable('Authors', 'Authors');
        $authorsTable->setPageObjectsXml($pageObjectsXml);
        $authorsTable->setFormConfiguration($formArray->AuthorsTable);
        $authorsTable->setEditPrivileges(true);
        $authorsTable->appendFormTable
        (
            $pageObjectsXml->xpath
            (
                 $formArray->AuthorsTable->recordDataXpath
            )
        );
        
        //BEGIN REFERENCE FIELDSET CONSTRUCTION
        $ReferenceFieldSet = new HtmlFormFieldSet("Reference","Reference");
        //REGISTER TABLE CONFIG WITH FORMTABLE OBJECT
        $ReferenceFieldSet->setFormConfiguration($formArray->ReferenceBlock);
        $ReferenceFieldSet->setFormData($referenceArray[0]);
        $ReferenceFieldSet->setPageObjectsXml($pageObjectsXml);
        //BUILDS THE VIEW/EDIT TABLES
        $ReferenceFieldSet->appendFormFieldSet();
        // END REFERENCE TABLE CONSTRUCTION
        
        return $ReferenceTypeFieldSet->getFieldSetSource()
            ."<br/>"
            .$authorsTable->getFormTableSource()
            .$ReferenceFieldSet->getFieldSetSource();
    }

    /**
     * Builds a list of References in Div blocks.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     * @param boolean $modal Modal window or not.
     * @param integer $memeId Associative meme id.
     * @param integer $schemaId Parent schema id.
     * @param integer $referenceId Parent reference id.
     */
    public static function getList(
        $formArray
        ,$pageObjectsXml
        ,$modal=null
        ,$memeId=null
        ,$parentReferenceId=null
    )
    {
        $listSource = "";
        $setdata = $pageObjectsXml->xpath(
            $formArray
                ->ReferenceListTable
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
            $listSource .= "<div class=\"{$modalClass}referencedivlistitem\">";
            $datePublished = null;
            $imageset = false;
            //LOOP THOUGH FORM ELEMENTS
            foreach($formArray->ReferenceListTable->formfield as $formfield)
            {
                $htmlFormField = FormFieldFactory::setFormfield($formfield,$rowdata,$pageObjectsXml);
                $htmlFormField->setSource();
                switch ((string) $formfield->label)
                {
                    case "SmallImage":
                        if (trim($htmlFormField->getSource(true)) != "&nbsp;")
                        {
                            $listSource .= 
                            	'<span style="float:right;position:relative;top:-5px;right:-5px;">' 
                                .$htmlFormField->getSource(true)
                                .'</span>';
                            $imageset = true;
                        }
                        break;
                    case "Date Published":
                        $datePublishedSource = $htmlFormField->getSource(true);
                        if (trim($datePublishedSource) != "31 DEC 1969")
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
                            '<h2><img src="'.ROOT_FOLDER.'framework/images/reference.gif" width="13" height="13" border="0"/>' 
                        	."&nbsp;".$htmlFormField->getSource(true)."</h2>";
                        break;
                    case "Reference":
                        $listSource .= $htmlFormField->getSource(true);
                        break;
                    case "Folksonomies":
                        $source = $htmlFormField->getSource(true);
                        if (trim($source) != "None")
                        {
                            $listSource .= "<b>Folksonomies:</b> ".$source;
                        }
                        
                        $br = "";
                        $clearBoth = "";
                        if ($imageset)
                        {
                            $br = "<br/>";
                            $clearBoth = '<div style="clear:both"></div>';
                        }
                        
                        if ($memeId || $parentReferenceId)
                        {
                            if (trim($source) == "None")
                            {
                                $listSource .= "&nbsp;";
                            }
                            
                            if ($memeId)
                            {
                                $parentId = "&memeid=".$memeId;
                            }
                            elseif ($parentReferenceId)
                            {
                                $parentId = "&parentreferenceid=".$parentReferenceId;
                            }
                            
                            $listSource .= "$clearBoth<span style=\"float:right;position:relative;right:-5px;\" class=\"menulink\">"
                            	."<a href=\"javascript:void(0)\""
                            	." onClick=\""
                            	."getContent('"
                            	. ROOT_FOLDER . "framework/api/processForm.php"
                            	."','"
                                . "application="
                                . Constants::getConstant('CURRENT_APPLICATION')
                                . "&pageCode=ParentChild"
                                . "&function=associate"
                                . $parentId
                                . "&referenceid="
                                . $rowdata->Id
                            	."','processFormCallback'"
                            	.");"
                            	."\">associate</a>"
                            	."</span>$br";
                        }
                        $listSource .=
                            $clearBoth;
                        break;
                    case "MemeCount":
                        $listSource .= "<span style=\"float:right;;position:relative;top:-20px;left:-70px;\">".$htmlFormField->getSource(true)."</span>";
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
