<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport( 'joomla.application.component.view');

class IpropertyViewOpenhouses extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app        = JFactory::getApplication();
		$doc        = JFactory::getDocument();
		$params     = $app->getParams();
        $settings   = ipropertyAdmin::config();
		$feedEmail  = $app->getCfg('feed_email', 'author');
		$siteEmail  = $app->getCfg('mailfrom');        
        
        // Set order, direction, and limit for feed
        $app->input->set('filter_order', 'p.publish_up');
        $app->input->set('filter_order_Dir', 'DESC');
        $app->input->set('limit', $settings->rss);
        
        // Get some data from the model	
		$company    = IpropertyHTML::buildCompany($app->input->get('id', '', 'uint'));
		$rows       = $this->get('Items');

		$doc->link = JRoute::_(ipropertyHelperRoute::getOpenHousesRoute());

		foreach ($rows as $row)
		{
			// Strip html from feed item title
            $title = ($row->ohname) ? $row->ohname : $row->street_address;
            $title .= ' - ' . $row->formattedprice;
			$title = $this->escape($title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Url link
			$link = $row->proplink;

			// Get row fulltext
			$row->fulltext = ipropertyHTML::snippet($row->short_description);

			// Get description, author and date
			$description = ipropertyHTML::snippet($row->short_description);
			$author = "Intellectual Property - The Thinkery LLC";
			@$date = ($row->enddate ? date('r', strtotime($row->enddate)) : '');

			// Load individual item creator class
			$item           = new JFeedItem;
			$item->title    = $title;
			$item->link     = $link;
			$item->date     = $date;
			$item->author   = $author;
            $item->category = $row->typename;

			if ($feedEmail == 'site')
			{
				$item->authorEmail = $siteEmail;
			}

			// Add readmore link to description if introtext is shown, show_readmore is true and fulltext exists
			if (!$params->get('feed_summary', 0) && $params->get('feed_show_readmore', 0) && $row->fulltext)
			{
				$description .= '<p class="feed-readmore"><a target="_blank" href ="' . $item->link . '">' . JText::_('COM_CONTENT_FEED_READMORE') . '</a></p>';
			}

			// Load item description and add div
			$item->description	= '<div class="feed-description">'.$description.'</div>';

			// Loads item info into rss array
			$doc->addItem($item);
		}
	}
}
