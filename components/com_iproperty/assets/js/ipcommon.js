/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

/*Removed as of v3.3 - use Joomla core.js functions
 * 
 * function listItemTask( id, task )
{
    var form = document.adminForm;
    form.editid.value    = id;
    form.task.value 	 = task;
    form.submit( task );
}

function submitbutton(pressbutton) 
{
    var form = document.adminForm;
    if (pressbutton == 'cancel') {
        submitform( pressbutton );
        return;
    }
    try {
        form.onsubmit();
    } catch(e) {
        alert(e);
    }
    submitform(pressbutton);
}*/

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
    return true;
}

function limitText(limitField, limitCount, limitNum) 
{
    if (limitField.value.length > limitNum) {
        limitField.value = limitField.value.substring(0, limitNum);
    } else {
        limitCount.value = limitNum - limitField.value.length;
    }
}

function cmdCalc_Click(form)
{
    if (form.price.value == 0 || form.price.value.length == 0) {
        form.price.style.color = "#ff0000";
        form.price.focus(); }
    else if (form.ir.value == 0 || form.ir.value.length == 0) {
        form.ir.style.color = "#ff0000";
        form.ir.focus(); }
    else if (form.term.value == 0 || form.term.value.length == 0) {
        form.term.style.color = "#ff0000";
        form.term.focus(); }
    else
        calculatePayment(form);
}

function calculatePayment(form)
{
    princ = form.price.value - form.dp.value;
    intRate = (form.ir.value/100) / 12;
    months = form.term.value * 12;
    form.pmt.value = Math.floor((princ*intRate)/(1-Math.pow(1+intRate,(-1*months)))*100)/100;
    form.principle.value = princ;
    form.payments.value = months;
}

function clearForm(oForm) 
{
    var elements = oForm.elements;
    //oForm.reset();

    for(i=0; i<elements.length; i++) {
        field_type = elements[i].type.toLowerCase();

        switch(field_type) {
            case "text":
            case "password":
            case "textarea":
            //case "hidden":
                elements[i].value = "";
            break;

            case "radio":
            case "checkbox":
                if (elements[i].checked) {
                    elements[i].checked = false;
                }
            break;

            case "select":
            case "select-one":
            case "select-multi":
                elements[i].selectedIndex = 0;
            break;

            default:
            break;
        }
    }
}