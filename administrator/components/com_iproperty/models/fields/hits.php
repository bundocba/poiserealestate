<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

class JFormFieldHits extends JFormField
{
	protected $type = 'Hits';

	protected function getInput()
    {
		$item = JRequest::getInt('id');
        if(!$item) return;
        
        // define variables
        $db     = JFactory::getDbo();
        $document = JFactory::getDocument();
        
        // See if hits exist
        $query = $db->getQuery(true);
        $query->select('hits')
            ->from('#__iproperty')
            ->where('id = '.$item); 
        $db->setQuery($query);
        
        $js = "
        (function($) {
            resetHits = function(){
                var checkurl = '".JURI::base('true')."/index.php?option=com_iproperty&task=ajax.resetHits';
                var propId = ".$item.";

                req = new Request({
                    method: 'post',
                    url: checkurl,
                    data: { 'prop_id': propId,
                            '".JSession::getFormToken()."':'1',
                            'format': 'raw'},
                    onRequest: function() {
                        document.id('hits_msg').set('html', '');
                        document.id('hits_msg').set('class', 'loading_div');
                    },
                    onSuccess: function(response) {
                        if(response){
                            document.id('hits_msg').set('class', 'alert alert-success');
                            document.id('jform_hits').value = '0';
                            document.id('hits_msg').set('html', response);                    
                        }else{
                            document.id('hits_msg').set('class', 'alert alert-error');
                            document.id('hits_msg').set('html', '".JText::_('COM_IPROPERTY_COUNTER_NOT_RESET')."');
                        }
                    }
                }).send();
            }
        })(jQuery);";
        
        $document->addScriptDeclaration($js);
        
        
        if($result = $db->loadResult())
        {
            ?>

            <div><div id="hits_msg"></div></div>
            <div class="control-group form-horizontal">
                <div class="control-label">
                    <input type="text" class="readonly input-small" id="jform_hits" value="<?php echo $result; ?>" />
                </div>
                <div class="controls">
                    <a class="btn btn-warning" onclick="resetHits();"><?php echo JText::_('COM_IPROPERTY_RESET'); ?></a>
                </div>
            </div>
            
            <?php
        }else{
            echo '0';
        }
	}
}