document.writeln("    <script type=\"text/javascript\">");
document.writeln("        var autoValues = \"\";");
document.writeln("        var autoCalSetOptions = \"\";");
document.writeln("	      function autoCalValidateMinMax(txtID, spanID, minVal, maxVal) {");
document.writeln("	        var boolResult = false;");
document.writeln("	        var txtValue = document.getElementById(txtID).value;");
document.writeln("	        txtValue = autoCalCleanVariable(txtValue);");
document.writeln("	        if (txtValue != \"\") {");
document.writeln("	            if (parseFloat(txtValue) == txtValue) {");
document.writeln("	                if (txtValue >= minVal && txtValue <= maxVal) {");
document.writeln("	                    boolResult = true;");
document.writeln("	                }");
document.writeln("	                else {");
document.writeln("	                    document.getElementById(spanID).innerHTML = \"*Please enter a value between \" + minVal + \" and \" + maxVal + \".\";");
document.writeln("	                }");
document.writeln("	            }");
document.writeln("	            else {");
document.writeln("	                document.getElementById(spanID).innerHTML = \"*Please enter a number value.\";");
document.writeln("	            }");
document.writeln("	        }");
document.writeln("	        else {");
document.writeln("	            document.getElementById(spanID).innerHTML = \"*This field cannot be blank.\";");
document.writeln("	        }");
document.writeln("	        return boolResult;");
document.writeln("	    }");
document.writeln("	    function autoCalValidateInterestRate(txtID, spanID, minVal, maxVal) {");
document.writeln("	        var boolResult = false;");
document.writeln("	        var txtValue = document.getElementById(txtID).value;");
document.writeln("	        txtValue = autoCalCleanVariable(txtValue);");
document.writeln("	        if (txtValue != \"\") {");
document.writeln("	            if (parseFloat(txtValue) == txtValue) {");
document.writeln("	                if (txtValue > minVal && txtValue <= maxVal) {");
document.writeln("	                    boolResult = true;");
document.writeln("	                }");
document.writeln("	                else if (txtValue <= minVal) {");
document.writeln("	                    document.getElementById(spanID).innerHTML = \"*Please enter a value greater than \" + minVal + \".\";");
document.writeln("	                }");
document.writeln("	                else if (txtValue > maxVal) {");
document.writeln("	                    document.getElementById(spanID).innerHTML = \"*Please enter a value between \" + minVal + \" and \" + maxVal + \".\";");
document.writeln("	                }");
document.writeln("	            }");
document.writeln("	            else {");
document.writeln("	                document.getElementById(spanID).innerHTML = \"*Please enter a number value.\";");
document.writeln("	            }");
document.writeln("	        }");
document.writeln("	        else {");
document.writeln("	            document.getElementById(spanID).innerHTML = \"*This field cannot be blank.\";");
document.writeln("	        }");
document.writeln("	        return boolResult;");
document.writeln("	    }");
document.writeln("");
document.writeln("	    function autoCalCleanVariable(string) {");
document.writeln("	        string = string.split(\" \").join(\"\");");
document.writeln("	        string = string.split(\",\").join(\"\");");
document.writeln("	        string = string.split(\"%\").join(\"\");");
document.writeln("	        string = string.split(\"$\").join(\"\");");
document.writeln("	        return string;");
document.writeln("	    }");
document.writeln("");
document.writeln("	    function autoCalcleanErrorSpan() {");
document.writeln("	        document.getElementById(\"errdownpayment\").innerHTML = \"\";");
document.writeln("	        document.getElementById(\"errAutoLoanAmount\").innerHTML = \"\";");
document.writeln("	        document.getElementById(\"errAutoLoanTerm\").innerHTML = \"\";");
document.writeln("	        document.getElementById(\"errAutoInterestRate\").innerHTML = \"\";");
document.writeln("	    }");
document.writeln("");
document.writeln("    function autoCalValidateInteger(val, min, max, errSpan) {");
document.writeln("        if (isNaN(val)) {");
document.writeln("            document.getElementById(errSpan).innerHTML = \"*Please enter a number value.\";");
document.writeln("            return false;");
document.writeln("        }");
document.writeln("        else if (val == \"\") {");
document.writeln("            document.getElementById(errSpan).innerHTML = \"*This field cannot be Blank\";");
document.writeln("            return false;");
document.writeln("        }");
document.writeln("        for (var i = 0; i < val.length; i++) {");
document.writeln("            var ch = val.charAt(i);");
document.writeln("            if (ch < \"0\" || ch > \"9\") {");
document.writeln("                if (ch == \"-\") {");
document.writeln("                    document.getElementById(errSpan).innerHTML = \"*Please enter a value between \" + min + \" and \" + max + \".\";");
document.writeln("                    return false;");
document.writeln("                }");
document.writeln("                else {");
document.writeln("                    document.getElementById(errSpan).innerHTML = \"*Please enter a number without decimal point\";");
document.writeln("                    return false;");
document.writeln("                }");
document.writeln("            }");
document.writeln("");
document.writeln("");
document.writeln("            else if (val >= min && val <= max) {");
document.writeln("                document.getElementById(errSpan).innerHTML = \"\";");
document.writeln("                continue;");
document.writeln("            }");
document.writeln("            else {");
document.writeln("                document.getElementById(errSpan).innerHTML = \"*Please enter a value between \" + min + \" and \" + max + \".\";");
document.writeln("                return false;");
document.writeln("            }");
document.writeln("        }");
document.writeln("        return true;");
document.writeln("    }");
document.writeln("	    function autoCalCalculateMonthlyPayment() {");
document.writeln("	        autoCalcleanErrorSpan();");
document.writeln("	        var downpayment = autoCalCleanVariable(document.getElementById(\"txtdownpayment\").value);");
document.writeln("	        var loanAmount = autoCalCleanVariable(document.getElementById(\"txtAutoLaonAmount\").value);");
document.writeln("	        var months = autoCalCleanVariable(document.getElementById(\"txtAutoLoanTerm\").value);");
document.writeln("	        var interestRate = autoCalCleanVariable(document.getElementById(\"txtAutoInterestRate\").value);");
document.writeln("");
document.writeln("	        var boolAmount = autoCalValidateMinMax(\"txtAutoLaonAmount\", \"errAutoLoanAmount\", 0, 10000000);");

