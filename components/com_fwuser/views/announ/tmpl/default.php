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
<style>
			.ontop {
				z-index: 999;
				width: 100%;
				height: 100%;
				top: 0;
				left: 0;
				display: none;
				position: absolute;				
				
				color: #aaaaaa;
				
				
			}
			#popup {
				width: 300px;
				height: 200px;
				position: absolute;
				color: #000000;
				background-color: #E4E2E3;
				/* To align popup window at the center of screen*/
				top: 50%;
				left: 50%;				padding-top: 5%;
                                padding-left: 5%;
			}
		</style>
                <script type="text/javascript">
			function pop(div) {
                                document.getElementById(div).style.display = "block";
				
			}
			function hidediv(div) {
				document.getElementById(div).style.display = 'none';
			}
			
		</script>
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
<div style="margin-bottom: 10%;margin-top: 5%;">
    <h2>ANNOUNCEMENTS</h2>
    <?php
        if(!empty($this->Items)){
    ?>
    <table width="100%">
        <tr class="tr_title">
            <th>
                Date
            </th>
            <th>
                Announcements
            </th>
            <th>
                
            </th>
        </tr>
        <?php
            $count = 0;
            foreach ($this->Items as $key => $item) {
                $count ++;
        ?>
        <tr style="border-bottom: 1px solid #8D8D8D;" height="40px">
            <td>
                <?php echo date('m/d/y', strtotime($item->datenow)); ?>
            </td>
            <td>
                <?php echo $item->name; ?>
            </td>
            <td>
                <div style="background-color: #032D5F;width: 63%;  float: right;text-align: center;"><a style="color: #fff;" id="popup_window" data-popup-target="#example-popup<?php echo $count;?>">Read more</a></div>
       <div id="example-popup<?php echo $count;?>" class="popup">
    <div class="popup-body">	<span class="popup-exit"></span>

            <div class="popup-content">
                <p>Date: <?php echo date('m/d/y', strtotime($item->datenow)); ?></p>
                
                <p><?php echo $item->description; ?></p>
            </div>
        </div>
    </div>
    <div class="popup-overlay"></div>
                
            </td>
        </tr>
        <?php
            }
        ?>
    </table>
    <?php 
        }
    ?>
</div>

