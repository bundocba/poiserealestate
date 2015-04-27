<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
?>

<div class="modal hide fade" id="calcModal" tabindex="-1" role="dialog" aria-labelledby="calcModalLabel" aria-hidden="true">
    <form name="IPMortgageCalc" action="" class="form-vertical ip-mtg-form">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 id="calcModalLabel"><?php echo JText::_('COM_IPROPERTY_MTG_CALCULATOR'); ?></h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid">        
                <div class="span12 pagination-centered">            
                    <fieldset>
                        <div class="span6">
                            <div class="control-group">
                                <div class="control-label"><?php echo JText::_('COM_IPROPERTY_HOUSE_PRICE'); ?></div>
                                <div class="controls"><input type="text" class="span12 inputbox" name="price" value="<?php echo $this->p->price; ?>" /></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo JText::_('COM_IPROPERTY_DOWN_PAYMENT'); ?></div>
                                <div class="controls"><input type="text" class="span12 inputbox" name="dp" value="0" onchange="calculatePayment(this.form)" /></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo JText::_('COM_IPROPERTY_ANNUAL_INTEREST'); ?></div>
                                <div class="controls"><input type="text" class="span12 inputbox" name="ir" value="7.0" /></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo JText::_('COM_IPROPERTY_TERM'); ?> (<?php echo JText::_('COM_IPROPERTY_YEARS'); ?>)</div>
                                <div class="controls inline"><input type="text" class="span12 inputbox" name="term" value="30" /></div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <div class="control-label"><?php echo JText::_('COM_IPROPERTY_MTG_PRINCIPLE'); ?></div>
                                <div class="controls"><input type="text" class="span12 inputbox" name="principle" /></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo JText::_('COM_IPROPERTY_TOTAL_PAYMENTS'); ?></div>
                                <div class="controls"><input type="text" class="span12 inputbox" name="payments" value="0" onchange="calculatePayment(this.form)" /></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo JText::_('COM_IPROPERTY_MONTHLY_PAYMENT'); ?></div>
                                <div class="controls"><input type="text" class="span12 inputbox" name="pmt" /></div>
                            </div>                    
                        </div>
                    </fieldset>            
                </div>        
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JCANCEL'); ?></button>
            <button class="btn btn-primary" onclick="cmdCalc_Click(this.form); return false;"><?php echo JText::_('COM_IPROPERTY_CALCULATE'); ?></button>
        </div>
    </form>
</div>
