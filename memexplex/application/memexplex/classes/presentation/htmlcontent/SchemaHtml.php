<?php

/**
 * A collection of functions to build HTML displays for schemas.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Jimmy Neutron
 */
class SchemaHtml
{

    /**
     * Builds a displayable Schema.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     * @param boolean $modal Modal window display or not.
     * @param integer $memeId Database Id for the meme association.
     */
    public static function getBlock($formArray,$pageObjectsXml,$modal=null)
    {
        $schemaArray = $pageObjectsXml->xpath
        (
            $formArray
                ->SchemaBlock
                    ->recordDataXpath
        );

        if ($pageObjectsXml->SchemaList->Schema->Id)
        {
            //Set Page Header
            HeaderFooter::$title = 'Schema: '.str_replace('"','',strip_tags($pageObjectsXml->SchemaList->Schema->Title));
            HeaderFooter::$description = str_replace('"','',strip_tags($pageObjectsXml->SchemaList->Schema->Description));
            
            if (count($schemaArray) == 0)
            {
                $schemaArray[0] = new SimpleXMLElement("<base></base>");
            }
            $modalClass = "";
            if ($modal)
            {
                $modalClass = "modal";
            }
            $SchemaDisplay = '<div class="titleblock"><h1>'.$pageObjectsXml->SchemaList->Schema->Title.'</h1></div>'
                .'<div class="'.$modalClass.'schemadivlistitem">'.$pageObjectsXml->SchemaList->Schema->Description.'</div>';
            $formConfig = new SimpleXMLElement("<base></base>");

            $formField = new FormFieldTaxonomy();
            $formField->setData
            (
                $formConfig
                ,$schemaArray[0]
                ,$pageObjectsXml
            );
            $formField->setDestination("SchemaList/");
            $formField->setDefaultValue("");
            $formField->setSource("");
            if ($formField->getSource(true) != "None")
            {
                $SchemaDisplay .= "<br/><b>Folksonomies:</b> " . $formField->getSource(true);
                HeaderFooter::$keywords .= $formField->getTaxonomyString().",";
            }

        }

        return $SchemaDisplay;
    }

    /**
     * Builds an editable Schema Form.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     */
    public static function getForm($formArray,$pageObjectsXml)
    {
        $schemaArray = $pageObjectsXml->xpath
        (
            $formArray
                ->SchemaBlock
                    ->recordDataXpath
        );
        if (count($schemaArray) == 0)
        {
            $schemaArray[0] = new SimpleXMLElement("<base></base>");
        }
        //BEGIN REFERENCE TABLE CONSTRUCTION
        $SchemaFieldSet = new HtmlFormFieldSet("Schema","Schema");
        //REGISTER TABLE CONFIG WITH FORMTABLE OBJECT
        $SchemaFieldSet->setFormConfiguration($formArray->SchemaBlock);
        $SchemaFieldSet->setFormData($schemaArray[0]);
        $SchemaFieldSet->setPageObjectsXml($pageObjectsXml);
        //BUILDS THE VIEW/EDIT TABLES
        $SchemaFieldSet->appendFormFieldSet();
        // END REFERENCE TABLE CONSTRUCTION
        return $SchemaFieldSet->getFieldSetSource();
    }

    /**
     * Builds a list of Memes in Div blocks.
     * 
     * @param SimpleXmlObject $formArray The form configuration.
     * @param SimpleXmlObject $pageObjectsXml Data for the page.
     */
    public static function getList(
        $formArray
        ,$pageObjectsXml
        ,$modal=null
        ,$memeId=null
        ,$parentSchemaId=null
    )
    {
        $listSource = "";
        $setdata = $pageObjectsXml->xpath
            (
                $formArray
                    ->SchemaListTable
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
            $listSource .= "<div class=\"{$modalClass}schemadivlistitem\">";
            $datePublished = null;
            //LOOP THOUGH FORM ELEMENTS
            foreach($formArray->SchemaListTable->formfield as $formfield)
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
                            '<h2><img src="'.ROOT_FOLDER.'framework/images/schema.gif" width="13" height="13" border="0"/>' 
                        	."&nbsp;".$htmlFormField->getSource(true)."</h2>";
                        break;
                    case "Description":
                        $listSource .= $htmlFormField->getSource(true);
                        break;
                    case "Folksonomies":
                        $source = $htmlFormField->getSource(true);
                        if (trim($source) != "None")
                        {
                            $listSource .= "<br/><b>Folksonomies:</b> ".$source;
                        }
                        if ($memeId || $parentSchemaId)
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
                            
                            if ($memeId)
                            {
                                $parentId = "&memeid=".$memeId;
                            }
                            elseif ($parentSchemaId)
                            {
                                $parentId = "&parentschemaid=".$parentSchemaId;
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
                                . "&schemaid="
                                . $rowdata->Id
                            	."','processFormCallback'"
                            	.");"
                            	."\">{$dis}associate</a>"
                            	."</span>";
                        }
                        break;
                    case "MemeCount":
                        $listSource .= "<span style=\"float:right;\">".$htmlFormField->getSource(true)."</span>";
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
