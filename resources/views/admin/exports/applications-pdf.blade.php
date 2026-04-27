<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Applications Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #00008B; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #00008B; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .header { margin-bottom: 20px; }
        .meta { color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ app_name() }} - Applications Report</h1>
        <p class="meta">Generated on: {{ date('Y-m-d H:i:s') }}</p>
        <p class="meta">Total Applications: {{ $applications->count() }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>App. No.</th>
                <th>Applicant</th>
                <th>Program</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $index => $app)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $app->application_number }}</td>
                <td>{{ $app->student ? $app->student->full_name : ($app->user->fullName() ?? 'N/A') }}</td>
                <td>{{ $app->program->name ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $app->status)) }}</td>
                <td>{{ $app->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
