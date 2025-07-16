<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Paysheet extends CI_Controller
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

        $data['title'] = "Pay Sheet | HRM System";
        $data['data_dep'] = $this->Db_model->getData('Dep_ID,Dep_Name', 'tbl_departments');
        $data['data_desig'] = $this->Db_model->getData('Des_ID,Desig_Name', 'tbl_designations');
        $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
        $data['data_branch'] = $this->Db_model->getData('B_id,B_name', 'tbl_branches');
        $data['data_ass_c'] = $this->Db_model->getData('ass_dep_id,ass_dep_name', 'tbl_assigned_department');
        $this->load->view('Reports/Payroll/Paysheet_report', $data);
    }

    /*
     * Insert Departmrnt
     */

    public function Report_department()
    {

        $Data['data_set'] = $this->Db_model->getData('id,Dep_Name', 'tbl_departments');

        $this->load->view('Reports/Master/rpt_Departments', $Data);
    }

    public function Pay_sheet_Report_By_Cat()
    {


        $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
        date_default_timezone_set('Asia/Colombo');
        $year = date("Y");

        $emp = $this->input->post("txt_emp");
        $emp_name = $this->input->post("txt_emp_name");
        $desig = $this->input->post("cmb_desig");
        $year1 = $this->input->post("cmb_year");
        $Month = $this->input->post("cmb_month");
        $to_date = $this->input->post("txt_to_date");
        $branch = $this->input->post("cmb_branch");
        $departments = $this->input->post("cmb_departments");
        $assDepartments = $this->input->post("cmb_ass_departments");
        


        //        $data['f_date'] = $from_date;
//        $data['t_date'] = $to_date;
        // Filter Data by categories
        $filter = '';

        //        if (($this->input->post("cmb_branch"))) {
//            if ($filter == null) {
//                $filter = " where tbl_branches.B_id  ='$branch' and  tbl_salary.Month = '$Month' and tbl_salary.Year ='$year1'";
//            } else {
//                $filter .= " where tbl_branches.B_id  ='$branch' and  tbl_salary.Month = '$Month' and tbl_salary.Year ='$year1'";
//            }
//        }

        if (($this->input->post("cmb_year"))) {
            if ($filter == null) {
                $filter = " where tbl_salary.Month = '$Month' and tbl_salary.Year ='$year1' and tbl_empmaster.status = 1";
            } else {
                $filter .= " where tbl_salary.Month = '$Month' and tbl_salary.Year ='$year1' and tbl_empmaster.status = 1";
            }
        }

        if (($this->input->post("cmb_departments"))) {
            if ($filter == null) {
                $filter = " where tbl_departments.Dep_ID = '$departments'";
            } else {
                $filter .= " AND tbl_departments.Dep_ID = '$departments'";
            }
        }
        
        if (($this->input->post("cmb_ass_departments"))) {
            if ($filter == null) {
                $filter = " where tbl_assigned_department.ass_dep_id = '$assDepartments'";
            } else {
                $filter .= " AND tbl_assigned_department.ass_dep_id = '$assDepartments'";
            }
        }



        $data['data_set'] = $this->Db_model->getfilteredData("SELECT 
                                                                    tbl_salary.id,
                                                                    tbl_salary.EmpNo,
                                                                    tbl_branches.B_name,
                                                                    tbl_empmaster.Emp_Full_Name,
                                                                    (tbl_salary.Incentive) as Incentive,
                                                                    tbl_salary.Late_deduction,
                                                                    tbl_empmaster.Fixed_Allowance,
                                                                    tbl_salary.EPFNO,
                                                                    tbl_salary.Ed_deduction,
                                                                    tbl_salary.Festivel_Advance,
                                                                    tbl_salary.Br_pay,
                                                                    tbl_salary.Fixed,
                                                                    tbl_salary.Performance,
                                                                    tbl_salary.Attendances,
                                                                    tbl_salary.Fuel,
                                                                    tbl_salary.Transport,
                                                                    tbl_salary.Traveling,
                                                                    tbl_salary.SPAllowance,
                                                                    tbl_salary.Increment,
                                                                    tbl_salary.Other_OT,
                                                                    tbl_salary.Total_F_Epf,
                                                                    tbl_salary.Month,
                                                                    tbl_salary.Year,
                                                                    tbl_salary.Basic_sal,
                                                                    tbl_salary.Payee_amount,
                                                                    tbl_salary.Basic_pay,
                                                                    tbl_salary.Late_deduction,
                                                                    tbl_salary.Late_min,
                                                                    tbl_departments.Dep_Name,
                                                                    tbl_salary.No_Pay_days,
                                                                    tbl_salary.no_pay_deduction,
                                                                    tbl_salary.Normal_OT_Hrs,
                                                                    tbl_salary.Normal_OT_Pay,
                                                                    tbl_salary.Double_OT_Hrs,
                                                                    tbl_salary.Double_OT_Pay,
                                                                    tbl_salary.EPF_Worker_Rate,
                                                                    tbl_salary.EPF_Worker_Amount,
                                                                    tbl_salary.EPF_Employee_Rate,
                                                                    tbl_salary.EPF_Employee_Amount,
                                                                    tbl_salary.ETF_Rate,
                                                                    tbl_salary.ETF_Amount,
                                                                    tbl_salary.Loan_Instalment,
                                                                    tbl_salary.Wellfare,
                                                                    tbl_salary.Stamp_duty,
                                                                    tbl_salary.Uniform,
                                                                    tbl_salary.Gross_sal,
                                                                    tbl_salary.Gross_pay,
                                                                    tbl_salary.Salary_advance,
                                                                    tbl_salary.tot_deduction,
                                                                    tbl_salary.days_worked,
                                                                    tbl_salary.Mobile_Ded,
                                                                    tbl_salary.Fuel_Ded,
                                                                    tbl_salary.Deduct_1,
                                                                    (Allowance_1+Allowance_2+Allowance_3) as Allowances,
                                                                    
                                                                    (Deduct_1+Deduct_2+Deduct_3) as Deductions,
                                                                    tbl_salary.D_Salary,
                                                                    tbl_salary.Net_salary,
                                                                    tbl_assigned_department.ass_dep_name,
                                                                    tbl_departments.Dep_Name,
                                                                    tbl_assigned_department.ass_dep_id
                                                                FROM
                                                                    tbl_salary
                                                                    INNER JOIN
                                                                    tbl_empmaster on tbl_empmaster.EmpNo = tbl_salary.EmpNo
                                                                        INNER JOIN
                                                                    tbl_departments ON tbl_departments.Dep_ID = tbl_empmaster.Dep_ID
                                                                        LEFT JOIN
                                                                    tbl_allowance_type ON tbl_allowance_type.Alw_ID = tbl_salary.Alw_ID_1
                                                                    INNER JOIN
                                                                    tbl_branches on tbl_branches.B_id = tbl_empmaster.B_id
                                                                    INNER JOIN 
                                                                    tbl_assigned_department ON tbl_assigned_department.ass_dep_id = tbl_empmaster.assigned_department_id {$filter} order by tbl_departments.Dep_Name ASC");



        $data['data_month'] = $Month;
        $data['data_year'] = $year1;

        //        var_dump($data);die;
        // echo json_encode();

        $this->load->view('Reports/Payroll/rpt_paysheet', $data);
        // $this->load->view('Reports/Payroll/rpt_paysheet_copy', $data);
// echo $this->db->last_query();

    }
    
//     public function Pay_sheet_Report_By_Cat_excel()
//     {


//         $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
//         date_default_timezone_set('Asia/Colombo');
//         $year = date("Y");

//         $emp = $this->input->post("txt_emp");
//         $emp_name = $this->input->post("txt_emp_name");
//         $desig = $this->input->post("cmb_desig");
//         $year1 = $this->input->post("cmb_year");
//         $Month = $this->input->post("cmb_month");
//         $to_date = $this->input->post("txt_to_date");
//         $branch = $this->input->post("cmb_branch");
//         $departments = $this->input->post("cmb_departments");
//         $assDepartments = $this->input->post("cmb_ass_departments");
        


//         //        $data['f_date'] = $from_date;
// //        $data['t_date'] = $to_date;
//         // Filter Data by categories
//         $filter = '';

//         //        if (($this->input->post("cmb_branch"))) {
// //            if ($filter == null) {
// //                $filter = " where tbl_branches.B_id  ='$branch' and  tbl_salary.Month = '$Month' and tbl_salary.Year ='$year1'";
// //            } else {
// //                $filter .= " where tbl_branches.B_id  ='$branch' and  tbl_salary.Month = '$Month' and tbl_salary.Year ='$year1'";
// //            }
// //        }

//         if (($this->input->post("cmb_year"))) {
//             if ($filter == null) {
//                 $filter = " where tbl_salary.Month = '$Month' and tbl_salary.Year ='$year1' and tbl_empmaster.status = 1";
//             } else {
//                 $filter .= " where tbl_salary.Month = '$Month' and tbl_salary.Year ='$year1' and tbl_empmaster.status = 1";
//             }
//         }

//         if (($this->input->post("cmb_departments"))) {
//             if ($filter == null) {
//                 $filter = " where tbl_departments.Dep_ID = '$departments'";
//             } else {
//                 $filter .= " AND tbl_departments.Dep_ID = '$departments'";
//             }
//         }
        
//         if (($this->input->post("cmb_ass_departments"))) {
//             if ($filter == null) {
//                 $filter = " where tbl_assigned_department.ass_dep_id = '$assDepartments'";
//             } else {
//                 $filter .= " AND tbl_assigned_department.ass_dep_id = '$assDepartments'";
//             }
//         }



//         $data['data_set'] = $this->Db_model->getfilteredData("SELECT 
//                                                                     tbl_salary.id,
//                                                                     tbl_salary.EmpNo,
//                                                                     tbl_branches.B_name,
//                                                                     tbl_empmaster.Emp_Full_Name,
//                                                                     (tbl_salary.Incentive) as Incentive,
//                                                                     tbl_salary.Late_deduction,
//                                                                     tbl_empmaster.Fixed_Allowance,
//                                                                     tbl_salary.EPFNO,
//                                                                     tbl_salary.Ed_deduction,
//                                                                     tbl_salary.Festivel_Advance,
//                                                                     tbl_salary.Br_pay,
//                                                                     tbl_salary.Fixed,
//                                                                     tbl_salary.Performance,
//                                                                     tbl_salary.Attendances,
//                                                                     tbl_salary.Fuel,
//                                                                     tbl_salary.Transport,
//                                                                     tbl_salary.Traveling,
//                                                                     tbl_salary.SPAllowance,
//                                                                     tbl_salary.Increment,
//                                                                     tbl_salary.Other_OT,
//                                                                     tbl_salary.Total_F_Epf,
//                                                                     tbl_salary.Month,
//                                                                     tbl_salary.Year,
//                                                                     tbl_salary.Basic_sal,
//                                                                     tbl_salary.Payee_amount,
//                                                                     tbl_salary.Basic_pay,
//                                                                     tbl_salary.Late_deduction,
//                                                                     tbl_salary.Late_min,
//                                                                     tbl_departments.Dep_Name,
//                                                                     tbl_salary.No_Pay_days,
//                                                                     tbl_salary.no_pay_deduction,
//                                                                     tbl_salary.Normal_OT_Hrs,
//                                                                     tbl_salary.Normal_OT_Pay,
//                                                                     tbl_salary.Double_OT_Hrs,
//                                                                     tbl_salary.Double_OT_Pay,
//                                                                     tbl_salary.EPF_Worker_Rate,
//                                                                     tbl_salary.EPF_Worker_Amount,
//                                                                     tbl_salary.EPF_Employee_Rate,
//                                                                     tbl_salary.EPF_Employee_Amount,
//                                                                     tbl_salary.ETF_Rate,
//                                                                     tbl_salary.ETF_Amount,
//                                                                     tbl_salary.Loan_Instalment,
//                                                                     tbl_salary.Wellfare,
//                                                                     tbl_salary.Stamp_duty,
//                                                                     tbl_salary.Uniform,
//                                                                     tbl_salary.Gross_sal,
//                                                                     tbl_salary.Gross_pay,
//                                                                     tbl_salary.Salary_advance,
//                                                                     tbl_salary.tot_deduction,
//                                                                     tbl_salary.days_worked,
//                                                                     tbl_salary.Mobile_Ded,
//                                                                     tbl_salary.Fuel_Ded,
//                                                                     tbl_salary.Deduct_1,
//                                                                     (Allowance_1+Allowance_2+Allowance_3) as Allowances,
                                                                    
//                                                                     (Deduct_1+Deduct_2+Deduct_3) as Deductions,
//                                                                     tbl_salary.D_Salary,
//                                                                     tbl_salary.Net_salary,
//                                                                     tbl_assigned_department.ass_dep_name,
//                                                                     tbl_departments.Dep_Name,
//                                                                     tbl_assigned_department.ass_dep_id
//                                                                 FROM
//                                                                     tbl_salary
//                                                                     INNER JOIN
//                                                                     tbl_empmaster on tbl_empmaster.EmpNo = tbl_salary.EmpNo
//                                                                         INNER JOIN
//                                                                     tbl_departments ON tbl_departments.Dep_ID = tbl_empmaster.Dep_ID
//                                                                         LEFT JOIN
//                                                                     tbl_allowance_type ON tbl_allowance_type.Alw_ID = tbl_salary.Alw_ID_1
//                                                                     INNER JOIN
//                                                                     tbl_branches on tbl_branches.B_id = tbl_empmaster.B_id
//                                                                     INNER JOIN 
//                                                                     tbl_assigned_department ON tbl_assigned_department.ass_dep_id = tbl_empmaster.assigned_department_id {$filter} order by tbl_departments.Dep_Name ASC");



//         $data['data_month'] = $Month;
//         $data['data_year'] = $year1;

//         //        var_dump($data);die;
//         echo json_encode();

//         // $this->load->view('Reports/Payroll/rpt_paysheet', $data);
//         // $this->load->view('Reports/Payroll/rpt_paysheet_copy', $data);
//         // echo $this->db->last_query();

//     }

public function Pay_sheet_Report_By_Cat_excel()
{
    $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
    
    $emp = $this->input->post("txt_emp");
    $emp_name = $this->input->post("txt_emp_name");
    $desig = $this->input->post("cmb_desig");
    $year = $this->input->post("cmb_year");
    $month = $this->input->post("cmb_month");
    $branch = $this->input->post("cmb_branch");
    $departments = $this->input->post("cmb_departments");
    $assDepartments = $this->input->post("cmb_ass_departments");
    
    $data['year'] = $year;
    $data['month'] = $month;
    
    // Filter Data by categories
    $filter = '';
    if ($year) {
        $filter = " WHERE tbl_salary.Month = '$month' AND tbl_salary.Year = '$year' AND tbl_empmaster.status = 1";
    }
    
    if ($departments) {
        $filter .= $filter ? " AND tbl_departments.Dep_ID = '$departments'" : " WHERE tbl_departments.Dep_ID = '$departments'";
    }
    
    if ($assDepartments) {
        $filter .= $filter ? " AND tbl_assigned_department.ass_dep_id = '$assDepartments'" : " WHERE tbl_assigned_department.ass_dep_id = '$assDepartments'";
    }
    
    if ($branch) {
        $filter .= $filter ? " AND tbl_branches.B_id = '$branch'" : " WHERE tbl_branches.B_id = '$branch'";
    }
    
    $data['data_set'] = $this->Db_model->getfilteredData("SELECT 
        tbl_salary.id,
        tbl_salary.EmpNo,
        tbl_branches.B_name,
        tbl_empmaster.Emp_Full_Name,
        tbl_salary.Incentive,
        tbl_salary.Late_deduction,
        tbl_empmaster.Fixed_Allowance,
        tbl_salary.EPFNO,
        tbl_salary.Ed_deduction,
        tbl_salary.Festivel_Advance,
        tbl_salary.Br_pay,
        tbl_salary.Fixed,
        tbl_salary.Performance,
        tbl_salary.Attendances,
        tbl_salary.Fuel,
        tbl_salary.Transport,
        tbl_salary.Traveling,
        tbl_salary.SPAllowance,
        tbl_salary.Increment,
        tbl_salary.Other_OT,
        tbl_salary.Total_F_Epf,
        tbl_salary.Month,
        tbl_salary.Year,
        tbl_salary.Basic_sal,
        tbl_salary.Payee_amount,
        tbl_salary.Basic_pay,
        tbl_salary.Late_deduction,
        tbl_salary.Late_min,
        tbl_departments.Dep_Name,
        tbl_salary.No_Pay_days,
        tbl_salary.no_pay_deduction,
        tbl_salary.Normal_OT_Hrs,
        tbl_salary.Normal_OT_Pay,
        tbl_salary.Double_OT_Hrs,
        tbl_salary.Double_OT_Pay,
        tbl_salary.EPF_Worker_Rate,
        tbl_salary.EPF_Worker_Amount,
        tbl_salary.EPF_Employee_Rate,
        tbl_salary.EPF_Employee_Amount,
        tbl_salary.ETF_Rate,
        tbl_salary.ETF_Amount,
        tbl_salary.Loan_Instalment,
        tbl_salary.Wellfare,
        tbl_salary.Stamp_duty,
        tbl_salary.Uniform,
        tbl_salary.Gross_sal,
        tbl_salary.Gross_pay,
        tbl_salary.Salary_advance,
        tbl_salary.tot_deduction,
        tbl_salary.days_worked,
        tbl_salary.Mobile_Ded,
        tbl_salary.Fuel_Ded,
        tbl_salary.Deduct_1,
        (Allowance_1+Allowance_2+Allowance_3) AS Allowances,
        (Deduct_1+Deduct_2+Deduct_3) AS Deductions,
        tbl_salary.D_Salary,
        tbl_salary.Net_salary,
        tbl_assigned_department.ass_dep_name,
        tbl_departments.Dep_Name,
        tbl_assigned_department.ass_dep_id
        FROM tbl_salary
        INNER JOIN tbl_empmaster ON tbl_empmaster.EmpNo = tbl_salary.EmpNo
        INNER JOIN tbl_departments ON tbl_departments.Dep_ID = tbl_empmaster.Dep_ID
        LEFT JOIN tbl_allowance_type ON tbl_allowance_type.Alw_ID = tbl_salary.Alw_ID_1
        INNER JOIN tbl_branches ON tbl_branches.B_id = tbl_empmaster.B_id
        INNER JOIN tbl_assigned_department ON tbl_assigned_department.ass_dep_id = tbl_empmaster.assigned_department_id
        {$filter} ORDER BY tbl_departments.Dep_Name ASC");
    
    // Load the HTML content with the table
    $html = $this->load->view('Reports/Payroll/rpt_paysheet_copy', $data, true);
    
    // Return the HTML response
    echo json_encode(['html' => $html]);
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
