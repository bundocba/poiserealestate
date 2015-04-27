<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport( 'joomla.application.component.controller');

class IpropertyController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{        
        $view   = $this->input->get('view', 'iproperty');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
        $views = array( 'category'=>'categories',
                        'property'=>'properties',
                        'agent'=>'agents',
                        'company'=>'companies',
                        'amenity'=>'amenities',
                        'openhouse'=>'openhouses',
                        'plan'=>'plans',
                        'subscription'=>'subscriptions',
                        'payment'=>'payments',
                        'user'=>'users',
                        'resreservation'=>'resreservations',
                        'resrate'=>'resrates',
                        'resstate'=>'resstates',
                        'respayment'=>'respayments');

        foreach( $views as $key => $value )
        {
            if ($view == $key && $layout == 'edit' && !$this->checkEditId('com_iproperty.edit.'.$key, $id)) 
            {
                // Somehow the person just went to the form - we don't allow that.
                $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
                $this->setMessage($this->getError(), 'error');
                $this->setRedirect(JRoute::_('index.php?option=com_iproperty&view='.$value, false));

                return false;
            }
        }
        
		parent::display($cachable);
        return $this;
	}
}
