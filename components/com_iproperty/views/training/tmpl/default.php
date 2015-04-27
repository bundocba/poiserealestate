<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('bootstrap.tooltip');
$user = JFactory::getUser();
$db = JFactory::getDBO();

//if no login

if(empty($user->id)){
	header('Location: http://poiserealestate.com/');
exit;
}

?>

<script type='text/javascript'>//<![CDATA[ 

jQuery(document).ready(function () {

    jQuery('[data-popup-target]').click(function () {
        jQuery('html').addClass('overlay');
        var activePopup = jQuery(this).attr('data-popup-target');
        jQuery(activePopup).addClass('visible');

    });

    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27 && jQuery('html').hasClass('overlay')) {
            clearPopup();
        }
    });

    jQuery('.popup-exit').click(function () {
        clearPopup();

    });
    jQuery('.popup-cancel').click(function () {
        clearPopup();

    });

    jQuery('.popup-overlay').click(function () {
        clearPopup();
    });

    function clearPopup() {
        jQuery('.popup.visible').addClass('transitioning').removeClass('visible');
        jQuery('html').removeClass('overlay');

        setTimeout(function () {
            jQuery('.popup').removeClass('transitioning');
        }, 200);
    }

});
 

</script>

<script>
    function validateForm(){
        var txt;
        var r = confirm("Do you want to continue!");
        if (r == true) {
            txt = "You pressed OK!";
        } else {
            txt = "You pressed Cancel!";
            return false;
        }
    }
</script>

<div class="ip-traininglist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>
    <?php if ($this->params->get('show_ip_title') && $this->iptitle) : ?>
        <div class="ip-mainheader">
            <h2>
                <?php echo $this->escape($this->iptitle); ?>
            </h2>
        </div>        
    <?php endif; ?>
    <div class="clearfix"></div>
    
    <?php 
   
  

    // display results for file
    if ($this->items)
    {
        /*echo 
            '<h2 class="training_header">'.JText::_('COM_IPROPERTY_TRAININGS').'</h2><span class="pull-right small ip-pagination-results">'.$this->pagination->getResultsCounter().'</span>';*/
         
            echo '<div class="row tr_title"  style="padding-top: 10px;">';
                echo '<div class="col-md-2">Date/Time</div>';
                echo '<div class="col-md-2" style="padding-right: 0px;">Course</div>';
                echo '<div class="col-md-2">Location</div>';
                //echo '<div class="col-md-2">Of out Pax</div>';
                echo '<div class="col-md-4">Description</div>';
                echo '<div class="col-md-2"></div>';
            echo '</div>';
            $count = 0;
            foreach($this->items as $c) :
                $count ++;
         ?>
    <form id="training" action="index.php?option=com_iproperty&view=trainingsubmit"  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
    <?php
                echo '<div class="row tr_template">';
                    echo '<div class="col-md-2">'.date('m/d/y', strtotime($c->createregister)).'</div>';
                    echo '<div class="col-md-2">'.$c->title.'</div>';
                    echo '<div class="col-md-2">'.$c->location.'</div>';
                    //echo '<div class="col-md-2" style="text-align: center;">'.$c->pax.'</div>';
                    echo '<div class="col-md-4"><div>'.$c->description.'</div>';
      ?>
        <a id="popup_window" data-popup-target="#example-popup<?php echo $count;?>">Read more</a>
       <div id="example-popup<?php echo $count;?>" class="popup">
    <div class="popup-body">	<span class="popup-exit"></span>

        <div class="popup-content">
            	

            <p><?php echo $c->description; ?></p>
        </div>
    </div>
</div>
<div class="popup-overlay"></div>
        <?php
                    echo '</div>';
                    //check register or unregister
                    $query = "SELECT register FROM #__iproperty_training_detail WHERE userid=".$user->id ." and trainingid=".$c->id;
                    //print_r($query);die;
                    $db->setQuery($query);
                    $register = $db->loadResult();
                    
                    echo '<div class="col-md-2"><div class="cssreadmore">';
                    if($register == 0)
                        //echo '<input class="cssbutton" type="submit" name="btnsubmit" value="Register" /></div></div>';
                        echo '<a id="popup_window" data-popup-target="#register-popup'.$count.'">Register</a></div></div>';
                    else
                        //echo '<input class="cssbutton2" type="submit" name="btnsubmit" value="Registered" /></div></div>';
                        echo '<a id="popup_window" data-popup-target="#register-popup'.$count.'">Registered</a></div></div>';
                    echo '</div>';
            ?>
      
           <!--show confirm when user register-->
            <div id="register-popup<?php echo $count;?>" class="popup">
                <div class="popup-body-register">

                    <div class="popup-content">
                        <p>You are registering for the course "<?php echo $c->title; ?>"</p>
                        <div class="btnregister"> 
                            <input class="cssbutton2" type="submit" name="btnsubmit" value="CONFIRM" />

                             <span class="popup-cancel">CANCEL</span>
                        </div>
                    </div>
                </div>
            </div>
    <input type="hidden" name="cid" value="<?php echo $c->id;?>" />
    <input type="hidden" name="option" value="com_iproperty" />
    <input type="hidden" name="task" value="training.register" />
    </form>
    <?php
            endforeach;
        /*echo
            '<div class="pagination">
                '.$this->pagination->rowgetPagesLinks().'<br />'.$this->pagination->getPagesCounter().'
             </div>';*/
    } else {
        echo $this->loadTemplate('noresult');
    }
    
    
    
    ?>
</div>