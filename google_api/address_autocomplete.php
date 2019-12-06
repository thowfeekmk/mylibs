<?php

namespace GoogleApi;

use GoogleApi\Address;

?>
<!DOCTYPE html>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script type="text/javascript" language="javascript" src="//code.jquery.com/jquery-1.12.4.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<div class="row">
    <div class="col-md-6">
        <table class="table table2">
            <tr>
                <th colspan="2">Google PHP API</th>
            </tr>
            <tr>
                <td>Address</td>
                <td><input id="address_input" value="" placeholder="Enter a location" class="form-control" /> </td>
            </tr>
            <tr>
                <td>Level Type</td>
                <td><input id="level_type2" value="" class="form-control" /> </td>
            </tr>
            <tr>
                <td>Level Number</td>
                <td><input id="level_number2" value="" class="form-control" /> </td>
            </tr>
            <tr>
                <td>Street Number</td>
                <td><input id="street_number2" value="" class="form-control" /> </td>
            </tr>
            <tr>
                <td>Street Name</td>
                <td><input id="street_name2" value="" class="form-control" /> </td>
            </tr>
            <tr>
                <td>Suburb</td>
                <td><input id="suburb2" value="" class="form-control" /> </td>
            </tr>
            <tr>
                <td>State</td>
                <td><input id="state2" value="" class="form-control" /> </td>
            </tr>
            <tr>
                <td>Postcode</td>
                <td><input id="postcode2" value="" class="form-control" /> </td>
            </tr>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        var cache = {};
        var token = "";
        $("#address_input").autocomplete({
            minLength: 1,
            source: function(request, process) {
                //resetAddressParts();
                if (request.term in cache) {
                    response(cache[request.term]);
                    return;
                }
                $.getJSON("google_address_ajx.php", {
                    action: 'search',
                    term: request.term,
                    token: token
                }, function(response) {
                    token = response.token;
                    return process(response.results);
                });
            },
            select: function(e, data) {
                setAddressParts(data.item.id, data.item.value);
            }
        });

        function setAddressParts(id, address) {
            $.getJSON("google_address_ajx.php", {
                action: 'select',
                term: address,
                id: id,
                token: token
            }, function(data) {
                console.log(data);
                $.each(data, function(key, val) {
                    if (key == "LevelType") {
                        $("#level_type2").val(val);
                    }
                    if (key == "LevelNumber") {
                        $("#level_number2").val(val);
                    }
                    if (key == "Number") {
                        $("#street_number2").val(val);
                    }
                    if (key == "Street") {
                        $("#street_name2").val(val);
                    }
                    if (key == "Suburb") {
                        $("#suburb2").val(val);
                    }
                    if (key == "State") {
                        $("#state2").val(val);
                    }
                    if (key == "Postcode") {
                        $("#postcode2").val(val);
                    }
                });
                token = "";
            });
        }
    });
</script>