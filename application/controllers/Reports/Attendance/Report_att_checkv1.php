<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Report_att_check extends CI_Controller
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
        $this->load->library("pdf_library");
        $this->load->model('Db_model', '', TRUE);
    }

    /*
     * Index page in Departmrnt
     */

    public function index()
    {
        // echo "ok";
        $data['title'] = "Attendance In Out Report Summery | HRM System";
        $data['data_dep'] = $this->Db_model->getData('Dep_ID,Dep_Name', 'tbl_departments');
        $data['data_desig'] = $this->Db_model->getData('Des_ID,Desig_Name', 'tbl_designations');
        $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
        $data['data_branch'] = $this->Db_model->getData('B_id,B_name', 'tbl_branches');
        $this->load->view('Reports/Attendance/Report_att_check', $data);
    }

    /*
     * Insert Departmrnt
     */

    public function Report_department()
    {

        $Data['data_set'] = $this->Db_model->getData('id,Dep_Name', 'tbl_departments');

        $this->load->view('Reports/Master/rpt_Departments', $Data);
    }

    public function Attendance_Report_By_Cat()
    {
        $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');

        $emp = $this->input->post("txt_emp");
        $emp_name = $this->input->post("txt_emp_name");
        $desig = $this->input->post("cmb_desig");
        $dept = $this->input->post("cmb_dep");
        $from_date = $this->input->post("txt_from_date");
        $to_date = $this->input->post("txt_to_date");
        $branch = $this->input->post("cmb_branch");

        $data['f_date'] = $from_date;
        $data['t_date'] = $to_date;

        $filter = '';

        if (($this->input->post("txt_from_date")) && ($this->input->post("txt_to_date"))) {
            if ($filter == '') {
                $filter = " where  ir.FDate between '$from_date' and '$to_date'";
            } else {
                $filter .= " AND  ir.FDate between '$from_date' and '$to_date'";
            }
        }
        if (($this->input->post("txt_emp"))) {
            if ($filter == null) {
                $filter = " where ir.EmpNo =$emp";
            } else {
                $filter .= " AND ir.EmpNo =$emp";
            }
        }

        if (($this->input->post("txt_emp_name"))) {
            if ($filter == null) {
                $filter = " where Emp.Emp_Full_Name ='$emp_name'";
            } else {
                $filter .= " AND Emp.Emp_Full_Name ='$emp_name'";
            }
        }
        if (($this->input->post("cmb_desig"))) {
            if ($filter == null) {
                $filter = " where dsg.Des_ID  ='$desig'";
            } else {
                $filter .= " AND dsg.Des_ID  ='$desig'";
            }
        }
        if (($this->input->post("cmb_dep"))) {
            if ($filter == null) {
                $filter = " where dep.Dep_id  ='$dept'";
            } else {
                $filter .= " AND dep.Dep_id  ='$dept'";
            }
        }

        if (($this->input->post("cmb_branch"))) {
            if ($filter == null) {
                $filter = " where br.B_id  ='$branch'";
            } else {
                $filter .= " AND br.B_id  ='$branch'";
            }
        }

        $data['employee_list'] = $this->Db_model->getfilteredData("
            SELECT DISTINCT ir.EmpNo, Emp.Emp_Full_Name
            FROM tbl_individual_roster ir
            LEFT JOIN tbl_empmaster Emp ON Emp.EmpNo = ir.EmpNo
            LEFT JOIN tbl_designations dsg ON dsg.Des_ID = Emp.Des_ID
            LEFT JOIN tbl_departments dep ON dep.Dep_id = Emp.Dep_id
            INNER JOIN tbl_branches br ON Emp.B_id = br.B_id
            {$filter} AND Emp.EmpNo != '00009000'
            ORDER BY Emp.Emp_Full_Name;
        ");

        $attendanceData = [];
        foreach ($data['employee_list'] as $employee) {
            $empNo = $employee->EmpNo;
            $empName = $employee->Emp_Full_Name;

            $employeeAttendance = [];
            for ($day = 1; $day <= 31; $day++) {
                $attendance = $this->Db_model->getfilteredData("
            SELECT 
                ir.InDate,
                ir.OutDate,
                ir.ShiftDay,
                ir.ShType,
                ir.FTime, -- Shift In
                ir.TTime, -- Shift Out
                ir.InTime,
                ir.OutTime,
                ir.DayStatus,
                ir.LateM,
                ir.ApprovedExH,
                ir.EarlyDepMin,
                ir.DayStatus
            FROM tbl_individual_roster ir
            WHERE ir.EmpNo = '$empNo'
            AND (DAY(ir.InDate) = '$day' OR DAY(ir.OutDate) = '$day')
            AND ir.InDate BETWEEN '$from_date' AND '$to_date'
        ");

                if (!empty($attendance)) {
                    foreach ($attendance as $record) {
                        $inDay = date('j', strtotime($record->InDate));
                        $outDay = date('j', strtotime($record->OutDate));

                        // Assign Shift In (FTime) to the correct day
                        if (!isset($employeeAttendance[$inDay])) {
                            $employeeAttendance[$inDay] = new stdClass();
                        }
                        $employeeAttendance[$inDay]->FTime = $record->FTime;
                        $employeeAttendance[$inDay]->TTime = $record->TTime;

                        // Assign Shift Out (TTime) to the correct day
                        if (!isset($employeeAttendance[$outDay])) {
                            $employeeAttendance[$outDay] = new stdClass();
                        }


                        $employeeAttendance[$outDay]->TTime = $record->TTime;

                        // Assign InTime to the correct day
                        $employeeAttendance[$inDay]->InTime = $record->InTime;

                        // Assign OutTime to the correct day
                        $employeeAttendance[$outDay]->OutTime = $record->OutTime;
                    }
                }
            }

            $attendanceData[] = [
                'EmpNo' => $empNo,
                'Emp_Full_Name' => $empName,
                'Attendance' => $employeeAttendance
            ];
        }

        $data['attendance_data'] = $attendanceData;


        // Load the report view
        $this->load->view('Reports/Attendance/rpt_check', $data);
    }




    function get_auto_emp_name()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->Db_model->get_auto_emp_name($q);
        }
    }

    function get_auto_emp_no()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->Db_model->get_auto_emp_no($q);
        }
    }
}
