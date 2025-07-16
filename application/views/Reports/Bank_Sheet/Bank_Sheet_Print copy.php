<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Table</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: lightgreen;
        }

        .highlight {
            background-color: orange;
        }

        .red-text {
            color: red;
        }
    </style>
</head>

<body>
    <!--<button id="exportBtn">Export to Excel</button>-->

    <table cellpadding="6" border="1">
        <thead>
            <tr>
                <th>Tran ID (04)</th>
                <th>Destination Bank (04)</th>
                <th>Destination Br (03)</th>
                <th>Destination Account (12)</th>
                <th>Account Name</th>
                <th>TRN Code (02)</th>
                <th>Return Code</th>
                <th>Cr/Dr Code (01)</th>
                <th>Return Date (06)</th>
                <th>Amount (12)</th>
                <th class="highlight">Currency Code (03)</th>
                <th class="highlight">Originating Bank (04)</th>
                <th class="highlight">Originating Branch (03)</th>
                <th class="highlight">Originating Account (12)</th>
                <th class="highlight">Originating Account Name (20)</th>
                <th class="highlight">Particulars (15)</th>
                <th class="highlight">Reference (15)</th>
                <th class="highlight">Value Date (Yymmdd) (06)</th>
                <th class="highlight">Security Field (06)</th>
                <th class="highlight">Filter (01)</th>
            </tr>
        </thead>
        <tbody id="payrollTable">
            <?php if (!empty($data_set)): ?>
                <?php $i = 1; // Initialize counter before the loop ?>
                <?php foreach ($data_set as $row): ?>
                    <tr>
                        <td><?= sprintf("%03d", $i) ?></td>
                        <td><?= htmlspecialchars($row->Bnk_ID) ?></td>
                        <td><?= htmlspecialchars($row->Bnk_Br_ID) ?></td>
                        <td><?= htmlspecialchars($row->Account_no) ?></td>
                        <td><?= htmlspecialchars($row->Emp_Full_Name) ?></td>
                        <td>23</td>
                        <td>00</td>
                        <td>0</td>
                        <td>
                            <?php
                            date_default_timezone_set("Asia/Colombo"); // Set Sri Lanka timezone
                            echo date("ymd"); // Outputs date in YYMMDD format
                            ?>
                        </td>
                        <td class="red-text"><?= sprintf("%012d", $row->D_Salary) ?></td>
                        <td>SLR</td>
                        <td>7056</td>
                        <td>216</td>
                        <td>001000028421</td>
                        <td>VOICE OF ASIA</td>
                        <td>0000000000033</td>
                        <td>SALMARCH 2022</td>
                        <td>220411</td>
                        <td></td>
                        <td>@</td>
                    </tr>
                    <?php $i++; // Increment counter ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="27" style="text-align: center;">No data available</td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        document.getElementById('exportBtn').addEventListener('click', function () {
            // Get the table
            var table = document.getElementById('payrollTable');

            // Convert table to a workbook
            var workbook = XLSX.utils.table_to_book(table, { sheet: "Payroll Data" });

            // Export workbook to Excel
            XLSX.writeFile(workbook, 'Payroll_Report.xlsx');
        });
    </script>
</body>

</html>