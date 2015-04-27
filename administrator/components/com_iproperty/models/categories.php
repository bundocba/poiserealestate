<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.modellist');

class IpropertyModelCategories extends JModelList
{
    protected $_escape  = 'htmlspecialchars';
    protected $_charset = 'UTF-8';
    
	public function __construct()
	{
		parent::__construct();
	}
    
    protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}
    
    public function getTable($type = 'Category', $prefix = 'IpropertyTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
    
    protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
        
        $state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		// List state information.
		parent::populateState('c.ordering', 'asc');
	}
	
	public function catLoop(&$i, $parent, $spcr, $published, $settings, $ipauth)
    {
        $query = $this->_db->getQuery(true);
        
        $query->select(
			$this->getState(
				'list.select',
                'c.*', 
                'c.state AS state', 
                'c.alias AS alias'
            )
        );
        $query->from('#__iproperty_categories AS c')
            ->where('parent = '.(int)$parent);
        
        // Join over the users for the checked out user.
		$query->select('COUNT(pm.id) AS entries');
		$query->join('LEFT', '#__iproperty_propmid AS pm ON pm.cat_id = c.id');
        
        // Join over the users for the checked out user.
		$query->select('ag.title AS groupname');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = c.access');
        
        // Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = c.checked_out');
        
        // Filter by published state
		$pubfilter = $this->getState('filter.state');
		if (is_numeric($pubfilter)) {
			$query->where('c.state = '.(int) $pubfilter);
		} else if ($pubfilter === '') {
			$query->where('(c.state IN (0, 1))');
		}
        
        // Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('c.id = '.(int) substr($search, 3));
			}
			else {
				$search     = JString::strtolower($search);
                $search     = explode(' ', $search);
                $searchwhere   = array();
                if (is_array($search)){ //more than one search word
                    foreach ($search as $word){
                        $searchwhere[] = 'LOWER(c.title) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(c.desc) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $word, true ).'%', false );
                    }
                } else {
                    $searchwhere[] = 'LOWER(c.title) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(c.desc) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $search, true ).'%', false );
                }
                $query->where('('.implode( ' OR ', $searchwhere ).')');
			}
		}
        
        // Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        if ($orderCol == 'ordering' || $orderCol == 'parent') {
			$orderCol = 'parent '.$orderDirn.', ordering';
		}
        $query->group('c.id');
		$query->order($this->_db->escape($orderCol.' '.$orderDirn));

        $this->_db->setQuery($query);
   		$items = $this->_db->loadObjectList();

        $c      = 0;
		$count  = count($items);
		//echo $count;
        if($count){
            foreach($items as $item){                
                $up     = ($c == 0) ? false : true;
                $down   = ($c + 1 == $count) ? false : true;

                $this->catOverview($i, $spcr, $item, $up, $down, $published, $settings, $ipauth);

                $i++;
                $c++;

                $this->catLoop( $i, $item->id, $spcr.'&mdash;', ($published == 0) ? $published : $item->state, $settings, $ipauth );                
            }
        } else if($parent == 0){
            echo '<tr><td colspan="9" style="text-align: center;">'.JText::_('COM_IPROPERTY_NO_RESULTS' ).'</td></tr>';
        }
	}   
	
	public function catOverview($i, $spcr, $item, $up, $down, $published, $settings, $ipauth, $listOrder = 'c.ordering', $listDirn = 'asc')
    {
        $user           = JFactory::getUser();
        $canCheckin     = $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
        $canEdit        = $ipauth->getAdmin();
        
        $saveOrder	= $listOrder == 'c.ordering';
        if ($saveOrder)
        {
            $saveOrderingUrl = 'index.php?option=com_iproperty&task=categories.saveOrderAjax&tmpl=component';
            JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
        }
		?>
   			 <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent; ?>">				
				<td class="order nowrap center hidden-phone">
				<?php if ($canEdit) :
					$disableClassName = '';
					$disabledLabel	  = '';

					if (!$saveOrder) :
						$disabledLabel    = JText::_('JORDERINGDISABLED');
						$disableClassName = 'inactive tip-top';
					endif; ?>
					<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
						<i class="icon-menu"></i>
					</span>
					<input type="text" style="display:none"  name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
				<?php else : ?>
					<span class="sortable-handler inactive" >
						<i class="icon-menu"></i>
					</span>
				<?php endif; ?>
				</td>
                <td class="center hidden-phone">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>                
                <td class="center hidden-phone"><?php echo ($item->icon) ? '<a href="../media/com_iproperty/categories/'.$item->icon. '" class="modal">'.ipropertyHTML::getCatIcon($item->id, 20, true).'</a>' : '--'; ?></td>
                <td class="nowrap has-context">
					<?php
                        if(!$item->state){
                            $item->title='<strong style="color:#999999">'.$item->title.'</strong>';
                            if($item->entries > 0){
                                $item->entries = '<strong style="color:#ff0000;">'.$item->entries.'</strong>';
                            }
                        }else if(!$published){
                            $item->title='<strong style="color:#ff0000;">'.$item->title.'</strong>';
                            if($item->entries > 0){
                                $item->entries = '<strong style="color:#ff0000;">'.$item->entries.'</strong>';
                            }
                        }

                        if ($item->checked_out){
                            echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin);
                        }
                        if ($canEdit)
                        {
                            ?>                    

                            <div class="pull-left">
                                <?php echo $spcr; ?><a href="<?php echo JRoute::_('index.php?option=com_iproperty&task=category.edit&id='.(int) $item->id); ?>"><?php echo $item->title; ?></a>
                                <div class="small"><?php echo $spcr.' '.JText::sprintf('JGLOBAL_LIST_ALIAS', $item->alias);?></div>
                            </div>

                            <div class="pull-right">
                            <?php
                                // Create dropdown items
                                if($canEdit):
                                    JHtml::_('dropdown.edit', $item->id, 'category.');
                                    JHtml::_('dropdown.divider');
                                endif;                                
                                
                                if($canEdit):
                                    if ($item->state) :
                                        JHtml::_('dropdown.unpublish', 'cb' . $i, 'categories.');
                                    else :
                                        JHtml::_('dropdown.publish', 'cb' . $i, 'categories.');
                                    endif;
                                    JHtml::_('dropdown.divider');
                                endif;                               

                                if ($item->checked_out && $canCheckin) :
                                    JHtml::_('dropdown.checkin', 'cb' . $i, 'categories.');
                                endif;

                                // Render dropdown list
                                echo JHtml::_('dropdown.render');
                            ?>
                            </div>
                    <?php
                        }else{
                            echo $spcr.$item->title;
                            echo '<div class="small">'.$spcr.' '.JText::sprintf('JGLOBAL_LIST_ALIAS', $item->alias).'</div>';
                        }
					?> 
                    
				</td>				
				<td class="small hidden-phone"><?php echo ipropertyHTML::snippet($item->desc, 150); ?></td>
                <td class="center"><?php echo JHtml::_('jgrid.published', $item->state, $i, 'categories.', true, 'cb'); ?></td>
				<td class="small center hidden-phone"><?php echo $item->groupname;?></td>
				<td class="small center hidden-phone"><?php echo $item->entries; ?></td>				
                <td class="center hidden-phone"><?php echo $item->id ;?></td>
			</tr>   
    	<?php	
	}
}//Class end
?>