/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var arrInput        = new Array(0);
var arrInputValue   = new Array(0);
var arrCatValue     = new Array(0);

function addInput() 
{
    arrInput.push(arrInput.length);
    arrInputValue.push("");
    arrCatValue.push("");
    display();
}

function display() 
{
    document.getElementById('parah').innerHTML = "";
    for (intI = 0; intI < arrInput.length; intI++) {
        document.getElementById('parah').innerHTML+= createInput(arrInput[intI], arrInputValue[intI], arrCatValue[intI]);
    }
}

function saveValue(intId, strValue) 
{
    arrInputValue[intId] = strValue;
}

function saveCatValue(intId, strValue)
{
    arrCatValue[intId] = strValue;
}

function createInput(id, value, catValue) 
{
    var GenAmen = (catValue == 0) ? ' selected="selected"' : '';
    var IntAmen = (catValue == 1) ? ' selected="selected"' : '';
    var ExtAmen = (catValue == 2) ? ' selected="selected"' : '';
	var AccAmen = (catValue == 3) ? ' selected="selected"' : '';
	var GreAmen = (catValue == 4) ? ' selected="selected"' : '';
	var SecAmen = (catValue == 5) ? ' selected="selected"' : '';
	var LanAmen = (catValue == 6) ? ' selected="selected"' : '';
	var ComAmen = (catValue == 7) ? ' selected="selected"' : '';
	var AppAmen = (catValue == 8) ? ' selected="selected"' : '';

    var inputs = '<div class="clearfix"></div><div class="span12 nowrap"><div class="control-group">\n\
                    <div class="control-label">\n\
                        <input type="text" name="title[]" class="inputbox" id="amenity '+id+'" onChange="javascript:saveValue('+id+',this.value)" value="'+value+'" />\n\
                    </div>\n\
                    <div class="controls">\n\
                        <select name="cat[]" class="inputbox" id="catamenity '+id+'" onChange="javascript:saveCatValue('+id+',this.selectedIndex)">\n\
                            <option value="0"'+GenAmen+'>'+window.AmenLocale.general+'</option>\n\
                            <option value="1"'+IntAmen+'>'+window.AmenLocale.interior+'</option>\n\
                            <option value="2"'+ExtAmen+'>'+window.AmenLocale.exterior+'</option>\n\\n\
							<option value="3"'+AccAmen+'>'+window.AmenLocale.accessibility+'</option>\n\\n\
							<option value="4"'+GreAmen+'>'+window.AmenLocale.green+'</option>\n\\n\
							<option value="5"'+SecAmen+'>'+window.AmenLocale.security+'</option>\n\\n\
							<option value="6"'+LanAmen+'>'+window.AmenLocale.landscape+'</option>\n\\n\
							<option value="7"'+ComAmen+'>'+window.AmenLocale.community+'</option>\n\\n\
							<option value="8"'+AppAmen+'>'+window.AmenLocale.appliance+'</option>\n\
                        </select>\n\
                    </div>\n\
                </div></div><div class="clearfix"></div>';
    return inputs;
}

function deleteInput() 
{
    if (arrInput.length > 0) {
        arrInput.pop();
        arrInputValue.pop();
        arrCatValue.pop();
    }
    display();
}