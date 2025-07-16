<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cover_Up_Leave_Entry extends CI_Controller
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
        $this->load->model('Db_model', '', TRUE);
    }

    /*
     * Index page
     */

    public function index()
    {

        $data['title'] = "Leave Entry | HRM System";
        $data['data_set'] = $this->Db_model->getData('EmpNo,Emp_Full_Name', 'tbl_empmaster');
        $data['data_leave'] = $this->Db_model->getData('Lv_T_ID,leave_name,leave_entitle', 'tbl_leave_types');
        $data['cup_leave'] = $this->Db_model->getData('CP_ID,EmpNo,Cover_Up_Date,Leave_Date,Leave_Day,Leave_Reason,Approved_by,Is_Approve', 'tbl_cover_up_leave_entry');
        $this->load->view('Leave_Transaction/Cover_Up_Leave_Entry/index', $data);
    }

    /*
     * Check Leave Balance
     */

    public function check_Leave()
    {


        $cat = $this->input->post('cmb_cat2');

        $query = $this->Db_model->getfilteredData("select Used, Balance from tbl_leave_allocation where EmpNo='" . $cat . "' ");

        $query;
    }

    /*
     * Dependent Dropdown
     */

    public function dropdown()
    {

        $cat = $this->input->post('cmb_cat');

        if ($cat == "Employee") {
            $query = $this->Db_model->get_dropdown();
            echo '<option value="" default>-- Select --</option>';
            foreach ($query->result() as $row) {

                echo "<option value='" . $row->EmpNo . "'>" . $row->Emp_Full_Name . "</option>";
            }
        }
    }

    /*
     * Insert Leave Data
     */

    //     public function insert_data() {

    //         $currentUser = $this->session->userdata('login_user');
//         $ApproveUser = $currentUser[0]->EmpNo;


    //         $cat = $this->input->post('cmb_cat');
//         if ($cat == "Employee") {
//             $cat2 = $this->input->post('txt_nic');
//             $string = "SELECT EmpNo FROM tbl_empmaster WHERE EmpNo='$cat2' and Status = 1";
//             $EmpData = $this->Db_model->getfilteredData($string);
//         }

    //         /*
//          * Set Defalt Date TimeZone
//          */
//         date_default_timezone_set('Asia/Colombo');
//         $date = date_create();
//         $timestamp = date_format($date, 'Y-m-d H:i:s');

    //         /*
//          * Leave Type Full Fay or Half Day
//          */
//         $leave_type = $this->input->post('cmb_leave_type');
//         $reason = $this->input->post('txt_reason');
//         $orderdate = $this->input->post('txt_from_date');
//         $from_date = $this->input->post('txt_from_date');
//         $to_date = $this->input->post('txt_to_date');
//         $Day_type = $this->input->post('cmb_day');


    // //        var_dump($Day_type);die;

    //         $orderdate = explode('/', $orderdate);
//         $year = $orderdate[0];
//         $month = $orderdate[1];

    //         $Emp = $EmpData[0]->EmpNo;

    //         $d1 = new DateTime($from_date);
//         $d2 = new DateTime($to_date);

    //         /*
//          * Get selected days count
//          */
//         $interval = $d2->diff($d1)->days;
//         $DaysInc = $d2->diff($d1)->days;
//         ++$DaysInc;

    //         // var_dump($DaysInc);

    //         /*
//          * Check If Selected Employee have Allocated Leave in Leave Allocation Table
//          */
//         $IsAllocate = $this->Db_model->getfilteredData("select count(EmpNo) as IsAllocate from tbl_leave_allocation where EmpNo=$Emp ");

    // //        $IsBalance = $this->Db_model->getfilteredData("select count(Balance) as Balance_lv from tbl_leave_allocation where EmpNo= $Emp and Balance >=$DaysInc");
//         $IsBalance = $this->Db_model->getfilteredData("select Balance from tbl_leave_allocation where EmpNo= $Emp and Lv_T_ID=$leave_type and Balance >=$DaysInc");

    //         // var_dump($Emp, $leave_type, $DaysInc);
// //        echo 'ssssss';
// //        var_dump($IsBalance);die;

    //         /*
//          * Get Individual Roster ID in Selected Date
//          */
//         $Roster_ID_S = $this->Db_model->getfilteredData("select count(ID_Roster) as ShftCount from tbl_individual_roster where FDate between '$from_date' and '$to_date' and EmpNo=$Emp");

    //         // echo '<pre>' . var_export($Roster_ID_S, true) . '</pre>';
//         // var_dump($from_date, $to_date, $Emp);


    // //        var_dump($Roster_ID_S);
// //        die;

    //         if ($IsAllocate[0]->IsAllocate == 0) {
//             $this->session->set_flashdata('error_message', 'Employee does not have Allocated Leaves');
//         } if ($IsBalance[0]->Balance == 0) {
//             $this->session->set_flashdata('error_message', 'Employee Required Leave Balanve Not Enough');
//         } else {


    //             for ($x = 0; $x <= $interval; $x++) {


    //                 /*
