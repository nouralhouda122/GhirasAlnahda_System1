<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>

        body {
            font-family: DejaVu Sans;
            direction: rtl;
            text-align: right;
            font-size: 12px;
        }

        h1 {
            text-align: center;
        }

        h2 {
            margin-top: 25px;
            border-bottom: 1px solid #999;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 7px;
        }

        th {
            font-weight: bold;
        }

    </style>
</head>

<body>

<h1>
    تقرير الحملة
</h1>

<p>
    تاريخ التقرير:
    {{ data_get($report, 'report_date', '-') }}
</p>

<h2>
    بيانات الحملة
</h2>

<table>

    <tr>
        <th>الحملة</th>
        <td>
            {{ data_get($report, 'campaign.name', '-') }}
        </td>
    </tr>

    <tr>
        <th>من</th>
        <td>
            {{ data_get($report, 'period.from', '-') }}
        </td>
    </tr>

    <tr>
        <th>إلى</th>
        <td>
            {{ data_get($report, 'period.to', '-') }}
        </td>
    </tr>

</table>


<h2>
    الأداء المالي
</h2>

<table>

    <tr>
        <th>عدد المتبرعين</th>

        <td>
            {{ data_get($report, 'financial.donor_count', 0) }}
        </td>
    </tr>

    <tr>
        <th>إجمالي التبرعات</th>

        <td>
            {{ data_get($report, 'financial.total_amount', 0) }}
        </td>
    </tr>

    <tr>
        <th>متوسط التبرع</th>

        <td>
            {{ data_get($report, 'financial.average_donation', 0) }}
        </td>
    </tr>

</table>


<h2>
    التوصيات
</h2>

@foreach(data_get($report, 'recommendations', []) as $recommendation)

    <p>
        @if(is_array($recommendation))

            {{ data_get($recommendation, 'title', '') }}

        @else

            {{ $recommendation }}

        @endif
    </p>

@endforeach

</body>

</html>
