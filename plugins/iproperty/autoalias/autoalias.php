<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.plugin.plugin');
jimport('joomla.html.html');

class plgIpropertyAutoAlias extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
        $this->loadLanguage();
		if(JRequest::getVar('iptask') == 'autoalias'){
            $section = JRequest::getVar('section');
            $this->_createAlias($section);
        }
    }

    public function onAfterRenderTools($user, $settings)
	{
        $app = JFactory::getApplication();
		if($app->getName() != 'administrator') return true;
        
        echo JHtmlBootstrap::addTab('ipAdminTools', 'ipautoalias', JText::_('PLG_IP_AA_AUTOALIAS'));
        echo '
            <a class="btn" href="index.php?option=com_iproperty&iptask=autoalias&section=cat" style="margin-bottom: 5px;">
                '.JHtml::_('image', 'administrator/modules/mod_ip_quickicon/images/icon-ip-cat.png', NULL, NULL, false).'
                <span>'.JText::_('PLG_IP_AA_CATALIAS').'</span>
            </a>
            <a class="btn" href="index.php?option=com_iproperty&iptask=autoalias&section=prop" style="margin-bottom: 5px;">
                '.JHtml::_('image', 'administrator/modules/mod_ip_quickicon/images/icon-ip-prop.png', NULL, NULL, false).'
                <span>'.JText::_('PLG_IP_AA_PROPALIAS').'</span>
            </a>
            <a class="btn" href="index.php?option=com_iproperty&iptask=autoalias&section=agent" style="margin-bottom: 5px;">
                '.JHtml::_('image', 'administrator/modules/mod_ip_quickicon/images/icon-ip-agents.png', NULL, NULL, false).'
                <span>'.JText::_('PLG_IP_AA_AGENTALIAS').'</span>
            </a>
            <a class="btn" href="index.php?option=com_iproperty&iptask=autoalias&section=company" style="margin-bottom: 5px;">
                '.JHtml::_('image', 'administrator/modules/mod_ip_quickicon/images/icon-ip-co.png', NULL, NULL, false).'
                <span>'.JText::_('PLG_IP_AA_COMPANYALIAS').'</span>
            </a>';
        echo JHtmlBootstrap::endTab();
		return true;
	}

    private function _createAlias($section)
    {
        $app    = JFactory::getApplication();
        $db     = JFactory::getDbo();
        $alias  = '';
        $rows   = 0; 
        
        switch($section){
            case 'cat':
                $query = $db->getQuery(true);
                $query->select('id, title');
                $query->from('#__iproperty_categories');
                $query->where('alias = ""');
                
                $db->setQuery($query);
                $result = $db->loadObjectList();
                
                if(count($result)){                    
                    foreach($result as $r){
                        try
                        {
                            $alias = JApplication::stringURLSafe($r->title.' '.$r->id);
                            //update db with clean alias here
                            $query = $db->getQuery(true);
                            $query->update('#__iproperty_categories')->set('alias = '.$db->Quote($alias));
                            $query->where('id = '.(int)$r->id);
                            $db->setQuery($query);
                            if (!$db->execute()) {
                                throw new Exception($db->getErrorMsg());
                            } else {
                                $rows = $db->getAffectedRows();
                            }
                        }
                        catch (Exception $e)
                        {
                            $this->setError($e->getMessage());
                            return false;
                        }
                    }                    
                } else {
                    $app->redirect('index.php?option=com_iproperty', JText::sprintf( JText::_('PLG_IP_AA_NONEFOUND'), $section ), 'notice');
                }                    
                break;
            case 'prop':
                $query = $db->getQuery(true);
                $query->select('id, city');
                $query->from('#__iproperty');
                $query->where('alias = ""');
                
                $db->setQuery($query);
                $result = $db->loadObjectList();
                
                if(count($result)){                    
                    foreach($result as $r){
                        try
                        {
                            $ptitle     = ipropertyHTML::getPropertyTitle($r->id);
                            $alias      = JApplication::stringURLSafe($ptitle.' '.$r->city.' '.$r->id);
                            //update db with clean alias here
                            $query = $db->getQuery(true);
                            $query->update('#__iproperty')->set('alias = '.$db->Quote($alias));
                            $query->where('id = '.(int)$r->id);
                            $db->setQuery($query);
                            if (!$db->execute()) {
                                throw new Exception($db->getErrorMsg());
                            } else {
                                $rows = $db->getAffectedRows();
                            }
                        }
                        catch (Exception $e)
                        {
                            $this->setError($e->getMessage());
                            return false;
                        }
                    }                    
                } else {
                    $app->redirect('index.php?option=com_iproperty', JText::sprintf( JText::_('PLG_IP_AA_NONEFOUND'), $section ), 'notice');
                }                    
                break;
            case 'agent':
                $query = $db->getQuery(true);
                $query->select('id, fname, lname');
                $query->from('#__iproperty_agents');
                $query->where('alias = ""');
                
                $db->setQuery($query);
                $result = $db->loadObjectList();
                
                if(count($result)){                    
                    foreach($result as $r){
                        try
                        {
                            $alias      = JApplication::stringURLSafe($r->fname.' '.$r->lname.' '.$r->id);
                            //update db with clean alias here
                            $query = $db->getQuery(true);
                            $query->update('#__iproperty_agents')->set('alias = '.$db->Quote($alias));
                            $query->where('id = '.(int)$r->id);
                            $db->setQuery($query);
                            if (!$db->execute()) {
                                throw new Exception($db->getErrorMsg());
                            } else {
                                $rows = $db->getAffectedRows();
                            }
                        }
                        catch (Exception $e)
                        {
                            $this->setError($e->getMessage());
                            return false;
                        }
                    }                    
                } else {
                    $app->redirect('index.php?option=com_iproperty', JText::sprintf( JText::_('PLG_IP_AA_NONEFOUND'), $section ), 'notice');
                }                    
                break;
            case 'company':
                $query = $db->getQuery(true);
                $query->select('id, name');
                $query->from('#__iproperty_companies');
                $query->where('alias = ""');
                
                $db->setQuery($query);
                $result = $db->loadObjectList();
                
                if(count($result)){                    
                    foreach($result as $r){
                        try
                        {
                            $alias      = JApplication::stringURLSafe($r->name.' '.$r->id);
                            //update db with clean alias here
                            $query = $db->getQuery(true);
                            $query->update('#__iproperty_companies')->set('alias = '.$db->Quote($alias));
                            $query->where('id = '.(int)$r->id);
                            $db->setQuery($query);
                            if (!$db->execute()) {
                                throw new Exception($db->getErrorMsg());
                            } else {
                                $rows = $db->getAffectedRows();
                            }
                        }
                        catch (Exception $e)
                        {
                            $this->setError($e->getMessage());
                            return false;
                        }
                    }                    
                } else {
                    $app->redirect('index.php?option=com_iproperty', JText::sprintf( JText::_('PLG_IP_AA_NONEFOUND'), $section ), 'notice');
                }                    
                break;
        }                
            
        $app->redirect('index.php?option=com_iproperty', JText::sprintf( JText::_('PLG_IP_AA_SUCCESS'), $rows, $section ));
    }
}
