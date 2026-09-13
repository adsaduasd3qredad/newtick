<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sales Report</title>
    <style>
        body { font-family: 'freeserif', sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Sales Report</h2>
    <p>Printed on: {{ now()->format('Y-m-d H:i:s') }}</p>

    <h3>Revenue by Payment Method</h3>
    <table>
        <tr>
            <th>Method</th>
            <th>Tickets</th>
            <th>Total Revenue</th>
        </tr>
        @foreach($paymentMethods as $pm)
        <tr>
            <td>{{ $pm->payment_method }}</td>
            <td>{{ $pm->tickets }}</td>
            <td>{{ number_format($pm->total, 2) }}</td>
        </tr>
        @endforeach
    </table>

    <h3>Daily Sales (Last 30 Days)</h3>
    <table>
        <tr>
            <th>Date</th>
            <th>Tickets</th>
            <th>Total Revenue</th>
        </tr>
        @foreach($dailySales as $ds)
        <tr>
            <td>{{ $ds->date }}</td>
            <td>{{ $ds->tickets }}</td>
            <td>{{ number_format($ds->total, 2) }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>