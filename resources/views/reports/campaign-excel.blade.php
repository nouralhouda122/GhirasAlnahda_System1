<!DOCTYPE html>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            text-align: right;
        }

        h1 {
            text-align: center;
        }

        h2 {
            margin-top: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: right;
            mso-number-format: "\@"; /* اجبار اكسل على قراءة البيانات كنصوص لتجنب مشاكل التنسيق */
        }

        th {
            font-weight: bold;
            background-color: #eeeeee;
        }

        td {
            vertical-align: top;
        }
    </style>
</head>

<body>

<h1>Campaign Report</h1>

<p>
    <strong>Report Date:</strong>
    {{ data_get($report, 'report_date', '-') }}
</p>

{{-- Campaign Information --}}
<h2>Campaign Information</h2>
<table>
    <tr>
        <th>Field</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>Campaign ID</td>
        <td>{{ data_get($report, 'campaign.id', '-') }}</td>
    </tr>
    <tr>
        <td>Campaign</td>
        <td>{{ data_get($report, 'campaign.title', '-') }}</td>
    </tr>
    <tr>
        <td>Campaign Type</td>
        <td>{{ data_get($report, 'campaign.type', '-') }}</td>
    </tr>
    <tr>
        <td>Location</td>
        <td>{{ data_get($report, 'campaign.location', '-') }}</td>
    </tr>
    <tr>
        <td>Start Date</td>
        <td>{{ data_get($report, 'campaign.start_date', '-') }}</td>
    </tr>
    <tr>
        <td>End Date</td>
        <td>{{ data_get($report, 'campaign.end_date', '-') }}</td>
    </tr>
    <tr>
        <td>Status</td>
        <td>{{ data_get($report, 'campaign.status', '-') }}</td>
    </tr>
</table>

{{-- Executive Summary --}}
<h2>Executive Summary</h2>
<table>
    <tr>
        <th>Indicator</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>Campaign Progress</td>
        <td>{{ data_get($report, 'executive_summary.summary.campaign_progress', 0) }}</td>
    </tr>
    <tr>
        <td>Achieved Goals</td>
        <td>{{ data_get($report, 'executive_summary.summary.achieved_goals.current', 0) }}</td>
    </tr>
    <tr>
        <td>Total Goals</td>
        <td>{{ data_get($report, 'executive_summary.summary.achieved_goals.total', 0) }}</td>
    </tr>
    <tr>
        <td>At-Risk Goals</td>
        <td>{{ data_get($report, 'executive_summary.summary.at_risk_goals.current', 0) }}</td>
    </tr>
    <tr>
        <td>Total At-Risk Goals</td>
        <td>{{ data_get($report, 'executive_summary.summary.at_risk_goals.total', 0) }}</td>
    </tr>
</table>

{{-- Financial --}}
<h2>Financial Performance</h2>
<table>
    <tr>
        <th>Indicator</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>Number of Donors</td>
        <td>{{ data_get($report, 'financial.donor_count', 0) }}</td>
    </tr>
    <tr>
        <td>Total Donations</td>
        <td>{{ data_get($report, 'financial.total_amount', 0) }}</td>
    </tr>
    <tr>
        <td>Average Donation</td>
        <td>{{ data_get($report, 'financial.average_donation', 0) }}</td>
    </tr>
    <tr>
        <td>Financial Target</td>
        <td>{{ data_get($report, 'financial.target', 0) }}</td>
    </tr>
    <tr>
        <td>Collected Amount</td>
        <td>{{ data_get($report, 'financial.collected', 0) }}</td>
    </tr>
    <tr>
        <td>Remaining Amount</td>
        <td>{{ data_get($report, 'financial.remaining', 0) }}</td>
    </tr>
    <tr>
        <td>Achievement Percentage</td>
        <td>{{ data_get($report, 'financial.achievement_percentage', 0) }}%</td>
    </tr>
</table>

