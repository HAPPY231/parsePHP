<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>Parse data to CSV file</title>
</head>
<body>
    <div id="parse-content">
        <h1>Parse data</h1>
        <form action="index.php" method="POST">
            <input type="submit" name="parse" value="Parse Data!">
        </form>
        <?php
            if(isset($_POST['parse'])) {
                parse();
                unset($_POST['parse']);
            }
        ?>

    </div>
</body>
</html>

<?php


    function parse(){
        //including phpQuery library
        require('phpQuery/phpQuery/phpQuery.php');

//Parsing the page from which we want to take data
        $parsehtml = phpQuery::newDocumentFileHTML('wo_for_parse.html');

//Tracking number
        $tracking_number = $parsehtml['#wo_number']->html();

//PO number
        $PO_number = $parsehtml['#po_number']->html();

//Date
        $scheduled_date = trim($parsehtml['#scheduled_date']->html());
        $scheduled_date = explode("<br>", $scheduled_date);
        $year_month_day = trim($scheduled_date[0]);
        $time_in_24_hour_format = date("H:i", strtotime($parsehtml['#scheduled_date span']->html()));
        $date = $year_month_day . " " . $time_in_24_hour_format;
        $date = date_create($date);
        $newDate = date_format($date, "Y-m-d H:i");

//Customer
        $customer = $parsehtml['#customer']->html();
        $customer = trim($customer);

//Trade
        $trade = $parsehtml['#trade']->html();

//NTE
        $NTE = $parsehtml['#nte']->html();
        $NTE = floatval(preg_replace('/[^\d.]/', '', $NTE));


//Store ID
        $store_ID = $parsehtml['#location_name']->html();

//Address
        $address_break = explode("</a>", $parsehtml['#location_address']->html());
        $address_break = explode("<br>", $address_break[1]);
        $address_line_one = trim($address_break[0]);
        $address_line_one = explode(" ", $address_line_one);
        $addres_street_number = $address_line_one[2] . " " . $address_line_one[0] . " " . $address_line_one[1];

        $address_line_two = trim($address_break[1]);
        $address_line_two = explode(" ", $address_line_two);
        $city = $address_line_two[0];
        $state = $address_line_two[1];
        $zip = $address_line_two[3];

//Phone
        $tel_number = $parsehtml['#location_phone']->html();
        $tel_number = floatval(preg_replace('/[^\d.]/', '', $tel_number));
        $tel_number = trim($tel_number);


        $file_open = fopen("requests.csv", "a");
        $no_rows = count(file("requests.csv"));

        if ($no_rows > 1) {
            $no_rows = ($no_rows - 1) + 1;
        }
        $request_data = array(
            'id' => $no_rows,
            'tracking_number' => $tracking_number,
            'po_number' => $PO_number,
            'data' => $newDate,
            'customer' => $customer,
            'trade' => $trade,
            'nte' => $NTE,
            'store_id' => $store_ID,
            'address' => $addres_street_number,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'telephone_number' => $tel_number
        );
        fputcsv($file_open, $request_data, ",");

echo<<<end
    <table>
      <tr>
        <th>Id</th>
        <th>Tracking Number</th>
        <th>PO Number</th>
        <th>Date</th>
        <th>Customer</th>
        <th>Trade</th>
        <th>NTE</th>
        <th>Store ID</th>
        <th>Address</th>
        <th>City</th>
        <th>State</th>
        <th>ZIP</th>
        <th>Telephone Number</th>
      </tr>
      <tr>
        <td>{$no_rows}</td>
        <td>{$tracking_number}</td>
        <td>{$PO_number}</td>
        <td>{$newDate}</td>
        <td>{$customer}</td>
        <td>{$trade}</td>
        <td>{$NTE}</td>
        <td>{$store_ID}</td>
        <td>{$addres_street_number}</td>
        <td>{$city}</td>
        <td>{$state}</td>
        <td>{$zip}</td>
        <td>{$tel_number}</td>
      </tr>
    </table>
end;
    }

