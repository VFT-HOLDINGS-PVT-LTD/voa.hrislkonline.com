<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!($this->session->userdata('login_user'))) {
            redirect(base_url() . "");
        }
        $this->load->model('Db_model', '', TRUE);
    }

    public function index() {

        $data['title'] = "Dashboard | HRM System";
        $data['count'] = $this->Db_model->getfilteredData('select count(EmpNo) as count_emp from tbl_empmaster');
        $data['Bdays'] = $this->Db_model->getfilteredData("select Emp_Full_Name,Tel_mobile,tbl_branches.B_name from tbl_empmaster
inner join tbl_branches
on tbl_branches.B_id = tbl_empmaster.B_id

 where date_format(DOB,'%m-%d') = date_format(curdate(),'%m-%d')");


        //**** Employee department chart data
        $data['sdata'] = $this->Db_model->getfilteredData("SELECT 
                                                            COUNT(EmpNo)as EmpCount , Dep_Name
                                                        FROM
                                                            tbl_empmaster
                                                                INNER JOIN
                                                            tbl_departments ON tbl_empmaster.Dep_ID = tbl_departments.Dep_ID
                                                        group by tbl_departments.Dep_ID");

$date1 = new DateTime("now", new DateTimeZone("Asia/Colombo"));
$timestamp1 = date_format($date1, 'Y-m-d');

//**** Employee day present (PR) count
$data['data3']  = $this->Db_model->getfilteredData("select count(EmpNo) as allcountemp from tbl_empmaster ");

// $data['data3']; die;
$data['data2'] = $this->Db_model->getfilteredData("SELECT COUNT(DISTINCT Enroll_No) AS count FROM tbl_u_attendancedata WHERE AttDate = '$timestamp1' AND Status = 0");
$data['data1'] =  intval($data['data3'][0]->allcountemp)-intval($data['data2'][0]->count);
$data['data4']='';

foreach ($data['data2'] as $sales) { 
    $data['data4'] = intval($sales->count);

} 
        
        $data['sdata_gender'] = $this->Db_model->getfilteredData("SELECT
            COUNT(*) AS total_count,
    COUNT(CASE WHEN Gender = 'Male' THEN 1 END) AS male_count,
    COUNT(CASE WHEN Gender = 'Female' THEN 1 END) AS female_count
FROM
    tbl_empmaster where Status=1");
        
     

        //**** Employee day present (PR) count
        $data['today_c'] = $this->Db_model->getfilteredData("select count(ID_Roster) as TodayCount from tbl_individual_roster where FDate = curdate() and DayStatus='PR' ");



        $currentUser = $this->session->userdata('login_user');
        $Emp = $currentUser[0]->EmpNo;
                $Year = date('Y');


        $data['data_leave'] = $this->Db_model->getfilteredData("SELECT 
                                                                        lv_typ.Lv_T_ID,
                                                                        lv_typ.leave_name,
                                                                        lv_al.Balance
                                                                    FROM
                                                                        tbl_leave_allocation lv_al
                                                                        right join
                                                                        tbl_leave_types lv_typ on lv_al.Lv_T_ID = lv_typ.Lv_T_ID
                                                                        where EmpNo='$Emp' 
                                                                    ");
        
//        var_dump($data['data_leave'] );die;



        $this->load->view('Dashboard/index', $data);
    }

}
