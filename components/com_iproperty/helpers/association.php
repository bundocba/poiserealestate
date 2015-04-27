<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */


defined( '_JEXEC' ) or die( 'Restricted access');

JLoader::register('IpropertyHelper', JPATH_ADMINISTRATOR . '/components/com_iproperty/helpers/iproperty.php');


abstract class IpropertyHelperAssociation
{
	public static function getAssociations($id = 0, $view = null)
	{	
        jimport('helper.route', JPATH_COMPONENT_SITE); 
        require_once(JPATH_COMPONENT_SITE.'/helpers/html.helper.php');

		$app            = JFactory::getApplication();
		$jinput         = $app->input;
        $menu           = $app->getMenu();
        $active         = $menu->getActive();
        
		$view           = is_null($view) ? $jinput->get('view') : $view;
		$id             = empty($id) ? $jinput->getInt('id') : $id;
        $associations   = array();
        $default_lang   = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        
        $return = array();
        
        $basic_array = array(
            'advsearch' => 'getAdvsearchRoute',
            'agents' => 'getAgentsRoute',
            'allproperties' => 'getAllPropertiesRoute',
            'companies' => 'getCompaniesRoute',
            'home' => 'getHomeRoute',
            'ipuser' => 'getIpuserRoute',
            'manage' => 'getManageRoute',
            'openhouses' => 'getOpenHousesRoute');
        
        // If the active menu item is in the array of IP items that need no id, then return the path 
        // from menu item associations
        if($active){
            if(array_key_exists($view, $basic_array)){
                $associations   = MenusHelper::getAssociations($active->id);
                $route          = $basic_array[$view];
                
                foreach($associations as $tag => $item)
                {
                    $return[$tag] = ipropertyHelperRoute::$route($tag);
                }
            }
        }
        
        // else, continue with finding the menu item type and Falang associations and basic associations
        // for any other IP views that require an id
        
        if ($view == 'agentproperties' && $id)
		{              
            $associations = ipropertyHTML::getFalangAssociations('iproperty_agents', $id);	
            if(!array_key_exists($default_lang, $associations)){
                $associations[$default_lang] = IpropertyHelperAssociation::getDefaultAssociations('iproperty_agents', $id);                    
            }                

            foreach ($associations as $tag => $item)
            {
                $return[$tag] = ipropertyHelperRoute::getAgentPropertyRoute($id.':'.$item, $tag);
            }
		}       
        
        if ($view == 'cat' && $id)
		{               
            $associations = ipropertyHTML::getFalangAssociations('iproperty_categories', $id);
            if(!array_key_exists($default_lang, $associations)){
                $associations[$default_lang] = IpropertyHelperAssociation::getDefaultAssociations('iproperty_categories', $id);                    
            }                

            foreach ($associations as $tag => $item)
            {
                $return[$tag] = ipropertyHelperRoute::getCatRoute($id.':'.$item, false, $tag);
            }
		}
        
        if ($view == 'companyagents' && $id)
		{              
            $associations = ipropertyHTML::getFalangAssociations('iproperty_companies', $id);	
            if(!array_key_exists($default_lang, $associations)){
                $associations[$default_lang] = IpropertyHelperAssociation::getDefaultAssociations('iproperty_companies', $id);                    
            }                

            foreach ($associations as $tag => $item)
            {
                $return[$tag] = ipropertyHelperRoute::getCompanyAgentRoute($id.':'.$item, $tag);
            }
		}
        
        if ($view == 'companyproperties' && $id)
		{              
            $associations = ipropertyHTML::getFalangAssociations('iproperty_companies', $id);	
            if(!array_key_exists($default_lang, $associations)){
                $associations[$default_lang] = IpropertyHelperAssociation::getDefaultAssociations('iproperty_companies', $id);                    
            }                

            foreach ($associations as $tag => $item)
            {
                $return[$tag] = ipropertyHelperRoute::getCompanyPropertyRoute($id.':'.$item, $tag);
            }
		}
        
        if ($view == 'contact' && $id)
		{
            if($jinput->get('layout') == 'agent'){
                $table  = 'iproperty_agents';
                $type   = 'agent';
            }else{
                $table  = 'iproperty_companies';
                $type   = 'company';
            }
               
            $associations = ipropertyHTML::getFalangAssociations($table, $id);	
            if(!array_key_exists($default_lang, $associations)){
                $associations[$default_lang] = IpropertyHelperAssociation::getDefaultAssociations($table, $id);                    
            }                

            foreach ($associations as $tag => $item)
            {
                $return[$tag] = ipropertyHelperRoute::getContactRoute($type, $id.':'.$item, $tag);
            }
		}

		if ($view == 'property' && $id)
		{
            $associations = ipropertyHTML::getFalangAssociations('iproperty', $id);		
            if(!array_key_exists($default_lang, $associations)){
                $associations[$default_lang] = IpropertyHelperAssociation::getDefaultAssociations('iproperty', $id);                    
            }

            foreach ($associations as $tag => $item)
            {
                $return[$tag] = ipropertyHelperRoute::getPropertyRoute($id.':'.$item, null, false, $tag);
            }
		}
        return $return;
	}
    