//                  * Get Individual Roster ID in Selected Date
//                  */
//                 $Roster_ID = $this->Db_model->getfilteredData("select ID_Roster from tbl_individual_roster where EmpNo ='$Emp' and Fdate = '$from_date' ");

    //                 $IsApprove = 1;
//                 $IsPending = 0;


    //                 if ($ApproveUser == $Emp) {
//                     $ApproveUser = '';
//                     $IsApprove = 0;
//                     $IsPending = 1;
//                 }

    //                 $Emp = $EmpData[0]->EmpNo;

    //                 $data = array(
//                     array(
//                         'EmpNo' => $EmpData[0]->EmpNo,
//                         'Lv_T_ID' => $leave_type,
//                         'Leave_Count' => $Day_type,
//                         'Leave_Date' => $from_date,
//                         'Apply_Date' => $timestamp,
//                         'Is_Approve' => $IsApprove,
//                         'Approved_by' => $ApproveUser,
//                         'Is_pending' => $IsPending,
//                         'Year' => $year,
//                         'Month' => $month,
//                         'Reason' => $reason,
//                         'Trans_time' => $timestamp,
//                     //    'ID_Roster' => $Roster_ID[0]->ID_Roster
//                 ));

    //                 $HasR = $this->Db_model->getfilteredData("select count(EmpNo) as HasRow from tbl_leave_entry where EmpNo = '$Emp' and Leave_Date = '$from_date' ");

    //                 if ($HasR[0]->HasRow >= 1) {
//                     $this->session->set_flashdata('error_message', 'Already Leave added these days');
//                 } else {
//                     /*
//                      * Insert Leave Data to leave entry table
//                      */
//                     $this->db->insert_batch('tbl_leave_entry', $data);




    //                     $Day_type_Int = (float) $Day_type;

    // //                    var_dump($Day_type_Int);die;

    //                     /*
//                      * Update Individual Roster Table Is Leave status and Leave Type
//                      */
//                     $DayStatus = 'LV'; //****** IF Apply Leave Update Individual Roster DayStatus As 'LV'
//                     $data = array("Lv_T_ID" => $leave_type, "Is_Leave" => 1, "nopay" => 0, "DayStatus" => $DayStatus, "LeaveM" => $Day_type_Int, "Att_Allow" => 0);
//                     $whereArr = array("ID_Roster" => $Roster_ID[0]->ID_Roster);
//                     $result = $this->Db_model->updateData("tbl_individual_roster", $data, $whereArr);

    //                     /*
//                      * Get Leave Balance and Used by Employee No | Year | Leave Type
//                      */
//                     $Balance_Usd = $this->Db_model->getfilteredData("select Balance,Used,Lv_T_ID from tbl_leave_allocation where EmpNo=$Emp and Year=$year and Lv_T_ID=$leave_type ");
// //                    var_dump($Balance_Usd);die;
//                     $Balance = $Balance_Usd[0]->Balance - $Day_type;


    //                     $Used = $Balance_Usd[0]->Used + $Day_type;
//                     $Lv_T_ID = $Balance_Usd[0]->Lv_T_ID;

    //                     $data_arr = array("Balance" => $Balance, "Used" => $Used);
//                     $whereArray = array("EmpNo" => $EmpData[0]->EmpNo, "Lv_T_ID" => $Lv_T_ID);
//                     $result = $this->Db_model->updateData("tbl_leave_allocation", $data_arr, $whereArray);


    //                     $this->session->set_flashdata('success_message', 'New Leave has been added successfully');
//                 }


    //                 ++$from_date;
//             }
//         }

    //         redirect('/Leave_Transaction/Leave_Entry/');
