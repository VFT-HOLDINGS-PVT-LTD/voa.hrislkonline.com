<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Leave_Approve extends CI_Controller
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
        $currentUser = $this->session->userdata('login_user');
        $Emp = $currentUser[0]->EmpNo;
        $data['title'] = "Leave Apply | HRM System";
        $data['data_dep'] = $this->Db_model->getData('Dep_ID,Dep_Name', 'tbl_departments');
        $data['data_desig'] = $this->Db_model->getData('Des_ID,Desig_Name', 'tbl_designations');
        $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
        $data['data_set'] = $this->Db_model->getData('EmpNo,Emp_Full_Name', 'tbl_empmaster');
        $data['data_leave'] = $this->Db_model->getfilteredData("SELECT 
                                                                        lv_typ.Lv_T_ID,
                                                                        lv_typ.leave_name
                                                                    FROM
                                                                        tbl_leave_allocation lv_al
                                                                        right join
                                                                        tbl_leave_types lv_typ on lv_al.Lv_T_ID = lv_typ.Lv_T_ID
                                                                        where EmpNo='$Emp'");
        $this->load->view('Leave_Transaction/Leave_Approve/index', $data);
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
     * Search Employees by cat
     */

    public function search_employee()
    {


        $emp = $this->input->post("txt_emp");
        $emp_name = $this->input->post("txt_emp_name");
        $desig = $this->input->post("cmb_desig");
        $dept = $this->input->post("cmb_dep");
        $from_date = $this->input->post("txt_from_date");
        $to_date = $this->input->post("txt_to_date");


        // Filter Data by categories
        $filter = '';


        if (($this->input->post("txt_from_date")) && ($this->input->post("txt_to_date"))) {
            if ($filter == '') {
                $filter = " AND  le.Leave_Date between '$from_date' and '$to_date'";
            } else {
                $filter .= " AND  le.Leave_Date  between '$from_date' and '$to_date'";
            }
        }

        if (($this->input->post("txt_emp"))) {
            if ($filter == null) {
                $filter = " AND em.EmpNo = '$emp'";
            } else {
                $filter .= " AND em.EmpNo = '$emp'";
            }
        }

        if (($this->input->post("txt_emp_name"))) {
            if ($filter == null) {
                $filter = " AND em.Emp_Full_Name= '$emp_name'";
            } else {
                $filter .= " AND em.Emp_Full_Name = '$emp_name'";
            }
        }

        $data['data_set'] = $this->Db_model->getfilteredData("SELECT 
                                                                    le.LV_ID,
                                                                    le.EmpNo,
                                                                    em.Emp_Full_Name,
                                                                    lt.leave_name,
                                                                    le.Apply_Date,
                                                                    le.month,
                                                                    le.Year,
                                                                    le.Is_pending,
                                                                    le.Leave_Date,
                                                                    le.Reason,
                                                                    le.Leave_Count
                                                                FROM
                                                                    tbl_leave_entry le
                                                                        INNER JOIN
                                                                    tbl_empmaster em ON em.EmpNo = le.EmpNo
                                                                        INNER JOIN
                                                                    tbl_leave_types lt ON lt.Lv_T_ID = le.Lv_T_ID
                                                                WHERE
                                                                    le.Is_pending = 1 and Is_Cancel=0 and Is_Sup_AD_APP =1 {$filter}");

        $this->load->view('Leave_Transaction/Leave_Approve/search_data', $data);
    }

    /*
     * Approve Leave request
     */

    public function approve($ID)
    {

        $currentUser = $this->session->userdata('login_user');
        $Emp = $currentUser[0]->EmpNo;

        $data = array(
            'Is_pending' => 0,
            'Is_Approve' => 1,
            'Approved_by' => $Emp,
        );

        $LTYpe = $this->Db_model->getfilteredData("select * from tbl_leave_entry where LV_ID = $ID");
        $Emp_LV = $LTYpe[0]->EmpNo;

        $Emp_Data = $this->Db_model->getfilteredData("select * from tbl_leave_entry where LV_ID=$ID");
        $Emp_No = $Emp_Data[0]->EmpNo;
        $leave_type = $Emp_Data[0]->Lv_T_ID;

        //Get Employee Contact Details

        $Emp_cont_Data = $this->Db_model->getfilteredData(" select EmpNo,Emp_Full_Name,Tel_mobile from tbl_empmaster where EmpNo=$Emp_No");
        $Tel = $Emp_cont_Data[0]->Tel_mobile;
        $Emp_Fullname = $Emp_cont_Data[0]->Emp_Full_Name;


        //***Get leave date by Leave ID 
        $Leave_data = $this->Db_model->getfilteredData("select * from tbl_leave_entry where LV_ID=$ID and EmpNo=$Emp_No");

        $from_date = $Leave_data[0]->Leave_Date;

        // echo $ID.''. $leave_type;
        // die;

        /*
         * Update Individual Roster Table Is Leave status and Leave Type
         */
        //Start
        $Roster_ID = $this->Db_model->getfilteredData("select * from tbl_individual_roster where EmpNo ='$Emp_No' and Fdate = '$from_date' ");
        // $rostID = $Roster_ID1[0]->ID_Roster;
        // print_r($Roster_ID1);
        // echo $from_date.''. $from_date;
        // die;

        $DayStatus = 'LV'; //****** IF Apply Leave Update Individual Roster DayStatus As 'LV'
        $data_RS = array("Lv_T_ID" => $leave_type, "Is_Leave" => 1, "nopay" => 0, "DayStatus" => $DayStatus, 'Is_processed' => 1, "Att_Allow" => 0);
        $whereArray = array("ID_Roster" => $Roster_ID[0]->ID_Roster);
        $results = $this->Db_model->updateData("tbl_individual_roster", $data_RS, $whereArray);

        $whereArr = array("LV_ID" => $ID);
        $result = $this->Db_model->updateData("tbl_leave_entry", $data, $whereArr);

        $empname1 = $this->Db_model->getfilteredData("SELECT tbl_empmaster.Grp_ID,tbl_empmaster.Emp_Full_Name,tbl_empmaster.E_mail FROM tbl_empmaster WHERE tbl_empmaster.EmpNo = '$Emp_LV'");
        $groupid = $empname1[0]->Grp_ID;
        $employee_email = $empname1[0]->E_mail;
        $empname = $this->Db_model->getfilteredData("SELECT tbl_emp_group.Sup_ID FROM tbl_emp_group WHERE tbl_emp_group.Grp_ID = '$groupid' ");
        $supid = $empname[0]->Sup_ID;

        $supplier_n = $this->Db_model->getfilteredData("SELECT tbl_empmaster.Emp_Full_Name FROM tbl_empmaster WHERE tbl_empmaster.EmpNo = '$supid' ");
        $supplier_name = $supplier_n[0]->Emp_Full_Name;

        // $config = array(
        //     'protocol' => 'smtp',
        //     'smtp_host' => 'mail.vfthris.com',
        //     'smtp_user' => 'mail@vfthris.com',
        //     'smtp_pass' => 'Wlm7?Ux7g[s1',
        //     'smtp_port' => 587,
        //     'charset' => 'utf-8',
        //     'mailtype' => 'html',
        //     'newline' => "\r\n"
        // );

        // $this->load->library("email");
        // $this->email->initialize($config);

        // $this->email->from("mail@vfthris.com");
        // $this->email->to($employee_email);
        // $this->email->message("Leave Request Reject By '$supplier_name'");
        // $this->email->subject("Leave Request");

        // if ($this->email->send()) {
        //     echo "Success";
        // } else {
        //     echo "Failed: " . $this->email->print_debugger();
        // }
        //End
        //****** Send message to leave request employee
        /*
         * SMS Server configuration
         */
        // $sender = "HRM SYSTEM";
        // $recipient = $Tel;
        // $message = 'System Response : ' . $Emp_Fullname . ' ' . 'Your Leave Request on' . ' ' . $from_date . ' ' . 'is Approved';

        // $url = 'http://127.0.0.1:9333/ozeki?';
        // $url .= "action=sendMessage";
        // $url .= "&login=admin";
        // $url .= "&password=abc123";
        // $url .= "&recepient=" . urlencode($recipient);
        // $url .= "&messageData=" . urlencode($message);
        // $url .= "&sender=" . urlencode($sender);
        // file($url);




        $this->session->set_flashdata('success_message', 'Leave Approved successfully');
        redirect(base_url() . "Leave_Transaction/Leave_Approve");
    }

    public function edit_lv($ID)
    {

        $data['title'] = "Leave Apply | HRM System";

        $this->load->view('Leave_Transaction/Leave_Edit/index', $data);



        //        $currentUser = $this->session->userdata('login_user');
        //        $Emp = $currentUser[0]->EmpNo;
        //
        //        $data = array(
        //            'Is_pending' => 0,
        //            'Is_Approve' => 1,
        //            'Approved_by' => $Emp,
        //        );
        //
        //
        //        $Emp_Data = $this->Db_model->getfilteredData("select * from tbl_leave_entry where LV_ID=$ID");
        //        $Emp_No = $Emp_Data[0]->EmpNo;
        //        
        //        //Get Employee Contact Details
        //       
        //        $Emp_cont_Data = $this->Db_model->getfilteredData(" select EmpNo,Emp_Full_Name,Tel_mobile from tbl_empmaster where EmpNo=$Emp_No");
        //        $Tel = $Emp_cont_Data[0]->Tel_mobile;
        //        $Emp_Fullname = $Emp_cont_Data[0]->Emp_Full_Name;
        //                
        //
        //        //***Get leave date by Leave ID 
        //        $Leave_data = $this->Db_model->getfilteredData("select * from tbl_leave_entry where LV_ID=$ID and EmpNo=$Emp_No");
        //
        //        $from_date = $Leave_data[0]->Leave_Date;
        //
        //        /*
        //         * Update Individual Roster Table Is Leave status and Leave Type
        //         */
        //        //Start
        //        $Roster_ID = $this->Db_model->getfilteredData("select ID_Roster from tbl_individual_roster where EmpNo ='$Emp_No' and Fdate = '$from_date' ");
        //        $DayStatus = 'LV'; //****** IF Apply Leave Update Individual Roster DayStatus As 'LV'
        //        $data_RS = array("Lv_T_ID" => $leave_type, "Is_Leave" => 1, "nopay" => 0, "DayStatus" => $DayStatus, 'Is_processed' => 1);
        //        $whereArray = array("ID_Roster" => $Roster_ID[0]->ID_Roster);
        //        $results = $this->Db_model->updateData("tbl_individual_roster", $data_RS, $whereArray);
        //
        //        $whereArr = array("LV_ID" => $ID);
        //        $result = $this->Db_model->updateData("tbl_leave_entry", $data, $whereArr);
        //        //End
        //
        //        //****** Send message to leave request employee
        //        /*
        //         * SMS Server configuration
        //         */
        //        $sender = "HRM SYSTEM";
        //        $recipient = $Tel;
        //        $message = 'System Response : ' . $Emp_Fullname .' '. 'Your Leave Request on'. ' '.$from_date.' '. 'is Approved';
        //
        //        $url = 'http://127.0.0.1:9333/ozeki?';
        //        $url .= "action=sendMessage";
        //        $url .= "&login=admin";
        //        $url .= "&password=abc123";
        //        $url .= "&recepient=" . urlencode($recipient);
        //        $url .= "&messageData=" . urlencode($message);
        //        $url .= "&sender=" . urlencode($sender);
        //        file($url);
        //
        //
        //
        //
        //        $this->session->set_flashdata('success_message', 'Leave Approved successfully');
        //        redirect(base_url() . "Leave_Transaction/Leave_Approve");
    }

    //sms
    public function sms()
    {

        // $sender = "Name";
        // $recipient = $user_details[$x]->contact_no;
        // $message = 'Dear Customer';

        // $url = 'http://127.0.0.1:9333/ozeki?';
        // $url .= "action=sendMessage";
        // $url .= "&login=admin";
        // $url .= "&password=abc123";
        // $url .= "&recepient=" . urlencode($recipient);
        // $url .= "&messageData=" . urlencode($message);
        // $url .= "&sender=" . urlencode($sender);
        // file($url);
    }

    /*
     * Reject Leave request
     */

    public function noti($ID)
    {


        //        var_dump($ID);die;

        $currentUser = $this->session->userdata('login_user');
        $Emp = $currentUser[0]->EmpNo;

        $data = array(
            'Is_Display' => 0
        );

        $whereArr = array("ID" => $ID);
        $result = $this->Db_model->updateData("tbl_notifications", $data, $whereArr);

        //        $this->session->set_flashdata('success_message', 'Leave Reject successfully');
        redirect(base_url() . "Leave_Transaction/Leave_Approve");
    }

    public function reject($ID)
    {


        $currentUser = $this->session->userdata('login_user');
        $Emp = $currentUser[0]->EmpNo;

        $data = array(
            'Is_pending' => 0,
            'Is_Approve' => 0,
            'Is_Cancel' => 1,
            'Approved_by' => $Emp,
        );

        $whereArr = array("LV_ID" => $ID);
        $result = $this->Db_model->updateData("tbl_leave_entry", $data, $whereArr);



        //        -------- Leave Allocation Update
        date_default_timezone_set('Asia/Colombo');
        $date = date_create();
        $year = date("Y");

        $LTYpe = $this->Db_model->getfilteredData("select * from tbl_leave_entry where LV_ID = $ID");
        $Emp_LV = $LTYpe[0]->EmpNo;
        $LeaveType = $LTYpe[0]->Lv_T_ID;
        $tbl_leave_count = $LTYpe[0]->Leave_Count;

        $Balance_Usd = $this->Db_model->getfilteredData("select Balance,Used,Lv_T_ID from tbl_leave_allocation where EmpNo=$Emp_LV and Year=$year and Lv_T_ID=$LeaveType ");
        //                    var_dump($Balance_Usd);die;
        $Day_type = $tbl_leave_count;
        $Balance = $Balance_Usd[0]->Balance + $Day_type;



        $Used = $Balance_Usd[0]->Used - $Day_type;
        if($Used < 0){
            $Used = 0;  
        }
        $Lv_T_ID = $Balance_Usd[0]->Lv_T_ID;

        $data_arr = array("Balance" => $Balance, "Used" => $Used);

        $whereArray = array("EmpNo" => $Emp_LV, "Lv_T_ID" => $Lv_T_ID);
        $result = $this->Db_model->updateData("tbl_leave_allocation", $data_arr, $whereArray);

        $empname1 = $this->Db_model->getfilteredData("SELECT tbl_empmaster.Grp_ID,tbl_empmaster.Emp_Full_Name,tbl_empmaster.E_mail FROM tbl_empmaster WHERE tbl_empmaster.EmpNo = '$Emp_LV'");
        $groupid = $empname1[0]->Grp_ID;
        $employee_email = $empname1[0]->E_mail;
        $empname = $this->Db_model->getfilteredData("SELECT tbl_emp_group.Sup_ID FROM tbl_emp_group WHERE tbl_emp_group.Grp_ID = '$groupid' ");
        $supid = $empname[0]->Sup_ID;
       
        $supplier_n = $this->Db_model->getfilteredData("SELECT tbl_empmaster.Emp_Full_Name FROM tbl_empmaster WHERE tbl_empmaster.EmpNo = '$supid' ");
        $supplier_name = $supplier_n[0]->Emp_Full_Name;
       
        // $config = array(
        //     'protocol' => 'smtp',
        //     'smtp_host' => 'mail.vfthris.com',
        //     'smtp_user' => 'mail@vfthris.com',
        //     'smtp_pass' => 'Wlm7?Ux7g[s1',
        //     'smtp_port' => 587,
        //     'charset' => 'utf-8',
        //     'mailtype' => 'html',
        //     'newline' => "\r\n"
        // );

        // $this->load->library("email");
        // $this->email->initialize($config);

        // $this->email->from("mail@vfthris.com");
        // $this->email->to($employee_email);
        // $this->email->message("Leave Request Reject By '$supplier_name'");
        // $this->email->subject("Leave Request");

        // if ($this->email->send()) {
        //     echo "Success";
        // } else {
        //     echo "Failed: " . $this->email->print_debugger();
        // }



        $this->session->set_flashdata('success_message', 'Leave Reject successfully');
        redirect(base_url() . "Leave_Transaction/Leave_Approve");
    }
}
