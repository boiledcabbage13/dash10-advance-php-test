<html>
    <head>
        <style type="text/css">
            body {
                font: 16px Roboto, Arial, Helvetica, Sans-serif;
            }
            td, th {
                padding: 4px 8px;
            }
            th {
                background: #eee;
                font-weight: 500;
            }
            tr:nth-child(odd) {
                background: #f4f4f4;
            }
        </style>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $player)
                <tr>
                    @foreach ($headingsKey as $headingKey)
                    <td>{{ $player[$headingKey] }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>