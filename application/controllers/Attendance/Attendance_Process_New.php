<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_Process_New extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!($this->session->userdata('login_user'))) {
            redirect(base_url() . "");
        }
        /*
         * Load Database model
         */
        $this->load->model('Db_model', '', true);
        $this->load->library('session');
    }

    /*
     * Index page
     */

    public function index()
    {

        $data['title'] = "Attendance Process | HRM System";
        $data['data_set'] = $this->Db_model->getData('EmpNo,Emp_Full_Name', 'tbl_empmaster');
        $data['data_shift'] = $this->Db_model->getData('ShiftCode,ShiftName', 'tbl_shifts');
        $data['data_roster'] = $this->Db_model->getData('RosterCode,RosterName', 'tbl_rosterpatternweeklyhd');

        $data['sh_employees'] = $this->Db_model->getfilteredData("SELECT
                                                                    tbl_empmaster.EmpNo
                                                                FROM
                                                                    tbl_empmaster
                                                                        LEFT JOIN
                                                                    tbl_individual_roster ON tbl_individual_roster.EmpNo = tbl_empmaster.EmpNo
                                                                    where tbl_individual_roster.EmpNo is null AND tbl_empmaster.status=1 and Active_process=1");
        //  $month = $this->session->userdata('monthData');
        // $month = "0";
        $monthData = $this->Db_model->getfilteredData("SELECT * FROM `tbl_att_process`;");
        $month = $monthData[0]->selected_month;

        // if($month == null){
        //     $month = "0";
        // }

        $data['MData'] = $month;

        // date_default_timezone_set('Asia/Colombo');
        // $from_date = date('Y-m-01'); // First day of the current month
        // $to_date = date('Y-m-t'); // Last day of the current month

        date_default_timezone_set('Asia/Colombo');

        $year = date('Y'); // Current year. You can also get this from POST if needed.

        // Create from_date and to_date based on selected month
        $from_date = date("Y-$month-01", strtotime("$year-$month-01"));
        $to_date = date("Y-m-t", strtotime($from_date));

        $ID_Roster = $this->Db_model->getfilteredData("SELECT COUNT(ID_Roster) AS ID FROM `tbl_individual_roster` WHERE `tbl_individual_roster`.`Is_processed` = '0' AND FDate BETWEEN '" . $from_date . "' AND '" . $to_date . "';");
        $data['WData'] = $ID_Roster[0]->ID;

        $this->load->view('Attendance/Attendance_Process/index', $data);
    }

    public function re_process()
    {
        $data['title'] = "Attendance Process | HRM System";
        $data['data_set'] = $this->Db_model->getData('EmpNo,Emp_Full_Name', 'tbl_empmaster');
        $data['data_shift'] = $this->Db_model->getData('ShiftCode,ShiftName', 'tbl_shifts');
        $data['data_roster'] = $this->Db_model->getData('RosterCode,RosterName', 'tbl_rosterpatternweeklyhd');

        $data['sh_employees'] = $this->Db_model->getfilteredData("SELECT
                                                                    tbl_empmaster.EmpNo
                                                                FROM
                                                                    tbl_empmaster
                                                                        LEFT JOIN
                                                                    tbl_individual_roster ON tbl_individual_roster.EmpNo = tbl_empmaster.EmpNo
                                                                    where tbl_individual_roster.EmpNo is null AND tbl_empmaster.status=1 and Active_process=1");

        $this->load->view('Attendance/Attendance_REProcess/index', $data);
    }

    /*
     * Insert Data
     */



    public function emp_attendance_process()
    {

        date_default_timezone_set('Asia/Colombo');
        $month = $this->input->post('cmb_month');

        //   If a new month is submitted, update session
        // if ($month) {
        //     // $this->session->set_userdata('monthData', $month);

        //     $data = array(
        //         'month' => $month
        //     );
        //     $this->Db_model->insertData('tbl_att_process', $data);
        // }


        date_default_timezone_set('Asia/Colombo');

        $year = date('Y'); // Current year. You can also get this from POST if needed.

        // Create from_date and to_date based on selected month
        $from_date = date("Y-m-01", strtotime("$year-$month-01")); // First day of the month
        $to_date = date("Y-m-t", strtotime($from_date));           // Last day of the month

        $query1 = "UPDATE tbl_individual_roster SET Is_processed = 0 WHERE FDate BETWEEN '" . $from_date . "' AND '" . $to_date . "';";
        

        $monthData = $this->Db_model->getfilteredData("SELECT * FROM `tbl_att_process`;");
        $month1 = $monthData[0]->month;

        if ($month1 == 0) {
          // Run the custom query
            $result1 = $this->Db_model->getUpdateData($query1);

            $query = "UPDATE `tbl_att_process` SET month = 1,selected_month = '" . $month . "' ;";

            // Run the custom query
            $result = $this->Db_model->getUpdateData($query);
        }


        if (empty($month)) {
            // $this->session->set_flashdata('error_message', 'Please Select Month');
            redirect('/Attendance/Attendance_Process_New');
        } else {
            date_default_timezone_set('Asia/Colombo');
            $year = date("Y");
            $HasRow = $this->Db_model->getfilteredData("SELECT COUNT(EmpNo) AS HasRow FROM tbl_individual_roster WHERE Is_processed = 0 AND EXTRACT(MONTH FROM FDate)=$month and EXTRACT(YEAR FROM FDate)=$year");
            if (!empty($HasRow)) {


                /*
                 * Get Employee Data
                 * Emp no , EPF No, Roster Type, Roster Pattern Code, Status
                 */
                //        $dtEmp['EmpData'] = $this->Db_model->getfilteredData("SELECT EmpNo,Enroll_No, EPFNO,RosterCode, Status  FROM  tbl_empmaster where status=1");
                $dtEmp['EmpData'] = $this->Db_model->getfilteredData("select * from tbl_individual_roster where EXTRACT(MONTH FROM FDate)=$month and EXTRACT(YEAR FROM FDate)=$year and Is_processed = '0'");

                $AfterShift = 0;

                if (!empty($dtEmp['EmpData'])) {

                    for ($x = 0; $x < count($dtEmp['EmpData']); $x++) {
                        $EmpNo = $dtEmp['EmpData'][$x]->EmpNo;

                        $FromDate = $dtEmp['EmpData'][$x]->FDate;
                        $ToDate = $dtEmp['EmpData'][$x]->TDate;
                        //Check If From date less than to Date
                        if ($FromDate <= $ToDate) {
                            $settings = $this->Db_model->getfilteredData("SELECT tbl_setting.Group_id,tbl_setting.Ot_m,tbl_setting.Ot_e,tbl_setting.Ot_d_Late,
                        tbl_setting.Late,tbl_setting.Ed,tbl_setting.Min_time_t_ot_m,tbl_setting.Min_time_t_ot_e,
                        tbl_setting.late_Grs_prd,tbl_setting.`Round`,tbl_setting.Hd_d_from,tbl_setting.Dot_f_holyday,tbl_setting.Dot_f_offday
                        FROM tbl_setting INNER JOIN tbl_emp_group ON tbl_setting.Group_id = tbl_emp_group.Grp_ID
                        INNER JOIN tbl_empmaster ON tbl_empmaster.Grp_ID = tbl_emp_group.Grp_ID WHERE tbl_empmaster.EmpNo = '$EmpNo'");
                            $ApprovedExH = 0;
                            $DayStatus = "not";
                            $ID_Roster = '';
                            $InDate = '';
                            $InTime = '';
                            $OutDate = '';
                            $OutTime = '';

                            $ID_Roster1 = '';
                            $InDate1 = '';
                            $InTime1 = '';
                            $OutDate1 = '';
                            $OutTime1 = '';

                            $from_date = '';
                            $from_time = '';
                            $to_date = '';
                            $to_time = '';
                            $Day_Type = 0;
                            $shift_type = '';

                            $from_date1 = '';
                            $from_time1 = '';
                            $to_date1 = '';
                            $to_time1 = '';
                            $Day_Type1 = 0;
                            $shift_type1 = '';
                            $shift_type2 = '';
                            $shift_day1 = '';
                            $GracePrd1 = '';
                            $cutofftime1 = '';

                            $lateM = '';
                            $ED = '';
                            $DayStatus = '';
                            $AfterShiftWH = '';
                            $BeforeShift = '';
                            $DOT = '';
                            $Late_Status = 0;
                            $NetLateM = 0;
                            $Nopay = 0;
                            $Nopay_Hrs = 0;
                            $OuttimeMS = 0;
                            $newDate = 0;
                            $ShiftType = ' ';
                            $DayStatus = ' ';
                            $InDate2 = '2000-00-00';
                            $InTime2 = '00:00:00';
                            $OutDate2 = '2000-00-00';
                            $OutTime2 = '00:00:00';



                            // **************************************************************************************//
                            // tbl_individual_roster eken shift details tika gannawa
                            $ShiftDetails['shift'] = $this->Db_model->getfilteredData("select `ID_Roster`,`ShType`,`ShiftDay`,`FDate`,`FTime`,`TDate`,`TTime`,`GracePrd`,`HDSession` from tbl_individual_roster where FDate = '$FromDate' AND EmpNo = '$EmpNo' ");

                            if (!empty($ShiftDetails['shift'][0]->ID_Roster)) {
                                $ID_Roster = $ShiftDetails['shift'][0]->ID_Roster;
                                if ($ShiftDetails['shift'][0]->ShType == null || empty($ShiftDetails['shift'][0]->ShType)) {
                                    $shift_type = 'EX';
                                }
                                $shift_type = $ShiftDetails['shift'][0]->ShType;
                                $shift_day = $ShiftDetails['shift'][0]->ShiftDay;
                                $from_date = $ShiftDetails['shift'][0]->FDate;
                                $from_time = $ShiftDetails['shift'][0]->FTime;
                                $to_date = $ShiftDetails['shift'][0]->TDate;
                                $to_time = $ShiftDetails['shift'][0]->TTime;
                                $GracePrd = $ShiftDetails['shift'][0]->GracePrd;
                                $cutofftime = $ShiftDetails['shift'][0]->HDSession;

                                if ($shift_type == "DU" || $shift_type == "EX" || $shift_type == " " || $shift_type == "null" || $shift_type == "") {
                                    $InDate = '';
                                    $InTime = '';
                                    $OutDate = '';
                                    $OutTime = '';

                                    $lateM = '';
                                    $ED = '';
                                    $DayStatus = '';
                                    $AfterShiftWH = '';
                                    $DOT = '';


                                    // from - old
                                    // $from_datetime = $from_date . ' ' . $from_time;
                                    // $time = new DateTime($from_datetime);
                                    // $time->modify('-2 hours');
                                    // $modified_fromtimepre = $time->format('Y-m-d H:i:s');
                                    // // from - future
                                    // $from_datetimen = $from_date . ' ' . $from_time;
                                    // $time = new DateTime($from_datetimen);
                                    // $time->modify('+2 hours');
                                    // $modified_fromtimeold = $time->format('Y-m-d H:i:s');


                                    // // to - old
                                    // $to_datetime = $to_date . ' ' . $to_time;
                                    // $time = new DateTime($to_datetime);
                                    // $time->modify('-2 hours');
                                    // $modified_totimepre = $time->format('Y-m-d H:i:s');
                                    // // to - future
                                    // $to_datetimen = $to_date . ' ' . $to_time;
                                    // $time = new DateTime($to_datetimen);
                                    // $time->modify('+2 hours');
                                    // $modified_totimeold = $time->format('Y-m-d H:i:s');

                                    // Get the CheckIN
                                    $dt_in_Records['dt_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from 
                                    tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='0' ");
                                    $InDate = $dt_in_Records['dt_Records'][0]->AttDate;
                                    $InTime = $dt_in_Records['dt_Records'][0]->INTime;

                                    // Get the CheckOut
                                    $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from 
                                    tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='1' ");
                                    $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                                    $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;

                                    // Out Ekak nethnm check nextday(1st nextDay)
                                    if ($OutTime == null) {
                                        $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                                        // $newDate = $to_date1;

                                        // $to_datetimen = $to_date . ' ' . $to_time;
                                        // $time = new DateTime($to_datetimen);
                                        // $time->modify('+27 hours');
                                        // $modified_totimeold = $time->format('Y-m-d H:i:s');

                                        // Get the CheckOut in the nextDay (before 9am)
                                        $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                    tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $newDate . "' AND Status='1' ");
                                        $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                                        $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;
                                    }
                                    // if(!empty($OutTime)){
                                    $dt_out_RecordsMS['dt_out_Recordsms'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                    tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $newDate . "' AND Status='0' AND AttTime < '" . $OutTime . "' ");
                                    // }
                                    $OuttimeMS = $dt_out_RecordsMS['dt_out_Recordsms'][0]->OutTime;

                                    if (!empty($OuttimeMS)) {
                                        $OutTime = null;
                                        $OutDate = null;

                                    }



                                }
                                $Shift_Day = '';
                                $SH['SH'] = $this->Db_model->getfilteredData("select ID_roster,EmpNo,ShiftCode,ShType,ShiftDay,Day_Type,FDate,FTime,TDate,TTime,ShType,GracePrd from tbl_individual_roster where Is_processed=0 and EmpNo='$EmpNo' and FDate='$FromDate'");
                                // $SH_Code = $SH['SH'][0]->ShiftCode;
                                $Shift_Day = $SH['SH'][0]->ShiftDay;
                                // //****Shift Type DU| EX
                                $ShiftType = $SH['SH'][0]->ShType;
                                // //****Individual Roster ID
                                // $ID_Roster = $SH['SH'][0]->ID_roster;
                                // //****Shift from time
                                $SHFT = $SH['SH'][0]->FTime;
                                $SHFD = $SH['SH'][0]->FDate;
                                // //****Shift to time
                                $SHTT = $SH['SH'][0]->TTime;
                                $SHTD = $SH['SH'][0]->TDate;

                                // Combine into full datetime strings
                                $ShiftStart = strtotime($SHFD . ' ' . $SHFT);
                                $ShiftEnd = strtotime($SHTD . ' ' . $SHTT);

                                // // //****Day Type Full day or Half day (1)or 0.5
                                // // $DayType = $SH['SH'][0]->Day_Type;

                                // // $GracePrd = $SH['SH'][0]->GracePrd;
                                // $SH_Code = 0;
                                // $Shift_Day = 0;
                                // $ShiftType = 0;
                                // $ID_Roster = 0;
                                // $Round = 0 ;
                                // /*
                                //  * Get OT Pattern Details
                                //  */
                                // // $OT['OT'] = $this->Db_model->getfilteredData("SELECT tbl_ot_pattern_dtl.DayCode,tbl_ot_pattern_dtl.OTCode,tbl_empmaster.EmpNo,tbl_ot_pattern_dtl.OTPatternName,tbl_ot_pattern_dtl.DUEX,tbl_ot_pattern_dtl.BeforeShift,tbl_ot_pattern_dtl.MinBS,tbl_ot_pattern_dtl.AfterShift,tbl_ot_pattern_dtl.MinAS,tbl_ot_pattern_dtl.RoundUp,tbl_ot_pattern_dtl.Rate,tbl_ot_pattern_dtl.Deduct_LNC FROM tbl_ot_pattern_dtl RIGHT JOIN tbl_empmaster ON tbl_ot_pattern_dtl.OTCode = tbl_empmaster.OTCode WHERE tbl_ot_pattern_dtl.DayCode ='$Shift_Day' and tbl_ot_pattern_dtl.DUEX='$ShiftType'");
                                // $AfterShiftWH = 0;

                                // $Round = $OT['OT'][0]->RoundUp;
                                // $BeforeShift = $OT['OT'][0]->BeforeShift;
                                // $AfterShift = $OT['OT'][0]->AfterShift;
                                // $Rate = $OT['OT'][0]->Rate;
                                // $DayCode = $OT['OT'][0]->DayCode;
                                // $Deduct_Lunch = $OT['OT'][0]->Deduct_LNC;
                                // $MinAS = $OT['OT'][0]->MinAS;

                                $lateM = 0;
                                $BeforeShift = 0;
                                $AfterShift = 0;
                                $Late_Status = 0;
                                $NetLateM = 0;
                                $ED = 0;
                                $EDF = 0;
                                $Att_Allowance = 1;
                                $Nopay = 0;
                                $MinAS = 0;
                                $Day = 0;
                                $apoint_date = 0;

                                // if ($InTime !== null) {
                                //     $InTime = substr($InTime, 0, 5);
                                // }
                                // **************************************************************************************//
                                if ($ShiftType == 'DU') {
                                    if ($OutTime == null || $OutTime == '') {
                                        $DayStatus = 'MS';
                                        $Late_Status = 0;
                                        $Nopay = 0;
                                        $Nopay_Hrs = 0;
                                        // $Nopay = 0.5;
                                        // $Nopay_Hrs = "255";
                                        // $DayStatus = 'NP-MS';
                                    }

                                    /*
                                     * If In Available & Out Missing
                                     */
                                    // if ($InTime != '') {
                                    //     $DayStatus = 'MS';
                                    //     $Late_Status = 0;
                                    //     $Nopay = 0;
                                    //     $Nopay_Hrs = 0;
                                    // }

                                    // If Out Available & In Missing
                                    // if ($OutTime != '') {
                                    //     $DayStatus = 'MS';
                                    //     $Late_Status = 0;
                                    //     $Nopay = 0;
                                    //     $Nopay_Hrs = 0;
                                    // }

                                    // If In Available & Out Missing
                                    if ($InTime != '' && $OutTime == '') {
                                        // $DayStatus = 'MS';
                                        // $Late_Status = 0;
                                        // $Nopay = 0.5;
                                        // $Nopay_Hrs = "255";
                                        // $DayStatus = 'NP-MS';
                                        $DayStatus = 'MS';
                                        $Late_Status = 0;
                                        $Nopay = 0;
                                        $Nopay_Hrs = 0;
                                    }

                                    // If Out Available & In Missing
                                    if ($OutTime != '' && $InTime == '') {
                                        // $DayStatus = 'MS';
                                        // $Late_Status = 0;
                                        // $Nopay = 0.5;
                                        // $Nopay_Hrs = "255";
                                        // $DayStatus = 'NP-MS';
                                        $DayStatus = 'MS';
                                        $Late_Status = 0;
                                        $Nopay = 0;
                                        $Nopay_Hrs = 0;
                                    }
                                    // **************************************************************************************//

                                    if ($InTime != '' && $InTime != $OutTime && $OutTime != '') {
                                        $FromStart = strtotime($InDate . ' ' . $InTime);
                                        $TotEnd = strtotime($OutDate . ' ' . $OutTime);

                                        $FromTo_Hrs = ($TotEnd - $FromStart) / 60;

                                        if ($FromTo_Hrs >= '510') {
                                            $Nopay = 0;
                                            $DayStatus = 'PR';
                                            $Nopay_Hrs = 0;
                                        } else {

                                            $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' AND Is_Approve = 1");
                                            if (empty($HaffDayaLeave[0]->Is_Approve)) {
                                                $Nopay = 0.5;
                                                $Nopay_Hrs = "255";
                                                $DayStatus = 'PR/NP-HFD';
                                            } else {
                                                $Nopay = 0;
                                                $Nopay_Hrs = "0";
                                                $DayStatus = 'PR';
                                            }
                                        }
                                    }
                                }

                                if ($InTime != '' && $InTime != $OutTime && $OutTime != '') {
                                    $FromStart = strtotime($InDate . ' ' . $InTime);
                                    $TotEnd = strtotime($OutDate . ' ' . $OutTime);

                                    $FromTo_Hrs = ($TotEnd - $FromStart) / 60;

                                    if ($FromTo_Hrs >= '510') {
                                        $Nopay = 0;
                                        $DayStatus = 'PR';
                                        $Nopay_Hrs = 0;
                                    } else {

                                        $ShortLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_shortlive WHERE EmpNo = $EmpNo AND tbl_shortlive.Date = '$FromDate' AND Is_Approve = 1");
                                        if (empty($ShortLeave[0]->Is_Approve)) {
                                            $Nopay = 0.5;
                                            $Nopay_Hrs = "255";
                                            $DayStatus = 'PR/NP-HFD';
                                        } else {
                                            $Nopay = 0;
                                            $Nopay_Hrs = "0";
                                            $DayStatus = 'PR';
                                        }
                                    }
                                }


                                if ($ShiftType == 'EX') {
                                    if ($OutTime == null || $OutTime == '') {
                                        $DayStatus = 'EX-MS';
                                        $Late_Status = 0;
                                        $Nopay = 0;
                                        $Nopay_Hrs = 0;
                                    }
                                    // If In Available & Out Missing
                                    if ($InTime != '' && $OutTime == '') {
                                        $DayStatus = 'EX-MS';
                                        $Late_Status = 0;
                                        $Nopay = 0;
                                        $Nopay_Hrs = 0;
                                    }

                                    // If Out Available & In Missing
                                    if ($OutTime != '' && $InTime == '') {
                                        $DayStatus = 'EX-MS';
                                        $Late_Status = 0;
                                        $Nopay = 0;
                                        $Nopay_Hrs = 0;
                                    }
                                    // **************************************************************************************//

                                    if ($InTime != '' && $InTime != $OutTime && $OutTime != '') {
                                        $Nopay = 0;
                                        $DayStatus = 'EX-PR';
                                        $Nopay_Hrs = 0;
                                    }
                                }
                                // **************************************************************************************//

                                // **************************************************************************************//

                                // // IN wenna kalin OT
                                // if ($InTime != '' && $InTime != $OutTime) {
                                //     $InTimeSrt = strtotime($InTime);
                                //     $SHStartTime = strtotime($SHFT);
                                //     $iCalc = round(($SHStartTime - $InTimeSrt) / 60);

                                //     if ($iCalc >= 0) {

                                //         $BeforeShift = $iCalc;

                                //         $BeforeShift = ($BeforeShift);
                                //     }
                                //     $testBSH = floor($BeforeShift);

                                //     if ($ShiftType == 'DU') {
                                //         $Late = true;

                                //         $lateM = 0 - $iCalc - $GracePrd;
                                //         $Late_Status = 1;

                                //         if ($lateM <= 0) {
                                //             $lateM = 0;
                                //         }
                                //     }
                                //     $Nopay = 0;
                                //     $DayStatus = 'PR';
                                //     $Nopay_Hrs = 0;
                                // } else

                                // **************************************************************************************//
                                $Nopay_Hrs = 0;
                                // Nopay
                                if ($InTime == '' && $OutTime == '' && $ShiftType == 'DU') {
                                    $DayStatus = 'AB';
                                    $Nopay = 1;
                                    // $Nopay_Hrs = (((strtotime($SHTT) - strtotime($SHFT))) / 60);
                                    $Nopay_Hrs = ($ShiftEnd - $ShiftStart) / 60;

                                    // if ($DayType == 0.5) {
                                    //     $Nopay = 0.5;
                                    //     $Nopay_Hrs = (((strtotime($SHTT) - strtotime($SHFT))) / 60);
                                    // }
                                    // $Att_Allowance = 0;

                                    // if ($InTime == '' && $OutTime == '' && $Day == 'EX') {
                                    //     $Nopay = 0;
                                    //     $Nopay_Hrs = 0;
                                    //     $DayStatus = 'EX';
                                    // }

                                }
                                if ($InTime == '00:00:00' && $OutTime == '00:00:00' && $ShiftType == 'DU') {
                                    $DayStatus = 'AB';
                                    $Nopay = 1;
                                    // $Nopay_Hrs = (((strtotime($SHTT) - strtotime($SHFT))) / 60);
                                    $Nopay_Hrs = ($ShiftEnd - $ShiftStart) / 60;

                                }
                                // **************************************************************************************//
                                //OT
                                $ApprovedExH = 0;
                                $SH_EX_OT = 0;
                                // if ($OutTime != '' && $InTime != $OutTime && $InTime != '' && $Day == 'DU' && $OutTime != "00:00:00") {

                                //     // **************************************************************************************//
                                //     // Out wunma passe OT
                                //     $SHIFTDAY['SHIFT'] = $this->Db_model->getfilteredData("SELECT `TDate` FROM tbl_individual_roster WHERE FDate = '$FromDate'");
                                //     $ToDateOT = $SHIFTDAY['SHIFT'][0]->TDate;
                                //     // date samanam
                                //     if ($ToDateOT == $OutDate) {
                                //         if ($AfterShift == 1) {

                                //             $OutTimeSrt = strtotime($OutTime);
                                //             $SHEndTime = strtotime($SHTT);

                                //             //*******Get Minutes
                                //             $iCalcOut = (($OutTimeSrt - $SHEndTime) / 60);
                                //             // if($OutTime >= "19:30:00"){
                                //             //     $icalData = $iCalcOut;
                                //             // }else{
                                //             //     $icalData = 0;
                                //             // }
                                //             $icalData = $iCalcOut - $MinAS; //windi 30kin pase OT hedenne(tbl_ot_pattern_dtl eken balanna)

                                //         } else if ($AfterShift == 0) {

                                //             $OutTimeSrt = strtotime($OutTime);
                                //             $SHEndTime = strtotime($SHTT);

                                //             //*******Get Minutes
                                //             $iCalcOut = (($OutTimeSrt - $SHEndTime) / 60);
                                //             $icalData = $iCalcOut;

                                //         }
                                //     } else {
                                //         // nextDay thiywam OT hedena widiha
                                //         if ($AfterShift == 1) {

                                //             // $SHEndTime = strtotime($SHTT);

                                //             // $OutTime;

                                //             // $H = explode(":",$OutTime);
                                //             // $H2 = $H[0];

                                //             // $OutT = $H2+24;

                                //             // $OutTimeSrt = strtotime($OutT);

                                //             // // $OTHHH = $OutT-$SHTT;

                                //             // $iCalcOut = (($OutTimeSrt - $SHEndTime) / 60);

                                //             // Define the two dates
                                //             $date1 = new DateTime($SHTT);
                                //             $date2 = new DateTime($OutTime);

                                //             // Subtract 24 hours from $date1
                                //             $date1->sub(new DateInterval('P1D')); // P1D represents a period of 1 day

                                //             // Calculate the difference in minutes
                                //             $interval = $date2->getTimestamp() - $date1->getTimestamp();
                                //             $totalMinutes = round($interval / 60); // Convert seconds to minutes

                                //             // Subtract 30 minutes
                                //             // $totalMinutes -= 30;

                                //             // Store the result in $icalData
                                //             $icalData = $totalMinutes;

                                //             // echo $icalData; // Output: Updated time difference in minutes

                                //             // Using codnighter
                                //             // echo $timeDifference;

                                //             // Using codnighter

                                //             //*******Get Minutes
                                //             // $iCalcOut = (($OutTimeSrt - $SHEndTime) / 60);
                                //             // $icalData = $iCalcOut - $MinAS;//windi 30kin pase OT hedenne(tbl_ot_pattern_dtl eken balanna)

                                //         } else if ($AfterShift == 0) { //30m gap ekak nethnm
                                //             // check
                                //             // Define the two dates
                                //             $date1 = new DateTime($SHTT);
                                //             $date2 = new DateTime($OutTime);

                                //             // Subtract 24 hours from $date1
                                //             $date1->sub(new DateInterval('P1D')); // P1D represents a period of 1 day

                                //             // Calculate the difference in minutes
                                //             $interval = $date2->getTimestamp() - $date1->getTimestamp();
                                //             $totalMinutes = round($interval / 60); // Convert seconds to minutes

                                //             // Subtract 30 minutes
                                //             $totalMinutes -= 30;

                                //             // Store the result in $icalData
                                //             $icalData = $totalMinutes;

                                //         }

                                //     }

                                //     // if ($icalData >= 0 && ) {
                                //     // }

                                //     // Out wunma passe OT
                                //     if ($icalData >= 0 && $AfterShift == 1) {
                                //         $AfterShiftWH = $icalData;
                                //     }

                                //     // **************************************************************************************//
                                //     // kalin giya ewa (ED)
                                //     $SHIFTDAY['SHIFT'] = $this->Db_model->getfilteredData("SELECT `TDate` FROM tbl_individual_roster WHERE FDate = '$FromDate'");
                                //     $ToDateOT = $SHIFTDAY['SHIFT'][0]->TDate;
                                //     // date samanam
                                //     if ($ToDateOT == $OutDate) {
                                //         if ($Day == 'DU') {
                                //             if ($OutTime < $SHTT) {
                                //                 $OutTimeSrt = strtotime($OutTime);
                                //                 $SHEndTime = strtotime($SHTT);
                                //                 $EDF = ($SHEndTime - $OutTimeSrt) / 60;

                                //                 // kalin gihhilanm haff day ekak thiynwda balanna
                                //                 $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' ");
                                //                 if (!empty($HaffDayaLeave[0]->Is_Approve)) {

                                //                     // Create a DateTime object
                                //                     $time = new DateTime($from_time);
                                //                     $time->modify('+4 hours +30 minutes');
                                //                     $modified_fromtimepre = $time->format('H:i:s');
                                //                     $SHstarttime = $modified_fromtimepre;
                                //                     // $SHstarttime = "14:00:00";

                                //                     $OutTimeSrt = strtotime($OutTime);
                                //                     $SHstartimeSrt = strtotime($SHstarttime);

                                //                     $iCalcHaffED = ($SHstartimeSrt - $OutTimeSrt) / 60;

                                //                     if ($iCalcHaffED >= 0) {
                                //                         //ED thiywa
                                //                         $ED = $iCalcHaffED;
                                //                         // $ED = $EDF + $iCalcHaffED;

                                //                     } else if ($iCalcHaffED <= 0) {
                                //                         //ED nee
                                //                         $ED = 0;
                                //                     }
                                //                 } else {

                                //                     $HDFDateSrt = strtotime($InDate);
                                //                     $OutDateSrt = strtotime($OutDate);

                                //                     if ($HDFDateSrt == $OutDateSrt) {

                                //                         $ED = $EDF;

                                //                     } else {
                                //                         $ED = 0;
                                //                     }
                                //                 }
                                //             }

                                //             // $ED = 0 - $icalData;
                                //             // if ($ED <= 0) {
                                //             //     $ED = 0;
                                //             // }
                                //         }
                                //     }

                                //     // **************************************************************************************//
                                //     // HaffDay walata kalin gihin nethnm (ED)
                                //     // if ($InTime != '' && $InTime != $OutTime && $Day == 'DU' || $OutTime != '' && $Day == 'DU') {

                                //     //     $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' ");
                                //     //     if (!empty($HaffDayaLeave[0]->Is_Approve)) {
                                //     //         // Create a DateTime object
                                //     //         $time = new DateTime($from_time);
                                //     //         $time->modify('+4 hours +30 minutes');
                                //     //         $modified_fromtimepre = $time->format('H:i:s');
                                //     //         $SHstarttime = $modified_fromtimepre;
                                //     //         // $SHstarttime = "14:00:00";

                                //     //         $OutTimeSrt = strtotime($OutTime);
                                //     //         $SHstartimeSrt = strtotime($SHstarttime);

                                //     //         $iCalcHaffED = ($SHstartimeSrt - $OutTimeSrt) / 60;

                                //     //         if ($iCalcHaffED <= 0) {
                                //     //             //ED nee

                                //     //             $ED = 0;
                                //     //         } else if ($iCalcHaffED >= 0) {

                                //     //             $HDFDateSrt = strtotime($InDate);
                                //     //             $OutDateSrt = strtotime($OutDate);

                                //     //             if ($HDFDateSrt == $OutDateSrt) {

                                //     //                 $ED = $iCalcHaffED;

                                //     //             } else {
                                //     //                 $ED = 0;
                                //     //             }
                                //     //         }
                                //     //     }
                                //     // }

                                // }

                                $lateM = 0; //late minutes
                                $ED = 0; //ED minutes
                                $iCalcHaffT = 0;
                                // New Late with HFD

                                // if ($InTime != '' && $InTime != $OutTime && $Day == 'DU' || $OutTime != '' && $Day == 'DU') {

                                //     $SHStartTime = strtotime($SHFT);
                                //     $InTimeSrt = strtotime($InTime);

                                //     $iCalc = ($InTimeSrt - $SHStartTime) / 60; //minutes

                                //     $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT count(EmpNo) as HasRow FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' ");

                                //     // if ($HaffDayaLeave[0]->HasRow == 0) {
                                //     //     $lateM = $iCalc - $GracePrd;
                                //     // }else{
                                //     //     $lateM = 0;
                                //     // }
                                //     $lateM = $iCalc - $GracePrd;


                                //     $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' ");
                                //     // echo $HaffDayaLeave[0]->Is_Approve.'********';
                                //     // haffDay thiywam (only) Morning
                                //     if (!empty($HaffDayaLeave[0]->Is_Approve)) {
                                //         $SHTtime = "14:00:00";
                                //         // $SHFttime = "19:00:00";

                                //         $InTimeSrt = strtotime($InTime);
                                //         $SHToTimeSrt = strtotime($SHTtime);

                                //         $iCalcHaffT = ($InTimeSrt - $SHToTimeSrt) / 60;

                                //         if ($InTime <= "11:00:00") {
                                //             $lateM;
                                //         } else {
                                //             if ($iCalcHaffT <= 0) {
                                //                 // welawta ewilla
                                //                 $lateM = 0;
                                //                 $Late_Status = 0;
                                //                 $DayStatus = 'HFD';

                                //             } else if ($iCalcHaffT >= 0) {
                                //                 $lateM = $iCalcHaffT;

                                //                 $DayStatus = 'HFD';
                                //             }
                                //         }



                                //     }


                                //     $SHEndTime = strtotime($SHTT);
                                //     $OutTimeSrt = strtotime($OutTime);

                                //     $iCalcED = ($SHEndTime - $OutTimeSrt) / 60; //minutes

                                //     if ($OutTime != '' && $InDate == $OutDate) {
                                //         if ($ED >= 0) {
                                //             $ED = $iCalcED;
                                //         }
                                //     }

                                //     // haffDay thiywam (only) Evening
                                //     if (!empty($HaffDayaLeave[0]->Is_Approve)) {
                                //         $HDFTtime = "14:00:00";

                                //         $HDFTimeSrt = strtotime($HDFTtime);
                                //         $OutTimeSrt = strtotime($OutTime);

                                //         $iCalcHaffT = ($HDFTimeSrt - $OutTimeSrt) / 60;

                                //         if ($InTime <= "13:00:00") {
                                //             if ($iCalcHaffT <= 0) {
                                //                 // welawta ewilla
                                //                 $ED = 0;
                                //                 // $Late_Status = 0;
                                //                 $DayStatus = 'HFD';
                                //                 $lateM;

                                //             } else if ($iCalcHaffT >= 0) {
                                //                 $DayStatus = 'HFD';

                                //                 $HDFDateSrt = strtotime($InDate);
                                //                 $OutDateSrt = strtotime($OutDate);

                                //                 if ($HDFDateSrt == $OutDateSrt) {

                                //                     $ED = $iCalcHaffT;
                                //                     $lateM;

                                //                 } else {
                                //                     $ED = 0;
                                //                 }
                                //             }
                                //         }

                                //     }

                                // }

                                // ===================Start Short Leave

                                // **************************************************************************************//
                                // Get the BreakkIN
                                $dt_Breakin_Records['dt_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='3' ");
                                $BreakInRecords = $dt_Breakin_Records['dt_Records'][0]->AttDate;
                                $BreakInDate = $dt_Breakin_Records['dt_Records'][0]->AttDate;
                                $BreakInTime = $dt_Breakin_Records['dt_Records'][0]->INTime;
                                $BreakInRec = 1;

                                // Get the BreakOut
                                $dt_Breakout_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='4' ");
                                $BreakOutDate = $dt_Breakout_Records['dt_out_Records'][0]->AttDate;
                                $BreakOutTime = $dt_Breakout_Records['dt_out_Records'][0]->OutTime;
                                $BreakOutRec = 0;
                                $BreakOutRecords = $dt_Breakout_Records['dt_out_Records'][0]->AttDate;

                                // // ShortLeave thani eka [(After)atharameda]
                                if ($BreakInTime != null && $BreakOutTime != null) {
                                    $BreakInTime = $dt_Breakin_Records['dt_Records'][0]->INTime;
                                    $BreakOutTime = $dt_Breakout_Records['dt_out_Records'][0]->OutTime;

                                    //Late(Short)
                                    $ShortLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_shortlive WHERE EmpNo = $EmpNo AND tbl_shortlive.Date = '$FromDate' AND Is_Approve = 1");
                                    if (!empty($ShortLeave[0]->Is_Approve)) {
                                        $SHFtime = $ShortLeave[0]->from_time;
                                        $SHTtime = $ShortLeave[0]->to_time;

                                        $BreakOutTimeSrt = strtotime($BreakOutTime);
                                        $SHToTimeSrt = strtotime($SHTtime);

                                        $iCalcShortLTIntv = ($BreakOutTimeSrt - $SHToTimeSrt) / 60;
                                        $DayStatus = 'SL';
                                        if ($iCalcShortLTIntv <= 0) {
                                            // welawta ewilla

                                        } else if ($iCalcShortLTIntv >= 0) {
                                            // welatwa ewilla ne(short leave & haffDay ektath passe late)
                                            $lateM = $iCalcHaffT + $iCalcShortLTIntv;

                                        }
                                    }

                                    // ED(Short)
                                    if (!empty($ShortLeave[0]->Is_Approve)) {
                                        $SHFtime = $ShortLeave[0]->from_time;
                                        $SHTtime = $ShortLeave[0]->to_time;

                                        $BreakInTimeSrt = strtotime($BreakInTime);
                                        $SHFromTimeSrt = strtotime($SHFtime);

                                        $iCalcShortLTIntvED = ($SHFromTimeSrt - $BreakInTimeSrt) / 60;
                                        $DayStatus = 'SL';

                                        if ($iCalcShortLTIntvED <= 0) {
                                            // ee welwta hari ee welwen passe hari gihinm

                                        } else if ($iCalcShortLTIntvED >= 0) {
                                            // kalin gihinm
                                            // $ED = $EDF + $iCalcShortLTIntvED;
                                            $ED = $iCalcShortLTIntvED;

                                        }
                                    }
                                }

                                // Hawasa ShortLeave thiynwam
                                $ShortLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_shortlive WHERE EmpNo = $EmpNo AND tbl_shortlive.Date = '$FromDate' AND Is_Approve = 1");
                                if (!empty($ShortLeave[0]->Is_Approve)) {
                                    if ($ShortLeave[0]->from_time >= '17:30:00') {

                                        $SHFtime = $ShortLeave[0]->from_time;
                                        $SHTtime = $ShortLeave[0]->to_time;

                                        if (($OutTime > '17:30:00') || ($OutTime < '18:00:00')) {
                                            // echo $InTime . '-' . $OutTime . ' || ' . $EmpNo;
                                            // echo "<br>";
                                            // echo $FromDate;
                                            // echo "<br>";
                                            $OutTimeSrt = strtotime($OutTime);
                                            $SHFromTimeSrt = strtotime($SHFtime);

                                            $iCalcShortLTED = ($SHFromTimeSrt - $OutTimeSrt) / 60;
                                            // echo $iCalcShortLTED;
                                            if ($iCalcShortLTED > 0) {
                                                $ED = 0;
                                                $ED = $iCalcShortLTED;
                                                $DayStatus = 'SL';

                                            } else {
                                                $ED = 0;
                                                $DayStatus = 'SL';
                                                // echo "2";
                                            }
                                        } else {

                                        }

                                    }
                                     $lateM = 0;
                                            $Late_Status = 0;
                                            $DayStatus = 'SL';
                                }

                                // Morning In Time ekata kalin short leave thiywam
                                $ShortLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_shortlive WHERE EmpNo = $EmpNo AND tbl_shortlive.Date = '$FromDate' AND Is_Approve = 1");
                                if (!empty($ShortLeave[0]->Is_Approve)) {
                                    $SHFtime = $ShortLeave[0]->from_time;
                                    $SHTtime = $ShortLeave[0]->to_time;

                                    $InTimeSrt = strtotime($InTime);
                                    $SHToTimeSrt = strtotime($SHTtime);

                                    $iCalcShortLT = ($InTimeSrt - $SHToTimeSrt) / 60;

                                    if ($SHFtime <= "10:00:00") {
                                        if ($iCalcShortLT <= 0) {
                                            // welawta ewilla
                                            $lateM = 0;
                                            $Late_Status = 0;
                                            $DayStatus = 'SL';

                                        } else {
                                            // welatwa ewilla ne(short leave ektath passe late /haffDay ne )
                                            $lateM = $iCalcShortLT;
                                            $DayStatus = 'SL';

                                            // echo "2gg";
                                        }
                                    }
                                     $lateM = 0;
                                            $Late_Status = 0;
                                            $DayStatus = 'SL';

                                }


                                // $$$$$$$$$$$$$$$$$$$$$$$//
                                // **************************************************************************************//
                                $OFFDAY['OFF'] = $this->Db_model->getfilteredData("select `ShType` from tbl_individual_roster where FDate = '$FromDate'  ");
                                $Day = $OFFDAY['OFF'][0]->ShType;

                                // if ($OutTime == "00:00:00") {
                                //     $DayStatus = 'MS';
                                //     $Late_Status = 0;
                                //     $Nopay = 0;
                                //     $OutTime = "00:00:00";
                                // }

                                if ($ShiftType == "OFF") {
                                    $DayStatus = 'OFF';
                                    $Late_Status = 0;
                                    $Nopay = 0;
                                    $InRecords = $FromDate;
                                    $OutDate = $FromDate;
                                    $InTime = "00:00:00";
                                    $OutTime = "00:00:00";
                                }

                                // $SH_EX_OT = 0;
                                // $NetLateM = 0;

                                // $ExH_OT = $BeforeShift + $AfterShiftWH;

                                // if ($NetLateM > $ExH_OT) {
                                //     $NetLateM = $NetLateM - $ExH_OT;
                                //     $ApprovedExH = 0;

                                // } else {
                                //     $ApprovedExHTemp = ($ExH_OT - $NetLateM);
                                //     $ApprovedExH = (floor(($ApprovedExHTemp) / $Round)) * $Round;

                                //     $NetLateM = 0;
                                // }

                                // if ($ApprovedExH >= 0) {

                                //     $dataArray = array(
                                //         'EmpNo' => $EmpNo,
                                //         'OTDate' => $FromDate,
                                //         'RateCode' => $Rate,
                                //         'OT_Cat' => $DayCode,
                                //         'OT_Min' => $ApprovedExH
                                //     );

                                //     //                            var_dump($dataArray);
                                //     $result = $this->Db_model->insertData("tbl_ot_d", $dataArray);
                                // }

                                // var_dump($InDate . ' ' . $InTime );
                                // echo "<br>";
                                // var_dump($OutDate . ' ' . $OutTime . ' ' . $EmpNo);
                                // echo "<br>";
                                // echo "<br>";

                                $Holiday = $this->Db_model->getfilteredData("select count(Hdate) as HasRow from tbl_holidays where Hdate = '$FromDate' ");
                                if ($Holiday[0]->HasRow == 1) {
                                    $DayStatus = 'HD';
                                    $Nopay = 0;
                                    $Nopay_Hrs = 0;
                                    $Att_Allowance = 0;
                                }
                                $Leave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='1' ");
                                if (!empty($Leave[0]->Is_Approve)) {
                                    $Nopay = 0;
                                    $DayStatus = 'LV';
                                    $Nopay_Hrs = 0;
                                    $Att_Allowance = 0;
                                    $ED = 0;
                                }

                                $CoverUPLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_cover_up_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' ");
                                if (!empty($CoverUPLeave[0]->Is_Approve)) {
                                    $Nopay = 0;
                                    $DayStatus = 'CUP-LV';
                                    $Nopay_Hrs = 0;
                                    $Att_Allowance = 0;
                                    $ED = 0;
                                }
                                //apoint date ekata kalin off dawasakath ab saha nopay wadinna one
                                if ($FromDate <= $apoint_date) {
                                    $Nopay = 1;
                                    $DayStatus = 'AB';
                                    $Nopay_Hrs = 0;
                                    $Att_Allowance = 0;
                                }
                                if ($settings[0]->Ot_e == 0) {
                                    $AfterShiftWH = 0;
                                }
                                if ($settings[0]->Late == 0) {
                                    $lateM = 0;
                                }
                                if ($settings[0]->Ed == 0) {
                                    $ED = 0;
                                }
                                if ($lateM >= 0) {
                                    $lateM;
                                } else {
                                    $lateM = 0;
                                }

                                if ($ED >= 0) {
                                    $ED;
                                } else {
                                    $ED = 0;
                                }


                                $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' AND Is_Approve = 1");
                                if (!empty($HaffDayaLeave[0]->Is_Approve)) {
                                    $DayStatus = 'HFD';
                                    if ($InTime == null || $InTime == "" || $InTime == "00:00:00") {
                                        $SHTtime = "14:00:00";
                                        $SHFttime = "19:00:00";
                                        $NewIntime = strtotime($SHTtime);
                                        $NewTotime = strtotime($SHFttime);

                                        $NewiCalcHaffT = ($NewTotime - $NewIntime) / 60;

                                        if ($NewiCalcHaffT >= 0) {
                                            $lateM = $NewiCalcHaffT;
                                            $DayStatus = 'HFD/AB';
                                        }
                                    }
                                } else {
                                    if ($InTime == "00:00:00" && $OutTime == "00:00:00" && $ShiftType == 'DU') {
                                        $DayStatus = 'AB';
                                        $Nopay = 1;
                                        // $Nopay_Hrs = (((strtotime($SHTT) - strtotime($SHFT))) / 60);
                                        $Nopay_Hrs = ($ShiftEnd - $ShiftStart) / 60;
                                    }
                                }

                                // if ($InTime == "00:00:00" || $InTime == null) {
                                //     $OutTime = "00:00:00";
                                //     $OutTime = "00:00:00";

                                // }
                                $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT COUNT(LV_ID) AS ID FROM tbl_leave_entry where EmpNo = '" . $EmpNo . "' and Leave_Date = '" . $FromDate . "' AND Leave_Count='0.5' AND Is_Approve = 1");
                                if ($HaffDayaLeave[0]->ID > 0) {
                                    $DayStatus = 'HFD';
                                    $Late_Status = 0;
                                    $Nopay = 0;
                                    $Nopay_Hrs = 0;
                                }
                                if ($ShiftType == ' ') {
                                    $DayStatus = 'AB';
                                    $Nopay = 1;
                                    // $Nopay_Hrs = (((strtotime($SHTT) - strtotime($SHFT))) / 60);
                                    $Nopay_Hrs = ($ShiftEnd - $ShiftStart) / 60;
                                }
                                if ($ShiftType == "OFF") {
                                    $DayStatus = 'OFF';
                                    $Late_Status = 0;
                                    $Nopay = 0;
                                    $InRecords = $FromDate;
                                    $OutDate = $FromDate;
                                    $InTime = "00:00:00";
                                    $OutTime = "00:00:00";
                                }

                                // if($DayStatus = 'HFD' && $ShiftType == 'DU'){
                                // }else{
                                //     if ($ShiftType == 'DU') {
                                //         // Combine into full datetime strings
                                // $FromStart = strtotime($InDate . ' ' . $InTime);
                                // $TotEnd   = strtotime($OutDate . ' ' . $OutTime);

                                // $FromTo_Hrs = ($TotEnd - $FromStart) / 60;

                                // if ($FromTo_Hrs >= '540'){
                                //     $DayStatus = 'PR';
                                // }else{
                                // 	$Nopay = 0.5;
                                // 	$Nopay_Hrs = "270";
                                // 	$DayStatus = 'NP-HFD';
                                // }
                                //     }
                                // }

                                // echo $EmpNo . ' ' . $from_date . ' ' . $to_date . ' ' . $from_time . ' ' . $to_time . ' ' . $shift_day . ' /' . $InTime . ' ' . $OutTime . ' ' . $ED . ' Nopay' . $Nopay_Hrs . ' ' . $DayStatus . '<br>';
                                // echo "<br>";
                                $data_arr = array("InRec" => 1, "RMonth" => $DayStatus, "InDate" => $FromDate, "InTime" => $InTime, "OutRec" => 1, "Day_Type" => $Day_Type, "OutDate" => $OutDate, "OutTime" => $OutTime, "nopay" => $Nopay, "Is_processed" => 1, "DayStatus" => $DayStatus, "BeforeExH" => $BeforeShift, "AfterExH" => $AfterShiftWH, "LateSt" => $Late_Status, "LateM" => $lateM, "EarlyDepMin" => $ED, "NetLateM" => $NetLateM, "ApprovedExH" => $ApprovedExH, "nopay_hrs" => $Nopay_Hrs, "Att_Allow" => $Att_Allowance, "DOT" => $DOT);
                                $whereArray = array("ID_roster" => $ID_Roster);
                                $result = $this->Db_model->updateData("tbl_individual_roster", $data_arr, $whereArray);

                            }
                            if (!empty($ShiftDetails['shift'][1]->ID_Roster)) {

                                $modified_totimeold1 = '';
                                $modified_fromtimeold1 = '';
                                $ID_Roster1 = $ShiftDetails['shift'][1]->ID_Roster;
                                if ($ShiftDetails['shift'][1]->ShType == null || empty($ShiftDetails['shift'][1]->ShType)) {
                                    $shift_type1 = 'EX';
                                }
                                $shift_type1 = $ShiftDetails['shift'][1]->ShType;
                                $shift_day1 = $ShiftDetails['shift'][1]->ShiftDay;
                                $from_date1 = $ShiftDetails['shift'][1]->FDate;
                                $from_time1 = $ShiftDetails['shift'][1]->FTime;
                                $to_date1 = $ShiftDetails['shift'][1]->TDate;
                                $to_time1 = $ShiftDetails['shift'][1]->TTime;
                                $GracePrd1 = $ShiftDetails['shift'][1]->GracePrd;
                                $cutofftime1 = $ShiftDetails['shift'][1]->HDSession;

                                if ($shift_type1 == "DU" || $shift_type == "EX" || $shift_type == " " || $shift_type == "null" || $shift_type == "") {
                                    $InDate1 = '';
                                    $InTime1 = '';
                                    $OutDate1 = '';
                                    $OutTime1 = '';

                                    $lateM1 = '';
                                    $ED1 = '';
                                    $DayStatus1 = '';
                                    $AfterShiftWH1 = '';
                                    $DOT1 = '';

                                    // from - old
                                    // $from_datetime1 = $from_date1 . ' ' . $from_time1;
                                    // $time1 = new DateTime($from_datetime1);
                                    // $time1->modify('-2 hours');
                                    // $modified_fromtimepre1 = $time1->format('Y-m-d H:i:s');
                                    // // from - future
                                    // $from_datetimen1 = $from_date1 . ' ' . $from_time1;
                                    // $time1 = new DateTime($from_datetimen1);
                                    // $time1->modify('+2 hours');
                                    // $modified_fromtimeold1 = $time1->format('Y-m-d H:i:s');

                                    // // to - old
                                    // $to_datetime1 = $to_date1 . ' ' . $to_time1;
                                    // $time1 = new DateTime($to_datetime1);
                                    // $time1->modify('-2 hours');
                                    // $modified_totimepre1 = $time1->format('Y-m-d H:i:s');
                                    // // to - future
                                    // $to_datetimen1 = $to_date1 . ' ' . $to_time1;
                                    // $time1 = new DateTime($to_datetimen1);
                                    // $time1->modify('+2 hours');
                                    // $modified_totimeold1 = $time1->format('Y-m-d H:i:s');

                                    // Get the CheckIN
                                    $dt_in_Records['dt_Records1'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='0' ");
                                    $InDate1 = $dt_in_Records['dt_Records1'][0]->AttDate;
                                    $InTime1 = $dt_in_Records['dt_Records1'][0]->INTime;

                                    // Get the CheckOut
                                    $dt_out_Records['dt_out_Records1'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='1' ");
                                    $OutDate1 = $dt_out_Records['dt_out_Records1'][0]->AttDate;
                                    $OutTime1 = $dt_out_Records['dt_out_Records1'][0]->OutTime;

                                    // Out Ekak nethnm check nextday(1st nextDay)
                                    // if ($OutTime1 == null) {
                                    //     $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                                    //     // $newDate = $to_date1;

                                    //     // Get the CheckOut in the nextDay (before 9am)
                                    //     $dt_out_Records['dt_out_Records1'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                    //     tbl_u_attendancedata where AttDateTimeStr BETWEEN '$modified_totimepre1' AND '$modified_totimeold1' AND Enroll_No='$EmpNo' and AttDate='" . $newDate . "' AND Status='1' ");//update the 9 to 11.59 
                                    //     // $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                    //     // tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='$newDate' AND AttTime <'09:00:00' "); old-code
                                    //     $OutDate1 = $dt_out_Records['dt_out_Records1'][0]->AttDate;
                                    //     $OutTime1 = $dt_out_Records['dt_out_Records1'][0]->OutTime;
                                    // }

                                    if ($OutTime1 == null) {
                                        $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                                        // $newDate = $to_date1;

                                        $to_datetimen1 = $to_date1 . ' ' . $to_time1;
                                        $time1 = new DateTime($to_datetimen1);
                                        $time1->modify('+27 hours');
                                        $modified_totimeold1 = $time1->format('Y-m-d H:i:s');

                                        // Get the CheckOut in the nextDay (before 9am)
                                        $dt_out_Records['dt_out_Records1'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                    tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $newDate . "' AND Status='1' ");
                                        $OutDate1 = $dt_out_Records['dt_out_Records1'][0]->AttDate;
                                        $OutTime1 = $dt_out_Records['dt_out_Records1'][0]->OutTime;
                                    }

                                    // if(!empty($OutTime)){
                                    $dt_out_RecordsMS['dt_out_Recordsms1'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                    tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $newDate . "' AND Status='0' AND AttTime < '" . $OutTime . "' ");
                                    // }
                                    $OuttimeMS1 = $dt_out_RecordsMS['dt_out_Recordsms1'][0]->OutTime;

                                    if (!empty($OuttimeMS1)) {
                                        $OutTime = null;
                                        $OutDate = null;

                                    }

                                    // if ($InTime1 != '' && $OutTime1 != '') {
                                    //     $fromtime1 = $InDate1 . " " . $InTime1;
                                    //     $totime1 = $OutDate1 . " " . $OutTime1;
                                    //     $timestamp11 = strtotime($fromtime1);
                                    //     $timestamp21 = strtotime($totime1);
                                    //     $time_difference_seconds1 = ($timestamp21 - $timestamp11);
                                    //     $time_difference_minutes1 = $time_difference_seconds1 / 60;
                                    //     if ($time_difference_minutes1 < 60) {
                                    //         $OutDate1 = '';
                                    //         $OutTime1 = '';
                                    //     }
                                    //     //ms wela thiyenne out time ekada balanw
                                    //     $fromtime1 = $to_date1 . " " . $to_time1;
                                    //     $deduct_fromtime1 = strtotime($fromtime1 . " -2 hour");
                                    //     $plus_fromtime1 = strtotime($fromtime1 . " +2 hour");
                                    //     $ct1 = $InDate1 . " " . $InTime1;
                                    //     $check_time1 = strtotime($ct1);
                                    //     if ($deduct_fromtime1 <= $check_time1 && $check_time1 <= $plus_fromtime1) {
                                    //         $OutDate1 = $InDate1;
                                    //         $OutTime1 = $InTime1;
                                    //         $InDate1 = '';
                                    //         $InTime1 = '';
                                    //     }
                                    // }
                                }
                                // echo $ID_Roster1.' '.$from_date1.' '.$to_date1.' '.$from_time1.' '.$to_time1.' '.$shift_day1.' /'.$InTime1.' -'.$OutTime1;
                                // echo "<br>";
                                // $InDate1 = $InDate;
                                // $InTime1 = $InTime;
                                // $OutDate1 =  $OutDate;
                                // $OutTime1 = $OutTime; 
                                // if ($InTime == "00:00:00" || $InTime == null) {
                                //     $OutTime = "00:00:00";
                                //     $OutTime = "00:00:00";

                                // }
                                $data_arr = array("InRec" => 1, "InDate" => $FromDate, "InTime" => $InTime1, "OutRec" => 1, "Day_Type" => $Day_Type, "OutDate" => $OutDate1, "OutTime" => $OutTime1, "nopay" => $Nopay, "Is_processed" => 1, "DayStatus" => $DayStatus, "BeforeExH" => $BeforeShift, "AfterExH" => $AfterShiftWH, "LateSt" => $Late_Status, "LateM" => $lateM, "EarlyDepMin" => $ED, "NetLateM" => $NetLateM, "ApprovedExH" => $ApprovedExH, "nopay_hrs" => $Nopay_Hrs, "Att_Allow" => $Att_Allowance, "DOT" => $DOT);
                                $whereArray = array("ID_roster" => $ID_Roster1);
                                $result = $this->Db_model->updateData("tbl_individual_roster", $data_arr, $whereArray);
                            }

                            if (!empty($ShiftDetails['shift'][2]->ID_Roster)) {


                                $ID_Roster2 = $ShiftDetails['shift'][2]->ID_Roster;
                                if ($ShiftDetails['shift'][2]->ShType == null || empty($ShiftDetails['shift'][2]->ShType)) {
                                    $shift_type2 = 'EX';
                                }
                                $shift_type2 = $ShiftDetails['shift'][2]->ShType;
                                $shift_day2 = $ShiftDetails['shift'][2]->ShiftDay;
                                $from_date2 = $ShiftDetails['shift'][2]->FDate;
                                $from_time2 = $ShiftDetails['shift'][2]->FTime;
                                $to_date2 = $ShiftDetails['shift'][2]->TDate;
                                $to_time2 = $ShiftDetails['shift'][2]->TTime;
                                $GracePrd2 = $ShiftDetails['shift'][2]->GracePrd;
                                $cutofftime2 = $ShiftDetails['shift'][2]->HDSession;

                                if ($shift_type2 == "DU" || $shift_type == "EX" || $shift_type == " " || $shift_type == "null" || $shift_type == "") {
                                    $InDate2 = '2000-00-00';
                                    $InTime2 = '00:00:00';
                                    $OutDate2 = '2000-00-00';
                                    $OutTime2 = '00:00:00';

                                    $lateM2 = '';
                                    $ED2 = '';
                                    $DayStatus2 = '';
                                    $AfterShiftWH2 = '';
                                    $DOT2 = '';

                                    // from - old
                                    $from_datetime2 = $from_date2 . ' ' . $from_time2;
                                    $time2 = new DateTime($from_datetime2);
                                    $time2->modify('-2 hours');
                                    $modified_fromtimepre2 = $time2->format('Y-m-d H:i:s');
                                    // from - future
                                    $from_datetimen2 = $from_date2 . ' ' . $from_time2;
                                    $time2 = new DateTime($from_datetimen2);
                                    $time2->modify('+2 hours');
                                    $modified_fromtimeold2 = $time2->format('Y-m-d H:i:s');

                                    // to - old
                                    $to_datetime2 = $to_date2 . ' ' . $to_time2;
                                    $time2 = new DateTime($to_datetime2);
                                    $time2->modify('-2 hours');
                                    $modified_totimepre2 = $time2->format('Y-m-d H:i:s');
                                    // to - future
                                    $to_datetimen2 = $to_date2 . ' ' . $to_time2;
                                    $time2 = new DateTime($to_datetimen2);
                                    $time2->modify('+2 hours');
                                    $modified_totimeold2 = $time2->format('Y-m-d H:i:s');


                                    // Get the CheckIN
                                    $dt_in_Records['dt_Records2'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where AttDateTimeStr BETWEEN '$modified_fromtimepre2' AND '$modified_fromtimeold2' AND Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='0' ");
                                    $InDate2 = $dt_in_Records['dt_Records2'][0]->AttDate;
                                    $InTime2 = $dt_in_Records['dt_Records2'][0]->INTime;

                                    // Get the CheckOut
                                    $dt_out_Records['dt_out_Records2'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where AttDateTimeStr BETWEEN '$modified_totimepre2' AND '$modified_totimeold2' AND Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='1' ");
                                    $OutDate2 = $dt_out_Records['dt_out_Records2'][0]->AttDate;
                                    $OutTime2 = $dt_out_Records['dt_out_Records2'][0]->OutTime;

                                    // Out Ekak nethnm check nextday(1st nextDay)
                                    if ($OutTime2 == null) {
                                        $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                                        // $newDate = $to_date1;

                                        // Get the CheckOut in the nextDay (before 9am)
                                        $dt_out_Records['dt_out_Records2'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where AttDateTimeStr BETWEEN '$modified_totimepre1' AND '$modified_totimeold1' AND Enroll_No='$EmpNo' and AttDate='" . $newDate . "' AND Status='1' ");//update the 9 to 11.59 
                                        // $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                        // tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='$newDate' AND AttTime <'09:00:00' "); old-code
                                        $OutDate2 = $dt_out_Records['dt_out_Records2'][0]->AttDate;
                                        $OutTime2 = $dt_out_Records['dt_out_Records2'][0]->OutTime;
                                    }

                                    if ($OutTime2 == null) {
                                        $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                                        // $newDate = $to_date1;

                                        $to_datetimen2 = $to_date2 . ' ' . $to_time2;
                                        $time2 = new DateTime($to_datetimen2);
                                        $time2->modify('+27 hours');
                                        $modified_totimeold2 = $time2->format('Y-m-d H:i:s');

                                        // Get the CheckOut in the nextDay (before 9am)
                                        $dt_out_Records['dt_out_Records2'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $newDate . "' AND Status='1' AND AttDateTimeStr <'$modified_totimeold2' ");
                                        $OutDate2 = $dt_out_Records['dt_out_Records2'][0]->AttDate;
                                        $OutTime2 = $dt_out_Records['dt_out_Records2'][0]->OutTime;
                                    }

                                    // if ($InTime1 != '' && $OutTime1 != '') {
                                    //     $fromtime1 = $InDate1 . " " . $InTime1;
                                    //     $totime1 = $OutDate1 . " " . $OutTime1;
                                    //     $timestamp11 = strtotime($fromtime1);
                                    //     $timestamp21 = strtotime($totime1);
                                    //     $time_difference_seconds1 = ($timestamp21 - $timestamp11);
                                    //     $time_difference_minutes1 = $time_difference_seconds1 / 60;
                                    //     if ($time_difference_minutes1 < 60) {
                                    //         $OutDate1 = '';
                                    //         $OutTime1 = '';
                                    //     }
                                    //     //ms wela thiyenne out time ekada balanw
                                    //     $fromtime1 = $to_date1 . " " . $to_time1;
                                    //     $deduct_fromtime1 = strtotime($fromtime1 . " -2 hour");
                                    //     $plus_fromtime1 = strtotime($fromtime1 . " +2 hour");
                                    //     $ct1 = $InDate1 . " " . $InTime1;
                                    //     $check_time1 = strtotime($ct1);
                                    //     if ($deduct_fromtime1 <= $check_time1 && $check_time1 <= $plus_fromtime1) {
                                    //         $OutDate1 = $InDate1;
                                    //         $OutTime1 = $InTime1;
                                    //         $InDate1 = '';
                                    //         $InTime1 = '';
                                    //     }
                                    // }
                                    // $InDate2 = $InDate;
                                    // $InTime2 = $InTime;
                                    // $OutDate2 =  $OutDate;
                                    // $OutTime2 = $OutTime; 
                                }
                                $data_arr = array("InRec" => 1, "InDate" => $FromDate, "InTime" => $InTime2, "OutRec" => 1, "Day_Type" => $Day_Type, "OutDate" => $OutDate2, "OutTime" => $OutTime2, "nopay" => $Nopay, "Is_processed" => 1, "DayStatus" => $DayStatus, "BeforeExH" => $BeforeShift, "AfterExH" => $AfterShiftWH, "LateSt" => $Late_Status, "LateM" => $lateM, "EarlyDepMin" => $ED, "NetLateM" => $NetLateM, "ApprovedExH" => $ApprovedExH, "nopay_hrs" => $Nopay_Hrs, "Att_Allow" => $Att_Allowance, "DOT" => $DOT);
                                $whereArray = array("ID_roster" => $ID_Roster2);
                                $result = $this->Db_model->updateData("tbl_individual_roster", $data_arr, $whereArray);
                                // echo $ID_Roster2.' '.$from_date2.' '.$to_date2.' '.$from_time2.' '.$to_time2.' '.$shift_day2.' /'.$InTime2.' '.$OutTime2;
                                // echo "<br>";
                            }
                            if (!empty($ShiftDetails['shift'][3]->ID_Roster)) {
                                $ID_Roster3 = $ShiftDetails['shift'][3]->ID_Roster;
                                $shift_type3 = $ShiftDetails['shift'][3]->ShType;
                                $shift_day3 = $ShiftDetails['shift'][3]->ShiftDay;
                                $from_date3 = $ShiftDetails['shift'][3]->FDate;
                                $from_time3 = $ShiftDetails['shift'][3]->FTime;
                                $to_date3 = $ShiftDetails['shift'][3]->TDate;
                                $to_time3 = $ShiftDetails['shift'][3]->TTime;
                                $GracePrd3 = $ShiftDetails['shift'][3]->GracePrd;
                                $cutofftime3 = $ShiftDetails['shift'][3]->HDSession;

                                if ($shift_type3 == "DU" || $shift_type == "EX" || $shift_type == " " || $shift_type == "null" || $shift_type == "") {
                                    $InDate3 = '';
                                    $InTime3 = '';
                                    $OutDate3 = '';
                                    $OutTime3 = '';

                                    $lateM3 = '';
                                    $ED3 = '';
                                    $DayStatus3 = '';
                                    $AfterShiftWH3 = '';
                                    $DOT3 = '';

                                    // from - old
                                    $from_datetime3 = $from_date3 . ' ' . $from_time3;
                                    $time3 = new DateTime($from_datetime3);
                                    $time3->modify('-2 hours');
                                    $modified_fromtimepre3 = $time3->format('Y-m-d H:i:s');
                                    // from - future
                                    $from_datetimen3 = $from_date3 . ' ' . $from_time3;
                                    $time3 = new DateTime($from_datetimen3);
                                    $time3->modify('+2 hours');
                                    $modified_fromtimeold3 = $time3->format('Y-m-d H:i:s');

                                    // to - old
                                    $to_datetime3 = $to_date3 . ' ' . $to_time3;
                                    $time3 = new DateTime($to_datetime3);
                                    $time3->modify('-2 hours');
                                    $modified_totimepre3 = $time3->format('Y-m-d H:i:s');
                                    // to - future
                                    $to_datetimen3 = $to_date3 . ' ' . $to_time3;
                                    $time3 = new DateTime($to_datetimen3);
                                    $time3->modify('+2 hours');
                                    $modified_totimeold3 = $time3->format('Y-m-d H:i:s');


                                    // Get the CheckIN
                                    $dt_in_Records['dt_Records3'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where AttDateTimeStr BETWEEN '$modified_fromtimepre3' AND '$modified_fromtimeold3' AND Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='0' ");
                                    $InDate3 = $dt_in_Records['dt_Records3'][0]->AttDate;
                                    $InTime3 = $dt_in_Records['dt_Records3'][0]->INTime;

                                    // Get the CheckOut
                                    $dt_out_Records['dt_out_Records3'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where AttDateTimeStr BETWEEN '$modified_totimepre3' ANDD '$modified_totimeold3' AND Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='1' ");
                                    $OutDate3 = $dt_out_Records['dt_out_Records3'][0]->AttDate;
                                    $OutTime3 = $dt_out_Records['dt_out_Records3'][0]->OutTime;

                                    // Out Ekak nethnm check nextday(1st nextDay)
                                    if ($OutTime3 == null) {
                                        $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                                        // $newDate = $to_date1;

                                        // Get the CheckOut in the nextDay (before 9am)
                                        $dt_out_Records['dt_out_Records3'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                tbl_u_attendancedata where AttDateTimeStr BETWEEN '$modified_totimepre3' AND '$modified_totimeold3' AND Enroll_No='$EmpNo' and AttDate='" . $newDate . "' AND Status='1' ");//update the 9 to 11.59 
                                        // $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                                        // tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='$newDate' AND AttTime <'09:00:00' "); old-code
                                        $OutDate3 = $dt_out_Records['dt_out_Records3'][0]->AttDate;
                                        $OutTime3 = $dt_out_Records['dt_out_Records3'][0]->OutTime;
                                    }

                                    // if ($InTime1 != '' && $OutTime1 != '') {
                                    //     $fromtime1 = $InDate1 . " " . $InTime1;
                                    //     $totime1 = $OutDate1 . " " . $OutTime1;
                                    //     $timestamp11 = strtotime($fromtime1);
                                    //     $timestamp21 = strtotime($totime1);
                                    //     $time_difference_seconds1 = ($timestamp21 - $timestamp11);
                                    //     $time_difference_minutes1 = $time_difference_seconds1 / 60;
                                    //     if ($time_difference_minutes1 < 60) {
                                    //         $OutDate1 = '';
                                    //         $OutTime1 = '';
                                    //     }
                                    //     //ms wela thiyenne out time ekada balanw
                                    //     $fromtime1 = $to_date1 . " " . $to_time1;
                                    //     $deduct_fromtime1 = strtotime($fromtime1 . " -2 hour");
                                    //     $plus_fromtime1 = strtotime($fromtime1 . " +2 hour");
                                    //     $ct1 = $InDate1 . " " . $InTime1;
                                    //     $check_time1 = strtotime($ct1);
                                    //     if ($deduct_fromtime1 <= $check_time1 && $check_time1 <= $plus_fromtime1) {
                                    //         $OutDate1 = $InDate1;
                                    //         $OutTime1 = $InTime1;
                                    //         $InDate1 = '';
                                    //         $InTime1 = '';
                                    //     }
                                    // }
                                    // $InDate3 = $InDate;
                                    // $InTime3 = $InTime;
                                    // $OutDate3 =  $OutDate;
                                    // $OutTime3 = $OutTime; 
                                    $data_arr = array("InRec" => 1, "InDate" => $FromDate, "InTime" => $InTime3, "OutRec" => 1, "Day_Type" => $Day_Type, "OutDate" => $OutDate3, "OutTime" => $OutTime3, "nopay" => $Nopay, "Is_processed" => 1, "DayStatus" => $DayStatus, "BeforeExH" => $BeforeShift, "AfterExH" => $AfterShiftWH, "LateSt" => $Late_Status, "LateM" => $lateM, "EarlyDepMin" => $ED, "NetLateM" => $NetLateM, "ApprovedExH" => $ApprovedExH, "nopay_hrs" => $Nopay_Hrs, "Att_Allow" => $Att_Allowance, "DOT" => $DOT);
                                    $whereArray = array("ID_roster" => $ID_Roster3);
                                    $result = $this->Db_model->updateData("tbl_individual_roster", $data_arr, $whereArray);
                                }

                                // echo $ID_Roster3.' '.$from_date3.' '.$to_date3.' '.$from_time3.' '.$to_time3.' '.$shift_day3.' /'.$InTime3.' -'.$OutTime3;
                                // echo "<br>";
                            }
                            //    var_dump($ShiftDetails['shift']);

                            // echo $ID_Roster.' '.$from_date.' '.$to_date.' '.$from_time.' '.$to_time.' '.$shift_day;
                            // echo "<br>";
                            // echo $ID_Roster1.' '.$from_date1.' '.$to_date1.' '.$from_time1.' '.$to_time1.' '.$shift_day1;
                            // echo "<br>";
                            // echo "<br>";
                            // duty dawasaska samnyen yana widiya


                            // if ($shift_type == "EX") {

                            //     //holiday walata double ot ynwd balnw
                            //     if ($settings[0]->Dot_f_holyday == 1) {

                            //         $InDate = '';
                            //         $InTime = '';
                            //         $OutDate = '';
                            //         $OutTime = '';


                            //         $lateM = '';
                            //         $ED = '';
                            //         $DayStatus = '';
                            //         $AfterShiftWH = '';
                            //         $DOT = '';
                            //         // Get the CheckIN
                            //         $dt_in_Records['dt_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from 
                            //         tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND (Status='0' OR AttTime <'14:00:00') ");
                            //         $InDate = $dt_in_Records['dt_Records'][0]->AttDate;
                            //         $InTime = $dt_in_Records['dt_Records'][0]->INTime;

                            //         // Get the CheckOut
                            //         $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from 
                            //         tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND (Status='1' OR AttTime > '13:00:00') ");
                            //         $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                            //         $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;


                            //         // Out Ekak nethnm check nextday(1st nextDay)
                            //         if ($FromDate != $to_date) {
                            //             // $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                            //             $newDate = $to_date;

                            //             // Get the CheckOut in the nextDay (before 9am)
                            //             $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                            //             tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='$newDate' AND AttTime <'09:00:00' AND Status='1' ");
                            //             $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                            //             $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;
                            //         }

                            //         if ($InTime != '' && $OutTime != '') {
                            //             $fromtime = $InDate . " " . $InTime;
                            //             $totime = $OutDate . " " . $OutTime;
                            //             $timestamp1 = strtotime($fromtime);
                            //             $timestamp2 = strtotime($totime);
                            //             $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //             $time_difference_minutes = $time_difference_seconds / 60;
                            //             if ($time_difference_minutes < 60) {
                            //                 $OutDate = '';
                            //                 $OutTime = '';
                            //             }
                            //             //ms wela thiyenne out time ekada balanw
                            //             $fromtime = $to_date . " " . $to_time;
                            //             $deduct_fromtime = strtotime($fromtime . " -2 hour");
                            //             $plus_fromtime = strtotime($fromtime . " +2 hour");
                            //             $ct = $InDate . " " . $InTime;
                            //             $check_time = strtotime($ct);
                            //             if ($deduct_fromtime <= $check_time && $check_time <= $plus_fromtime) {
                            //                 $OutDate = $InDate;
                            //                 $OutTime = $InTime;
                            //                 $InDate = '';
                            //                 $InTime = '';
                            //             }
                            //         }

                            //         $icaldot = 0;
                            //         if ($OutTime != '' && $InTime != $OutTime && $InTime != '' && $shift_type == 'EX' && $OutTime != "00:00:00") {
                            //             $fromtime = $InDate . " " . $InTime;
                            //             $totime = $OutDate . " " . $OutTime;
                            //             $timestamp1 = strtotime($fromtime);
                            //             $timestamp2 = strtotime($totime);
                            //             $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //             $time_difference_minutes = $time_difference_seconds / 60;
                            //             $icaldot = round($time_difference_minutes, 2);
                            //         }
                            //         if ($icaldot >= 0) {
                            //             $DOT = $icaldot;
                            //         } else {
                            //             $DOT = 0;
                            //         }

                            //         //   dot naththan samanya process eka 
                            //     } else {
                            //         $InDate = '';
                            //         $InTime = '';
                            //         $OutDate = '';
                            //         $OutTime = '';


                            //         $lateM = '';
                            //         $ED = '';
                            //         $DayStatus = '';
                            //         $AfterShiftWH = '';
                            //         // Get the CheckIN
                            //         $dt_in_Records['dt_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from 
                            //         tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND (Status='0' OR AttTime <'14:00:00') ");
                            //         $InDate = $dt_in_Records['dt_Records'][0]->AttDate;
                            //         $InTime = $dt_in_Records['dt_Records'][0]->INTime;

                            //         // Get the CheckOut
                            //         $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from 
                            //         tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND (Status='1' OR AttTime > '13:00:00') ");
                            //         $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                            //         $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;


                            //         // Out Ekak nethnm check nextday(1st nextDay)
                            //         if ($FromDate != $to_date) {
                            //             // $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                            //             $newDate = $to_date;

                            //             // Get the CheckOut in the nextDay (before 9am)
                            //             $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                            //             tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='$newDate' AND AttTime <'09:00:00' AND Status='1' ");
                            //             $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                            //             $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;
                            //         }
                            //         if ($InTime != '' && $OutTime != '') {
                            //             $fromtime = $InDate . " " . $InTime;
                            //             $totime = $OutDate . " " . $OutTime;
                            //             $timestamp1 = strtotime($fromtime);
                            //             $timestamp2 = strtotime($totime);
                            //             $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //             $time_difference_minutes = $time_difference_seconds / 60;
                            //             if ($time_difference_minutes < 60) {
                            //                 $OutDate = '';
                            //                 $OutTime = '';
                            //             }
                            //             //ms wela thiyenne out time ekada balanw
                            //             $fromtime = $to_date . " " . $to_time;
                            //             $deduct_fromtime = strtotime($fromtime . " -2 hour");
                            //             $plus_fromtime = strtotime($fromtime . " +2 hour");
                            //             $ct = $InDate . " " . $InTime;
                            //             $check_time = strtotime($ct);
                            //             if ($deduct_fromtime <= $check_time && $check_time <= $plus_fromtime) {
                            //                 $OutDate = $InDate;
                            //                 $OutTime = $InTime;
                            //                 $InDate = '';
                            //                 $InTime = '';
                            //             }
                            //         }

                            //         $lateM = 0;
                            //         // $BeforeShift = 0;
                            //         $Late_Status = 0;
                            //         $NetLateM = 0;
                            //         $ED = 0;
                            //         $EDF = 0;
                            //         $Att_Allowance = 1;
                            //         $Nopay = 0;
                            //         $AfterShiftWH = 0;
                            //         $lateM = 0; //late minutes
                            //         // $ED = 0; //ED minutes
                            //         $iCalcHaffT = 0;

                            //         if ($InTime != $OutTime && $shift_type == 'DU' && $OutTime != '') {
                            //             // date samanam
                            //             $iCalcHaffED = 0;
                            //             $iCalcHaff = 0;
                            //             if ($settings[0]->Ed == 1) {

                            //                 $fromtime = $to_date . " " . $to_time;
                            //                 $totime = $OutDate . " " . $OutTime;
                            //                 $timestamp1 = strtotime($totime);
                            //                 $timestamp2 = strtotime($fromtime);
                            //                 $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                 $time_difference_minutes = $time_difference_seconds / 60;
                            //                 $iCalcHaffED = round($time_difference_minutes, 2);
                            //                 $ED = $iCalcHaffED;
                            //                 // kalin gihhilanm haff day ekak thiynwda balanna
                            //                 $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' AND Is_Approve = '1' ");
                            //                 if (!empty($HaffDayaLeave[0]->Is_Approve)) {

                            //                     if ($cutofftime != '00:00:00') {

                            //                         $fromtime = $from_date . " " . $cutofftime;
                            //                         $totime = $OutDate . " " . $OutTime;
                            //                         $timestamp1 = strtotime($totime);
                            //                         $timestamp2 = strtotime($fromtime);
                            //                         $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                         $time_difference_minutes = $time_difference_seconds / 60;
                            //                         $iCalcHaff = round($time_difference_minutes, 2);
                            //                         $DayStatus = 'HFD';
                            //                     }
                            //                     if ($iCalcHaff <= 0) {
                            //                         $ED = 0;
                            //                     } else {
                            //                         $ED = $iCalcHaff;
                            //                     }
                            //                 }
                            //             }
                            //         }

                            //         $ApprovedExH = 0;
                            //         $SH_EX_OT = 0;
                            //         $icalData = 0;
                            //         if ($OutTime != '' && $InTime != $OutTime && $InTime != '' && $shift_type == 'EX' && $OutTime != "00:00:00") {

                            //             // group eke evening ot thiyenawanan
                            //             if ($settings[0]->Ot_e == 1) {

                            //                 //min time to ot eka hada gannawa group setting table eken
                            //                 $min_time_to_ot = $settings[0]->Min_time_t_ot_e;
                            //                 $dateTime = new DateTime($to_time);
                            //                 $dateTime->add(new DateInterval('PT' . $min_time_to_ot . 'M'));
                            //                 $shift_evning = $dateTime->format('H:i:s');

                            //                 if ($shift_evning < $OutTime) {
                            //                     $fromtime = $to_date . " " . $to_time;
                            //                     $totime = $OutDate . " " . $OutTime;
                            //                     $timestamp1 = strtotime($fromtime);
                            //                     $timestamp2 = strtotime($totime);
                            //                     $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                     $time_difference_minutes = $time_difference_seconds / 60;
                            //                     $icalData = round($time_difference_minutes, 2);
                            //                 }
                            //             }

                            //             // Out wunma passe OT
                            //             if ($icalData >= 0) {
                            //                 $AfterShiftWH = $icalData;
                            //             } else {
                            //                 $AfterShiftWH = 0;
                            //             }

                            //             // **************************************************************************************//
                            //             // kalin giya ewa (ED)


                            //         }


                            //         // New Late with HFD
                            //         $iCalclate = 0;
                            //         $iCalc = 0;
                            //         if ($InTime != '' && $InTime != $OutTime && $shift_type == 'EX' || $OutTime != '' && $shift_type == 'EX') {


                            //             if ($settings[0]->Late == 1) {

                            //                 $late_grass_period = $settings[0]->late_Grs_prd;
                            //                 $dateTime = new DateTime($from_time);
                            //                 $dateTime->add(new DateInterval('PT' . $late_grass_period . 'M'));
                            //                 $late_from_time_with_grsprd = $dateTime->format('H:i:s');

                            //                 $fromtime = $from_date . " " . $late_from_time_with_grsprd;
                            //                 $totime = $InDate . " " . $InTime;
                            //                 $timestamp1 = strtotime($fromtime);
                            //                 $timestamp2 = strtotime($totime);
                            //                 $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                 $time_difference_minutes = $time_difference_seconds / 60;
                            //                 $iCalclate = round($time_difference_minutes, 2);
                            //                 $lateM = $iCalclate;
                            //                 // kalin gihhilanm haff day ekak thiynwda balanna
                            //                 $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' AND Is_Approve = '1' ");
                            //                 if (!empty($HaffDayaLeave[0]->Is_Approve)) {

                            //                     if ($cutofftime != '00:00:00') {
                            //                         $fromtime = $from_date . " " . $cutofftime;
                            //                         $totime = $InDate . " " . $InTime;
                            //                         $timestamp1 = strtotime($fromtime);
                            //                         $timestamp2 = strtotime($totime);
                            //                         $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                         $time_difference_minutes = $time_difference_seconds / 60;
                            //                         $iCalc = round($time_difference_minutes, 2);
                            //                         $DayStatus = 'HFD';
                            //                     }
                            //                 }
                            //                 if ($iCalc < 0) {
                            //                     $lateM = 0;
                            //                 } else {
                            //                     $lateM = $iCalc;
                            //                 }
                            //             }
                            //         }

                            //         $AfterShiftWH = round($AfterShiftWH, 2);
                            //         $lateM = round($lateM, 2);
                            //         if ($settings[0]->Ot_d_Late == 1) {
                            //             $deduction = ($AfterShiftWH - $lateM);

                            //             if ($deduction < 0) {
                            //                 $lateM = abs($deduction);
                            //             }
                            //             if ($deduction == 0) {
                            //                 $lateM = 0;
                            //                 $AfterShiftWH = 0;
                            //             }
                            //             if ($deduction > 0) {
                            //                 $AfterShiftWH = abs($deduction);
                            //             }
                            //         }
                            //     }
                            // }

                            // if ($shift_type == "OFF") {


                            //     //holiday walata double ot ynwd balnw
                            //     if ($settings[0]->Dot_f_offday == 1) {

                            //         $InDate = '';
                            //         $InTime = '';
                            //         $OutDate = '';
                            //         $OutTime = '';


                            //         $lateM = '';
                            //         $ED = '';
                            //         $DayStatus = '';
                            //         $AfterShiftWH = '';
                            //         $DOT = '';
                            //         // Get the CheckIN
                            //         $dt_in_Records['dt_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from 
                            //         tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND (Status='0' OR AttTime <'14:00:00') ");
                            //         $InDate = $dt_in_Records['dt_Records'][0]->AttDate;
                            //         $InTime = $dt_in_Records['dt_Records'][0]->INTime;

                            //         // Get the CheckOut
                            //         $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from 
                            //         tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND (Status='1' OR AttTime > '13:00:00') ");
                            //         $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                            //         $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;


                            //         // Out Ekak nethnm check nextday(1st nextDay)
                            //         if ($FromDate != $to_date) {
                            //             // $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                            //             $newDate = $to_date;

                            //             // Get the CheckOut in the nextDay (before 9am)
                            //             $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                            //             tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='$newDate' AND AttTime <'09:00:00' AND Status='1' ");
                            //             $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                            //             $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;
                            //         }

                            //         if ($InTime != '' && $OutTime != '') {
                            //             $fromtime = $InDate . " " . $InTime;
                            //             $totime = $OutDate . " " . $OutTime;
                            //             $timestamp1 = strtotime($fromtime);
                            //             $timestamp2 = strtotime($totime);
                            //             $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //             $time_difference_minutes = $time_difference_seconds / 60;
                            //             if ($time_difference_minutes < 60) {
                            //                 $OutDate = '';
                            //                 $OutTime = '';
                            //             }
                            //             //ms wela thiyenne out time ekada balanw
                            //             $fromtime = $to_date . " " . $to_time;
                            //             $deduct_fromtime = strtotime($fromtime . " -2 hour");
                            //             $plus_fromtime = strtotime($fromtime . " +2 hour");
                            //             $ct = $InDate . " " . $InTime;
                            //             $check_time = strtotime($ct);
                            //             if ($deduct_fromtime <= $check_time && $check_time <= $plus_fromtime) {
                            //                 $OutDate = $InDate;
                            //                 $OutTime = $InTime;
                            //                 $InDate = '';
                            //                 $InTime = '';
                            //             }
                            //         }

                            //         $icaldot = 0;
                            //         if ($OutTime != '' && $InTime != $OutTime && $InTime != '' && $shift_type == 'OFF' && $OutTime != "00:00:00") {

                            //             $fromtime = $InDate . " " . $InTime;
                            //             $totime = $OutDate . " " . $OutTime;
                            //             $timestamp1 = strtotime($fromtime);
                            //             $timestamp2 = strtotime($totime);
                            //             $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //             $time_difference_minutes = $time_difference_seconds / 60;
                            //             $icaldot = round($time_difference_minutes, 2);
                            //         }
                            //         if ($icaldot >= 0) {
                            //             $DOT = $icaldot;
                            //         } else {
                            //             $DOT = 0;
                            //         }

                            //         //   dot naththan samanya process eka 
                            //     } 
                            //     else {

                            //         $InDate = '';
                            //         $InTime = '';
                            //         $OutDate = '';
                            //         $OutTime = '';


                            //         $lateM = '';
                            //         $ED = '';
                            //         $DayStatus = '';
                            //         $AfterShiftWH = '';
                            //         // Get the CheckIN
                            //         $dt_in_Records['dt_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from 
                            //         tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND (Status='0' OR AttTime <'14:00:00') ");
                            //         $InDate = $dt_in_Records['dt_Records'][0]->AttDate;
                            //         $InTime = $dt_in_Records['dt_Records'][0]->INTime;

                            //         // Get the CheckOut
                            //         $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from 
                            //         tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND (Status='1' OR AttTime > '13:00:00') ");
                            //         $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                            //         $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;


                            //         // Out Ekak nethnm check nextday(1st nextDay)
                            //         if ($FromDate != $to_date) {
                            //             // $newDate = date('Y-m-d', strtotime($FromDate . ' +1 day'));
                            //             $newDate = $to_date;

                            //             // Get the CheckOut in the nextDay (before 9am)
                            //             $dt_out_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as OutTime,Enroll_No,AttDate from 
                            //             tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='$newDate' AND AttTime <'09:00:00' AND Status='1' ");
                            //             $OutDate = $dt_out_Records['dt_out_Records'][0]->AttDate;
                            //             $OutTime = $dt_out_Records['dt_out_Records'][0]->OutTime;
                            //         }
                            //         if ($InTime != '' && $OutTime != '') {
                            //             $fromtime = $InDate . " " . $InTime;
                            //             $totime = $OutDate . " " . $OutTime;
                            //             $timestamp1 = strtotime($fromtime);
                            //             $timestamp2 = strtotime($totime);
                            //             $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //             $time_difference_minutes = $time_difference_seconds / 60;
                            //             if ($time_difference_minutes < 60) {
                            //                 $OutDate = '';
                            //                 $OutTime = '';
                            //             }
                            //             //ms wela thiyenne out time ekada balanw
                            //             $fromtime = $to_date . " " . $to_time;
                            //             $deduct_fromtime = strtotime($fromtime . " -2 hour");
                            //             $plus_fromtime = strtotime($fromtime . " +2 hour");
                            //             $ct = $InDate . " " . $InTime;
                            //             $check_time = strtotime($ct);
                            //             if ($deduct_fromtime <= $check_time && $check_time <= $plus_fromtime) {
                            //                 $OutDate = $InDate;
                            //                 $OutTime = $InTime;
                            //                 $InDate = '';
                            //                 $InTime = '';
                            //             }
                            //         }

                            //         $lateM = 0;
                            //         // $BeforeShift = 0;
                            //         $Late_Status = 0;
                            //         $NetLateM = 0;
                            //         $ED = 0;
                            //         $EDF = 0;
                            //         $Att_Allowance = 1;
                            //         $Nopay = 0;
                            //         $AfterShiftWH = 0;
                            //         $lateM = 0; //late minutes
                            //         // $ED = 0; //ED minutes
                            //         $iCalcHaffT = 0;

                            //         if ($InTime != $OutTime && $shift_type == 'DU' && $OutTime != '') {
                            //             // date samanam
                            //             $iCalcHaffED = 0;
                            //             $iCalcHaff = 0;
                            //             if ($settings[0]->Ed == 1) {

                            //                 $fromtime = $to_date . " " . $to_time;
                            //                 $totime = $OutDate . " " . $OutTime;
                            //                 $timestamp1 = strtotime($totime);
                            //                 $timestamp2 = strtotime($fromtime);
                            //                 $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                 $time_difference_minutes = $time_difference_seconds / 60;
                            //                 $iCalcHaffED = round($time_difference_minutes, 2);
                            //                 $ED = $iCalcHaffED;
                            //                 // kalin gihhilanm haff day ekak thiynwda balanna
                            //                 $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' AND Is_Approve = '1' ");
                            //                 if (!empty($HaffDayaLeave[0]->Is_Approve)) {

                            //                     if ($cutofftime != '00:00:00') {

                            //                         $fromtime = $from_date . " " . $cutofftime;
                            //                         $totime = $OutDate . " " . $OutTime;
                            //                         $timestamp1 = strtotime($totime);
                            //                         $timestamp2 = strtotime($fromtime);
                            //                         $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                         $time_difference_minutes = $time_difference_seconds / 60;
                            //                         $iCalcHaff = round($time_difference_minutes, 2);
                            //                         $DayStatus = 'HFD';
                            //                     }
                            //                     if ($iCalcHaff <= 0) {
                            //                         $ED = 0;
                            //                     } else {
                            //                         $ED = $iCalcHaff;
                            //                     }
                            //                 }
                            //             }
                            //         }

                            //         $ApprovedExH = 0;
                            //         $SH_EX_OT = 0;
                            //         $icalData = 0;
                            //         if ($OutTime != '' && $InTime != $OutTime && $InTime != '' && $shift_type == 'OFF' && $OutTime != "00:00:00") {

                            //             // group eke evening ot thiyenawanan
                            //             if ($settings[0]->Ot_e == 1) {

                            //                 //min time to ot eka hada gannawa group setting table eken
                            //                 $min_time_to_ot = $settings[0]->Min_time_t_ot_e;
                            //                 $dateTime = new DateTime($to_time);
                            //                 $dateTime->add(new DateInterval('PT' . $min_time_to_ot . 'M'));
                            //                 $shift_evning = $dateTime->format('H:i:s');

                            //                 if ($shift_evning < $OutTime) {
                            //                     $fromtime = $to_date . " " . $to_time;
                            //                     $totime = $OutDate . " " . $OutTime;
                            //                     $timestamp1 = strtotime($fromtime);
                            //                     $timestamp2 = strtotime($totime);
                            //                     $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                     $time_difference_minutes = $time_difference_seconds / 60;
                            //                     $icalData = round($time_difference_minutes, 2);
                            //                 }
                            //             }

                            //             // Out wunma passe OT
                            //             if ($icalData >= 0) {
                            //                 $AfterShiftWH = $icalData;
                            //             } else {
                            //                 $AfterShiftWH = 0;
                            //             }

                            //             // **************************************************************************************//

                            //         }


                            //         // New Late with HFD
                            //         $iCalclate = 0;
                            //         $iCalc = 0;
                            //         if ($InTime != '' && $InTime != $OutTime && $shift_type == 'OFF' || $OutTime != '' && $shift_type == 'OFF') {


                            //             if ($settings[0]->Late == 1) {

                            //                 $late_grass_period = $settings[0]->late_Grs_prd;
                            //                 $dateTime = new DateTime($from_time);
                            //                 $dateTime->add(new DateInterval('PT' . $late_grass_period . 'M'));
                            //                 $late_from_time_with_grsprd = $dateTime->format('H:i:s');

                            //                 $fromtime = $from_date . " " . $late_from_time_with_grsprd;
                            //                 $totime = $InDate . " " . $InTime;
                            //                 $timestamp1 = strtotime($fromtime);
                            //                 $timestamp2 = strtotime($totime);
                            //                 $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                 $time_difference_minutes = $time_difference_seconds / 60;
                            //                 $iCalclate = round($time_difference_minutes, 2);
                            //                 $lateM = $iCalclate;
                            //                 // kalin gihhilanm haff day ekak thiynwda balanna
                            //                 $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' AND Is_Approve = '1' ");
                            //                 if (!empty($HaffDayaLeave[0]->Is_Approve)) {

                            //                     if ($cutofftime != '00:00:00') {
                            //                         $fromtime = $from_date . " " . $cutofftime;
                            //                         $totime = $InDate . " " . $InTime;
                            //                         $timestamp1 = strtotime($fromtime);
                            //                         $timestamp2 = strtotime($totime);
                            //                         $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //                         $time_difference_minutes = $time_difference_seconds / 60;
                            //                         $iCalc = round($time_difference_minutes, 2);
                            //                         $DayStatus = 'HFD';
                            //                     }
                            //                 }
                            //                 if ($iCalc < 0) {
                            //                     $lateM = 0;
                            //                 } else {
                            //                     $lateM = $iCalc;
                            //                 }
                            //             }
                            //         }

                            //         $AfterShiftWH = round($AfterShiftWH, 2);
                            //         $lateM = round($lateM, 2);
                            //         if ($settings[0]->Ot_d_Late == 1) {
                            //             $deduction = ($AfterShiftWH - $lateM);

                            //             if ($deduction < 0) {
                            //                 $lateM = abs($deduction);
                            //             }
                            //             if ($deduction == 0) {
                            //                 $lateM = 0;
                            //                 $AfterShiftWH = 0;
                            //             }
                            //             if ($deduction > 0) {
                            //                 $AfterShiftWH = abs($deduction);
                            //             }
                            //         }
                            //     }
                            // }

                            // // **************************************************************************************//
                            // if ($InTime == $OutTime || $OutTime == null || $OutTime == '') {
                            //     $DayStatus = 'MS';
                            //     $Late_Status = 0;
                            //     $Nopay = 0;
                            //     $Nopay_Hrs = 0;
                            //     $Day_Type = 0.5;
                            // }

                            // /*
                            //  * If In Available & Out Missing
                            //  */
                            // if ($InTime != '' && $InTime == $OutTime) {
                            //     $DayStatus = 'MS';
                            //     $Late_Status = 0;
                            //     $Nopay = 0;
                            //     $Nopay_Hrs = 0;
                            //     $OutTime = "00:00:00";
                            //     $Day_Type = 0.5;
                            // }

                            // // If Out Available & In Missing
                            // if ($OutTime != '' && $OutTime == $InTime) {
                            //     $DayStatus = 'MS';
                            //     $Late_Status = 0;
                            //     $Nopay = 0;
                            //     $Nopay_Hrs = 0;
                            //     $OutTime = "00:00:00";
                            //     $Day_Type = 0.5;
                            // }

                            // // If In Available & Out Missing
                            // if ($InTime != '' && $OutTime == '') {
                            //     $DayStatus = 'MS';
                            //     $Late_Status = 0;
                            //     $Nopay = 0;
                            //     $Nopay_Hrs = 0;
                            //     $Day_Type = 0.5;
                            // }

                            // // If Out Available & In Missing
                            // if ($OutTime != '' && $InTime == '') {
                            //     $DayStatus = 'MS';
                            //     $Late_Status = 0;
                            //     $Nopay = 0;
                            //     $Nopay_Hrs = 0;
                            //     $Day_Type = 0.5;
                            // }
                            // // **************************************************************************************//

                            // if ($OutTime == "00:00:00") {
                            //     $DayStatus = 'MS';
                            //     $Late_Status = 0;
                            //     $Nopay = 0;
                            //     $OutTime = "00:00:00";
                            //     $Day_Type = 0.5;
                            // }

                            // if ($InTime != '' && $InTime != $OutTime && $OutTime != '' && ($InTime != '00:00:00' && $OutTime != '00:00:00')) {
                            //     $Nopay = 0;
                            //     $DayStatus = 'PR';
                            //     $Nopay_Hrs = 0;
                            //     $Day_Type = 1;
                            // }

                            // // **************************************************************************************//
                            // $Nopay_Hrs = 0;
                            // // Nopay
                            // if ($InTime == '' && $OutTime == '' && $shift_type == 'DU') {
                            //     $DayStatus = 'AB';
                            //     $Nopay = 1;
                            //     $Nopay_Hrs = (((strtotime($to_time) - strtotime($from_time))) / 60);
                            //     $Day_Type = 1;
                            //     // if ($InTime == '' && $OutTime == '' && $shift_type == 'EX') {
                            //     //     $Nopay = 0;
                            //     //     $Nopay_Hrs = 0;
                            //     //     $DayStatus = 'EX';
                            //     // }
                            // }
                            // if ($InTime == '' && $OutTime == '' && $shift_type == 'EX') {
                            //     $DayStatus = 'AB';
                            //     $Nopay = 1;
                            //     $Nopay_Hrs = (((strtotime($to_time) - strtotime($from_time))) / 60);
                            //     $Day_Type = 1;
                            //     // if ($InTime == '' && $OutTime == '' && $shift_type == 'EX') {
                            //     //     $Nopay = 0;
                            //     //     $Nopay_Hrs = 0;
                            //     //     $DayStatus = 'EX';
                            //     // }
                            // }
                            // if ($InTime == '' && $OutTime == '' && $shift_type == 'OFF') {
                            //     $DayStatus = 'OFF';
                            //     $Nopay = 1;
                            //     $Nopay_Hrs = (((strtotime($to_time) - strtotime($from_time))) / 60);
                            //     $Day_Type = 1;
                            //     // if ($InTime == '' && $OutTime == '' && $shift_type == 'EX') {
                            //     //     $Nopay = 0;
                            //     //     $Nopay_Hrs = 0;
                            //     //     $DayStatus = 'EX';
                            //     // }
                            // }



                            // // ===================Start Short Leave

                            // // **********************************************Short Leave****************************************//
                            // // Get the BreakkIN
                            // $dt_Breakin_Records['dt_Records'] = $this->Db_model->getfilteredData("select min(AttTime) as INTime,Enroll_No,AttDate from tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='3' ");
                            // $BreakInRecords = $dt_Breakin_Records['dt_Records'][0]->AttDate;
                            // $BreakInDate = $dt_Breakin_Records['dt_Records'][0]->AttDate;
                            // $BreakInTime = $dt_Breakin_Records['dt_Records'][0]->INTime;
                            // $BreakInRec = 1;

                            // // Get the BreakOut
                            // $dt_Breakout_Records['dt_out_Records'] = $this->Db_model->getfilteredData("select max(AttTime) as OutTime,Enroll_No,AttDate from tbl_u_attendancedata where Enroll_No='$EmpNo' and AttDate='" . $FromDate . "' AND Status='4' ");
                            // $BreakOutDate = $dt_Breakout_Records['dt_out_Records'][0]->AttDate;
                            // $BreakOutTime = $dt_Breakout_Records['dt_out_Records'][0]->OutTime;
                            // $BreakOutRec = 0;
                            // $BreakOutRecords = $dt_Breakout_Records['dt_out_Records'][0]->AttDate;

                            // // // ShortLeave thani eka [(After)atharameda]
                            // if ($BreakInTime != null && $BreakOutTime != null) {

                            //     $BreakInTime = $dt_Breakin_Records['dt_Records'][0]->INTime;
                            //     $BreakOutTime = $dt_Breakout_Records['dt_out_Records'][0]->OutTime;

                            //     //Late(Short)
                            //     $ShortLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_shortlive WHERE EmpNo = $EmpNo AND tbl_shortlive.Date = '$FromDate' ");
                            //     if (!empty($ShortLeave[0]->Is_Approve)) {
                            //         $SHFtime = $ShortLeave[0]->from_time;
                            //         $SHTtime = $ShortLeave[0]->to_time;

                            //         $BreakOutTimeSrt = strtotime($BreakOutTime);
                            //         $SHToTimeSrt = strtotime($SHTtime);

                            //         $iCalcShortLTIntv = ($BreakOutTimeSrt - $SHToTimeSrt) / 60;
                            //         $DayStatus = 'SL';
                            //         if ($iCalcShortLTIntv <= 0) {
                            //             // welawta ewilla

                            //         } else if ($iCalcShortLTIntv >= 0) {
                            //             // welatwa ewilla ne(short leave & haffDay ektath passe late)
                            //             $lateM = $iCalcHaffT + $iCalcShortLTIntv;
                            //         }
                            //     }

                            //     // ED(Short)
                            //     if (!empty($ShortLeave[0]->Is_Approve)) {
                            //         $SHFtime = $ShortLeave[0]->from_time;
                            //         $SHTtime = $ShortLeave[0]->to_time;

                            //         $BreakInTimeSrt = strtotime($BreakInTime);
                            //         $SHFromTimeSrt = strtotime($SHFtime);

                            //          $iCalcShortLTIntvED = ($SHFromTimeSrt - $BreakInTimeSrt) / 60;
                            //         $DayStatus = 'SL';

                            //         if ($iCalcShortLTIntvED < 0) {
                            //             // ee welwta hari ee welwen passe hari gihinm
                            //             $ED = 0;
                            //         } else if ($iCalcShortLTIntvED >= 0) {
                            //             // kalin gihinm
                            //             // $ED = $EDF + $iCalcShortLTIntvED;
                            //             $ED = $iCalcShortLTIntvED;
                            //         }
                            //         // echo $ED;
                            //     }
                            // } else {
                            //     // Hawasa ShortLeave thiynwam
                            //     $ShortLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_shortlive WHERE EmpNo = $EmpNo AND tbl_shortlive.Date = '$FromDate' ");
                            //     if (!empty($ShortLeave[0]->Is_Approve)) {
                            //         // echo "   okkk";
                            //         // if ($ShortLeave[0]->to_time >= $totime) {

                            //         $SHFtime = $ShortLeave[0]->from_time;
                            //         $SHTtime = $ShortLeave[0]->to_time;

                            //         // if (($OutTime > '17:30:00') || ($OutTime < '18:00:00')) {
                            //         // echo $InTime . '-' . $OutTime . ' || ' . $EmpNo;
                            //         // echo "<br>";
                            //         // echo $FromDate;
                            //         // echo "<br>";
                            //         $OutTimeSrt = strtotime($OutTime);
                            //         $SHFromTimeSrt = strtotime($SHFtime);

                            //         $iCalcShortLTED = ($SHFromTimeSrt - $OutTimeSrt) / 60;
                            //         // echo $iCalcShortLTED;
                            //         if ($iCalcShortLTED > 0) {
                            //             $ED = 0;
                            //             $ED = $iCalcShortLTED;
                            //             $DayStatus = 'SL';
                            //         } else {
                            //             $ED = 0;
                            //             $DayStatus = 'SL';
                            //             // echo "2";
                            //         }
                            //         // } else {
                            //         // }
                            //         // }
                            //     }

                            //     // Morning In Time ekata kalin short leave thiywam
                            //     $ShortLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_shortlive WHERE EmpNo = $EmpNo AND tbl_shortlive.Date = '$FromDate' ");
                            //     if (!empty($ShortLeave[0]->Is_Approve)) {
                            //         $SHFtime = $ShortLeave[0]->from_time;
                            //         $SHTtime = $ShortLeave[0]->to_time;

                            //         $InTimeSrt = strtotime($InTime);
                            //         $SHToTimeSrt = strtotime($SHTtime);

                            //         $iCalcShortLT = ($InTimeSrt - $SHToTimeSrt) / 60;

                            //         if ($SHFtime <= $fromtime) {
                            //             if ($iCalcShortLT <= 0) {
                            //                 // welawta ewilla
                            //                 $lateM = 0;
                            //                 $Late_Status = 0;
                            //                 $DayStatus = 'SL';
                            //             } else {
                            //                 // welatwa ewilla ne(short leave ektath passe late /haffDay ne )
                            //                 $lateM = $iCalcShortLT;
                            //                 $DayStatus = 'SL';

                            //                 // echo "2gg";
                            //             }
                            //         }
                            //     }
                            //     // **********************************************Short Leave****************************************//

                            //     // $$$$$$$$$$$$$$$$$$$$$$$//
                            //     // **************************************************************************************//

                            // }





                            // if ($shift_type == "OFF") {
                            //     $DayStatus = 'OFF';
                            //     $Late_Status = 0;
                            //     $Nopay = 0;
                            //     $InRecords = $FromDate;
                            //     $OutDate = $FromDate;
                            //     $InTime = "00:00:00";
                            //     $OutTime = "00:00:00";
                            // }



                            // $Holiday = $this->Db_model->getfilteredData("select count(Hdate) as HasRow from tbl_holidays where Hdate = '$FromDate' ");
                            // if ($Holiday[0]->HasRow == 1) {
                            //     $DayStatus = 'HD';
                            //     $Nopay = 0;
                            //     $Nopay_Hrs = 0;
                            //     $Att_Allowance = 0;
                            //     $Day_Type = 0;
                            // }
                            // $Leave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='1' AND Is_Approve = '1' ");
                            // if (!empty($Leave[0]->Is_Approve)) {
                            //     $Nopay = 0;
                            //     $DayStatus = 'LV';
                            //     $Nopay_Hrs = 0;
                            //     $Att_Allowance = 0;
                            //     $Day_Type = 1;
                            //     if ($InTime != '' && $InTime != $OutTime && $OutTime != '') {
                            //         $Nopay = 0;
                            //         $DayStatus = 'LV-PR';
                            //         $Nopay_Hrs = 0;
                            //         $Day_Type = 1;
                            //     }
                            // }

                            // $halfd_late = 0;
                            // $HaffDayaLeave = $this->Db_model->getfilteredData("SELECT * FROM tbl_leave_entry where EmpNo = $EmpNo and Leave_Date = '$FromDate' AND Leave_Count='0.5' AND Is_Approve = '1' ");
                            // if (!empty($HaffDayaLeave[0]->Is_Approve)) {

                            //     if ($InTime == '' && $OutTime == '' && $shift_type == 'DU') {

                            //         $fromtime = $from_date . " " . $cutofftime;
                            //         $totime = $from_date . " " . $from_time;
                            //         $timestamp1 = strtotime($totime);
                            //         $timestamp2 = strtotime($fromtime);
                            //         $time_difference_seconds = ($timestamp2 - $timestamp1);
                            //         $time_difference_minutes = $time_difference_seconds / 60;
                            //         $halfd_late = round($time_difference_minutes, 2);
                            //         $DayStatus = 'HFD-AB';
                            //         $lateM = $halfd_late;
                            //     }
                            // }

                            // if ($lateM >= 0) {
                            //     $lateM;
                            // } else {
                            //     $lateM = 0;
                            // }

                            // if ($ED >= 0) {
                            //     $ED;
                            // } else {
                            //     $ED = 0;
                            // }


                            // echo $ID_Roster;
                            // echo "<br/>";
                            // echo $EmpNo;
                            // echo "<br/>";
                            // echo $FromDate;
                            // echo "<br/>";
                            // echo "from date-" . $from_date;
                            // echo "<br/>";
                            // echo "from time-" . $from_time;
                            // echo "<br/>";
                            // echo "in date-" . $InDate;
                            // echo "<br/>";
                            // echo "in time-" . $InTime;
                            // echo "<br/>";
                            // echo "<br/>";
                            // echo "to date-" . $to_date;
                            // echo "<br/>";
                            // echo "to time-" . $to_time;
                            // echo "<br/>";
                            // echo "out date-" . $OutDate;
                            // echo "<br/>";
                            // echo "out time-" . $OutTime;
                            // echo "<br/>";
                            // echo "Late " . $lateM;
                            // echo "<br/>";
                            // echo "ED " . $ED;
                            // echo "<br/>";
                            // echo "DayStatus " . $DayStatus;
                            // echo "<br/>";
                            // echo "OT " . $AfterShiftWH;
                            // echo "<br/>";
                            // echo "dot" . $DOT;
                            // echo "<br/>";
                            // // // echo "in 3-" . $InmoTime3;
                            // // // echo "<br/>";
                            // // // echo "out 3-" . $OutDate3;
                            // // // echo "<br/>";
                            // // // echo "out 3-" . $OutTime3;
                            // // // echo "<br/>";
                            // // // echo "workhours1-" . $workhours1;
                            // // // echo "<br/>";
                            // // // echo "workhours2-" . $workhours2;
                            // // // echo "<br/>";
                            // // // echo "workhours3-" . $workhours3;
                            // // // echo "<br/>";
                            // // // echo "workhours3-" . $workhours;
                            // // // echo "<br/>";
                            // // // echo "dot1-" . $DOT1;
                            // // // echo "<br/>";
                            // // // echo "dot2-" . $DOT2;
                            // // // echo "<br/>";
                            // // // echo "dot3-" . $DOT3;
                            // // // echo "<br/>";
                            // // // echo "dot-" . $DOT;
                            // // // echo "<br/>";
                            // // // echo "out" . $OutTime;
                            // // // echo "<br/>";
                            // // // echo "outd-" . $OutDate;
                            // echo "<br/>";
                            // echo "<br/>";
                            // echo "<br/>";
                            // echo "<br/>";
                            // die;
                            //  $data_arr = array("InRec" => 1, "InDate" => $FromDate, "InTime" => $InTime, "OutRec" => 1,"Day_Type" => $Day_Type, "OutDate" => $OutDate, "OutTime" => $OutTime, "nopay" => $Nopay, "Is_processed" => 1, "DayStatus" => $DayStatus, "BeforeExH" => $BeforeShift, "AfterExH" => $AfterShiftWH, "LateSt" => $Late_Status, "LateM" => $lateM, "EarlyDepMin" => $ED, "NetLateM" => $NetLateM, "ApprovedExH" => $ApprovedExH, "nopay_hrs" => $Nopay_Hrs, "Att_Allow" => $Att_Allowance,"DOT" => $DOT);
                            //  $whereArray = array("ID_roster" => $ID_Roster);
                            // $result = $this->Db_model->updateData("tbl_individual_roster", $data_arr, $whereArray);

                        
                        }
                    }
                }
                $HasRow2 = $this->Db_model->getfilteredData("SELECT COUNT(EmpNo) AS HasRow FROM tbl_individual_roster WHERE Is_processed = 0 AND EXTRACT(MONTH FROM FDate)=$month and EXTRACT(YEAR FROM FDate)=$year");
                if ($HasRow2[0]->HasRow == 0) {
                    $query = "UPDATE `tbl_att_process` SET month = 0,selected_month = '0' ;";
                    // Run the custom query
                    $result = $this->Db_model->getUpdateData($query);
                }

                // $this->session->set_flashdata('success_message', 'Attendance Process successfully');
                // redirect('/Attendance/Attendance_Process_New');
            }else {
                $this->session->set_flashdata('success_message', 'Attendance Process successfully');
                redirect('/Attendance/Attendance_Process_New');

            }
            // $ID_Roster = $this->Db_model->getfilteredData("SELECT COUNT(ID_Roster) AS ID FROM `tbl_individual_roster` WHERE `tbl_individual_roster`.`Is_processed` = '0' AND FDate BETWEEN '" . $from_date . "' AND '" . $to_date . "';");
            // $WData = $ID_Roster[0]->ID;
            // $this->session->set_flashdata('WData', $WData);



            $this->session->set_flashdata('success_message', 'Attendance Process successfully');
            redirect('/Attendance/Attendance_Process_New');
            // }
        }
    }
}