    protected function getDefaultAssociations($reftable, $id, $reffield = 'alias')
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        
        $query->select($reffield)
                ->from('#__'.$reftable)
                ->where('id = '.(int)$id)
                ->where('state = 1');

        $db->setQuery($query, 0, 1);

        try
        {
            $item = $db->loadResult();
        }
        catch (RuntimeException $e)
        {
            throw new Exception($e->getMessage(), 500);
        }

        if ($item)
        {
            return $item;
        }
    }
}

/*
// Falang helper to customize falang module routing
// Only uncomment if using the Falang language switcher module
// The following modification must be made to have effect:

//## Begin Falang module modification
///////////////////////////////////////////////////////////////////////////////
//Open the 'modules/mod_falang/mod_falang.php' file and change:

//$list       =  modFalangHelper::getList($params);

//TO:

//$extension  = JFactory::getApplication()->input->getCmd('option');
//$helper     = (class_exists($extension.'FalangHelper')) ? $extension.'FalangHelper' : 'modFalangHelper';
//$list       =  $helper::getList($params);
//////////////////////////////////////////////////////////////////////////////////

require_once(JPATH_SITE.'/modules/mod_falang/helper.php');

class com_ipropertyFaLangHelper extends modFalangHelper
{
    public static function getList(&$params)
	{        
        $lang = JFactory::getLanguage();
		$languages	= JLanguageHelper::getLanguages();
		$app		= JFactory::getApplication();
        
        $ignore_array = array('advsearch','agentform','agents','allproperties','companies','companyform','feed','home','ipuser','manage','openhouseform','openhouses','propform');
        if($app->input->get('option') == 'com_iproperty' && in_array($app->input->get('view'), $ignore_array)){
            return parent::getList($params);
        }
        
        //use to remove default language code in url
        $lang_codes 	= JLanguageHelper::getLanguages('lang_code');
        $default_lang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        $default_sef 	= $lang_codes[$default_lang]->sef;


        $menu = $app->getMenu();
        $active = $menu->getActive();
        $uri = JURI::getInstance();

        if (FALANG_J30) {
            $assoc = isset($app->item_associations) ? $app->item_associations : 0;
        } else {
            $assoc = $app->get('menu_associations', 0);
        }

		if ($assoc) {
            $ipassociations = IpropertyHelperAssociation::getAssociations($app->input->getInt('id'), $app->input->get('view'));
            //if ($active) {
				//$associations = MenusHelper::getAssociations($active->id);
			//}
		}
        
   		foreach($languages as $i => &$language) 
        {
			// Do not display language without frontend UI
			if (!JLanguage::exists($language->lang_code)) {
				unset($languages[$i]);
			}
            if (FALANG_J30) {
                $language_filter = JLanguageMultilang::isEnabled();
            } else {
                $language_filter = $app->getLanguageFilter();
            }
            
            //set language active before language filter use for sh404 notice
            $language->active =  $language->lang_code == $lang->getTag();
            if ($language_filter) {
                if (isset($ipassociations[$language->lang_code])) {
                    //var_dump('here1'.$ipassociations[$language->lang_code]);
                    if ($app->getCfg('sef')=='1') {
                        $language->link = JRoute::_($ipassociations[$language->lang_code]);
                    }
                    else {
                        $language->link = $ipassociations[$language->lang_code];
                    }
                }else if (isset($associations[$language->lang_code]) && $menu->getItem($associations[$language->lang_code])) {
                    $itemid = $associations[$language->lang_code];
                    
                    //var_dump('here2'.$associations[$language->lang_code]);
                    if ($app->getCfg('sef')=='1') {
                        $language->link = JRoute::_('index.php?lang='.$language->sef.'&Itemid='.$itemid);
                    }
                    else {
                        $language->link = 'index.php?lang='.$language->sef.'&Itemid='.$itemid;
                    }
                } else {
                    //sef case
                    //var_dump('here3');
                    if ($app->getCfg('sef')=='1') {

                         //$uri->setVar('lang',$language->sef);
                         $router = JApplication::getRouter();
                         $tmpuri = clone($uri);

                         $router->parse($tmpuri);

                         $vars = $router->getVars();
                         //workaround to fix index language
                         $vars['lang'] = $language->sef;

                        //case of category article
                        if (!empty($vars['view']) && $vars['view'] == 'article' && !empty($vars['option']) && $vars['option'] == 'com_content') {

                            if (FALANG_J30){
                                JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
                                $model =& JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request'=>true));
                                $appParams = JFactory::getApplication()->getParams();
                            } else {
                                JModel::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
                                $model =& JModel::getInstance('Article', 'ContentModel', array('ignore_request'=>true));
                                $appParams = JFactory::getApplication()->getParams();
                }


                            $model->setState('params', $appParams);

                            //in sef some link have this url
                            //index.php/component/content/article?id=39
                            //id is not in vars but in $tmpuri
                            if (empty($vars['id'])) {
                                $tmpid = $tmpuri->getVar('id');
                                if (!empty($tmpid)) {
                                    $vars['id'] = $tmpuri->getVar('id');
                                } else {
                                    continue;
            }
        }

                            $item =& $model->getItem($vars['id']);

                            //get alias of content item without the id , so i don't have the translation
                            $db = JFactory::getDbo();
                            $query = $db->getQuery(true);
                            $query->select('alias')->from('#__content')->where('id = ' . (int) $item->id);
                            $db->setQuery($query);
                            $alias = $db->loadResult();

                            $vars['id'] = $item->id.':'.$alias;
                            $vars['catid'] =$item->catid.':'.$item->category_alias;
                        }

                        $url = 'index.php?'.JURI::buildQuery($vars);
                        $language->link = JRoute::_($url);

                        //TODO check performance 3 queries by languages -1
                        //
                        // Replace the slug from the language switch with correctly translated slug.
                        // $language->lang_code language de la boucle (icone lien)
                        // $lang->getTag() => language en cours sur le site
                        // $default_lang langue par default du site
                        //
                        if($lang->getTag() != $language->lang_code && !empty($vars['Itemid']))
                        {
                            $fManager = FalangManager::getInstance();
                            $id_lang = $fManager->getLanguageID($language->lang_code);
                            $db = JFactory::getDbo();
                            // get translated path if exist
                            $query = $db->getQuery(true);
                            $query->select('fc.value')
                                ->from('#__falang_content fc')
                                ->where('fc.reference_id = '.(int)$vars['Itemid'])
                                ->where('fc.language_id = '.(int) $id_lang )
                                ->where('fc.reference_field = \'path\'')
                                ->where('fc.reference_table = \'menu\'');
                            $db->setQuery($query);
                            $translatedPath = $db->loadResult();

                            // $translatedPath not exist if not translated or site default language
                            // don't pass id to the query , so no translation given by falang
                            $query = $db->getQuery(true);
                            $query->select('m.path')
                                ->from('#__menu m')
                                ->where('m.id = '.(int)$vars['Itemid']);
                            $db->setQuery($query);
                            $originalPath = $db->loadResult();

                            $pathInUse = null;
                            //si on est sur une page traduite on doit récupérer la traduction du path en cours
                            if ($default_lang != $lang->getTag() ) {
                                $id_lang = $fManager->getLanguageID($lang->getTag());
                                // get translated path if exist
                                $query = $db->getQuery(true);
                                $query->select('fc.value')
                                    ->from('#__falang_content fc')
                                    ->where('fc.reference_id = '.(int)$vars['Itemid'])
                                    ->where('fc.language_id = '.(int) $id_lang )
                                    ->where('fc.reference_field = \'path\'')
                                    ->where('fc.reference_table = \'menu\'');
                                $db->setQuery($query);
                                $pathInUse = $db->loadResult();

                            }

                            if (!isset($translatedPath)) {
                                $translatedPath = $originalPath;
                            }

                            // not exist if not translated or site default language
                            if (!isset($pathInUse)) {
                                $pathInUse = $originalPath ;
                            }

                            //make replacement in the url

                            //si language de boucle et language site
                            if($language->lang_code == $default_lang) {
                                if (isset($pathInUse) && isset($originalPath)){
                                    $language->link = str_replace($pathInUse, $originalPath, $language->link);
                                }
                            } else {
                                if (isset($pathInUse) && isset($translatedPath)){
                                    $language->link = str_replace($pathInUse, $translatedPath, $language->link);
                                }
                            }

                        }
                    }
                }
            }
        }
		return $languages;
	}    
}*/