{{-- Volunteers --}}
<h2>Volunteer Performance</h2>
<table>
    <tr>
        <th>Indicator</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>Required Volunteers</td>
        <td>{{ data_get($report, 'volunteers.required', 0) }}</td>
    </tr>
    <tr>
        <td>Registered Volunteers</td>
        <td>{{ data_get($report, 'volunteers.registered', 0) }}</td>
    </tr>
    <tr>
        <td>Active Volunteers</td>
        <td>{{ data_get($report, 'volunteers.active', 0) }}</td>
    </tr>
    <tr>
        <td>Attendance</td>
        <td>{{ data_get($report, 'volunteers.attendance', 0) }}</td>
    </tr>
    <tr>
        <td>Total Volunteer Hours</td>
        <td>{{ data_get($report, 'volunteers.hours', 0) }}</td>
    </tr>
    <tr>
        <td>Coverage Percentage</td>
        <td>{{ data_get($report, 'volunteers.coverage_percentage', 0) }}%</td>
    </tr>
</table>

{{-- Goals --}}
<h2>Goals and Indicators</h2>
<table>
    <tr>
        <th>Goal ID</th>
        <th>Goal</th>
        <th>Progress</th>
        <th>Status</th>
        <th>Weight</th>
        <th>Indicator</th>
        <th>Score</th>
        <th>Indicator Status</th>
    </tr>
    @foreach(data_get($report, 'goals', []) as $goal)
        @php
            $indicators = data_get($goal, 'indicators', []);
        @endphp
        @if(empty($indicators))
            <tr>
                <td>{{ data_get($goal, 'id', '') }}</td>
                <td>{{ data_get($goal, 'title', '') }}</td>
                <td>{{ data_get($goal, 'progress', '') }}</td>
                <td>{{ data_get($goal, 'status', '') }}</td>
                <td>{{ data_get($goal, 'weight', '') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @else
            @foreach($indicators as $indicator)
                <tr>
                    <td>{{ data_get($goal, 'id', '') }}</td>
                    <td>{{ data_get($goal, 'title', '') }}</td>
                    <td>{{ data_get($goal, 'progress', '') }}</td>
                    <td>{{ data_get($goal, 'status', '') }}</td>
                    <td>{{ data_get($goal, 'weight', '') }}</td>
                    <td>{{ data_get($indicator, 'name', '') }}</td>
                    <td>{{ data_get($indicator, 'score', '') }}</td>
                    <td>{{ data_get($indicator, 'status', '') }}</td>
                </tr>
            @endforeach
        @endif
    @endforeach
</table>

{{-- Trend --}}
<h2>Trend</h2>
<table>
    <tr>
        <th>Date</th>
        <th>Score</th>
        <th>Phase</th>
    </tr>
    @foreach(data_get($report, 'trend', []) as $trend)
        <tr>
            <td>{{ data_get($trend, 'date', '') }}</td>
            <td>{{ data_get($trend, 'score', '') }}</td>
            <td>{{ data_get($trend, 'phase', '') }}</td>
        </tr>
    @endforeach
</table>

{{-- Recommendations --}}
<h2>Recommendations</h2>
<table>
    <tr>
        <th>Type</th>
        <th>Priority</th>
        <th>Title</th>
        <th>Description</th>
        <th>Current</th>
        <th>Required</th>
        <th>Coverage Percentage</th>
        <th>Action</th>
    </tr>
    @foreach(data_get($report, 'recommendations', []) as $recommendation)
        <tr>
            <td>{{ data_get($recommendation, 'type', '') }}</td>
            <td>{{ data_get($recommendation, 'priority', '') }}</td>
            <td>{{ data_get($recommendation, 'title', '') }}</td>
            <td>{{ data_get($recommendation, 'description', '') }}</td>
            <td>{{ data_get($recommendation, 'current', '') }}</td>
            <td>{{ data_get($recommendation, 'required', '') }}</td>
            <td>{{ data_get($recommendation, 'coverage_percentage', '') }}</td>
            <td>{{ data_get($recommendation, 'action.type', '') }}</td>
        </tr>
    @endforeach
</table>

</body>
</html>