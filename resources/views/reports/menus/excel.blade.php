<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Report {{ $date }}</title>
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
                <th colspan="6" style="font-weight: bold; text-align: center; text-transform: uppercase;">MENU REPORT</th>
            </tr>
            <tr>
                <th colspan="6" style="font-weight: bold; text-align: center; text-transform: uppercase;">{{ $date }}</th>
            </tr>
            <tr>
                <th style="width: 5em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">NO.</th>
                <th style="width: 25em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">MENU NAME</th>
                <th style="width: 15em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">CATEGORY NAME</th>
                <th style="width: 15em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">MENU PRICE</th>
                <th style="width: 10em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">TOTAL QTY</th>
                <th style="width: 15em; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #f2f2f2;">TOTAL SALES</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($menuReports as $index => $menuReport)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $menuReport['menu_name'] }}</td>
                    <td>{{ $menuReport['category_name'] }}</td>
                    <td>{{ $menuReport['menu_price'] }}</td>
                    <td>{{ $menuReport['total_quantity'] }}</td>
                    <td>{{ $menuReport['total_sales'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
