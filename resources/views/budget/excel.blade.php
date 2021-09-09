<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($budgets as $budget)
                <tr>
                    <td>{{ $budget->name ?? '' }}</td>
                    <td>{{ $budget->date_view }}</td>
                    <td>{{ $budget->amount ?? '' }}</td>
                    <td>{{ $budget->createdBy->name ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
