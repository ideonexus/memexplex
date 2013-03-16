<?php

/**
 * Builds a TinyMCE FormField
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldTextArea
 * @see FormFieldInterface
 */
class FormFieldTextAreaRich extends FormFieldTextArea
implements FormFieldInterface
{
    /**
     * TinyMCE Commands
     * http://wiki.moxiecode.com/index.php/TinyMCE:Commands
     */
    public function setSource()
    {
        $rootFolder = ROOT_FOLDER;

        //Examples of optional buttons to add to the text editor.
        //theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
        //theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,cleanup,help,code,|,preview,|,forecolor,backcolor",
        //theme_advanced_buttons3 : "tablecontrols,|,visualaid,removeformat,|,sub,sup,|,iespell,media,hr,advhr,",

        //Examples of formats
        //alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
        //aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
        //alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
        //alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},
        //bold : {inline : 'span', 'classes' : 'bold'},
        //italic : {inline : 'span', 'classes' : 'italic'},
        //underline : {inline : 'span', 'classes' : 'underline', exact : true},
        //strikethrough : {inline : 'del'}

        //Examples of plugins
        //plugins : "pagebreak,layer,table,advhr,advimage,advlink,iespell,inlinepopups,preview,media,searchreplace,contextmenu,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist",

        $this->source .=
<<<EOD
<!-- TinyMCE -->
<script type="text/javascript">
addLoadEvent(function() {
	tinyMCE.init({
		// General options
		mode : "none",
		elements : "{$this->id}",
		theme : "advanced",
		plugins : "table",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,sub,sup,|,backcolor,forecolor,fontselect,fontsizeselect,",
		theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,tablecontrols",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",

		// Example content CSS (should be your site CSS)
		content_css : "{$rootFolder}framework/css/tinymce_style.css"
	});
});

function {$this->id}activateTinyMCE() {
	if (tinyMCE.getInstanceById('{$this->id}')){
    	tinyMCE.get('{$this->id}').show();
    }else{
    	tinyMCE.execCommand("mceAddControl", true, "{$this->id}");
    }
}

function {$this->id}triggerTinyMCESave() {
	if (tinyMCE.getInstanceById('{$this->id}')){
    	if (tinyMCE.get('{$this->id}').isHidden()!=true){
    		tinyMCE.triggerSave(true,true);
    		tinyMCE.get('{$this->id}').hide();
        }
    }
}

function {$this->id}triggerTinyMCEUnsubscribe() {
	FormDeltaCheckObserver.unsubscribe({$this->id}triggerTinyMCESave);
	if (tinyMCE.getInstanceById('{$this->id}')){
        tinyMCE.execCommand('mceFocus', false, '{$this->id}');
        tinyMCE.execCommand('mceRemoveControl', false, '{$this->id}');
    }
    ajaxGetHTMLObserver.unsubscribe(this);
}

addLoadEvent(function() {
	setTimeout('FormDeltaCheckObserver.subscribe({$this->id}triggerTinyMCESave)',200);
	setTimeout('ajaxGetHTMLObserver.subscribe({$this->id}triggerTinyMCEUnsubscribe)', 200);
});
</script>
EOD;

        $wysiwygWidth = $this->cols * 8.3;
        $textareaValue = $this->defaultValue;

        //FUNCTION REMOVES TINYMCE CONTROL ON SUCCESSFUL FORM
        //EXECUTION, CLEARING THE WAY FOR THE REFRESHED FORMELEMENT.
        $this->source .=
<<<EOD
<!-- /TinyMCE -->
<div>
	<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
	<div>
		<textarea id="{$this->id}" name="{$this->id}" rows="{$this->rows}" cols="{$this->cols}" style="width: {$wysiwygWidth}px;">{$textareaValue}</textarea>
	</div>

	<!-- Some integration calls -->
	<a href="javascript:void(0);" onmousedown="{$this->id}activateTinyMCE()">HTML Editor</a>
	/ <a href="javascript:void(0);" onmousedown="tinyMCE.get('{$this->id}').hide();">Plain Text</a>
</div>
EOD;

        JavaScript::addJavaScriptInclude("tiny_mce");
    }
}
