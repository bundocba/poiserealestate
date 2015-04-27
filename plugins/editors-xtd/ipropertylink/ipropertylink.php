<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgButtonIpropertyLink extends JPlugin
{
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }
    
    public function onDisplay($name)
	{
        $app    = JFactory::getApplication();
        $vlink  = ($app->getName() == 'site') ? 'allproperties' : 'properties';
        
        $script = "
        function ipSelectListing_" . $name . "(id, title, link) {
            var tag = '<a href=\"' + link + '\">' + title + '</a>';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);
        
        JHTML::_('behavior.modal');
        
        /*
		 * Use the built-in element view to select the listing.
		 * Currently uses blank class.
		 */
        $link = 'index.php?option=com_iproperty&amp;view='.$vlink.'&amp;layout=modal&amp;tmpl=component&amp;'.JSession::getFormToken().'=1&field=' . $name;


		$button = new JObject;
		$button->modal = true;
        $button->class = 'btn';
		$button->link = $link;
		$button->text = JText::_('PLG_IP_LINK_SELECT_LISTING');
		$button->name = 'file-add';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}
}