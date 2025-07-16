<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class Report_Bank_Sheet extends CI_Controller
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

        $data['title'] = "Bank Sheet | HRM System";
        $data['data_dep'] = $this->Db_model->getData('Dep_ID,Dep_Name', 'tbl_departments');
        $data['data_desig'] = $this->Db_model->getData('Des_ID,Desig_Name', 'tbl_designations');
        $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
        $data['data_branch'] = $this->Db_model->getData('B_id,B_name', 'tbl_branches');
        $data['data_ass_c'] = $this->Db_model->getData('ass_dep_id,ass_dep_name', 'tbl_assigned_department');

        $this->load->view('Reports/Bank_Sheet/index', $data);
    }

    /*
     * Insert Departmrnt
     */

    public function Report_department()
    {

        $Data['data_set'] = $this->Db_model->getData('id,Dep_Name', 'tbl_departments');

        $this->load->view('Reports/Master/rpt_Departments', $Data);
    }

    public function Bank_Sheet_Report_By_Cat1()
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


        // Filter Data by categories
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



        $data['data_set'] = $this->Db_model->getfilteredData("SELECT 
                                                                    ir.EmpNo,
                                                                    Emp.Emp_Full_Name,
                                                                    ir.InDate,
                                                                    ir.OutDate,
                                                                    ir.ShiftDay,
                                                                    ir.ShType,
                                                                    ir.FTime,
                                                                    ir.TTime,
                                                                    ir.InTime,
                                                                    ir.OutTime,
                                                                    ir.DayStatus,
                                                                    ir.ApprovedExH,
                                                                     ir.NetLateM,
                                                                    br.B_name,
                                                                    ir.AfterExH,
                                                                    ir.LateM,
                                                                    ir.DOT,
                                                                    ir.EarlyDepMin
                                                                FROM
                                                                    tbl_individual_roster ir
                                                                        LEFT JOIN
                                                                    tbl_empmaster Emp ON Emp.EmpNo = ir.EmpNo
                                                                        LEFT JOIN
                                                                    tbl_designations dsg ON dsg.Des_ID = Emp.Des_ID
                                                                        LEFT JOIN
                                                                    tbl_departments dep ON dep.Dep_id = Emp.Dep_id
                                                                    inner join
                                                                    tbl_branches br on Emp.B_id = br.B_id

        
                                                                    {$filter} AND Emp.EmpNo != '00009000' GROUP BY ir.FDate , Emp.EmpNo order by Emp.EmpNo,ir.FDate,ir.InTime;");

        //        var_dump($data);die;

        $this->load->view('Reports/Attendance/rpt_In_Out_Sum', $data);
    }

    public function Bank_Sheet_Report_By_Cat()
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
        `tbl_empmaster`.EmpNo,
        `tbl_empmaster`.Emp_Full_Name,
        `tbl_empmaster`.Bnk_ID,
        `tbl_empmaster`.Bnk_Br_ID,
        `tbl_empmaster`.Account_no,
        `tbl_empmaster`.NIC,
        `tbl_empmaster`.Tel_mobile,
        `tbl_empmaster`.E_mail,
        `tbl_salary`.D_Salary
        FROM `tbl_empmaster` 
        INNER JOIN `tbl_salary` 
        ON `tbl_empmaster`.EmpNo = `tbl_salary`.EmpNo 
        INNER JOIN `tbl_departments` 
        ON `tbl_departments`.Dep_ID = `tbl_empmaster`.Dep_ID 
        INNER JOIN `tbl_assigned_department` 
        ON `tbl_assigned_department`.ass_dep_id = `tbl_empmaster`.assigned_department_id
        -- WHERE `tbl_salary`.`Year` = '2025' AND `tbl_salary`.`Month` = '02' 
        {$filter}
        ORDER BY `tbl_empmaster`.EmpNo ASC;");

        // Load the HTML content with the table
        $html = $this->load->view('Reports/Bank_Sheet/Bank_Sheet_Print', $data, true);

        // Return the HTML response
        echo json_encode(['html' => $html]);
    }
    
    public function Bank_Sheet_Report_By_Cat_Excel()
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

        $data_set = $this->Db_model->getfilteredData("SELECT 
        `tbl_empmaster`.EmpNo,
        `tbl_empmaster`.Emp_Full_Name,
        `tbl_empmaster`.Bnk_ID,
        `tbl_empmaster`.Bnk_Br_ID,
        `tbl_empmaster`.Account_no,
        `tbl_empmaster`.NIC,
        `tbl_empmaster`.Tel_mobile,
        `tbl_empmaster`.E_mail,
        `tbl_salary`.D_Salary 
        FROM `tbl_empmaster` 
        INNER JOIN `tbl_salary` 
        ON `tbl_empmaster`.EmpNo = `tbl_salary`.EmpNo 
        INNER JOIN `tbl_departments` 
        ON `tbl_departments`.Dep_ID = `tbl_empmaster`.Dep_ID 
        INNER JOIN `tbl_assigned_department` 
        ON `tbl_assigned_department`.ass_dep_id = `tbl_empmaster`.assigned_department_id
        {$filter}
        ORDER BY `tbl_empmaster`.EmpNo ASC;");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach (range('A', 'K') as $columID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutoSize(true);
        }
        $sheet->setCellValue('A1', 'COMPANY REF');
        $sheet->setCellValue('B1', 'BENEFICIARY NAME');
        $sheet->setCellValue('C1', 'ACCOUNT NO');
        $sheet->setCellValue('D1', 'BANK CODE');
        $sheet->setCellValue('E1', 'BRANCH CODE');
        $sheet->setCellValue('F1', 'AMOUNT');
        $sheet->setCellValue('G1', 'REMARKS');
        $sheet->setCellValue('H1', 'NIC');
        $sheet->setCellValue('I1', 'SENDER MOBILE');
        $sheet->setCellValue('J1', 'RECEIVER MOBILE');
        $sheet->setCellValue('K1', 'BENEFICIARY EMAIL');


        $sheet->getStyle('A1:K1')->getFont()->setBold(true);


        $x = 2;
        $i = 1;
        foreach ($data_set as $row) {
            $styleArray = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ];
            
            $acc_number = strval($row->Account_no);
            
            
            $sheet->setCellValue('A' . $x, 'SAL' . strtoupper(date('F')) . date('Y'));
            $sheet->setCellValue('B' . $x, $row->Emp_Full_Name);
            $sheet->setCellValueExplicit('C' . $x, $acc_number, DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $x, $row->Bnk_ID);
            $sheet->setCellValue('E' . $x, $row->Bnk_Br_ID);
            $sheet->setCellValue('F' . $x, str_pad($row->D_Salary, 12, '0', STR_PAD_LEFT));
            $sheet->setCellValue('G' . $x, 'SAL' . strtoupper(date('F')) . date('Y'));
            $sheet->setCellValueExplicit('H' . $x, $row->NIC, DataType::TYPE_STRING);
            // $sheet->setCellValue('D' . $x, $row->Account_no);
            $sheet->setCellValue('I' . $x, '0');
            $sheet->setCellValue('J' . $x, $row->Tel_mobile);
            $sheet->setCellValue('K' . $x, $row->E_mail);
            
            // Apply right alignment to the entire row
            $sheet->getStyle("A$x:T$x")->applyFromArray($styleArray);
            $x++;
            $i++; 
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Bank_Sheet.xlsx';
        // $writer->save($filename);
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');

        // $html = $this->load->view('Reports/Bank_Sheet/Bank_Sheet_Print', $data, true);

        // Return the HTML response
        // echo json_encode(['html' => $html]);
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