document.writeln("	        var boolpayment = autoCalValidateMinMax(\"txtdownpayment\", \"errdownpayment\", 0, 10000000);");

document.writeln("	        var boolTerm = autoCalValidateInteger(months, 1, 480, \"errAutoLoanTerm\");");
document.writeln("    if (autoCalSetOptions[2] == \"200\") {");
document.writeln("        if (boolTerm)");
document.writeln("            document.getElementById(\"LoanTermErrDiv\").style.display = 'none';");
document.writeln("        else");
document.writeln("            document.getElementById(\"LoanTermErrDiv\").style.display = 'block';");
document.writeln("    }");
document.writeln("    else {");
document.writeln("        document.getElementById(\"LoanTermErrDiv\").style.display = 'none';");
document.writeln("    }");
document.writeln("	        var boolRate = autoCalValidateInterestRate(\"txtAutoInterestRate\", \"errAutoInterestRate\", 0, 99);");
document.writeln("	    var checkdepro = true;    ");
document.writeln("	   if(loanAmount - downpayment < 1 ){     ");
document.writeln("	    document.getElementById(\"errdownpayment\").innerHTML = \"*Please enter a value Down Payment < Auto loan amount\";");
document.writeln("	    checkdepro = false;   ");
document.writeln("	  }     ");
document.writeln("	        if (boolpayment && boolAmount && boolRate && boolTerm && checkdepro) {");
document.writeln("	            var factor = 1;");
document.writeln("	            var rate = parseFloat(interestRate) / 1200;");
document.writeln("	            var interestRatePlusOne = parseFloat(rate) + 1;");
document.writeln("	            for (var i = 0; i < months; i++) {");
document.writeln("	                factor = parseFloat(factor) * parseFloat(interestRatePlusOne);");
document.writeln("	            }");
document.writeln("	            loanAmount = loanAmount - downpayment");
document.writeln("	            var result = (parseFloat(parseFloat(loanAmount) * parseFloat(factor) * parseFloat(rate)) / (parseFloat(factor) - 1)).toFixed(2);");
document.writeln("	            document.getElementById(\"autoCalrate\").innerHTML = autoCalCleanVariable(document.getElementById(\"txtAutoInterestRate\").value);");
document.writeln("	            document.getElementById(\"autoCalyear\").innerHTML = autoCalCleanVariable(document.getElementById(\"txtAutoLoanTerm\").value) +\" months\";");
document.writeln("	            //var checkresul = document.getElementById(\"txtdownpayment\").value - document.getElementById(\"txtAutoLaonAmount\").value");
document.writeln("	            document.getElementById(\"autoCalamount\").innerHTML = autoCalFormatCurrency(loanAmount);");
document.writeln("	            document.getElementById(\"autoCalmonthlyPayment\").innerHTML = autoCalFormatCurrency(result);");
document.writeln("	            document.getElementById(\"autoCalresultDiv\").style.display = \"block\";");
document.writeln("	            document.getElementById(\"autoCalbtnText\").innerHTML=\"Recalculate\";");
document.writeln("	        }");
document.writeln("	    }");
document.writeln("        function autoCalFormatCurrency(num) {");
document.writeln("                num = num.toString().replace(/\$|\,/g, '');");
document.writeln("                if (isNaN(num))");
document.writeln("                    num = \"0\";");
document.writeln("                sign = (num == (num = Math.abs(num)));");
document.writeln("                num = Math.floor(num * 100 + 0.50000000001);");
document.writeln("                cents = num % 100;");
document.writeln("                num = Math.floor(num / 100).toString();");
document.writeln("                if (cents < 10)");
document.writeln("                    cents = \"0\" + cents;");
document.writeln("                for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)");
document.writeln("                    num = num.substring(0, num.length - (4 * i + 3)) + ',' +");
document.writeln("                num.substring(num.length - (4 * i + 3));");
document.writeln("                return (((sign) ? '' : '-') + '$' + num + '.' + cents);");
document.writeln("         }");
document.writeln("");
document.writeln("         function autoCalGetColor()");
document.writeln("         {");
document.writeln("            if(autoCalSetOptions[0]==\"1\")");
document.writeln("            {");
document.writeln("                document.getElementById(\"autoCalheaderDiv\").className = \"BankrateFCC_boxhead-container-small\" +\" BankrateFCC_calc-blue-border\";");
document.writeln("                document.getElementById(\"autoCalbodyDiv\").className = \"BankrateFCC_calc-container-small\" + \" BankrateFCC_calc-blue\" + \" BankrateFCC_calc-blue-border\";");
document.writeln("                document.getElementById(\"autoCalresultDiv\").className = \"BankrateFCC_results-container\" + \" BankrateFCC_calc-blue-border\";");
document.writeln("                document.getElementById(\"autoCalfooterDiv\").className = \"BankrateFCC_footer-container small\" +\" BankrateFCC_calc-dkblue\";");
document.writeln("            }");
document.writeln("            if(autoCalSetOptions[0]==\"2\")");
document.writeln("            {");
document.writeln("                document.getElementById(\"autoCalheaderDiv\").className=\"BankrateFCC_boxhead-container-small\" +\" BankrateFCC_calc-orange-border\";");
document.writeln("                document.getElementById(\"autoCalbodyDiv\").className=\"BankrateFCC_calc-container-small\" + \" BankrateFCC_calc-orange\" + \" BankrateFCC_calc-orange-border\";");
document.writeln("                document.getElementById(\"autoCalresultDiv\").className = \"BankrateFCC_results-container\" + \" BankrateFCC_calc-orange-border\";");
document.writeln("                document.getElementById(\"autoCalfooterDiv\").className=\"BankrateFCC_footer-container small\" +\" BankrateFCC_calc-dkorange\";");
document.writeln("");
document.writeln("            }");
document.writeln("            if(autoCalSetOptions[0]==\"3\")");
document.writeln("            {");
document.writeln("                document.getElementById(\"autoCalheaderDiv\").className = \"BankrateFCC_boxhead-container-small\" +\" BankrateFCC_calc-gray-border\";");
document.writeln("                document.getElementById(\"autoCalbodyDiv\").className = \"BankrateFCC_calc-container-small\" + \" BankrateFCC_calc-gray\" + \" calc-gray-border\";");
document.writeln("                document.getElementById(\"autoCalresultDiv\").className = \"BankrateFCC_results-container\" + \" BankrateFCC_calc-gray-border\";");
document.writeln("                document.getElementById(\"autoCalfooterDiv\").className = \"BankrateFCC_footer-container small\" +\" BankrateFCC_calc-dkgray\";");
document.writeln("");
document.writeln("            }");
document.writeln("            if(autoCalSetOptions[0]==\"4\")");
document.writeln("            {");
document.writeln("                document.getElementById(\"autoCalheaderDiv\").className = \"BankrateFCC_boxhead-container-small\" +\" BankrateFCC_calc-green-border\";");
document.writeln("                document.getElementById(\"autoCalbodyDiv\").className = \"BankrateFCC_calc-container-small\" + \" BankrateFCC_calc-green\" + \" BankrateFCC_calc-green-border\";");
document.writeln("                document.getElementById(\"autoCalresultDiv\").className = \"BankrateFCC_results-container\" + \" BankrateFCC_calc-green-border\";");
document.writeln("                document.getElementById(\"autoCalfooterDiv\").className = \"BankrateFCC_footer-container small\" +\" BankrateFCC_calc-dkgreen\";");
document.writeln("");
document.writeln("            }");
document.writeln("         }");
document.writeln("");
document.writeln("        function autoCalSetWidthOptions()");
document.writeln("        {");
document.writeln("	       var autoSizeOfWidget = autoCalSetOptions[2];");

