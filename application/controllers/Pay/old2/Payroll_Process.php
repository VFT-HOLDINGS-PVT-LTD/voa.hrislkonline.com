<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payroll_Process extends CI_Controller
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

        $this->load->helper('url');
        $data['title'] = "Payroll Process | HRM SYSTEM";
        $data['data_emp'] = $this->Db_model->getData('EmpNo,Emp_Full_Name', 'tbl_empmaster');
        $this->load->view('Payroll/Payroll_process/index', $data);
    }

    /*
     * Payroll Process
     */

    public function emp_payroll_process()
    {
        //die;
        date_default_timezone_set('Asia/Colombo');
        $year = date("Y");
        $month = $this->input->post('cmb_month');

        $date = date_create();
        $timestamp = date_format($date, 'Y-m-d H:i:s');


        $dtEmp['EmpData'] = $this->Db_model->getfilteredData("SELECT EmpNo,EMP_ST_ID,Enroll_No,BR1,BR2, EPFNO,Dep_ID,Des_ID,RosterCode, Status  FROM  tbl_empmaster where status=1 and Active_process=1");
        //        $dtEmp['EmpData'] = $this->Db_model->getfilteredData("SELECT EmpNo,Enroll_No, EPFNO,Dep_ID,Des_ID,RosterCode, Status  FROM  tbl_empmaster where EmpNo=3316");
        //For loop for All active employees 
        for ($x = 0; $x < count($dtEmp['EmpData']); $x++) {



            $EmpNo = $dtEmp['EmpData'][$x]->EmpNo;
            $EpfNo = $dtEmp['EmpData'][$x]->EPFNO;
            $Dep_ID = $dtEmp['EmpData'][$x]->Dep_ID;
            $Des_ID = $dtEmp['EmpData'][$x]->Des_ID;
            $br1 = $dtEmp['EmpData'][$x]->BR1;
            $br2 = $dtEmp['EmpData'][$x]->BR2;

            $Emp_ST = $dtEmp['EmpData'][$x]->EMP_ST_ID;


            $HasRow = $this->Db_model->getfilteredData("select count(EmpNo) as HasRow from tbl_salary where EmpNo=$EmpNo and month=$month and year=$year");

            //            var_dump($HasRow);die;
            //IF Salary records have in Salary table update salary records into salary table

            if ($HasRow[0]->HasRow > 0) {

                //            var_dump($dtEmp['EmpData']);die;
                //Get Employee Basic Salary | Incentive
                $SalData = $this->Db_model->getfilteredData("select EmpNo,EPFNO, Is_EPF,Dep_ID, Des_ID,Basic_Salary,Fixed_Allowance,Incentive,is_nopay_calc from tbl_empmaster where EmpNo=$EmpNo");
                $BasicSal = $SalData[0]->Basic_Salary;
                $Incentive = $SalData[0]->Incentive;
                $Fixed_Allowance = $SalData[0]->Fixed_Allowance;
                $Is_EPF = $SalData[0]->Is_EPF;
                $is_no_pay = $SalData[0]->is_nopay_calc;


                //**** Get Nopay days
                $Nopay = $this->Db_model->getfilteredData("select sum(nopay) as nopay, sum(nopay_hrs) nopay_hrs,sum(Att_Allow) as Att_Allow from tbl_individual_roster where EmpNo=$EmpNo and EXTRACT(MONTH FROM FDate)=$month and EXTRACT(YEAR FROM FDate)=$year");
                $NopayDays = $Nopay[0]->nopay;

                // var_dump($NopayDays);


                // if ($NopayDays == 0) {
                //     $NopayDays = 0;
                //     $Att_Allowance = 5000;
                //     //                    die;
                // }



                // $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);


                // var_dump($days . $Nopay[0]->Att_Allow);

                // $Att_Allowance = 0;
                // if ($Nopay[0]->Att_Allow == $days) {
                //     $Att_Allowance = 5000;
                // }


                if ($NopayDays == null) {
                    $NopayDays = 0;
                }



                //**** Calculate no pay amount
                $NopayRate = ($BasicSal + $Incentive) / 30;


                if ($is_no_pay == 1) {
                    $NopayDays = 0;
                }

                $Nopay_Deduction = $NopayRate * $NopayDays;


                //**** Get Allowance Details
                $budget_relevance = $this->Db_model->getfilteredData("select Br_ID, Amount from tbl_varialble_br where EmpNo=$EmpNo and Month=$month and Year=$year");
                $welfair = $this->Db_model->getfilteredData("select welfair_id, Amount from tbl_variable_welfair where EmpNo=$EmpNo and Month=$month and Year=$year");

                // var_dump($Nopay_Deduction . 'no pay days' . $NopayDays);

                //Get Variable Allowances details
                $Allowances = $this->Db_model->getfilteredData("select Alw_ID, Amount from tbl_varialble_allowance where EmpNo=$EmpNo and Month=$month and Year=$year ORDER BY tbl_varialble_allowance.Alw_ID");
                $FixedAllowances = $this->Db_model->getfilteredData("select Alw_ID, Amount from tbl_fixed_allowance where EmpNo=$EmpNo ORDER BY tbl_fixed_allowance.Alw_ID");

                $tbattendencebonus = 0;
                $tbprodinc1 = 0;
                $tbprodinc2 = 0;
                $tbspc1 = 0;
                $tbspc2 = 0;
                $Allowance_1 = 0;
                $Allowance_2 = 0;
                $Allowance_3 = 0;
                $Att_Allowance = 0;
                $welfair_1 = 0;
                if (!empty($welfair)) {
                    $welfair_1 = $welfair[0]->Amount;
                }
                /*
                 * Allowence special types
                 */

                if (!empty($Allowances[6]->Alw_ID)) {
                    if ($Allowances[6]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[6]->Amount;
                    }
                } else if (empty($Allowances[6]->Alw_ID) && !empty($FixedAllowances[6]->Alw_ID)) {
                    if ($FixedAllowances[6]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[6]->Amount;
                    }
                }
                if (!empty($Allowances[5]->Alw_ID)) {
                    if ($Allowances[5]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[5]->Amount;
                    }
                } else if (empty($Allowances[5]->Alw_ID) && !empty($FixedAllowances[5]->Alw_ID)) {
                    if ($FixedAllowances[5]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[5]->Amount;
                    }
                }
                if (!empty($Allowances[4]->Alw_ID)) {
                    if ($Allowances[4]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[4]->Amount;
                    }
                } else if (empty($Allowances[4]->Alw_ID) && !empty($FixedAllowances[4]->Alw_ID)) {
                    if ($FixedAllowances[4]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[4]->Amount;
                    }
                }
                if (!empty($Allowances[3]->Alw_ID)) {
                    if ($Allowances[3]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[3]->Amount;
                    }
                } else if (empty($Allowances[3]->Alw_ID) && !empty($FixedAllowances[3]->Alw_ID)) {
                    if ($FixedAllowances[3]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[3]->Amount;
                    }
                }
                if (!empty($Allowances[2]->Alw_ID)) {
                    if ($Allowances[2]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[2]->Amount;
                    }
                } else if (empty($Allowances[2]->Alw_ID) && !empty($FixedAllowances[2]->Alw_ID)) {
                    if ($FixedAllowances[2]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[2]->Amount;
                    }
                }
                if (!empty($Allowances[1]->Alw_ID)) {
                    if ($Allowances[1]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[1]->Amount;
                    }
                } else if (empty($Allowances[1]->Alw_ID) && !empty($FixedAllowances[1]->Alw_ID)) {
                    if ($FixedAllowances[1]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[1]->Amount;
                    }
                }
                if (!empty($Allowances[0]->Alw_ID)) {
                    if ($Allowances[0]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[0]->Amount;
                    }
                } else if (empty($Allowances[0]->Alw_ID) && !empty($FixedAllowances[0]->Alw_ID)) {
                    if ($FixedAllowances[0]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[0]->Amount;
                    }
                }

                //Get Variable Deductions details
                $Deductions = $this->Db_model->getfilteredData("select Ded_ID,Amount from tbl_variable_deduction where EmpNo=$EmpNo and Month=$month and Year=$year");
                $FixedDeductions = $this->Db_model->getfilteredData("select Deduction_ID,Amount from tbl_fixed_deduction where EmpNo=$EmpNo ORDER BY tbl_fixed_deduction.Deduction_ID");

                $payee = $this->Db_model->getfilteredData("SELECT * FROM tbl_payee");
                $stamp_Duty = $this->Db_model->getfilteredData("select ID, Amount from tbl_variable_stamp where EmpNo=$EmpNo and Month=$month and Year=$year");
                $fixed_stamp_Duty = $this->Db_model->getfilteredData("select ID, Amount from tbl_variable_stamp where EmpNo=$EmpNo and Month='0' ");

                //Get Salary Advance details
                $Sal_Advance = $this->Db_model->getfilteredData("select Amount from tbl_salary_advance where Is_Approve=1 and EmpNo=$EmpNo and month=$month and year = $year");

                $Fest_Advance = $this->Db_model->getfilteredData("SELECT Amount from tbl_festivel_advance where EmpNo=$EmpNo and Month=$month and Year = $year");
                $uniform = 0;
                $other = 0;
                $deduction1 = 0;
                $deduction2 = 0;

                $stamp_Duty1 = 0;
                if (!empty($stamp_Duty)) {
                    $stamp_Duty1 =  $stamp_Duty[0]->Amount;
                } else if (empty($stamp_Duty) && !empty($fixed_stamp_Duty)) {
                    $stamp_Duty1 =  $fixed_stamp_Duty[0]->Amount;
                }

                if (empty($Deductions[3]->Ded_ID) && !empty($FixedDeductions[3]->Deduction_ID)) {
                    if ($FixedDeductions[3]->Deduction_ID == 1) {
                        $uniform = $FixedDeductions[3]->Amount;
                    } else if ($FixedDeductions[3]->Deduction_ID == 2) {
                        $other = $FixedDeductions[3]->Amount;
                    } else if ($FixedDeductions[3]->Deduction_ID == 3) {
                        $deduction1 = $FixedDeductions[3]->Amount;
                    } else if ($FixedDeductions[3]->Deduction_ID == 4) {
                        $deduction2 = $FixedDeductions[3]->Amount;
                    }
                } else if (!empty($Deductions[3]->Ded_ID)) {
                    if ($Deductions[3]->Ded_ID == 1) {
                        $uniform = $Deductions[3]->Amount;
                    } else if ($Deductions[3]->Ded_ID == 2) {
                        $other = $Deductions[3]->Amount;
                    } else if ($Deductions[3]->Ded_ID == 3) {
                        $deduction1 = $Deductions[3]->Amount;
                    } else if ($Deductions[3]->Ded_ID == 4) {
                        $deduction2 = $Deductions[3]->Amount;
                    }
                }
                if (empty($Deductions[2]->Ded_ID) && !empty($FixedDeductions[2]->Deduction_ID)) {
                    if ($FixedDeductions[2]->Deduction_ID == 1) {
                        $uniform = $FixedDeductions[2]->Amount;
                    } else if ($FixedDeductions[2]->Deduction_ID == 2) {
                        $other = $FixedDeductions[2]->Amount;
                    } else if ($FixedDeductions[2]->Deduction_ID == 3) {
                        $deduction1 = $FixedDeductions[2]->Amount;
                    } else if ($FixedDeductions[2]->Deduction_ID == 4) {
                        $deduction2 = $FixedDeductions[2]->Amount;
                    }
                } else if (!empty($Deductions[2]->Ded_ID)) {
                    if ($Deductions[2]->Ded_ID == 1) {
                        $uniform = $Deductions[2]->Amount;
                    } else if ($Deductions[2]->Ded_ID == 2) {
                        $other = $Deductions[2]->Amount;
                    } else if ($Deductions[2]->Ded_ID == 3) {
                        $deduction1 = $Deductions[2]->Amount;
                    } else if ($Deductions[2]->Ded_ID == 4) {
                        $deduction2 = $Deductions[2]->Amount;
                    }
                }
                if (empty($Deductions[1]->Ded_ID) && !empty($FixedDeductions[1]->Deduction_ID)) {
                    if ($FixedDeductions[1]->Deduction_ID == 1) {
                        $uniform = $FixedDeductions[1]->Amount;
                    } else if ($FixedDeductions[1]->Deduction_ID == 2) {
                        $other = $FixedDeductions[1]->Amount;
                    } else if ($FixedDeductions[1]->Deduction_ID == 3) {
                        $deduction1 = $FixedDeductions[1]->Amount;
                    } else if ($FixedDeductions[1]->Deduction_ID == 4) {
                        $deduction2 = $FixedDeductions[1]->Amount;
                    }
                } else if (!empty($Deductions[1]->Ded_ID)) {
                    if ($Deductions[1]->Ded_ID == 1) {
                        $uniform = $Deductions[1]->Amount;
                    } else if ($Deductions[1]->Ded_ID == 2) {
                        $other = $Deductions[1]->Amount;
                    } else if ($Deductions[1]->Ded_ID == 3) {
                        $deduction1 = $Deductions[1]->Amount;
                    } else if ($Deductions[1]->Ded_ID == 4) {
                        $deduction2 = $Deductions[1]->Amount;
                    }
                }
                if (empty($Deductions[0]->Ded_ID) && !empty($FixedDeductions[0]->Deduction_ID)) {
                    if ($FixedDeductions[0]->Deduction_ID == 1) {
                        $uniform = $FixedDeductions[0]->Amount;
                    } else if ($FixedDeductions[0]->Deduction_ID == 2) {
                        $other = $FixedDeductions[0]->Amount;
                    } else if ($FixedDeductions[0]->Deduction_ID == 3) {
                        $deduction1 = $FixedDeductions[0]->Amount;
                    } else if ($FixedDeductions[0]->Deduction_ID == 4) {
                        $deduction2 = $FixedDeductions[0]->Amount;
                    }
                } else if (!empty($Deductions[0]->Ded_ID)) {
                    if ($Deductions[0]->Ded_ID == 1) {
                        $uniform = $Deductions[0]->Amount;
                    } else if ($Deductions[0]->Ded_ID == 2) {
                        $other = $Deductions[0]->Amount;
                    } else if ($Deductions[0]->Ded_ID == 3) {
                        $deduction1 = $Deductions[0]->Amount;
                    } else if ($Deductions[0]->Ded_ID == 4) {
                        $deduction2 = $Deductions[0]->Amount;
                    }
                }

                if ($Fest_Advance == null) {
                    $Festivel_Advance = 0;
                } else {
                    $Festivel_Advance = $Fest_Advance[0]->Amount;
                }

                //Get loan details
                $Loan = $this->Db_model->getfilteredData("select Loan_ID,Loan_amount,Month_Installment,FullAmount,Paid_Amount from tbl_loans where Is_Settled=0 and EmpNo=$EmpNo");



                /*
                 * Loan Details
                 */

                if (empty($Loan[0]->Loan_ID)) {
                    $LoanID = 0;
                } else {
                    $LoanID = $Loan[0]->Loan_ID;
                }


                if (empty($Loan[0]->Month_Installment)) {
                    $LoanMonth = 0;
                } else {
                    $LoanMonth = $Loan[0]->Month_Installment;
                }

                /*
                 * Salary Advance Details
                 */


                if (empty($Sal_Advance[0]->Amount)) {
                    $Sal_advance = 0;
                } else {
                    $Sal_advance = $Sal_Advance[0]->Amount;
                }
                /*
                 * Budget relevances
                 */


                if (empty($br1)) {
                    $budgetrelevance1 = 0;
                } else {
                    $budgetrelevance1 = $br1;
                }



                if (empty($br2)) {
                    $budgetrelevance2 = 0;
                } else {
                    $budgetrelevance2 = $br2;
                }

                /*
                 * Allowance Details
                 */


                if (empty($Allowances[6]->Alw_ID)) {
                    $Allowance_ID_1 = 0;
                } else {
                    $Allowance_ID_1 = $Allowances[0]->Alw_ID;
                }

                // if (empty($Allowances[6]->Amount)) {
                //     $Allowance_1 = 0;
                // } else {
                //     $Allowance_1 = $Allowances[0]->Amount;
                // }

                if (empty($Allowances[7]->Alw_ID)) {
                    $Allowance_ID_2 = 0;
                } else {
                    $Allowance_ID_2 = $Allowances[1]->Alw_ID;
                }

                // if (empty($Allowances[7]->Amount)) {
                //     $Allowance_2 = 0;
                // } else {
                //     $Allowance_2 = $Allowances[1]->Amount;
                // }

                if (empty($Allowances[8]->Alw_ID)) {
                    $Allowance_ID_3 = 0;
                } else {
                    $Allowance_ID_3 = $Allowances[2]->Alw_ID;
                }

                // if (empty($Allowances[8]->Amount)) {
                //     $Allowance_3 = 0;
                // } else {
                //     $Allowance_3 = $Allowances[2]->Amount;
                // }

                if (empty($Allowances[9]->Alw_ID)) {
                    $Allowance_ID_4 = 0;
                } else {
                    $Allowance_ID_4 = $Allowances[3]->Alw_ID;
                }

                // if (empty($Allowances[9]->Amount)) {
                //     $Allowance_4 = 0;
                // } else {
                //     $Allowance_4 = $Allowances[3]->Amount;
                // }

                if (empty($Allowances[10]->Alw_ID)) {
                    $Allowance_ID_5 = 0;
                } else {
                    $Allowance_ID_5 = $Allowances[4]->Alw_ID;
                }

                // if (empty($Allowances[10]->Amount)) {
                //     $Allowance_5 = 0;
                // } else {
                //     $Allowance_5 = $Allowances[4]->Amount;
                // }

                /*
                 * Deduction Details
                 */

                if (empty($Deductions[0]->Ded_ID)) {
                    $Deduction_ID_1 = 0;
                } else {
                    $Deduction_ID_1 = $Deductions[0]->Ded_ID;
                }

                // if (empty($Deductions[0]->Amount)) {
                //     $Deduction_1 = 0;
                // } else {
                //     $Deduction_1 = $Deductions[0]->Amount;
                // }

                if (empty($Deductions[1]->Ded_ID)) {
                    $Deduction_ID_2 = 0;
                } else {
                    $Deduction_ID_2 = $Deductions[1]->Ded_ID;
                }

                // if (empty($Deductions[1]->Amount)) {
                //     $Deduction_2 = 0;
                // } else {
                //     $Deduction_2 = $Deductions[1]->Amount;
                // }

                if (empty($Deductions[2]->Ded_ID)) {
                    $Deduction_ID_3 = 0;
                } else {
                    $Deduction_ID_3 = $Deductions[2]->Ded_ID;
                }

                // if (empty($Deductions[2]->Amount)) {
                //     $Deduction_3 = 0;
                // } else {
                //     $Deduction_3 = $Deductions[2]->Amount;
                // }





                // //Get Overtime details
                // $Overtime = $this->Db_model->getfilteredData("select sum(ApprovedExH) as OT from tbl_individual_roster where EmpNo=$EmpNo and EXTRACT(MONTH FROM FDate)=$month and RYear=$year");
                // // ---------Normal OT Calculation
                // $Overtime = $this->Db_model->getfilteredData("select sum(OT_Min) as N_OT from tbl_ot_d where EmpNo='$EmpNo' and RateCode = 1.5 and EXTRACT(MONTH FROM OTDate)=$month and  EXTRACT(YEAR FROM OTDate) =$year");

                // $N_OT_Hours = $Overtime[0]->N_OT;

                $Overtime_DB = $this->Db_model->getfilteredData("select sum(DOT) as D_OT from tbl_individual_roster where EmpNo='$EmpNo' and EXTRACT(MONTH FROM FDate)=$month and  EXTRACT(YEAR FROM FDate) =$year");
                $Overtime = $this->Db_model->getfilteredData("select sum(AfterExH) as N_OT from tbl_individual_roster where EmpNo='$EmpNo' and EXTRACT(MONTH FROM FDate)=$month and  EXTRACT(YEAR FROM FDate) =$year");

                $N_OT_Hours = $Overtime[0]->N_OT;

                $D_OT_Hours = $Overtime_DB[0]->D_OT;

                $OT_Rate = ((($BasicSal + $Fixed_Allowance) / 240) * 1.5);
                $N_OT_Amount = $OT_Rate * ($N_OT_Hours / 60);

                // var_dump($D_OT_Hours . '_Emp' . $EmpNo);

                $OT_Rate_2 = ((($BasicSal + $Fixed_Allowance) / 240) * 2);
                $D_OT_Amount = $OT_Rate_2 * ($D_OT_Hours / 60);




                //                if (!empty($NopayDays)) {
                //
                //                    $Incentive = $Incentive - (($Incentive / 30) * $NopayDays);
                //                }
                //*** Get Late Minutes
                $Late_Min = $this->Db_model->getfilteredData("select sum(LateM) as LateMin from tbl_individual_roster where EmpNo=$EmpNo and EXTRACT(MONTH FROM FDate)=$month and RYear=$year");

                //*** Late Min
                if (empty($Late_Min[0]->LateMin)) {
                    $Late_Min = 0;
                } else {
                    $Late_Min = $Late_Min[0]->LateMin;
                }

                //240 = 30*8
                $Late_rate = (($BasicSal / 240)) / 60;
                //** Late Amount
                //                $Late_Amount = $Late_rate * $Late_Min;
                //                Kangara Holdigs
                $Late_Amount = 0;


                //---------------------  only kangara holdings
                // if ($BasicSal >= 50000) {

                //     $N_OT_Hours = 0;
                //     $N_OT_Amount = 0;
                //     $D_OT_Hours = 0;
                //     $D_OT_Amount = 0;
                // }


                // if ($Is_EPF == 0) {
                //     $EPF_Worker = 0;
                //     $EPF_Employer = 0;
                //     $ETF = 0;
                // }

                //---------------------  only kangara holdings



                //All budgetrelevances
                $budgetrelevances = $budgetrelevance1 + $budgetrelevance2;
                //All Allowances
                $Allowances = $Allowance_1 + $Allowance_2 + $Allowance_3 ;

                //Calculate Gross salary
                $Gross_sal = ($BasicSal + $Fixed_Allowance + $Incentive + $budgetrelevances);

                /*
                *payee tax calculate start
                */
                $st_gross_Pay = $Gross_sal * 12;

                $free_rate = 100000;
                $anual_freee_rate = $free_rate * 12;
                $payee_now_amount = 0;

                $calculate_gross_pay = $st_gross_Pay - $anual_freee_rate;

                if (0 > $calculate_gross_pay) {
                    $payee_now_amount = 0;
                } else {
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[0]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[0]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[1]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[1]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[2]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[2]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[3]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[3]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[4]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[4]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        $payeeamount = ($calculate_gross_pay / 12) * ($payee[5]->Tax_rate / 100);
                        $calculate_gross_pay -= 500000;
                        $payee_now_amount += $payeeamount;
                    }
                }
                /*
                *payee tax calculate end
                */

                //Calculate EPF Employee
                $EPF_Worker = (8 / 100) * ($BasicSal + $Fixed_Allowance + $budgetrelevances);

                //Total for epf
                $tottal_for_epf = $BasicSal + $Fixed_Allowance + $budgetrelevances;

                //Calculate EPF Employer
                $EPF_Employer = (12 / 100) * ($BasicSal + $Fixed_Allowance + $budgetrelevances);

                //Calculate ETF Employee
                $ETF = (3 / 100) * ($BasicSal + $Fixed_Allowance + $budgetrelevances);

                //Calculate Total Deductions
                $Tot_deductions = $EPF_Worker + $Sal_advance + $Festivel_Advance + $uniform + $other + $deduction1 + $deduction2 + $LoanMonth + $Late_Amount + $Nopay_Deduction;

                //Calculate Net Salary
                $netSal = ($Gross_sal + $N_OT_Amount + $D_OT_Amount + $Att_Allowance + $Allowances) - $Tot_deductions;

                //calculate Gross pay
                $grosspay = $Gross_sal + $N_OT_Amount + $D_OT_Amount + $Att_Allowance + $Allowances;

                $D_Salary = $Gross_sal - $Tot_deductions;







                $data = array(
                    'EmpNo' => $EmpNo,
                    'EPFNO' => $EpfNo,
                    'Basic_sal' => $BasicSal,
                    'Total_F_Epf' => $tottal_for_epf,
                    'Br_pay' => $budgetrelevances,
                    'Incentive' => $Incentive,
                    'Dep_ID' => $Dep_ID,
                    'Des_ID' => $Des_ID,
                    'No_Pay_days' => $NopayDays,
                    'no_pay_deduction' => $Nopay_Deduction,
                    'EPF_Worker_Rate' => 8,
                    'EPF_Worker_Amount' => $EPF_Worker,
                    'EPF_Employee_Rate' => 12,
                    'EPF_Employee_Amount' => $EPF_Employer,
                    'ETF_Rate' => 3,
                    'ETF_Amount' => $ETF,
                    'Loan_Instalment' => $LoanMonth,
                    'Salary_advance' => $Sal_advance,
                    // 'Late_min' => 0,
                    'Late_min' => $Late_Min,
                    // 'Late_deduction' => 0,
                    'Late_deduction' => $Late_Amount,
                    'Alw_ID_1' => $Allowance_ID_1,
                    'Allowance_1' => $Allowance_1,
                    'Alw_ID_2' => $Allowance_ID_2,
                    'Allowance_2' => $Allowance_2,
                    'Alw_ID_3' => $Allowance_ID_3,
                    'Allowance_3' => $Allowance_3,
                    'Alw_ID_4' => $Allowance_ID_4,
                    'Allowance_4' => 0,
                    'Alw_ID_5' => $Allowance_ID_5,
                    'Allowance_5' => 0,
                    'Att_Allowance' => $Att_Allowance,
                    'Fixed' => $tbattendencebonus,
                    'Prod_inc1' => $tbprodinc1,
                    'Prod_inc2' => $tbprodinc2,
                    'Spc_inc1' => $tbspc1,
                    'Spc_inc2' => $tbspc2,
                    'Normal_OT_Hrs' => ($N_OT_Hours / 60),
                    'Normal_OT_Pay' => $N_OT_Amount,
                    'Double_OT_Hrs' => ($D_OT_Hours / 60),
                    'Double_OT_Pay' => $D_OT_Amount,
                    'Uniform' => $uniform,
                    'Ded_ID_1' => $Deduction_ID_1,
                    'Deduct_1' => $other,
                    'Ded_ID_2' => $Deduction_ID_2,
                    'Deduct_2' => $deduction1,
                    'Ded_ID_3' => $Deduction_ID_3,
                    'Deduct_3' => $deduction2,
                    'Wellfare' => $welfair_1,
                    'Payee_amount' => $payee_now_amount,
                    'Stamp_duty' => $stamp_Duty1,
                    'tot_deduction' => $Tot_deductions,
                    'Gross_pay' => $grosspay,
                    'Gross_sal' => $Gross_sal,
                    'D_Salary' => $D_Salary,
                    'Net_salary' => $netSal
                );


                //***** Update Salary Table
                $whereArray = array("EmpNo" => $EmpNo, 'Month' => $month, 'Year' => $year);
                $result = $this->Db_model->updateData("tbl_salary", $data, $whereArray);


                /*
                 * Loan Amount
                 */
                if (empty($Loan[0]->Paid_Amount)) {
                    $PaidAmount = 0;
                } else {
                    $PaidAmount = $Loan[0]->Paid_Amount;
                }

                if (empty($Loan[0]->FullAmount)) {
                    $Full_Amount = 0;
                } else {
                    $Full_Amount = $Loan[0]->FullAmount;
                }





                //****** Loan Amount deduction process
                $PaidAmount_to = $PaidAmount + $LoanMonth;
                $BalanceAmount = $Full_Amount - $PaidAmount_to;

                if ($BalanceAmount <= 0) {
                    $Is_Settele = 1;
                } else {
                    $Is_Settele = 0;
                }


                $data_loan = array(
                    'EmpNo' => $EmpNo,
                    'Paid_Amount' => $PaidAmount_to,
                    'Balance_amount' => $BalanceAmount,
                    'Is_Settled' => $Is_Settele,
                );



                $HasRow = $this->Db_model->getfilteredData("select count(EmpNo) as HasRow from tbl_loan_trans where EmpNo=$EmpNo and month=$month and year=$year");


                if ($LoanMonth == 0) {
                } {
                    $dataArray = array(
                        'Year' => $year,
                        'EmpNo' => $EmpNo,
                        'Month' => $month,
                        'Amount_month' => $LoanMonth,
                        'Loan_ID' => $LoanID,
                        'Time_Trans' => $timestamp,
                    );

                    $this->Db_model->insertData("tbl_loan_trans", $dataArray);
                }




                $HasRow = $this->Db_model->getfilteredData("select count(EmpNo) as HasRow from tbl_loan_trans where EmpNo=$EmpNo and month=$month and year=$year ");

                if ($HasRow[0]->HasRow) {
                } else {
                    $whereArray_loan = array("EmpNo" => $EmpNo);
                    $result = $this->Db_model->updateData("tbl_loans", $data_loan, $whereArray_loan);
                }


                //die;
                //*******Else Salary records haven't in Salary table insert salary records into salary table
            } else {


                $SalData = $this->Db_model->getfilteredData("select EmpNo,EPFNO,Is_EPF, Dep_ID, Des_ID,Basic_Salary,Fixed_Allowance,Incentive,is_nopay_calc from tbl_empmaster where EmpNo=$EmpNo");
                $BasicSal = $SalData[0]->Basic_Salary;
                $Incentive = $SalData[0]->Incentive;
                $Fixed_Allowance = $SalData[0]->Fixed_Allowance;
                $Is_EPF = $SalData[0]->Is_EPF;
                $is_no_pay = $SalData[0]->is_nopay_calc;

                //**** Get Nopay days
                $Nopay = $this->Db_model->getfilteredData("select sum(nopay) as nopay, sum(nopay_hrs) nopay_hrs,sum(Att_Allow) as Att_Allow from tbl_individual_roster where EmpNo=$EmpNo and EXTRACT(MONTH FROM FDate)=$month and EXTRACT(YEAR FROM FDate)=$year");
                $NopayDays = $Nopay[0]->nopay;

                // var_dump($NopayDays);


                // if ($NopayDays == 0) {
                //     $NopayDays = 0;
                //     $Att_Allowance = 5000;
                //     //                    die;
                // }



                $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);


                // var_dump($days . $Nopay[0]->Att_Allow);

                $Att_Allowance = 0;
                // if ($Nopay[0]->Att_Allow == $days) {
                //     $Att_Allowance = 5000;
                // }


                if ($NopayDays == null) {
                    $NopayDays = 0;
                }



                //**** Calculate no pay amount
                $NopayRate = ($BasicSal + $Incentive) / 30;


                if ($is_no_pay == 1) {
                    $NopayDays = 0;
                }

                $Nopay_Deduction = $NopayRate * $NopayDays;




                //**** Get Allowance Details
                // $budget_relevance = $this->Db_model->getfilteredData("select Br_ID, Amount from tbl_varialble_br where EmpNo=$EmpNo and Month=$month and Year=$year");
                $welfair = $this->Db_model->getfilteredData("select welfair_id, Amount from tbl_variable_welfair where EmpNo=$EmpNo and Month=$month and Year=$year");

                //**** Get Allowance Details
                // $Allowancescount = $this->Db_model->getfilteredData("select count(Alw_ID) as HasRow from tbl_varialble_allowance where EmpNo=$EmpNo and Month=$month and Year=$year");
                $Allowances = $this->Db_model->getfilteredData("select Alw_ID, Amount from tbl_varialble_allowance where EmpNo=$EmpNo and Month=$month and Year=$year ORDER BY tbl_varialble_allowance.Alw_ID");
                $FixedAllowances = $this->Db_model->getfilteredData("select Alw_ID, Amount from tbl_fixed_allowance where EmpNo=$EmpNo ORDER BY tbl_fixed_allowance.Alw_ID");


                $tbattendencebonus = 0;
                $tbprodinc1 = 0;
                $tbprodinc2 = 0;
                $tbspc1 = 0;
                $tbspc2 = 0;
                $Allowance_1 = 0;
                $Allowance_2 = 0;
                $Allowance_3 = 0;
                
                $welfair_1 = 0;


                if (!empty($welfair)) {
                    $welfair_1 = $welfair[0]->Amount;
                }

                /*
                 * Allowence special types
                 */

                 if (!empty($Allowances[6]->Alw_ID)) {
                    if ($Allowances[6]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[6]->Amount;
                    } else if ($Allowances[6]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[6]->Amount;
                    }
                } else if (empty($Allowances[6]->Alw_ID) && !empty($FixedAllowances[6]->Alw_ID)) {
                    if ($FixedAllowances[6]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[6]->Amount;
                    } else if ($FixedAllowances[6]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[6]->Amount;
                    }
                }
                if (!empty($Allowances[5]->Alw_ID)) {
                    if ($Allowances[5]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[5]->Amount;
                    } else if ($Allowances[5]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[5]->Amount;
                    }
                } else if (empty($Allowances[5]->Alw_ID) && !empty($FixedAllowances[5]->Alw_ID)) {
                    if ($FixedAllowances[5]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[5]->Amount;
                    } else if ($FixedAllowances[5]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[5]->Amount;
                    }
                }
                if (!empty($Allowances[4]->Alw_ID)) {
                    if ($Allowances[4]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[4]->Amount;
                    } else if ($Allowances[4]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[4]->Amount;
                    }
                } else if (empty($Allowances[4]->Alw_ID) && !empty($FixedAllowances[4]->Alw_ID)) {
                    if ($FixedAllowances[4]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[4]->Amount;
                    } else if ($FixedAllowances[4]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[4]->Amount;
                    }
                }
                if (!empty($Allowances[3]->Alw_ID)) {
                    if ($Allowances[3]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[3]->Amount;
                    } else if ($Allowances[3]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[3]->Amount;
                    }
                } else if (empty($Allowances[3]->Alw_ID) && !empty($FixedAllowances[3]->Alw_ID)) {
                    if ($FixedAllowances[3]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[3]->Amount;
                    } else if ($FixedAllowances[3]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[3]->Amount;
                    }
                }
                if (!empty($Allowances[2]->Alw_ID)) {
                    if ($Allowances[2]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[2]->Amount;
                    } else if ($Allowances[2]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[2]->Amount;
                    }
                } else if (empty($Allowances[2]->Alw_ID) && !empty($FixedAllowances[2]->Alw_ID)) {
                    if ($FixedAllowances[2]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[2]->Amount;
                    } else if ($FixedAllowances[2]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[2]->Amount;
                    }
                }
                if (!empty($Allowances[1]->Alw_ID)) {
                    if ($Allowances[1]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[1]->Amount;
                    } else if ($Allowances[1]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[1]->Amount;
                    }
                } else if (empty($Allowances[1]->Alw_ID) && !empty($FixedAllowances[1]->Alw_ID)) {
                    if ($FixedAllowances[1]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[1]->Amount;
                    } else if ($FixedAllowances[1]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[1]->Amount;
                    }
                }
                if (!empty($Allowances[0]->Alw_ID)) {
                    if ($Allowances[0]->Alw_ID == 1) {
                        $tbattendencebonus = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 2) {
                        $tbprodinc1 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 3) {
                        $tbprodinc2 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 4) {
                        $tbspc1 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 5) {
                        $tbspc2 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 6) {
                        $Allowance_1 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 7) {
                        $Allowance_2 = $Allowances[0]->Amount;
                    } else if ($Allowances[0]->Alw_ID == 8) {
                        $Allowance_3 = $Allowances[0]->Amount;
                    }
                } else if (empty($Allowances[0]->Alw_ID) && !empty($FixedAllowances[0]->Alw_ID)) {
                    if ($FixedAllowances[0]->Alw_ID == 1) {
                        $tbattendencebonus = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 2) {
                        $tbprodinc1 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 3) {
                        $tbprodinc2 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 4) {
                        $tbspc1 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 5) {
                        $tbspc2 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 6) {
                        $Allowance_1 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 7) {
                        $Allowance_2 = $FixedAllowances[0]->Amount;
                    } else if ($FixedAllowances[0]->Alw_ID == 8) {
                        $Allowance_3 = $FixedAllowances[0]->Amount;
                    }
                }




                // for($x = 0; $x < $Allowancescount[0]->HasRow; $x++){
                //     if($Allowances[$x]->Alw_ID==1){
                //        $tbattendencebonus = $Allowances[$x]->Amount; 
                //     }else if($Allowances[$x]->Alw_ID== 2){
                //         $tbprodinc1 = $Allowances[$x]->Amount;
                //     }else if($Allowances[$x]->Alw_ID== 3){
                //         $tbprodinc2 = $Allowances[$x]->Amount;
                //     }else if($Allowances[$x]->Alw_ID== 4){
                //         $tbspc1 = $Allowances[$x]->Amount;
                //     }else if($Allowances[$x]->Alw_ID== 5){
                //         $tbspc2 = $Allowances[$x]->Amount;
                //     }
                // }

                //**** Get deduction Details
                $Deductions = $this->Db_model->getfilteredData("select Ded_ID,Amount from tbl_variable_deduction where EmpNo=$EmpNo and Month=$month and Year=$year");
                $FixedDeductions = $this->Db_model->getfilteredData("select Deduction_ID,Amount from tbl_fixed_deduction where EmpNo=$EmpNo ORDER BY tbl_fixed_deduction.Deduction_ID");

                $payee = $this->Db_model->getfilteredData("SELECT * FROM tbl_payee");
                $stamp_Duty = $this->Db_model->getfilteredData("select ID, Amount from tbl_variable_stamp where EmpNo=$EmpNo and Month=$month and Year=$year");
                $fixed_stamp_Duty = $this->Db_model->getfilteredData("select ID, Amount from tbl_variable_stamp where EmpNo=$EmpNo and Month='0' ");

                //**** Get salary advance
                $Sal_Advance = $this->Db_model->getfilteredData("select Amount from tbl_salary_advance where Is_Approve=1 and EmpNo=$EmpNo and month=$month and year = $year");

                $Fest_Advance = $this->Db_model->getfilteredData("SELECT Amount from tbl_festivel_advance where EmpNo=$EmpNo and Month=$month and Year = $year");
                $uniform = 0;
                $other = 0;
                $deduction1 = 0;
                $deduction2 = 0;

                $stamp_Duty1 = 0;
                if (!empty($stamp_Duty)) {
                    $stamp_Duty1 =  $stamp_Duty[0]->Amount;
                } else if (empty($stamp_Duty) && !empty($fixed_stamp_Duty)) {
                    $stamp_Duty1 =  $fixed_stamp_Duty[0]->Amount;
                }

                

                

                

                if (empty($Deductions[3]->Ded_ID) && !empty($FixedDeductions[3]->Deduction_ID)) {
                    if ($FixedDeductions[3]->Deduction_ID == 1) {
                        $uniform = $FixedDeductions[3]->Amount;
                    } else if ($FixedDeductions[3]->Deduction_ID == 2) {
                        $other = $FixedDeductions[3]->Amount;
                    } else if ($FixedDeductions[3]->Deduction_ID == 3) {
                        $deduction1 = $FixedDeductions[3]->Amount;
                    } else if ($FixedDeductions[3]->Deduction_ID == 4) {
                        $deduction2 = $FixedDeductions[3]->Amount;
                    }
                } else if (!empty($Deductions[3]->Ded_ID)) {
                    if ($Deductions[3]->Ded_ID == 1) {
                        $uniform = $Deductions[3]->Amount;
                    } else if ($Deductions[3]->Ded_ID == 2) {
                        $other = $Deductions[3]->Amount;
                    } else if ($Deductions[3]->Ded_ID == 3) {
                        $deduction1 = $Deductions[3]->Amount;
                    } else if ($Deductions[3]->Ded_ID == 4) {
                        $deduction2 = $Deductions[3]->Amount;
                    }
                }
                if (empty($Deductions[2]->Ded_ID) && !empty($FixedDeductions[2]->Deduction_ID)) {
                    if ($FixedDeductions[2]->Deduction_ID == 1) {
                        $uniform = $FixedDeductions[2]->Amount;
                    } else if ($FixedDeductions[2]->Deduction_ID == 2) {
                        $other = $FixedDeductions[2]->Amount;
                    } else if ($FixedDeductions[2]->Deduction_ID == 3) {
                        $deduction1 = $FixedDeductions[2]->Amount;
                    } else if ($FixedDeductions[2]->Deduction_ID == 4) {
                        $deduction2 = $FixedDeductions[2]->Amount;
                    }
                } else if (!empty($Deductions[2]->Ded_ID)) {
                    if ($Deductions[2]->Ded_ID == 1) {
                        $uniform = $Deductions[2]->Amount;
                    } else if ($Deductions[2]->Ded_ID == 2) {
                        $other = $Deductions[2]->Amount;
                    } else if ($Deductions[2]->Ded_ID == 3) {
                        $deduction1 = $Deductions[2]->Amount;
                    } else if ($Deductions[2]->Ded_ID == 4) {
                        $deduction2 = $Deductions[2]->Amount;
                    }
                }
                if (empty($Deductions[1]->Ded_ID) && !empty($FixedDeductions[1]->Deduction_ID)) {
                    if ($FixedDeductions[1]->Deduction_ID == 1) {
                        $uniform = $FixedDeductions[1]->Amount;
                    } else if ($FixedDeductions[1]->Deduction_ID == 2) {
                        $other = $FixedDeductions[1]->Amount;
                    } else if ($FixedDeductions[1]->Deduction_ID == 3) {
                        $deduction1 = $FixedDeductions[1]->Amount;
                    } else if ($FixedDeductions[1]->Deduction_ID == 4) {
                        $deduction2 = $FixedDeductions[1]->Amount;
                    }
                } else if (!empty($Deductions[1]->Ded_ID)) {
                    if ($Deductions[1]->Ded_ID == 1) {
                        $uniform = $Deductions[1]->Amount;
                    } else if ($Deductions[1]->Ded_ID == 2) {
                        $other = $Deductions[1]->Amount;
                    } else if ($Deductions[1]->Ded_ID == 3) {
                        $deduction1 = $Deductions[1]->Amount;
                    } else if ($Deductions[1]->Ded_ID == 4) {
                        $deduction2 = $Deductions[1]->Amount;
                    }
                }
                if (empty($Deductions[0]->Ded_ID) && !empty($FixedDeductions[0]->Deduction_ID)) {
                    if ($FixedDeductions[0]->Deduction_ID == 1) {
                        $uniform = $FixedDeductions[0]->Amount;
                    } else if ($FixedDeductions[0]->Deduction_ID == 2) {
                        $other = $FixedDeductions[0]->Amount;
                    } else if ($FixedDeductions[0]->Deduction_ID == 3) {
                        $deduction1 = $FixedDeductions[0]->Amount;
                    } else if ($FixedDeductions[0]->Deduction_ID == 4) {
                        $deduction2 = $FixedDeductions[0]->Amount;
                    }
                } else if (!empty($Deductions[0]->Ded_ID)) {
                    if ($Deductions[0]->Ded_ID == 1) {
                        $uniform = $Deductions[0]->Amount;
                    } else if ($Deductions[0]->Ded_ID == 2) {
                        $other = $Deductions[0]->Amount;
                    } else if ($Deductions[0]->Ded_ID == 3) {
                        $deduction1 = $Deductions[0]->Amount;
                    } else if ($Deductions[0]->Ded_ID == 4) {
                        $deduction2 = $Deductions[0]->Amount;
                    }
                }
                

                if ($Fest_Advance == null) {
                    $Festivel_Advance = 0;
                } else {
                    $Festivel_Advance = $Fest_Advance[0]->Amount;
                }

                //**** Get loan Details
                $Loan = $this->Db_model->getfilteredData("select Loan_ID,Loan_amount,Month_Installment,FullAmount,Paid_Amount from tbl_loans where Is_Settled=0 and EmpNo=$EmpNo");

                /*
                 * Loan Details
                 */

                if (empty($Loan[0]->Loan_ID)) {
                    $LoanID = 0;
                } else {
                    $LoanID = $Loan[0]->Loan_ID;
                }


                if (empty($Loan[0]->Month_Installment)) {
                    $LoanMonth = 0;
                } else {
                    $LoanMonth = $Loan[0]->Month_Installment;
                }

                /*
                 * Salary Advance Details
                 */


                if (empty($Sal_Advance[0]->Amount)) {
                    $Sal_advance = 0;
                } else {
                    $Sal_advance = $Sal_Advance[0]->Amount;
                }

                /*
                 * Budget relevances
                 */
                if (empty($br1)) {
                    $budgetrelevance1 = 0;
                } else {
                    $budgetrelevance1 = $br1;
                }



                if (empty($br2)) {
                    $budgetrelevance2 = 0;
                } else {
                    $budgetrelevance2 = $br2;
                }

                /*
                 * Allowance Details
                 */


                if (empty($Allowances[6]->Alw_ID)) {
                    $Allowance_ID_1 = 0;
                } else {
                    $Allowance_ID_1 = $Allowances[0]->Alw_ID;
                }



                if (empty($Allowances[7]->Alw_ID)) {
                    $Allowance_ID_2 = 0;
                } else {
                    $Allowance_ID_2 = $Allowances[1]->Alw_ID;
                }



                if (empty($Allowances[8]->Alw_ID)) {
                    $Allowance_ID_3 = 0;
                } else {
                    $Allowance_ID_3 = $Allowances[2]->Alw_ID;
                }


                if (empty($Allowances[9]->Alw_ID)) {
                    $Allowance_ID_4 = 0;
                } else {
                    $Allowance_ID_4 = $Allowances[3]->Alw_ID;
                }



                if (empty($Allowances[10]->Alw_ID)) {
                    $Allowance_ID_5 = 0;
                } else {
                    $Allowance_ID_5 = $Allowances[4]->Alw_ID;
                }




                /*
                 * Deduction Details
                 */

                if (empty($Deductions[0]->Ded_ID)) {
                    $Deduction_ID_1 = 0;
                } else {
                    $Deduction_ID_1 = $Deductions[0]->Ded_ID;
                }



                if (empty($Deductions[1]->Ded_ID)) {
                    $Deduction_ID_2 = 0;
                } else {
                    $Deduction_ID_2 = $Deductions[1]->Ded_ID;
                }



                if (empty($Deductions[2]->Ded_ID)) {
                    $Deduction_ID_3 = 0;
                } else {
                    $Deduction_ID_3 = $Deductions[2]->Ded_ID;
                }



                //Get Overtime details
                $Overtime_DB = $this->Db_model->getfilteredData("select sum(DOT) as D_OT from tbl_individual_roster where EmpNo='$EmpNo' and EXTRACT(MONTH FROM FDate)=$month and  EXTRACT(YEAR FROM FDate) =$year");
                $Overtime = $this->Db_model->getfilteredData("select sum(AfterExH) as N_OT from tbl_individual_roster where EmpNo='$EmpNo' and EXTRACT(MONTH FROM FDate)=$month and  EXTRACT(YEAR FROM FDate) =$year");

                $N_OT_Hours = $Overtime[0]->N_OT;

                $D_OT_Hours = $Overtime_DB[0]->D_OT;

                $OT_Rate = ((($BasicSal + $Fixed_Allowance) / 240) * 1.5);
                $N_OT_Amount = $OT_Rate * ($N_OT_Hours / 60);



                $OT_Rate_2 = ((($BasicSal + $Fixed_Allowance) / 240) * 2);
                $D_OT_Amount = $OT_Rate_2 * ($D_OT_Hours / 60);


                //*** Get Late Minutes
                $Late_Min = $this->Db_model->getfilteredData("select sum(LateM) as LateMin from tbl_individual_roster where EmpNo=$EmpNo and EXTRACT(MONTH FROM FDate)=$month and RYear=$year");

                //*** Late Min

                if (empty($Late_Min[0]->LateMin)) {
                    $Late_Min = 0;
                } else {
                    $Late_Min = $Late_Min[0]->LateMin;
                }


                $Late_rate = (($BasicSal / 240)) / 60;
                //** Late Amount
                //                $Late_Amount = $Late_rate * $Late_Min;
                //                 Kangara Holdigs
                $Late_Amount = 0;

                //---------------------  only kangara holdings

                // if ($BasicSal >= 50000) {

                //     $N_OT_Hours = 0;
                //     $N_OT_Amount = 0;
                //     $D_OT_Hours = 0;
                //     $D_OT_Amount = 0;
                // }

                // if ($Is_EPF == 0) {
                //     $EPF_Worker = 0;
                //     $EPF_Employer = 0;
                //     $ETF = 0;
                // }
                //---------------------  only kangara holdings


                //junrich br price it is set to basic pay

                //junrich br price it is set to basic pay

                //All budgetrelevances
                $budgetrelevances = $budgetrelevance1 + $budgetrelevance2;
                //All Allowances
                $Allowances = $Allowance_1 + $Allowance_2 + $Allowance_3;

                //Calculate Gross salary
                $Gross_sal = ($BasicSal + $Fixed_Allowance + $Incentive + $budgetrelevances);


                /*
                *payee tax calculate start
                */
                $st_gross_Pay = $Gross_sal * 12;


                $free_rate = 100000;
                $anual_freee_rate = $free_rate * 12;
                $payee_now_amount = 0;

                $calculate_gross_pay = $st_gross_Pay - $anual_freee_rate;


                if (0 > $calculate_gross_pay) {
                    $payee_now_amount = 0;
                } else {
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[0]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[0]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[1]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[1]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[2]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[2]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[3]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[3]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        if ($calculate_gross_pay >= 500000) {
                            $payeeamount = (500000 / 12) * ($payee[4]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        } else if (0 < $calculate_gross_pay && $calculate_gross_pay < 500000) {
                            $payeeamount = ($calculate_gross_pay / 12) * ($payee[4]->Tax_rate / 100);
                            $calculate_gross_pay -= 500000;
                            $payee_now_amount += $payeeamount;
                        }
                    }
                    if (0 < $calculate_gross_pay) {
                        $payeeamount = ($calculate_gross_pay / 12) * ($payee[5]->Tax_rate / 100);
                        $calculate_gross_pay -= 500000;
                        $payee_now_amount += $payeeamount;
                    }
                }
                /*
                *payee tax calculate end
                */

                //Calculate EPF Employee
                $EPF_Worker = (8 / 100) * ($BasicSal + $Fixed_Allowance + $budgetrelevances);

                //Total for epf
                $tottal_for_epf = $BasicSal + $Fixed_Allowance + $budgetrelevances;

                //Calculate EPF Employer
                $EPF_Employer = (12 / 100) * ($BasicSal + $Fixed_Allowance + $budgetrelevances);

                //Calculate ETF Employee
                $ETF = (3 / 100) * ($BasicSal + $Fixed_Allowance + $budgetrelevances);

                //Calculate Total Deductions
                $Tot_deductions = $EPF_Worker + $Sal_advance + $Festivel_Advance + $uniform + $other + $deduction1 + $deduction2 + $LoanMonth + $Late_Amount + $Nopay_Deduction;

                //Calculate Net Salary
                $netSal = ($Gross_sal + $N_OT_Amount + $D_OT_Amount + $Att_Allowance + $Allowances) - $Tot_deductions;

                //calculate Gross pay
                $grosspay = $Gross_sal + $N_OT_Amount + $D_OT_Amount + $Att_Allowance + $Allowances;

                $D_Salary = $Gross_sal - $Tot_deductions;


                $data = array(
                    array(
                        'EmpNo' => $EmpNo,
                        'EPFNO' => $EpfNo,
                        'Month' => $month,
                        'Year' => $year,
                        'Basic_sal' => $BasicSal,
                        'Total_F_Epf' => $tottal_for_epf,
                        'Br_pay' => $budgetrelevances,
                        'Incentive' => $Incentive,
                        'Dep_ID' => $Dep_ID,
                        'Des_ID' => $Des_ID,
                        'No_Pay_days' => $NopayDays,
                        'no_pay_deduction' => $Nopay_Deduction,
                        'EPF_Worker_Rate' => 8,
                        'EPF_Worker_Amount' => $EPF_Worker,
                        'EPF_Employee_Rate' => 12,
                        'EPF_Employee_Amount' => $EPF_Employer,
                        'ETF_Rate' => 3,
                        'ETF_Amount' => $ETF,
                        'Loan_Instalment' => $LoanMonth,
                        'Salary_advance' => $Sal_advance,
                        'Late_min' => $Late_Min,
                        'Late_deduction' => $Late_Amount,
                        // 'Late_min' => 0,
                        // 'Late_deduction' => 0,
                        'Alw_ID_1' => $Allowance_ID_1,
                        'Allowance_1' => $Allowance_1,
                        'Alw_ID_2' => $Allowance_ID_2,
                        'Allowance_2' => $Allowance_2,
                        'Alw_ID_3' => $Allowance_ID_3,
                        'Allowance_3' => $Allowance_3,
                        'Alw_ID_4' => $Allowance_ID_4,
                        'Allowance_4' => 0,
                        'Alw_ID_5' => $Allowance_ID_5,
                        'Allowance_5' => 0,
                        'Att_Allowance' => $Att_Allowance,
                        'Fixed' => $tbattendencebonus,
                        'Prod_inc1' => $tbprodinc1,
                        'Prod_inc2' => $tbprodinc2,
                        'Spc_inc1' => $tbspc1,
                        'Spc_inc2' => $tbspc2,
                        'Normal_OT_Hrs' => ($N_OT_Hours / 60),
                        'Normal_OT_Pay' => $N_OT_Amount,
                        'Double_OT_Hrs' => ($D_OT_Hours / 60),
                        'Double_OT_Pay' => $D_OT_Amount,
                        'Uniform' => $uniform,
                        'Ded_ID_1' => $Deduction_ID_1,
                        'Deduct_1' => $other,
                        'Ded_ID_2' => $Deduction_ID_2,
                        'Deduct_2' => $deduction1,
                        'Ded_ID_3' => $Deduction_ID_3,
                        'Deduct_3' => $deduction2,
                        'Wellfare' => $welfair_1,
                        'Payee_amount' => $payee_now_amount,
                        'Stamp_duty' => $stamp_Duty1,
                        'tot_deduction' => $Tot_deductions,
                        'Gross_pay' => $grosspay,
                        'Gross_sal' => $Gross_sal,
                        'D_Salary' => $D_Salary,
                        'Net_salary' => $netSal
                    )
                );



                $this->db->insert_batch('tbl_salary', $data);

                if (empty($Loan[0]->Paid_Amount)) {
                    $PaidAmount = 0;
                } else {
                    $PaidAmount = $Loan[0]->Paid_Amount;
                }


                $PaidAmount_to = $PaidAmount + $LoanMonth;


                if (empty($Loan[0]->FullAmount)) {
                    $Full_Amount = 0;
                } else {
                    $Full_Amount = $Loan[0]->FullAmount;
                }



                $BalanceAmount = $Full_Amount - $PaidAmount_to;

                if ($BalanceAmount == 0) {
                    $Is_Settele = 1;
                } else {
                    $Is_Settele = 0;
                }


                $data_loan = array(
                    'EmpNo' => $EmpNo,
                    'Paid_Amount' => $PaidAmount_to,
                    'Balance_amount' => $BalanceAmount,
                    'Is_Settled' => $Is_Settele,
                );


                $whereArray_loan = array("EmpNo" => $EmpNo);
                $result = $this->Db_model->updateData("tbl_loans", $data_loan, $whereArray_loan);


                $this->session->set_flashdata('success_message', 'Allovance added successfully');
            }
        }
        //        die;

        $this->session->set_flashdata('success_message', 'Payroll Process successfully');
        redirect(base_url() . 'Pay/Payroll_Process');
    }
}
