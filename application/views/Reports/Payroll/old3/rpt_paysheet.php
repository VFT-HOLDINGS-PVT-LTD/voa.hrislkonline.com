<?php

$date = date("Y/m/d");

$data_month;

//var_dump($data_c[0]->id);die;
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Paysheet_Month_' . $data_month . '.pdf');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . '', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


$PDF_HEADER_TITLE = $data_cmp[0]->Company_Name;
$PDF_HEADER_LOGO_WIDTH = '0';
$PDF_HEADER_LOGO = '';
$PDF_HEADER_STRING = '';


// set default header data
$pdf->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE . '', $PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------    
// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('helvetica', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.



$pdf->SetMargins(5, 14, 15, 0, true);
$pdf->AddPage('L', 'LEGAL');
//$pdf->SetMargins(0, 0, 0, true);
// set text shadow effect
$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.0, 'depth_h' => 0.0, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

// Set some content to print
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
        border-bottom: 1px dashed black;
        font-size: 8px;
        padding: 3px;
    }
    th {
        font-weight: bold;
        border-top: 1px solid black;
    }
</style>

<h6 style="margin-left:0px; text-align:center;">PAY SHEET</h6>
<div style="font-size: 11px; float: left; border-bottom: solid #000 1px;">Year: ' . $data_year . ' &nbsp; Month: ' . date('F', mktime(0, 0, 0, $data_month)) . '</div><br>';

$chunks = array_chunk($data_set, 13);

