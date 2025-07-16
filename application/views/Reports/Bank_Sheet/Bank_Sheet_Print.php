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
            background-color: #e19c13;
            color: white
        }

        .red-text {
            color: red;
        }
    </style>
</head>

<body>
    <!--<button id="exportBtn">Export to Excel</button>-->
    <img src="<?php echo base_url(); ?>assets/images/SampathBank.png" alt="" srcset="">
    <table cellpadding="6" border="1">
        <thead>
            <tr>
                <th class="highlight">COMPANY REF</th>
                <th class="highlight">BENEFICIARY NAME</th>
                <th class="highlight">ACCOUNT NO</th>
                <th class="highlight">BANK CODE</th>
                <th class="highlight">BRANCH CODE</th>
                <th class="highlight">AMOUNT</th>
                <th class="highlight">REMARKS</th>
                <th class="highlight">NIC</th>
                <th class="highlight">SENDER MOBILE</th>
                <th class="highlight">RECEIVER MOBILE</th>
                <th class="highlight">BENEFICIARY EMAIL</th>
            </tr>
        </thead>
        <tbody id="payrollTable">
            <?php if (!empty($data_set)): ?>
                <?php $i = 1; // Initialize counter before the loop ?>
                <?php foreach ($data_set as $row): ?>
                    <tr>
                        <td>SAL<?php echo strtoupper(date('F')) . date('Y');?></td>
                        <td><?= htmlspecialchars($row->Emp_Full_Name) ?></td>
                        <td><?= htmlspecialchars($row->Account_no) ?></td>
                        <td><?= htmlspecialchars($row->Bnk_ID) ?></td>
                        <td><?= htmlspecialchars($row->Bnk_Br_ID) ?></td>
                        <td><?= sprintf("%012d", $row->D_Salary) ?></td>
                        <td>SAL<?php echo strtoupper(date('F')) . date('Y');?></td>
                        <td><?= $row->NIC ?></td>
                        <td>0</td>
                        <td><?= $row->Tel_mobile ?></td>
                        <td><?= $row->E_mail ?></td>
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