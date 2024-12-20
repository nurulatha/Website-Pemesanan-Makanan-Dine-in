<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Table Report {{ $date }}</title>
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
    <h3 style="text-align: center; margin-bottom: 0.5rem;">TABLE REPORT</h3>
    <h3 style="text-align: center; margin-top: 0;">{{ $date }}</h3>

    <table border="1" style="border-collapse: collapse; margin: auto;">
        <thead>
            <th style="text-align: center; padding: 10px; background-color: var(--primary-color); color: white;">NO.</th>
            <th style="text-align: center; padding: 10px 30px; background-color: var(--primary-color); color: white;">TOTAL ORDERS</th>
            <th style="text-align: center; padding: 10px 50px; background-color: var(--primary-color); color: white;">TOTAL SALES</th>
        </thead>
        <tbody>
            @foreach ($tableReports as $index => $tableReport)
                <tr>
                    <td style="text-align: center; padding: 10px">{{ $tableReport['table_id'] }}</td>
                    <td style="text-align: right; padding: 10px">{{ $tableReport['total_orders'] }}</td>
                    <td style="text-align: right; padding: 10px">{{ $tableReport['total_sales'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
