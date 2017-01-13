<h2><?php echo $text_credit_card; ?></h2>
<div class="content" id="payment">
    <span class="error" id="twocheckout_api_error" style="display:none"><?php echo $text_cc_error; ?></span>
    <form id="co-payment-form">
        <input id="sellerId" type="hidden" maxlength="16" width="20" value=<?php echo $twocheckout_api_sid; ?>>
        <input id="token" name="token" type="hidden" width="10" value="">
        <input id="publishableKey" type="hidden" maxlength="16" width="20" value=<?php echo $twocheckout_api_public_key; ?>>
    <table class="form">
        <tr>
            <td><?php echo $entry_cc_number; ?></td>
            <td><input type="text" id="ccNo" value="" /></td>
        </tr>
        <tr>
            <td><?php echo $entry_cc_expire_date; ?></td>
            <td><select id="expMonth">
                    <?php foreach ($months as $month) { ?>
                    <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
                    <?php } ?>
                </select>
                /
                <select id="expYear">
                    <?php foreach ($year_expire as $year) { ?>
                    <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
                    <?php } ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $entry_cc_cvv2; ?></td>
            <td><input type="text" id="cvv" value="" size="3" /></td>
        </tr>
    </table>
    </form>
</div>
<div class="buttons">
    <div class="right"><input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" loading-text="Loading..." class="btn btn-primary" onclick="retrieveToken();" /></div>
</div>
<script type="text/javascript">
    function successCallback(data) {
        var data = eval(data);

        $(function() {
            $.ajaxSetup({
                error: function(jqXHR, exception) {
                    if (jqXHR.status === 0) {
                        alert('Not connect.\n Verify Network.');
                    } else if (jqXHR.status == 404) {
                        alert('Requested page not found. [404]');
                    } else if (jqXHR.status == 500) {
                        alert('Internal Server Error [500].');
                    } else if (exception === 'parsererror') {
                        alert('Requested JSON parse failed.');
                    } else if (exception === 'timeout') {
                        alert('Time out error.');
                    } else if (exception === 'abort') {
                        alert('Ajax request aborted.');
                    } else {
                        alert('Uncaught Error.\n' + jqXHR.responseText);
                    }
                }
            });
       });

        if(data.exception != null || (data.validationErrors != null && data.validationErrors.length > 0)){
            console.log(data);
        }
        else{
            var tco_token = eval(data).response.token.token;
            var dataObj = {};
            dataObj['token']=tco_token;

            $.ajax({
                url: 'index.php?route=payment/twocheckout_api/send',
                type: 'get',
                contentType: 'application/json',
                data: dataObj,
                processData:true,
                dataType: 'json',
                beforeSend: function() {
                    $('#button-confirm').attr('disabled', true);
                    $('#button-confirm').button('loading');
                },
                complete: function() {
                    $('#button-confirm').attr('disabled', false);
                    $('#button-confirm').button('reset');
                    $('.attention').remove();
                },
                success: function(json) {
                    if (json['error']) {
                        clearPaymentFields();
                        alert(json['error']);
                    } else if (json['response']['responseCode'] == 'APPROVED') {
                        window.location = json['oc_redirect'];
                    }
                }
            });
        }
    }

    function errorCallback(data) {
        clearPaymentFields();
        if (data.errorCode === 200) {
            TCO.requestToken(successCallback, errorCallback, 'myCCForm');
        } else if (data.errorCode == 401) {
            $( "#twocheckout_api_error" ).show();
        } else{
            alert(data.errorMsg);
        }
    }

    function retrieveToken() {
        $( "#twocheckout_api_error" ).hide();
        if(typeof TCO.requestToken == 'undefined'){
            alert("Error Processing Payment");
        }
        else {
            $('#ccNo').val($('#ccNo').val().replace(/[^0-9\.]+/g,''));
            $('#cvv').val($('#cvv').val().replace(/[^0-9\.]+/g,''));
            TCO.requestToken(successCallback, errorCallback, 'co-payment-form');
        }
    }

    function clearPaymentFields() {
        $('#ccNo').val('');
        $('#cvv').val('');
        $('#expMonth').val('');
        $('#expYear').val('');
    }
</script>
<?php
    if ($twocheckout_api_test) {
        echo '<script type="text/javascript" src="https://sandbox.2checkout.com/checkout/api/script/publickey/"></script>';
        echo '<script type="text/javascript" src="https://sandbox.2checkout.com/checkout/api/2co.js"></script>';
    } else {
        echo '<script type="text/javascript" src="https://www.2checkout.com/checkout/api/script/publickey/"></script>';
        echo '<script type="text/javascript" src="https://www.2checkout.com/checkout/api/2co.js"></script>';     
    }
?>