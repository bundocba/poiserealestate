<?php defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$Itemid=JRequest::getVar('Itemid',0);
$user=&JFactory::getUser();
$user_id=$user->get('id');
$document = & JFactory::getDocument();
$_title_tag=JText::_('ANNOUN LIST');
$document->setTitle($_title_tag);
$session = JFactory::getSession();


if($session->get('link_url')==''){
	$link_url=JURI::root();
}else{
	$link_url=$session->get('link_url');
}
echo '<link rel="stylesheet" href="'.JURI::root().'components/com_fwuser/css/css.css" type="text/css" />';
echo '<script type="text/javascript" src="'.JURI::root().'components/com_fwuser/js/js.js"></script>';
?>

<?php
    if ($this->items)
    {
        echo 
            '<h2 class="ip-property-header-search">'.JText::_('LATEST PROPERTY').'</h2>';
			echo '<div class="row clearfix">';
					echo '<div class="col-sm-2"></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('PROPERTY_TYPE').'</label></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('PRICE').'</label></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('STATUS').'</label></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('DISTRICT').'</label></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('MORE_INFO').'</label></div>';
				echo '</div>';
				echo '<hr>';
            foreach($this->items as $p) :
				echo '<div class="row">';
					echo '<div class="col-sm-2 text_algin_center">'.$p->thumb.'</div>';
					echo '<div class="col-sm-2 text_algin_center">'.$p->property_type.'</div>';
					echo '<div class="col-sm-2 text_algin_center">'.$p->price.'</div>';
					echo '<div class="col-sm-2 text_algin_center">'.$p->stypename.'</div>';
					echo '<div class="col-sm-2 text_algin_center">'.$p->city.'</div>';
					echo '<div class="col-sm-2 text_algin_center"><a href="'.$p->proplink.'">'.JText::_('MORE_INFO').'</a></div>';
				echo '</div>';
				echo '<hr>';
            endforeach;
        
    } else {
       
    }
?>
