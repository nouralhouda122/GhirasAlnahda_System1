<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">

    <style>

        body {
            font-family: DejaVu Sans;
            direction: ltr;
            text-align: left;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
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
            text-align: left;
        }

        th {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .section {
            margin-top: 20px;
        }

        .recommendation {
            margin-bottom: 8px;
            padding: 5px;
        }

    </style>
</head>

<body>

<h1>
    Campaign Report
</h1>

<p>
    <strong>Report Date:</strong>
    {{ data_get($report, 'report_date', '-') }}
</p>


{{-- Campaign Information --}}

<h2>
    Campaign Information
</h2>

<table>

    <tr>
        <th>Campaign</th>
        <td>
            {{ data_get($report, 'campaign.title', '-') }}
        </td>
    </tr>

    <tr>
        <th>Campaign Type</th>
        <td>
            {{ data_get($report, 'campaign.type', '-') }}
        </td>
    </tr>

    <tr>
        <th>Location</th>
        <td>
            {{ data_get($report, 'campaign.location', '-') }}
        </td>
    </tr>

    <tr>
        <th>Start Date</th>
        <td>
            {{ data_get($report, 'campaign.start_date', '-') }}
        </td>
    </tr>

    <tr>
        <th>End Date</th>
        <td>
            {{ data_get($report, 'campaign.end_date', '-') }}
        </td>
    </tr>

    <tr>
        <th>Status</th>
        <td>
            {{ data_get($report, 'campaign.status', '-') }}
        </td>
    </tr>

</table>


{{-- Executive Summary --}}

<h2>
    Executive Summary
</h2>

<table>

    <tr>
        <th>Campaign Progress</th>
        <td>
            {{ data_get($report, 'executive_summary.summary.campaign_progress', 0) }}
        </td>
    </tr>

    <tr>
        <th>Achieved Goals</th>
        <td>
            {{ data_get($report, 'executive_summary.summary.achieved_goals.current', 0) }}
            /
            {{ data_get($report, 'executive_summary.summary.achieved_goals.total', 0) }}
        </td>
    </tr>

    <tr>
        <th>At-Risk Goals</th>
        <td>
            {{ data_get($report, 'executive_summary.summary.at_risk_goals.current', 0) }}
            /
            {{ data_get($report, 'executive_summary.summary.at_risk_goals.total', 0) }}
        </td>
    </tr>

</table>


{{-- Financial Performance --}}

<h2>
    Financial Performance
</h2>

<table>

    <tr>
        <th>Number of Donors</th>

        <td>
            {{ data_get($report, 'financial.donor_count', 0) }}
        </td>
    </tr>

    <tr>
        <th>Total Donations</th>

        <td>
            {{ data_get($report, 'financial.total_amount', 0) }}
        </td>
    </tr>

    <tr>
        <th>Average Donation</th>

        <td>
            {{ data_get($report, 'financial.average_donation', 0) }}
        </td>
    </tr>

    <tr>
        <th>Financial Target</th>

        <td>
            {{ data_get($report, 'financial.target', 0) }}
        </td>
    </tr>

    <tr>
        <th>Collected Amount</th>

        <td>
            {{ data_get($report, 'financial.collected', 0) }}
        </td>
    </tr>

    <tr>
        <th>Remaining Amount</th>

        <td>
            {{ data_get($report, 'financial.remaining', 0) }}
        </td>
    </tr>

    <tr>
        <th>Achievement Percentage</th>

        <td>
            {{ data_get($report, 'financial.achievement_percentage', 0) }}%
        </td>
    </tr>

</table>


{{-- Volunteer Performance --}}

<h2>
    Volunteer Performance
</h2>

<table>

    <tr>
        <th>Required Volunteers</th>
        <td>
            {{ data_get($report, 'volunteers.required', 0) }}
        </td>
    </tr>

    <tr>
        <th>Registered Volunteers</th>
        <td>
            {{ data_get($report, 'volunteers.registered', 0) }}
        </td>
    </tr>

    <tr>
        <th>Active Volunteers</th>
        <td>
            {{ data_get($report, 'volunteers.active', 0) }}
        </td>
    </tr>

    <tr>
        <th>Attendance</th>
        <td>
            {{ data_get($report, 'volunteers.attendance', 0) }}
        </td>
    </tr>

    <tr>
        <th>Total Volunteer Hours</th>
        <td>
            {{ data_get($report, 'volunteers.hours', 0) }}
        </td>
    </tr>

    <tr>
        <th>Coverage Percentage</th>
        <td>
            {{ data_get($report, 'volunteers.coverage_percentage', 0) }}%
        </td>
    </tr>

</table>


{{-- Goals --}}

<h2>
    Goals
</h2>

<table>

    <tr>
        <th>Goal</th>
        <th>Progress</th>
        <th>Status</th>
        <th>Weight</th>
    </tr>

    @foreach(data_get($report, 'goals', []) as $goal)

        <tr>

            <td>
                {{ data_get($goal, 'title', '-') }}
            </td>

            <td>
                {{ data_get($goal, 'progress', 0) }}
            </td>

            <td>
                {{ data_get($goal, 'status', '-') }}
            </td>

            <td>
                {{ data_get($goal, 'weight', 0) }}
            </td>

        </tr>

    @endforeach

</table>


{{-- Recommendations --}}

<h2>
    Recommendations
</h2>

<table>

    <tr>
        <th>Type</th>
        <th>Priority</th>
        <th>Title</th>
        <th>Description</th>
    </tr>

    @foreach(data_get($report, 'recommendations', []) as $recommendation)

        <tr>

            <td>
                {{ data_get($recommendation, 'type', '-') }}
            </td>

            <td>
                {{ data_get($recommendation, 'priority', '-') }}
            </td>

            <td>
                {{ data_get($recommendation, 'title', '-') }}
            </td>

            <td>
                {{ data_get($recommendation, 'description', '-') }}
            </td>

        </tr>

    @endforeach

</table>

</body>

</html>