foreach ($chunks as $index => $chunk) {
    $html .= '<table cellpadding="3">
        <thead style="border-bottom: #000 solid 1px;">
            <tr style="border-bottom: 1px solid black; font-weight:bold"> 
                <th style="width:60px;">EMP NO</th>                      
                <th style="width:100px;">NAME</th>                       
                <th style="width:50px;">BASIC SALARY</th>
                <th style="width:40px;">BR</th>
                <th style="width:50px;">TOTAL FOR EPF</th>
                <th style="width:60px;">OT 15</th>
                <th style="width:60px;">FIXED</th>
                <th style="width:55px;">PROD INC I</th>
                <th style="width:50px;">PROD INC II</th>
                <th style="width:40px;">SPC.ALL</th>
                <th style="width:55px;">GROSS PAY</th>
                <th style="width:55px;">EPF 8%</th>
                <th style="width:60px;">ADV.PAID</th>
                <th style="width:55px;">OTHER</th>                       
                <th style="width:70px;">P.A.Y.E</th>
                <th style="width:70px;">WELFAIR</th>
                <th style="width:50px;">TOTAL DEDUCTION</th>
                <th style="width:50px;">NET SALARY</th>                        
                <th style="width:50px;">EPF 12%</th>
                <th style="width:50px;">BALANCE</th>                                                                                                                                                                                                            
            </tr>
            <tr style="border-bottom: 1px solid black; font-weight:bold"> 
                <th style="width:60px; border-bottom: 1px solid black;"></th>                      
                <th style="width:100px; border-bottom: 1px solid black;"></th>                      
                <th style="width:50px; border-bottom: 1px solid black;">SAL ARREAS</th>
                <th style="width:40px; border-bottom: 1px solid black;">NO PAY</th>
                <th style="width:50px; border-bottom: 1px solid black;">SUNDAY OT</th>
                <th style="width:60px; border-bottom: 1px solid black;">OT 20</th>
                <th style="width:60px; border-bottom: 1px solid black;">ATTBON</th>
                <th style="width:55px; border-bottom: 1px solid black;">SPC INC</th>
                <th style="width:50px; border-bottom: 1px solid black;">OTHER ALLOW</th>
                <th style="width:40px; border-bottom: 1px solid black;"></th>
                <th style="width:55px; border-bottom: 1px solid black;"></th>
                <th style="width:55px; border-bottom: 1px solid black;"></th>
                <th style="width:60px; border-bottom: 1px solid black;">FEST ADV</th>
                <th style="width:55px; border-bottom: 1px solid black;">UNIFORM</th>                      
                <th style="width:70px; border-bottom: 1px solid black;">STAMP</th>
                <th style="width:70px; border-bottom: 1px solid black;">LOAN</th>
                <th style="width:50px; border-bottom: 1px solid black;"></th>
                <th style="width:50px; border-bottom: 1px solid black;">STAMP</th>                       
                <th style="width:50px; border-bottom: 1px solid black;">ETF 3%</th>
                <th style="width:50px; border-bottom: 1px solid black;"></th>                                                                                                                                                                                                                      
            </tr>
        </thead>
        <tbody>';

    foreach ($chunk as $data) {
        $html .= '<tr>
            <td style="width:60px;">' . $data->EmpNo . '</td>                      
            <td style="width:100px;">' . $data->Emp_Full_Name . '</td>                       
            <td style="width:50px;">' . number_format($data->Basic_sal, 2, '.', ',')  . '</td>
            <td style="width:40px;">' . number_format($data->Br_pay, 2, '.', ',')  . '</td>
            <td style="width:50px;">' . number_format($data->Total_F_Epf, 2, '.', ',') . '</td>
            <td style="width:60px;">' . number_format($data->Normal_OT_Pay, 2, '.', ',') . '</td>
            <td style="width:60px;">' . number_format($data->Fixed, 2, '.', ',') . '</td>
            <td style="width:55px;">' . number_format($data->Prod_inc1, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->Prod_inc2, 2, '.', ',') . '</td>
            <td style="width:40px;">' . number_format($data->spc_all, 2, '.', ',') . '</td>
            <td style="width:55px;">' .  number_format($data->Gross_pay, 2, '.', ',') . '</td>
            <td style="width:55px;">' . number_format($data->EPF_Worker_Amount, 2, '.', ',') . '</td>
            <td style="width:60px;">' . number_format($data->Salary_advance, 2, '.', ',')  . '</td>
            <td style="width:55px;">' . number_format($data->Deductions, 2, '.', ',') . '</td>                       
            <td style="width:70px;">' . number_format($data->Payee_amount, 2, '.', ',') . '</td>
            <td style="width:70px;">' . number_format($data->Wellfare, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->tot_deduction, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->D_Salary, 2, '.', ',') . '</td>                        
            <td style="width:50px;">' . number_format($data->EPF_Employee_Amount, 2, '.', ',') . '</td>
            <td style="width:50px;">' . number_format($data->Net_salary, 2, '.', ',') . '</td>                                                     
        </tr>
        <tr>
            <td style="width:60px; border-bottom: 1px solid black;"></td>                       
            <td style="width:100px; border-bottom: 1px solid black;"></td>                          
            <td style="width:50px; border-bottom: 1px solid black;">' . number_format(0, 2, '.', ',')  . '</td>
            <td style="width:40px; border-bottom: 1px solid black;">' . number_format($data->no_pay_deduction, 2, '.', ',') . '</td>
            <td style="width:50px; border-bottom: 1px solid black;">' . number_format($data->Double_OT_Pay, 2, '.', ',')  . '</td>
            <td style="width:60px; border-bottom: 1px solid black;">' . number_format($data->Double_OT_Pay, 2, '.', ',') . '</td>
            <td style="width:60px; border-bottom: 1px solid black;">' . number_format($data->Att_Allowance, 2, '.', ',') . '</td>
            <td style="width:55px; border-bottom: 1px solid black;">' . number_format($data->Spc_inc1, 2, '.', ',') . '</td>
            <td style="width:50px; border-bottom: 1px solid black;">' . number_format($data->Allowances, 2, '.', ',') . '</td>
            <td style="width:40px; border-bottom: 1px solid black;"></td>
            <td style="width:55px; border-bottom: 1px solid black;"></td>
            <td style="width:55px; border-bottom: 1px solid black;"></td>
            <td style="width:60px; border-bottom: 1px solid black;">' . number_format($data->Festivel_Advance, 2, '.', ',') . '</td>
            <td style="width:55px; border-bottom: 1px solid black;">' . number_format(0, 2, '.', ',')  . '</td>                           
            <td style="width:70px; border-bottom: 1px solid black;">' . number_format(0, 2, '.', ',') . '</td>                               
            <td style="width:70px; border-bottom: 1px solid black;">' . number_format($data->Loan_Instalment, 2, '.', ',') . '</td>                       
            <td style="width:50px; border-bottom: 1px solid black;"></td>
            <td style="width:50px; border-bottom: 1px solid black;">' . number_format(0, 2, '.', ',') . '</td>
            <td style="width:50px; border-bottom: 1px solid black;">' . number_format($data->ETF_Amount, 2, '.', ',') . '</td>
            <td style="width:50px; border-bottom: 1px solid black;"></td>                        
        </tr>';
    }
    $html .= '</tbody></table>';

    if ($index < count($chunks) - 1) {
        $html .= '<div class="page-break"></div>';
    }
}

$html .= '</div>';


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------    
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('Paysheet_Month_' . $data_month . '.pdf', 'I');

//============================================================+
    // END OF FILE
    //============================================================+
