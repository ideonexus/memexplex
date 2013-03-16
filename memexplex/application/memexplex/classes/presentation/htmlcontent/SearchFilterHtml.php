<?php

/**
 * Functions to build HTML for search filter forms.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Mister Mxyzptlk
 */
class SearchFilterHtml
{

    /**
     * Builds a search filter form.
     */
    public static function getSearchFilter($formArray,$pageObjectsXml)
    {
        $filterSource = "";
        //BUILD FILTER FORM
        $filterForm = new HtmlForm;
        if (!Constants::getConstant('AJAX_METHOD'))
        {
            $filterForm->setFormConfiguration($formArray);
            $formBlock = new HtmlFormBlock();
            $formBlock->setFormConfiguration($formArray->filter);
        }
        else
        {
            $filterForm->setFormConfiguration($formArray->ajaxCall);
            $formBlock = new HtmlFormBlock();
            $formBlock->setFormConfiguration($formArray->ajaxCall->ajaxCall);
        }
        $formBlock->setPageObjectsXml($pageObjectsXml);
        $formBlock->appendFormBlock();

        $filterForm->appendFormContent($formBlock->getBlockSource(false));
        $filterForm->buildForm();
        $filterSource = $filterForm->getSource();
        return $filterSource;
    }

}
