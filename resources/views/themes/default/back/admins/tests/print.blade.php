<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Results</title>

    <link rel="stylesheet" href="{{ asset('back-assets/css/bootstrap.min.css') }}">

    <style>
        @page { size: A4 landscape; margin: 10mm; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }

        body {
            padding: 20px;
            font-family: Arial, sans-serif;
            color: #000;
        }

        h2 {
            text-align: center;
            margin: 0 0 8px 0;
            font-weight: 700;
        }

        .info-box {
            text-align: center;
            margin-bottom: 14px;
            font-size: 14px;
            line-height: 1.4;
        }

        .info-box .line {
            display: block;
            margin: 2px 0;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse !important;
            font-size: 13px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000 !important;
            padding: 8px !important;
            text-align: center !important;
            vertical-align: middle !important;
            word-wrap: break-word;
            white-space: normal;
        }

        thead th {
            background-color: #f2f2f2 !important;
            font-weight: 700;
        }

        tbody tr { page-break-inside: avoid; }

        .btn { border: 1px solid #000; }


/* ===== HEADER ===== */
.print-header {
    text-align: center;
    margin-bottom: 25px;
}

/* Logo */
.print-logo {
    width: 220px;
    height: auto;
    margin-bottom: 10px;
}

/* Divider line */
.divider {
    width: 100%;
    height: 5px;
    background-color: #f00606ff;
    margin: 10px auto 20px auto;
}

/* Title */
h2 {
    font-weight: 700;
    margin-bottom: 10px;
}

/* Info text */
.info-box {
    font-size: 15px;
    margin-bottom: 25px;
}

.info-box span {
    margin: 0 20px;
    font-weight: 600;
}

/* Print rules */
@media print {
    .no-print {
        display: none !important;
    }

    body {
        margin: 0;
    }
}


    </style>
</head>

<body>
<div class="print-header">

    <img src="{{ asset('assets/themes/default/front/images/logo.png') }}" class="print-logo">

    <div class="divider"></div> 
</div>

<div class="no-print mb-3 d-flex gap-2">
    <button class="btn btn-primary" onclick="window.print()">Print</button>
    <button class="btn btn-secondary" onclick="window.close()">Close</button>
</div>


<h2>Students Test Results</h2>

<div class="info-box">
    <span class="line"><strong>Course:</strong> {{ $rows->first()->course_name ?? '-' }}</span>
    <span class="line"><strong>Test:</strong> {{ $rows->first()->test_name ?? '-' }}</span>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 18%;">Student</th>
            <th style="width: 10%;">Last Attempt</th>
            <th style="width: 12%;">Status</th>
            <th style="width: 10%;">Score</th>
            <th style="width: 10%;">Total Score</th>
            <th style="width: 25%;">Start Time</th>
            <th style="width: 5%;">Correct</th>
            <th style="width: 5%;">Wrong</th>
        </tr>
    </thead>

    <tbody>
        @foreach($rows as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->student_name }}</td>
                <td>{{ $r->last_attempt }}</td>
                <td>{{ ucfirst($r->status) }}</td>
                <td>{{ $r->final_score }}</td>
                <td>{{ $r->test_total_score }}</td>
                <td>{{ $r->started_at }}</td>
                <td>{{ $r->correct_answers ?? 0 }}</td>
                <td>{{ $r->wrong_answers ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