document.writeln("           var moz = (typeof document.implementation != 'undefined') && (typeof document.implementation.createDocument != 'undefined');");
document.writeln("            var ie = (typeof window.ActiveXObject != 'undefined');");

document.writeln("    if (autoSizeOfWidget == \"200\") {");
document.writeln("        document.getElementById(\"autoCaltitleDiv\").className = \"BankrateFCC_boxhead_200_auto\";");
document.writeln("        document.getElementById(\"autoBRLogo\").style.width = \"110px\";");
document.writeln("");
document.writeln("        if(autoCalSetOptions[1] == \"Verdana\")");
document.writeln("            document.getElementById(\"autoCaltitleDiv\").style.fontSize = '10px';");
document.writeln("        else");
document.writeln("            document.getElementById(\"autoCaltitleDiv\").style.fontSize = '11px';");
document.writeln("");
document.writeln("        for (var i = 1; i <= 3; i++)");
document.writeln("            document.getElementById(\"autoCalip\" + i).style.width = \"105px\";");
document.writeln("");
document.writeln("    }");
document.writeln("    else if (autoSizeOfWidget == \"225\") {");
document.writeln("        document.getElementById(\"autoCaltitleDiv\").className = \"BankrateFCC_boxhead_200_auto\";");
document.writeln("        document.getElementById(\"autoBRLogo\").style.width = \"115px\";");
document.writeln("");
document.writeln("        if(ie && autoCalSetOptions[1] == \"Verdana\")");
document.writeln("            document.getElementById(\"autoCaltitleDiv\").style.fontSize = '10px';");
document.writeln("        else");
document.writeln("            document.getElementById(\"autoCaltitleDiv\").style.fontSize = '11px';");
document.writeln("");
document.writeln("        for (var i = 1; i <= 3; i++)");
document.writeln("            document.getElementById(\"autoCalip\" + i).style.width = \"130px\";");
document.writeln("");
document.writeln("    }");
document.writeln("    else if (autoSizeOfWidget == \"250\") {");
document.writeln("        document.getElementById(\"autoCaltitleDiv\").className = \"BankrateFCC_boxhead_200_auto\";");
document.writeln("        document.getElementById(\"autoBRLogo\").style.width = \"115px\";");
document.writeln("        document.getElementById(\"autoCaltitleDiv\").style.fontSize = '11px';");
document.writeln("        for (var i = 1; i <= 3; i++)");
document.writeln("            document.getElementById(\"autoCalip\" + i).style.width = \"150px\";");
document.writeln("    }");
document.writeln("    else {");
document.writeln("        document.getElementById(\"autoCaltitleDiv\").className = \"BankrateFCC_boxhead\";");
document.writeln("        document.getElementById(\"autoBRLogo\").style.width = \"115px\";");
document.writeln("        document.getElementById(\"autoCaltitleDiv\").style.fontSize = '12px';");
document.writeln("        for (var i = 1; i <= 3; i++)");
document.writeln("            document.getElementById(\"autoCalip\" + i).style.width = \"180px\";");
document.writeln("    }");
document.writeln("            if(moz){");
document.writeln("               document.getElementById(\"autoCalfooterDiv\").style.width = parseInt(autoSizeOfWidget) + parseInt(2) + \"px\";");
document.writeln("               document.getElementById(\"autoCalbodyDiv\").style.width = autoSizeOfWidget - parseInt(20) + \"px\";");
document.writeln("               document.getElementById(\"autoCalheaderDiv\").style.width = parseInt(autoSizeOfWidget)  + \"px\";");
document.writeln("");
document.writeln("            }");
document.writeln("            else if(ie){");
document.writeln("               document.getElementById(\"autoCalfooterDiv\").style.width = parseInt(autoSizeOfWidget) + parseInt(2) + \"px\";");
document.writeln("               document.getElementById(\"autoCalbodyDiv\").style.width = parseInt(autoSizeOfWidget) - parseInt(20) + \"px\";");
document.writeln("               document.getElementById(\"autoCalheaderDiv\").style.width = parseInt(autoSizeOfWidget) + \"px\";");
document.writeln("               if(autoSizeOfWidget==\"250\" && autoCalSetOptions[1]==\"Verdana\"){");
document.writeln("                 document.getElementById(\"autoCaltitleDiv\").style.fontSize = \"11\";");
document.writeln("                 document.getElementById(\"autoBRLogo\").style.width = \"91px\";");
document.writeln("               }");
document.writeln("               else if(autoSizeOfWidget==\"250\"){");
document.writeln("                   document.getElementById(\"autoBRLogo\").style.width =  \"109px\";");
document.writeln("               }");
document.writeln("               if(autoSizeOfWidget==\"250\"){");
document.writeln("                   document.getElementById(\"autoCalDivCalculate\").style.marginLeft=\"70px\";");
document.writeln("               }");
document.writeln("               else if(autoSizeOfWidget==\"300\"){");
document.writeln("                   document.getElementById(\"autoCalDivCalculate\").style.marginLeft=\"90px\";");
document.writeln("               }");
document.writeln("               else if(autoSizeOfWidget==\"350\"){");
document.writeln("                   document.getElementById(\"autoCalDivCalculate\").style.marginLeft=\"120px\";");
document.writeln("               }");
document.writeln("");
document.writeln("            }");
document.writeln("        }");
document.writeln("");
document.writeln("        function autoCalSetFontOptions()");
document.writeln("        {");
document.writeln("            for(var i=1;i<=3;i++)");
document.writeln("             document.getElementById(\"autoCalip\"+i).style.fontFamily=autoCalSetOptions[1];");
document.writeln("             document.getElementById(\"autoCalyear\").style.fontFamily=autoCalSetOptions[1];");
document.writeln("             document.getElementById(\"autoCalrate\").style.fontFamily=autoCalSetOptions[1];");
document.writeln("             document.getElementById(\"autoCalamount\").style.fontFamily=autoCalSetOptions[1];");
document.writeln("             document.getElementById(\"autoCalmonthlyPayment\").style.fontFamily=autoCalSetOptions[1];");
document.writeln("             document.getElementById(\"autoCaltitleDiv\").style.fontFamily=autoCalSetOptions[1];");
document.writeln("");
document.writeln("             for(var j=1;j<=4;j++)");
document.writeln("               document.getElementById(\"autoCalresult\"+j).style.fontFamily=autoCalSetOptions[1];");
document.writeln("             document.getElementById(\"autoCalMsgInfo\").style.fontFamily=autoCalSetOptions[1];");
document.writeln("        }");
document.writeln("");
document.writeln("        function autoCalcinit()");
document.writeln("        {");
document.writeln("          autoValues = (document.getElementById('autoCal').value);");
document.writeln("          autoCalSetOptions= autoValues.split(',');");
document.writeln("            autoCalSetWidthOptions();");
document.writeln("            autoCalGetColor();");
document.writeln("            autoCalSetFontOptions();");
document.writeln("        }");
document.writeln("    </script>");