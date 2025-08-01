<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_Manual_Entry extends CI_Controller {

    public function __construct() {
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

    public function index() {

        $data['title'] = "Attendance Manual Entry | HRM System";
        $data['data_set'] = $this->Db_model->getData('EmpNo,Emp_Full_Name', 'tbl_empmaster');
        $data['data_dep'] = $this->Db_model->getData('Dep_ID,Dep_Name', 'tbl_departments');
        $data['data_desig'] = $this->Db_model->getData('Des_ID,Desig_Name', 'tbl_designations');
        $data['data_grp'] = $this->Db_model->getData('Grp_ID,EmpGroupName', 'tbl_emp_group');
        $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
        $data['data_set_att'] = $this->Db_model->getfilteredData("SELECT `M_ID`,`EmpNo`,`Emp_Full_Name`,`Att_Date`,`In_Time`,`tbl_manual_entry`.`Status`,`Reason` from tbl_manual_entry inner join tbl_empmaster on tbl_empmaster.EmpNo = tbl_manual_entry.Enroll_No WHERE Is_Admin_App_ID=1 order by M_ID desc");


        $this->load->view('Attendance/Attendance_Manual_Entry/index', $data);
    }

    public function dropdown() {

        $cat = $this->input->post('cmb_cat');

        if ($cat == "Employee") {
            $query = $this->Db_model->get_dropdown();
            echo '<option value="" default>-- Select --</option>';
            foreach ($query->result() as $row) {

                echo "<option value='" . $row->EmpNo . "'>" . $row->Emp_Full_Name . "</option>";
            }
        }

        if ($cat == "Department") {
            $query = $this->Db_model->get_dropdown_dep();
            echo '<option value="" default>-- Select --</option>';
            foreach ($query->result() as $row) {
                echo "<option value='" . $row->Dep_ID . "'>" . $row->Dep_Name . "</option>";
            }
        }
        if ($cat == "Designation") {
            $query = $this->Db_model->get_dropdown_des();
            echo '<option value="" default>-- Select --</option>';
            foreach ($query->result() as $row) {
                echo "<option value='" . $row->Des_ID . "'>" . $row->Desig_Name . "</option>";
            }
        }
        if ($cat == "Employee_Group") {
            $query = $this->Db_model->get_dropdown_group();
            echo '<option value="" default>-- Select --</option>';
            foreach ($query->result() as $row) {
                echo "<option value='" . $row->Grp_ID . "'>" . $row->EmpGroupName . "</option>";
            }
        }

        if ($cat == "Company") {
            $query = $this->Db_model->get_dropdown_comp();
            echo '<option value="" default>-- Select --</option>';
            foreach ($query->result() as $row) {
                echo "<option value='" . $row->Cmp_ID . "'>" . $row->Company_Name . "</option>";
            }
        }
    }

    /*
     * Search Employee Manual Attendance Entry
     */

    public function emp_manual_entry() {


        $emp = $this->input->post("txt_emp");
        $emp_name = $this->input->post("txt_emp_name");
        $desig = $this->input->post("cmb_desig");
        $dept = $this->input->post("cmb_dep");
        $comp = $this->input->post("cmb_comp");

        $att_date = $this->input->post("att_date");
        $in_time = $this->input->post("in_time");
        // $out_time = $this->input->post("out_time");
        $out_time = "00:00:00";
        $reason = $this->input->post("txt_reason");
        $satus = $this->input->post('employee_status');
        $App_Sup_User = 1;
        $Is_App_Sup_User = 1;

        if($satus== 'Active'){
            $st = "0";
        }
        // else{
        //     $st = "1";
        // }
        if($satus== 'Inactive'){
            $st = "1";
        }
        $EmpData = $this->Db_model->getfilteredData("select EmpNo,Enroll_No from tbl_empmaster where EmpNo ='$emp' or Emp_Full_Name='$emp_name' ");

        $EnrollNo = $EmpData[0]->Enroll_No;

        $data = array(
            'Att_Date' => $att_date,
            'In_Time' => $in_time,
            'Out_Time' => $out_time,
            'Enroll_No' => $EnrollNo,
            'Reason' => $reason,
            'Status' => $st,
            'App_Sup_User' => 1,
            'Is_App_Sup_User' => 1,
            'App_Sup_User' => 1,
            'Is_App_Sup_User' => 1
        );

        $this->Db_model->insertData('tbl_manual_entry', $data);


        $originalDate = $att_date.' '.$in_time;
        $date = DateTime::createFromFormat('Y/m/d H:i', $originalDate);
        $formattedDate = $date->format('Y-m-d H:i:s');

        $data = array(
            'AttDate' => $att_date,
            'AttTime' => $in_time,
            'AttDateTimeStr' => $formattedDate,
            'Enroll_No' => $EnrollNo,
            'AttPlace' => "null",
            'Status' => $st,
            'verify_type' => "0",
            'EventName' => "null",
        );
        $this->Db_model->insertData('tbl_u_attendancedata', $data);


        $this->session->set_flashdata('success_message', 'Manual Entry added successfully');

        redirect(base_url() . "Attendance/Attendance_Manual_Entry");
    }

}
