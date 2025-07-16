<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ADD_Employees extends CI_Controller
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
        $this->load->library('form_validation');
    }

    public function index()
    {


        $data['title'] = "ADD Employees | HRM SYSTEM";
        $data['data_dep'] = $this->Db_model->getData('Dep_ID,Dep_Name', 'tbl_departments');
        $data['data_desig'] = $this->Db_model->getData('Des_ID,Desig_Name', 'tbl_designations');
        $data['data_grp'] = $this->Db_model->getData('Grp_ID,EmpGroupName', 'tbl_emp_group');
        $data['data_u_lvl'] = $this->Db_model->getData('user_level_id,user_level_name', 'tbl_user_level_master');
        $data['data_Rstr'] = $this->Db_model->getData('RosterCode,RosterName', 'tbl_rosterpatternweeklyhd');
        $data['data_ot'] = $this->Db_model->getData('OTCode,OTName', 'tbl_ot_pattern_hd');
        $data['data_branch'] = $this->Db_model->getData('B_id,B_name', 'tbl_branches');
        $data['data_bank'] = $this->Db_model->getData('Bnk_ID,bank_name', 'tbl_banks');
        $data['data_epf'] = $this->Db_model->getData('EPF_CAT,EPF_CAT_Name', 'tbl_epf_cat');
        $data['data_status'] = $this->Db_model->getData('EMP_ST_ID,EMP_ST_Name', 'tbl_emp_status');
        $this->load->view('Employee_Management/ADD_Employees/index', $data);
    }

    public function check_emp()
    {
        //get the username  
        $EmpNo = $this->input->post('txt_emp_no');

        $result = $this->Db_model->getfilteredData("select count(EmpNo) as EmpNo from tbl_empmaster where EmpNo = '$EmpNo' ");


        //if number of rows fields is bigger them 0 that means it's NOT available '  
        if ($result[0]->EmpNo == 0) {

            echo 0;
        } else {
            //else if it's not bigger then 0, then it's available '  
            //and we send 1 to the ajax request  
            echo 1;
        }
    }

    //***** INsert Employee
    public function insert_Data()
    {

        $Emp_No = $this->input->post('txt_emp_no');

        $Image = md5($Emp_No);



        $config['upload_path'] = 'assets/images/Employees/';
        $config['allowed_types'] = 'jpg|png|docx';
        $config['max_size'] = 100000;
        $config['max_width'] = 4000;
        $config['max_height'] = 4000;
        //      $config['file_name'] = $Image;
        $config['file_name'] = $Image . ".jpg";
        $this->load->library('upload', $config);

        echo $this->input->post('cmb_if_epf');

        /*
         * 'image'  selected image id,name
         */
        if (!$this->upload->do_upload('img_employee')) {
            $error = array('error' => $this->upload->display_errors());

            //            var_dump($error);
        } else {
            $data = array('upload_data' => $this->upload->data());
            //            var_dump($data);
        }

        $Password = $this->input->post('txt_nic');


        $Is_Allow = $this->input->post('Is_Allow');
        if ($Is_Allow == null) {
            $Is_Allow = 1;
        } else {
            $Is_Allow = 1;
        }

        $Is_EPF = $this->input->post('cmb_if_epf');
        if ($Is_EPF == null) {
            $Is_EPF = 0;
        }
        // $this->form_validation->set_rules('txt_emp_no', 'Employee Number', 'required|alpha_numeric');
        // $this->form_validation->set_rules('txt_enroll_no', 'Enrollment Number', 'required|alpha_numeric');
        // $this->form_validation->set_rules('txt_epf_no', 'EPF Number', 'required|alpha_numeric');
        // $this->form_validation->set_rules('cmb_epf_cat', 'EPF Category', 'required');
        // $this->form_validation->set_rules('txt_ocp_code', 'Occupation Code', 'required|alpha_numeric');
        // $this->form_validation->set_rules('cmb_emp_status', 'Employee Status', 'required');
        // $this->form_validation->set_rules('cmb_emp_title', 'Title', 'required');
        // $this->form_validation->set_rules('txt_emp_name', 'Full Name', 'required');
        // $this->form_validation->set_rules('txt_emp_name_init', 'Name with Initials', 'required');
        // $this->form_validation->set_rules('txt_basic_sal', 'Basic Salary', 'required|numeric');
        // $this->form_validation->set_rules('cmb_bank', 'Bank', 'required');
        // $this->form_validation->set_rules('txt_B_Branch', 'Bank Branch', 'required');
        // $this->form_validation->set_rules('txt_account', 'Account Number', 'required|numeric');
        // $this->form_validation->set_rules('txt_address', 'Address', 'required');
        // $this->form_validation->set_rules('cmb_district', 'District', 'required');
        // $this->form_validation->set_rules('txt_city', 'City', 'required');
        // $this->form_validation->set_rules('txt_cont_home', 'Home Contact Number', 'required|numeric');
        // $this->form_validation->set_rules('txt_cont_mobile', 'Mobile Contact Number', 'required|numeric');
        // $this->form_validation->set_rules('txt_email', 'Email', 'required|valid_email');
        // $this->form_validation->set_rules('txt_nic', 'NIC', 'required|alpha_numeric');
        // $this->form_validation->set_rules('txt_passport', 'Passport', 'required|alpha_numeric');
        // $this->form_validation->set_rules('txt_dob', 'Date of Birth', 'required|valid_date');
        // $this->form_validation->set_rules('cmb_religin', 'Religion', 'required');
        // $this->form_validation->set_rules('cmb_civil_status', 'Civil Status', 'required');
        // $this->form_validation->set_rules('cmb_blood', 'Blood Group', 'required');
        // $this->form_validation->set_rules('txt_rel_name', 'Relative Name', 'required');
        // $this->form_validation->set_rules('txt_rel_cont', 'Relative Contact Number', 'required|numeric');
        // $this->form_validation->set_rules('txt_no_child', 'Number of Children', 'required|numeric');
        // $this->form_validation->set_rules('txt_user_name', 'Username', 'required|alpha_numeric');
        // $this->form_validation->set_rules('Password', 'Password', 'required');
        // $this->form_validation->set_rules('cmb_user_level', 'User Level', 'required');

        // if ($this->form_validation->run() == FALSE) {
        //     // Validation failed
        //     $errors = validation_errors();
        //     $this->session->set_flashdata('error_message', $errors);
        //     // Handle errors (e.g., display errors to the user)
        // } else {
            $data = array(
                'EmpNo' => $this->input->post('txt_emp_no'),
                'Enroll_No' => $this->input->post('txt_enroll_no'),
                'EPFNO' => $this->input->post('txt_epf_no'),
                'EPF_CAT' => $this->input->post('cmb_epf_cat'),
                // 'Is_EPF' =>$this->input->post('cmb_if_epf'),
                'OCP_Code' => $this->input->post('txt_ocp_code'),
                'EMP_ST_ID' => $this->input->post('cmb_emp_status'),
                'Title' => $this->input->post('cmb_emp_title'),
                'Emp_Full_Name' => $this->input->post('txt_emp_name'),
                'Emp_Name_Int' => $this->input->post('txt_emp_name_init'),
                'Image' => $Image . ".jpg",
                'Gender' => $this->input->post('cmb_gender'),
                'Status' => 1,
                'Dep_ID' => $this->input->post('cmb_dep'),
                'Des_ID' => $this->input->post('cmb_desig'),
                'Grp_ID' => $this->input->post('cmb_group'),
                'RosterCode' => 'RS0001',
                'OTCode' => $this->input->post('cmb_ot_pattern'),
                'B_id' => $this->input->post('cmb_branch'),
                'BR1' => $this->input->post('txt_BG_Allowance1'),
                'BR2' => $this->input->post('txt_BG_Allowance2'),
                'ApointDate' => $this->input->post('txt_appoint_date'),
                'Permanent_Date' => $this->input->post('txt_permanent_date'),
                'Basic_Salary' => $this->input->post('txt_basic_sal'),
                'Incentive' => $this->input->post('txt_Incentive'),
                'Bnk_ID' => $this->input->post('cmb_bank'),
                'Bnk_Br_ID' => $this->input->post('txt_B_Branch'),
                'Account_no' => $this->input->post('txt_account'),
                'Is_EPF' => $Is_EPF,
                'Address' => $this->input->post('txt_address'),
                'District' => $this->input->post('cmb_district'),
                'City' => $this->input->post('txt_city'),
                'Tel_home' => $this->input->post('txt_cont_home'),
                'Tel_mobile' => $this->input->post('txt_cont_mobile'),
                'E_mail' => $this->input->post('txt_email'),
                'NIC' => $this->input->post('txt_nic'),
                'Passport' => $this->input->post('txt_passport'),
                'DOB' => $this->input->post('txt_dob'),
                'Religion' => $this->input->post('cmb_religin'),
                'Civil_status' => $this->input->post('cmb_civil_status'),
                'Blood_group' => $this->input->post('cmb_blood'),
                'Relations_name' => $this->input->post('txt_rel_name'),
                'Relations_Tel' => $this->input->post('txt_rel_cont'),
                'No_Of_Child' => $this->input->post('txt_no_child'),
                'Is_allow_login' => 1,
                'username' => $this->input->post('txt_user_name'),
                'Password' => hash('sha512', $Password),
                //            'user_p_id' => 2,
                'user_p_id' => $this->input->post('cmb_user_level'),
                'Cmp_ID' => 1,
                'Active_process' => 1,
            );
            $result = $this->Db_model->insertData("tbl_empmaster", $data);
            $this->session->set_flashdata('success_message', 'Employee Added');
        // }
        redirect('/Employee_Management/ADD_Employees/');
    }
}