//     }

    public function insert_data()
    {

        $currentUser = $this->session->userdata('login_user');
        $ApproveUser = $currentUser[0]->EmpNo;

        // $cat = $this->input->post('cmb_cat');
        // if ($cat == "Employee") {
        //     $cat2 = $this->input->post('cmb_cat2');
        //     $string = "SELECT EmpNo FROM tbl_empmaster WHERE EmpNo='$cat2' and Status = 1";
        //     $EmpData = $this->Db_model->getfilteredData($string);
        // }

        /*
         * Set Defalt Date TimeZone
         */
        date_default_timezone_set('Asia/Colombo');
        $date = date_create();
        $timestamp = date_format($date, 'Y-m-d H:i:s');

        /*
         * Leave Type Full Fay or Half Day
         */
        $leave_type = $this->input->post('cmb_leave_type');
        $reason = $this->input->post('txt_reason');
        // $orderdate = $this->input->post('Cover_Up_date');
        $from_date = $this->input->post('Cover_Up_date');
        $to_date = $this->input->post('Leave_date');
        $Day_type = $this->input->post('cmb_day');
        $nametoNum = $this->input->post('txt_nic');

        $string = "SELECT EmpNo FROM tbl_empmaster WHERE EmpNo='$nametoNum' and Status = 1";
        $EmpData = $this->Db_model->getfilteredData($string);


        // echo $from_date;
        // die;

        // echo $cat2;
        // echo "<br>";
        // echo $name;

        //    var_dump($Day_type);die;

        // $orderdate = explode('/', $orderdate);
        // $year = $orderdate[0];
        // $month = $orderdate[1];

        $Emp = $EmpData[0]->EmpNo;

        $d1 = new DateTime($from_date);
        $d2 = new DateTime($to_date);

        /*
         * Get selected days count
         */
        $interval = $d2->diff($d1)->days;
        $DaysInc = $d2->diff($d1)->days;
        ++$DaysInc;


        // for ($x = 0; $x <= $interval; $x++) {
            $from_dateNew = false;
            $IsApprove = 1;
            $IsPending = 0;
            $ApproveUser;

            // $d1 = new DateTime($from_date);
            // // check the Saturday (6) and Sunday (7)
            // if ($d1->format('N') == 6 || $d1->format('N') == 7) {
            //     // $from_dateNew = "1996-01-01"; // Output "not" for Saturday (6) and Sunday (7)
            // } else {

            //     // $from_dateNew = $d1->format('Y-m-d') . "\n"; // Print the current date
            //     $holidays = $this->Db_model->getfilteredData("select count(Holy_ID) as ID from tbl_holidays where Hdate='$from_date'");
            //     if ($holidays[0]->ID != 1) {
            //         $from_dateNew = true;
            //         $from_date;
            //     }
            // }

            $Emp = $EmpData[0]->EmpNo;
            // $d1->modify('+1 day'); // Increment the date by one day

            // if ($from_dateNew) {
                $data = explode('/', $from_date);

                $year1 = $data[0];
                $month1 = $data[1];
                $data = array(
                    'EmpNo' => $EmpData[0]->EmpNo,
                    // 'CP_Lv_T_ID' => $leave_type,
                    'Leave_Day' => 'Full Day',
                    'Cover_Up_Date' => $from_date,
                    'Leave_Date' => $to_date,
                    'Is_Approve' => $IsApprove,
                    'Approved_by' => $ApproveUser,
                    'Leave_Reason' => $reason,
                    'Update_Date_Time' => $timestamp,
                );

                $this->Db_model->insertData('tbl_cover_up_leave_entry', $data);


                // $HasR = $this->Db_model->getfilteredData("select count(EmpNo) as HasRow from tbl_leave_entry where EmpNo = '$Emp' and Leave_Date = '$from_date' ");

                // if ($HasR[0]->HasRow >= 1) {
                //     $this->session->set_flashdata('error_message', 'Already Leave added these days');
                // } else {
                //     // Insert Leave Data to leave entry table

                //     $Day_type_Int = (float) $Day_type;

                //     // Update Individual Roster Table Is Leave status and Leave Type
                //     // $DayStatus = 'LV'; //****** IF Apply Leave Update Individual Roster DayStatus As 'LV'
                //     // $data = array("Lv_T_ID" => $leave_type, "Is_Leave" => 1, "nopay" => 0, "DayStatus" => $DayStatus, "LeaveM" => $Day_type_Int, "Att_Allow" => 0);
                //     // $whereArr = array("ID_Roster" => $Roster_ID[0]->ID_Roster);
                //     // $result = $this->Db_model->updateData("tbl_individual_roster", $data, $whereArr);

                //     // Get Leave Balance and Used by Employee No | Year | Leave Type
                //     $Balance_Usd = $this->Db_model->getfilteredData("select Balance,Used,Lv_T_ID from tbl_leave_allocation where EmpNo=$Emp and Year='" . $year1 . "' and Lv_T_ID=$leave_type ");
                //     $Balance = $Balance_Usd[0]->Balance - $Day_type;

                //     $Used = $Balance_Usd[0]->Used + $Day_type;
                //     $Lv_T_ID = $Balance_Usd[0]->Lv_T_ID;

                //     $data_arr = array("Balance" => $Balance, "Used" => $Used);
                //     $whereArray = array("EmpNo" => $EmpData[0]->EmpNo, "Lv_T_ID" => $Lv_T_ID);
                //     $result = $this->Db_model->updateData("tbl_leave_allocation", $data_arr, $whereArray);
                // }
            // }

            // ++$from_date;
        // }

        $this->session->set_flashdata('success_message', 'New Leave has been added successfully');
        redirect('/Leave_Transaction/Cover_Up_Leave_Entry/');
    }
    public function search()
    {
        $json = [];
        $this->load->database();

        if (!empty($this->input->get("q"))) {
            $this->db->like('EmpNo', $this->input->get("q"));
            $query = $this->db->select('EmpNo as id, EmpNo as text')
                ->limit(10)
                ->get("tbl_empmaster");
            $json = $query->result();
        }

        echo json_encode($json);
    }

    public function get_mem_data($id1)
    {
        $result = $this->Db_model->getfilteredData("select * from tbl_empmaster where EmpNo = '$id1'");
        echo json_encode($result);
    }

}
