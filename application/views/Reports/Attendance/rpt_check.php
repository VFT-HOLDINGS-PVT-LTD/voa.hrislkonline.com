<?php

require_once('application/third_party/tcpdf/tcpdf.php'); // Include the TCPDF library

function formatTime($time) {
    if ($time != 'No Data' && !empty($time)) {
        $timeParts = explode(":", $time);
        if (count($timeParts) >= 2) {
            return $timeParts[0] . ':' . $timeParts[1];
        }
    }
    return $time;
}

$date = date("Y/m/d");

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
ini_set('memory_limit', '-1');
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('IN OUT Report from ' . $f_date . ' to ' . $t_date);
$pdf->SetSubject('Attendance Report');
$pdf->SetKeywords('TCPDF, PDF, attendance, report');

// Set default header and footer
$PDF_HEADER_TITLE = $data_cmp[0]->Company_Name . "- " . 'Department: ' . $dept . ' - IN OUT REPORT';
$PDF_HEADER_LOGO_WIDTH = '0';
$PDF_HEADER_LOGO = '';
$PDF_HEADER_STRING = '';

$pdf->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE, $PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set margins: left = 0, top = 12, right = 0
$pdf->SetMargins(5, 12, 5);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(5);

// Enable auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setFontSubsetting(true);
$pdf->SetFont('helvetica', '', 5.59);
$pdf->AddPage();

$html = '<div style="font-size:11px;">From Date: ' . $f_date . ' - To Date: ' . $t_date . '</div>';
$html .= '<table border="0" cellpadding="4">';
$html .= '<tr>
            <th style="width:50px;border-bottom:1px solid black;border-top:1px solid black;">EMP NO</th>
            <th style="width:90px;border-bottom:1px solid black;border-top:1px solid black;">NAME</th>
            <th style="width:50px;border-bottom:1px solid black;border-top:1px solid black;">HEADING</th>';

// Generate table headers with day numbers and days of the week
$daysInMonth = date('t', strtotime($f_date));
for ($day = 1; $day <= $daysInMonth; $day++) {
    $dateString = date('Y-m-', strtotime($f_date)) . $day;
    $dayOfWeek = date('D', strtotime($dateString));
    $html .= '<th style="width:25.5px;border-bottom:1px solid black;border-top:1px solid black;">' . $day . ' ' . $dayOfWeek . '</th>';
}

$html .= '<th style="width:25.5px;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;">Total</th>';
$html .= '</tr><tbody>';

$tableCounter = 0; // Counter to track the number of tables

$tableCounter = 0; // Counter to track the number of tables

foreach ($attendance_data as $data) {
    if ($tableCounter > 0 && $tableCounter % 5 == 0) {
        // Close the table and write the current content to the PDF
        $html .= '</tbody></table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Add a new page
        $pdf->AddPage();

        // Reinitialize the HTML for the table header
        $html = '<table border="0" cellpadding="4">';
        $html .= '<thead><tr>
                    <th style="width:50px;border-bottom:1px solid black;border-top:1px solid black;">EMP NO</th>
                    <th style="width:90px;border-bottom:1px solid black;border-top:1px solid black;">NAME</th>
                    <th style="width:50px;border-bottom:1px solid black;border-top:1px solid black;">HEADING</th>';

        // Recreate the headers with days of the week
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateString = date('Y-m-', strtotime($f_date)) . $day;
            $dayOfWeek = date('D', strtotime($dateString));
            $html .= '<th style="width:25.5px;border-bottom:1px solid black;border-top:1px solid black;">'.$day.'  ' . $dayOfWeek . '</th>';
        }
        $html .= '<th style="width:25.5px;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;">Total</th>';
        $html .= '</tr></thead><tbody>';
    }

    $html .= '<tr>
                <td rowspan="5" style="text-align:center;width:50px;">' . $data['EmpNo'] . '</td>
                <td rowspan="5" style="text-align:center;width:90px;">' . $data['Emp_Full_Name'] . '</td>
                <td style="text-align:center;width:50px;">SH IN</td>';
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $ftime = isset($data['Attendance'][$day]->FTime) ? formatTime($data['Attendance'][$day]->FTime) : '00:00';
        $html .= '<td style="text-align:center;width:25.5px;">' . $ftime . '</td>';
    }
    $html .= '<th style="width:25.5px;border-right:1px solid black;border-left:1px solid black;"></th>';
    $html .= '</tr>';

    $html .= '<tr>
                <td style="text-align:center;width:50px;">SH OUT</td>';
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $ttime = isset($data['Attendance'][$day]->TTime) ? formatTime($data['Attendance'][$day]->TTime) : '00:00';
        $html .= '<td style="text-align:center;width:25.5px;">' . $ttime . '</td>';
    }
    $html .= '<th style="width:25.5px;border-right:1px solid black;border-left:1px solid black;"></th>';
    $html .= '</tr>';

    $html .= '<tr>
                <td style="text-align:center;width:50px;">IN</td>';
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $intime = isset($data['Attendance'][$day]->InTime) ? formatTime($data['Attendance'][$day]->InTime) : ' ';
        $html .= '<td style="text-align:center;width:25.5px;">' . $intime . '</td>';
    }
    $html .= '<th style="width:25.5px;border-right:1px solid black;border-left:1px solid black;"></th>';
    $html .= '</tr>';

    $html .= '<tr>
                <td style="text-align:center;width:50px;">OUT</td>';
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $outtime = isset($data['Attendance'][$day]->OutTime) ? formatTime($data['Attendance'][$day]->OutTime) : ' ';
        $html .= '<td style="text-align:center;width:25.5px;">' . $outtime . '</td>';
    }
    $html .= '<th style="width:25.5px;border-right:1px solid black;border-left:1px solid black;"></th>';
    $html .= '</tr>';

    $html .= '<tr>
            <td colspan="1" style="text-align:center;">Leave</td>';
for ($day = 1; $day <= $daysInMonth; $day++) {
    // Get the DayStatus for the current day, or set it to an empty string if not available
    $dayStatus = isset($data['Attendance'][$day]->DayStatus) ? $data['Attendance'][$day]->DayStatus : '';

    // Only display "LV" for Leave and "HD" for Holiday
    if ($dayStatus == 'LV') {
        $statusDisplay = 'LV';
    }elseif ($dayStatus == 'CUP-LV') {
        $statusDisplay = 'CUP-LV';
    }elseif ($dayStatus == 'HD') {
        $statusDisplay = 'HD';
    }elseif ($dayStatus == 'HFD') {
        $statusDisplay = 'HFD';
    }elseif ($dayStatus == 'AB') {
        $statusDisplay = 'AB';
    }elseif ($dayStatus == 'OFF') {
        $statusDisplay = 'OFF';
    }else {
        $statusDisplay = ''; // Show nothing for other statuses
    }

    // Render the status (LV/HD) or leave it blank
    $html .= '<td style="text-align:center;width:25.5px;">' . $statusDisplay . '</td>';
}

$html .= '<th style="width:25.5px;border-right:1px solid black;border-left:1px solid black;"></th>';
$html .= '</tr>';



    // Add an empty row for spacing between sets
    $html .= '<tr><td colspan="' . ($daysInMonth + 3) . '" style="height:20px;border-right:1px solid black;"></td>
    <td colspan="' . ($daysInMonth + 1) . '" style="height:20px;border-right:1px solid black;border-left:1px solid black;"></td>
    </tr>';
    

    $tableCounter++; // Increment the counter
}

// Close the last table and write the final content
$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF
$pdf->Output('IN_OUT_Report_' . $f_date . '_to_' . $t_date . '.pdf', 'I');

?>
