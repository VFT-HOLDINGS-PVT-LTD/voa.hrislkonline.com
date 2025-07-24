<?php

// Sort the $data_set array by ass_dep_name
usort($data_set, function($a, $b) {
    return strcmp($a->ass_dep_name, $b->ass_dep_name);
});

// Get the current date
$date = date("Y/m/d");
$data_month;

// Include TCPDF library and create a new PDF document
// $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Create a new PDF document with A3 size
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A3', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Paysheet_Month_' . $data_month . '.pdf');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Header and Footer Data
$PDF_HEADER_TITLE = $data_cmp[0]->Company_Name;
$PDF_HEADER_LOGO_WIDTH = '0';
$PDF_HEADER_LOGO = '';
$PDF_HEADER_STRING = '';

$pdf->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE . '', $PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// Header and Footer Fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Monospaced Font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Auto Page Break
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Image Scale
// $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// Set image scale ratio to 80%
$pdf->setImageScale(1.6);


// Optional Language Array
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// Font Settings
$pdf->setFontSubsetting(true);
$pdf->SetFont('helvetica', '', 14, '', true);

// Page Settings
$pdf->SetMargins(5, 14, 15, 0, true);

// Group data by department
$ass_departments = [];
foreach ($data_set as $data) {
    $ass_departments[$data->ass_dep_name][] = $data;
}

// Function to generate PDF content for a single department
function generateDepartmentPDF($pdf, $ass_department_name, $ass_department_data, $data_year, $data_month) {
    // Start a new page for this department
    $pdf->AddPage('L', 'LEGAL');

    // Add the department heading
    $html = '
    <style>
        @media print {
            .page-break {
                page-break-before: always;
            }
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border-bottom: 1px solid black;
            font-size: 8px;
            padding: 5px;
        }
        th {
            font-weight: bold;
            border-top: 1px solid black;
        }
    </style>

    <h6 style="margin-left:0px; text-align:center;">PAY SHEET</h6>
    <div style="font-size: 11px; float: left; border-bottom: solid #000 1px;">
        Year: ' . $data_year . ' &nbsp; Month: ' . date('F', mktime(0, 0, 0, $data_month)) . '<span style="color:red"> &nbsp; Assigned Company: ' . $ass_department_name . '</span>
    </div>
    <br>
    <table cellpadding="6" style="margin-left: -10px;">
        <thead style="border-bottom: #000 solid 1px;">
            <tr style="border-bottom: 1px solid black; font-weight:bold"> 
                <th style="width:50px;">EMP NO</th>   
                <th style="width:90px;">DEP NAME</th>                                          
                <th style="width:130px;">NAME</th>                       
                <th style="width:50px;">BASIC SALARY</th>
                <th style="width:50px;">BR</th>
                <th style="width:50px;">TOTAL FOR EPF</th>
                <th style="width:65px; font-size: 7px;">PERFORMANCE</th>
                <th style="width:55px;">ATTENDANCES</th>
                <th style="width:50px;">TRANSPORT</th>
                <th style="width:50px;">FUEL</th>
                <th style="width:50px;">TRAVELING</th>
                <th style="width:50px;">SPALLOWANCE</th>
                <th style="width:50px;">INCREMENT</th>
                <th style="width:45px;">OTHER OT</th>
                <th style="width:55px;">GROSS PAY</th>

                <th style="width:40px; font-size: 7px;">ADV.PAID</th>
                <th style="width:45px;">P.A.Y.E</th>
                <th style="width:50px;">MOBILE-DED</th>
                <th style="width:50px;">FUEL-DED</th>
                <th style="width:50px;">OTHER-DED</th>
                <th style="width:50px;">WELFAIR</th>
                <th style="width:40px;">NO PAY</th>
                <th style="width:40px;">STAMP-D</th>              
                <th style="width:50px;">EPF 8%</th>
                <th style="width:50px;">TOT DEDUCTION</th>
                <th style="width:70px;">NET SALARY</th>                        
                <th style="width:50px;">EPF 12%</th>
                <th style="width:50px;">ETF 3%</th>
                <th style="width:70px; font-size: 7px;">BALANCE</th>                                                                                                                                                                                                            
            </tr>
        </thead>
        <tbody>';

    // Populate Table Rows
    foreach ($ass_department_data as $data) {
        $html .= '
        <tr>
            <td style="width:50px;">' . $data->EmpNo . '</td> 
            <td style="width:90px;">' . $data->Dep_Name . '</td>                                           
            <td style="width:130px;">' . $data->Emp_Full_Name . '</td>                       
            <td style="width:50px;">' . number_format($data->Basic_sal, 2, '.', ',')  . '</td>
            <td style="width:50px;">' . number_format($data->Br_pay, 2, '.', ',')  . '</td>
            <td style="width:50px;">' . number_format($data->Total_F_Epf, 2, '.', ',') . '</td>
            <td style="width:65px;">' . number_format($data->Performance, 2, '.', ',') . '</td>
            <td style="width:55px;">' . number_format($data->Attendances, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->Transport, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->Fuel, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->Traveling, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->SPAllowance, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->Increment, 2, '.', ',') . '</td>
            <td style="width:45px;">' . number_format($data->Other_OT, 2, '.', ',') . '</td>
            <td style="width:55px;">' .  number_format($data->Gross_pay, 2, '.', ',') . '</td>

            <td style="width:40px;">' . number_format($data->Salary_advance, 2, '.', ',')  . '</td>
            <td style="width:45px;">' . number_format($data->Payee_amount, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->Mobile_Ded, 2, '.', ',') . '</td>
            <td style="width:50px; ">' . number_format($data->Fuel_Ded, 2, '.', ',') . '</td>
            <td style="width:50px; ">' . number_format($data->Deduct_1, 2, '.', ',') . '</td> 
            <td style="width:50px; ">' . number_format($data->Wellfare, 2, '.', ',') . '</td>                                                              
            <td style="width:40px;">' . number_format($data->no_pay_deduction, 2, '.', ',') . '</td>
            <td style="width:40px;">' . number_format($data->Stamp_duty, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->EPF_Worker_Amount, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->tot_deduction, 2, '.', ',') . '</td>
            <td style="width:70px;">' . number_format($data->D_Salary, 2, '.', ',') . '</td>                        
            <td style="width:50px;">' . number_format($data->EPF_Employee_Amount, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->ETF_Amount, 2, '.', ',') . '</td>
            <td style="width:70px;">' . number_format($data->D_Salary, 2, '.', ',') . '</td>                                                                                                                                                                                                            
        </tr>';
    }
    
    $html .= '</tbody></table>';
    
    // Output Department Table
    $pdf->writeHTML($html, true, false, true, false, '');
}

// Iterate over each department and generate the PDF content
foreach ($ass_departments as $ass_department_name => $ass_department_data) {
    generateDepartmentPDF($pdf, $ass_department_name, $ass_department_data, $data_year, $data_month);
}

// Output the final PDF
$pdf->Output('Paysheet_' . $data_month . '.pdf', 'I');

?>
