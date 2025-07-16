<?php

defined('BASEPATH') or exit ('No direct script access allowed');

class View_Short_Leave extends CI_Controller
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

        $data['title'] = "View Short Leave | HRM System";
        // $data['data_set'] = $this->Db_model->getData('EmpNo,Emp_Full_Name', 'tbl_empmaster');
        // $data['data_dep'] = $this->Db_model->getData('Dep_ID,Dep_Name', 'tbl_departments');
        // $data['data_desig'] = $this->Db_model->getData('Des_ID,Desig_Name', 'tbl_designations');
        // $data['data_grp'] = $this->Db_model->getData('Grp_ID,EmpGroupName', 'tbl_emp_group');
        // $data['data_cmp'] = $this->Db_model->getData('Cmp_ID,Company_Name', 'tbl_companyprofile');
        $data['data_set_att'] = $this->Db_model->getfilteredData("select * from tbl_shortlive inner join tbl_empmaster on tbl_empmaster.EmpNo = tbl_shortlive.EmpNo where tbl_shortlive.Is_pending='1' order by ID desc");


        $this->load->view('Leave_Transaction/View_Short_Leave/index', $data);
    }

    public function ajax_Status($id)
    {
        // echo $id;
        $data_arr = array("Is_pending" => 0, "Is_Approve" => 0, "Is_Cancel" => 1);
        $whereArray = array("ID" => $id);
        $result = $this->Db_model->updateData("tbl_shortlive", $data_arr, $whereArray);
    }

    public function ajax_Status_Aprove($id)
    {
        // echo $id;
        $data_arr = array("Is_pending" => 0, "Is_Approve" => 1, "Is_Cancel" => 0);
        $whereArray = array("ID" => $id);
        $result = $this->Db_model->updateData("tbl_shortlive", $data_arr, $whereArray);

        $this->session->set_flashdata('success_message', 'successfully');
        redirect(base_url() . "Leave_Transaction/View_Short_Leave");
    }

    public function delete($ID) {

        // echo $ID;

        $table = "tbl_shortlive";
        $where = 'id';
        $this->Db_model->delete_by_id($ID, $where, $table);
        // echo json_encode(array("status" => TRUE));

        // $currentUser = $this->session->userdata('login_user');
        // $Emp = $currentUser[0]->EmpNo;

        // $data = array(
        //     'Is_pending' => 0,
        //     'Is_Approve' => 0,
        //     'Is_Cancel' => 1,
        //     'Approved_by' => $Emp,
        // );

        // $whereArr = array("id" => $ID);
        // $result = $this->Db_model->updateData("tbl_salary_advance", $data, $whereArr);

        $this->session->set_flashdata('success_message', 'Reject successfully');
        redirect(base_url() . "Leave_Transaction/View_Short_Leave");
    }




}
