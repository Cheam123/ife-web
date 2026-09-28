<!doctype html>

<html>
    <head>
        <title>Task Report</title>
        <meta c`harset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body {
                /* font-family: simhei; */
                font-family: sans-serif;
                font-size  : 15px;
            }
            hr {
                border: none;
                height: 1px;
                /* Set the hr color */
                color: #333;  /* old IE */
                background-color: #333;  /* Modern Browsers */
            }
            .center {
                text-align: center;
            }
            tr, td {
                line-height: 100%;
            }
            .lineheight85 {
                line-height: 150%;
            }
        </style>
    </head>
    <body style="margin: 0px 0px;">

        @include('pdf.task.sections.headers.index', [
            'data' => $data,
        ])

        @include('pdf.task.sections.item.index', [
            'data' => $data,
        ])

    </body>
</html>
