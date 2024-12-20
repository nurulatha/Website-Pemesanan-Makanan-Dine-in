<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Menu Report {{ $date }}</title>
    <style>
        :root {
            --primary-color: #2c4053;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>

<body>
    <h3 style="text-align: center; margin-bottom: 0.5rem;">MENU REPORT</h3>
    <h3 style="text-align: center; margin-top: 0;">{{ $date }}</h3>

    <table border="1" style="border-collapse: collapse; margin: auto;">
        <thead>
            <th style="text-align: center; padding: 10px; background-color: var(--primary-color); color: white;">NO.</th>
            <th style="text-align: center; padding: 10px; background-color: var(--primary-color); color: white;">MENU NAME</th>
            <th style="text-align: center; padding: 10px; background-color: var(--primary-color); color: white;">CATEGORY NAME</th>
            <th style="text-align: center; padding: 10px; background-color: var(--primary-color); color: white;">MENU PRICE</th>
            <th style="text-align: center; padding: 10px; background-color: var(--primary-color); color: white;">TOTAL QTY</th>
            <th style="text-align: center; padding: 10px; background-color: var(--primary-color); color: white;">TOTAL SALES</th>
        </thead>
        <tbody>
            @foreach ($menuReports as $index => $menuReport)
                <tr>
                    <td style="text-align: center; padding: 10px">{{ $index + 1 }}</td>
                    <td style="padding: 10px">{{ $menuReport['menu_name'] }}</td>
                    <td style="padding: 10px">{{ $menuReport['category_name'] }}</td>
                    <td style="text-align: right; padding: 10px">{{ $menuReport['menu_price'] }}</td>
                    <td style="text-align: right; padding: 10px">{{ $menuReport['total_quantity'] }}</td>
                    <td style="text-align: right; padding: 10px">{{ $menuReport['total_sales'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
