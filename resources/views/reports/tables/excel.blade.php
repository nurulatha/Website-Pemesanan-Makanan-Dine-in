<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Report</title>
    <style type="text/css">
        .str {
            mso-number-format: \@;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th colspan="3" style="font-weight: bold; text-align: center; text-transform: uppercase;">TABLE REPORT</th>
            </tr>
            <tr>
                <th colspan="3" style="font-weight: bold; text-align: center; text-transform: uppercase;">{{ $date }}</th>
            </tr>
            <tr>
                <th style="width: 5em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">NO.</th>
                <th style="width: 15em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">TOTAL ORDERS</th>
                <th style="width: 15em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">TOTAL SALES</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tableReports as $index => $tableReport)
                <tr>
                    <td style="text-align: center;">{{ $tableReport['table_id'] }}</td>
                    <td>{{ $tableReport['total_orders'] }}</td>
                    <td>{{ $tableReport['total_sales'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
