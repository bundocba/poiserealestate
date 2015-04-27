<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

class PlgFinderIPproperties extends FinderIndexerAdapter
{
	protected $context          = 'IPproperties';
	protected $extension        = 'com_iproperty';
	protected $type_title       = 'Property';
	protected $table            = '#__iproperty';
	protected $state_field      = 'state';
    protected $layout           = 'property';
    protected $autoloadLanguage = true;    

	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'com_iproperty.property')
		{
			$id = $table->id;
		}
		elseif ($context == 'com_finder.index')
		{
			$id = $table->link_id;
		}
		else
		{
			return true;
		}
		// Remove the items.
		return $this->remove($id);
	}

	public function onFinderAfterSave($context, $row, $isNew)
	{		
        // We only want to handle IP properties here
		if ($context == 'com_iproperty.property')
		{
			// Check if the access levels are different
			if (!$isNew && $this->old_access != $row->access)
			{
				// Process the change.
				$this->itemAccessChange($row);
			}

			// Reindex the item
			$this->reindex($row->id);
		}

		return true;
	}

	public function onFinderBeforeSave($context, $row, $isNew)
	{
		// We only want to handle IP properties here
		if ($context == 'com_iproperty.property')
		{
			// Query the database for the old access level if the item isn't new
			if (!$isNew)
			{
				$this->checkItemAccess($row);
			}
		}

		return true;
	}
    
    public function onFinderChangeState($context, $pks, $value)
	{
		// We only want to handle IP properties here
		if ($context == 'com_iproperty.property')
		{
			$this->itemStateChange($pks, $value);
		}
        
		// Handle when the plugin is disabled
		if ($context == 'com_plugins.plugin' && $value === 0)
		{
			$this->pluginDisable($pks);
		}
	}

	protected function index(FinderIndexerResult $item, $format = 'html')
	{		
        // Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}
        
        // Set the field to use as the title depending on plugin settings
        $title_field    = ($this->params->get('title_field', 'a.title') == 'a.street') ? 'street' : 'title';        
        $default_lang   = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        $mls_id         = ($item->mls_id) ? ' - '.$item->mls_id : '';
        
        // Trigger the onContentPrepare event.
		$item->summary  = FinderIndexerHelper::prepareContent($item->summary);
		$item->body     = FinderIndexerHelper::prepareContent($item->body);	
        
        // Add the meta-data processing instructions.
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'created_by_alias');

        // Translate the state
        $item->state = $this->translateState($item->state);

        // Add the type taxonomy data.
        $item->addTaxonomy('Type', 'Property');
        
        // Add the category taxonomy data.
        $categories = ipropertyHTML::getAvailableCats($item->id);
        foreach($categories as $c){
            $item->addTaxonomy('Property Type', ipropertyHTML::getCatIcon($c, false, true, true));
        }

        // Add the agent taxonomy data.
        $agents = ipropertyHTML::getAvailableAgents($item->id);
        foreach($agents as $a){
            $item->addTaxonomy('Agent', $a->name, $a->state);
        }
        
        // Add the sale type taxonomy data.
		if (!empty($item->stype) && $this->params->get('tax_add_stype', true))
		{
			$item->addTaxonomy('Stype', ipropertyHTML::get_stype($item->stype));
		}
        
        // Add the city taxonomy data.
		if (!empty($item->city) && $this->params->get('tax_add_city', true))
		{
			$item->addTaxonomy('City', $item->city);
		}
        
        // Add the county taxonomy data.
		if (!empty($item->county) && $this->params->get('tax_add_county', true))
		{
			$item->addTaxonomy('County', $item->county);
		}
        
        // Add the region taxonomy data.
		if (!empty($item->region) && $this->params->get('tax_add_region', true))
		{
			$item->addTaxonomy('Region', $item->region);
		}
        
        // Add the province taxonomy data.
		if (!empty($item->province) && $this->params->get('tax_add_province', true))
		{
			$item->addTaxonomy('Province', $item->province);
		}
        
        // Add the state taxonomy data.
		if (!empty($item->locstate) && $this->params->get('tax_add_locstate', true))
		{
			$item->addTaxonomy('Locstate', $item->locstate);
		}
        
        // Add the country taxonomy data.
		if (!empty($item->country) && $this->params->get('tax_add_country', true))
		{
			$item->addTaxonomy('Country', $item->country);
		}
        
        // Call IP helper to see if there are any Falang translations related to this item
        $associations = ipropertyHTML::getFalangAssociations('iproperty', $item->id, $title_field);
        if(!array_key_exists($default_lang, $associations)){
            $associations[$default_lang] = $item->title;                 
        }

        // For each association, create an index item
        foreach($associations as $key => $value)
        { 
            // Title will be either title or street field with mls or ref# tacked on
            $item->title = $value.$mls_id;

            $item->language = $key;
            
            // Build the necessary route and path information.
            $item->url      = $this->getURL($item->id, $this->extension, $this->layout).'&lang='.$key;            
            $item->route    = ipropertyHelperRoute::getPropertyRoute($item->slug, $item->firstcat, false, $key);
            $item->path     = FinderIndexerHelper::getContentPath($item->route);

            // Get content extras.
            FinderIndexerHelper::getContentExtras($item);

            // Index the item.
            $this->indexer->index($item);
        }
	}

	protected function setup()
	{
		// Load dependent classes.
        require_once(JPATH_SITE . '/components/com_iproperty/helpers/route.php');
        require_once(JPATH_SITE . '/components/com_iproperty/helpers/html.helper.php');

		return true;
	}
    
    protected function getItems($offset, $limit, $query = null)
	{
        $items          = array();        

        // Get the property items to index.
		$this->db->setQuery($this->getListQuery($query), $offset, $limit);
		$rows = $this->db->loadAssocList();

		// Convert the items to result objects.
		foreach ($rows as $row)
		{
            // Convert the item to a result object.
            $item = JArrayHelper::toObject($row, 'FinderIndexerResult');
                
            // Set the item type.
            $item->type_id = $this->type_id;

            // Set the mime type.
            $item->mime = $this->mime;

            // Set the item layout.
            $item->layout = $this->layout;               

            // Add the item to the stack.
            $items[] = $item;
		}

		return $items;
	}

	protected function getListQuery($query = null)
	{
		$db = JFactory::getDbo();

		// Check if we can use the supplied SQL query.
		$query = $query instanceof JDatabaseQuery ? $query : $db->getQuery(true)
			->select('a.id AS id, a.title, a.alias, a.short_description AS summary, a.description AS body, a.mls_id')
            ->select($this->params->get('title_field', 'a.title').' as title')
            ->select('a.region, a.city, a.province')
			->select('a.stype, a.state, a.created AS start_date, a.created_by')
			->select('a.modified, a.modified_by')
			->select('a.metakey, a.metadesc, a.access, (SELECT pm.cat_id FROM #__iproperty_propmid AS pm WHERE pm.prop_id = a.id AND amen_id = 0 LIMIT 1) AS first_cat')
			->select('a.publish_up AS publish_start_date, a.publish_down AS publish_end_date');

		// Handle the alias CASE WHEN portion of the query
		$case_when_item_alias = ' CASE WHEN ';
		$case_when_item_alias .= $query->charLength('a.alias', '!=', '0');
		$case_when_item_alias .= ' THEN ';
		$a_id = $query->castAsChar('a.id');
		$case_when_item_alias .= $query->concatenate(array($a_id, 'a.alias'), ':');
		$case_when_item_alias .= ' ELSE ';
		$case_when_item_alias .= $a_id.' END as slug';
		$query->select($case_when_item_alias);
        
		$query->select('u.name AS author')
			->from('#__iproperty AS a')
			->join('LEFT', '#__users AS u ON u.id = a.created_by');

        $query->select('s.title AS locstate')
			->join('LEFT', '#__iproperty_states AS s ON s.id = a.locstate');
        
        $query->select('c.title AS country')
			->join('LEFT', '#__iproperty_countries AS c ON c.id = a.country');

		return $query;
	}
}
