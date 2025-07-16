<?php

defined('BASEPATH') or exit ('No direct script access allowed');

class Short_Leave_Request extends CI_Controller
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
        $data['title'] = "Leave Entry | HRM System";
        $data['data_set'] = $this->Db_model->getData('EmpNo,Emp_Full_Name', 'tbl_empmaster');
        $data['data_leave'] = $this->Db_model->getData('Lv_T_ID,leave_name,leave_entitle', 'tbl_leave_types');
        $data['data_set_att'] = $this->Db_model->getfilteredData("select * from tbl_shortlive inner join tbl_empmaster on tbl_empmaster.EmpNo = tbl_shortlive.EmpNo where tbl_empmaster.EmpNo=$Emp order by ID desc");
        
        $this->load->view('Leave_Transaction/Short_Leave_Request/index', $data);

        // $this->load->view('Leave_Transaction/Short_Leave_Entry/index', $data);

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



    public function insert_data()
    {

        $currentUser = $this->session->userdata('login_user');
        $ApproveUser = $currentUser[0]->EmpNo;
        $Emp = $this->input->post('txt_employee');
        $date1 = $this->input->post('att_date');
        $from_time = $this->input->post('in_time');
        $to_time = $this->input->post('out_time');

        date_default_timezone_set('Asia/Colombo');
        $date = new DateTime();
        $timestamp1 = date_format($date, 'Y-m-d');

        $orderdate1 = explode('/', $date1);
        $year1 = $orderdate1[0];

        $month2 = $orderdate1[1];

        $date = $date1;
        $H = explode("/", $date);
        $month = $H[1];


        // $Monthonly = date('Y/m/d');
        // $M = explode("/", $Monthonly);
        // $Month1 = $M[1];

        // $leaveentity = $this->Db_model->getfilteredData("SELECT * FROM tbl_emp_group INNER JOIN tbl_empmaster ON tbl_emp_group.Grp_ID = tbl_empmaster.Grp_ID WHERE tbl_empmaster.EmpNo = '$Emp' ");
        // $shortleaveDate = $useentity["sh"][0]->Date;
        // if (empty($useentity["sh"])) {

        $dateTime = date('Y/m/d h:i:s', time());
        $useentity["sh"] = $this->Db_model->getfilteredData("SELECT * FROM tbl_shortlive WHERE `EmpNo` = '$Emp' AND `Month`='$month'");

        if (!empty($useentity["sh"])) {
            // echo "thiywa2";
            foreach ($useentity["sh"] as $data) {
                // echo json_encode($data);
                // echo "1";
                // thiynwanam
                if ($data != null) {
                    $shortleaveDate = $data->Date;
                    $ID = $data->ID;
                    $MonthData = $data->Month;

                    $this->session->set_flashdata('error_message', 'Already Have a Short Leave');
                    redirect('Leave_Transaction/Short_Leave_Request/index');
                 
                } else {

                    $data = array(
                        'EmpNo' => $Emp,
                        'from_time' => $from_time,
                        'to_time' => $to_time,
                        'Date' => $date1,
                        'Month' => $month,
                        'used' => 1,
                        // 'balance' => $leaveentity[0]->NosLeaveForMonth - 1,
                        'balance' => '0',
                        'Apply_Date' => $dateTime,
                        'Is_pending' => '1',
                        'Is_Approve' => '0',
                    );
                    $this->Db_model->insertData('tbl_shortlive', $data);
                    $this->session->set_flashdata('success_message', 'Employee Short Leave Added');
                    redirect('Leave_Transaction/Short_Leave_Request/index');
                }
            }
        } else {
            // echo "nee2";
            // echo "<br>";
            $data = array(
                'EmpNo' => $Emp,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'Date' => $date1,
                'Month' => $month,
                'used' => 1,
                // 'balance' => $leaveentity[0]->NosLeaveForMonth - 1,
                'balance' => '0',
                'Apply_Date' => $dateTime,
                'Is_pending' => '1',
                'Is_Approve' => '0',
            );
            $this->Db_model->insertData('tbl_shortlive', $data);
            $this->session->set_flashdata('success_message', 'Employee Short Leave Added');
            redirect('Leave_Transaction/Short_Leave_Request/index');

        }



    }
}
