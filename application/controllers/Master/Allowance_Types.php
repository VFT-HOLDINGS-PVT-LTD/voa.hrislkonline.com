<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Allowance_Types extends CI_Controller {

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

        $this->load->helper('url');
        $data['title'] = "Allowance | HRM SYSTEM";
        $data['data_set'] = $this->Db_model->getData('Alw_ID,Allowance_name,IsActive,isFixed', 'tbl_allowance_type');
        $this->load->view('Master/Allowance_Types/index', $data);
    }
    
    /*
     * Insert Data
     */

    public function insert_data() {
        
        
        $Fixed=$this->input->post('isFixed');
        if($Fixed==null){
            $Fixed=0;
            
        }

        $data = array(
            'Allowance_name' => $this->input->post('txt_allowance'),
            'IsActive' => 1,
            'isFixed' => $Fixed
                
        );

        $result = $this->Db_model->insertData("tbl_allowance_type", $data);


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {

            $this->db->trans_commit();
            $this->session->set_flashdata('success_message', 'New Allowance has been added successfully');
        }

        redirect(base_url() . 'Master/Allowance_Types/'); //*********Redirect to designation form
        
    }
    
       /*
     * Get data
     */

    public function get_details() {
        $id = $this->input->post('id');

//                    echo "OkM " . $id;

        $whereArray = array('Alw_ID' => $id);

        $this->Db_model->setWhere($whereArray);
        $dataObject = $this->Db_model->getData('Alw_ID,Allowance_name,IsActive,isFixed', 'tbl_allowance_type');
        
       

        $array = (array) $dataObject;
        echo json_encode($array);
    }

    /*
     * Edit Data
     */

    public function edit() {
        $ID = $this->input->post("id");
        $Name = $this->input->post("Allowance_Name");
       
        
        $BF=$this->input->post('is_Fixced');
        if ($BF == null) {
            $BF = 0;
        } elseif ($BF == 'on') {
            $BF=1;
        }
        
//        var_dump($Is_fxd.$Is_Act);die;


        $data = array("Allowance_name" => $Name,"isFixed"=>$BF,"IsActive"=>1);
        $whereArr = array("Alw_ID" => $ID);
        $result = $this->Db_model->updateData("tbl_allowance_type", $data, $whereArr);
        redirect(base_url() . "Master/Allowance_Types");
    }

    /*
     * Delete Data
     */

    public function ajax_delete($id) {
        $table = "tbl_allowance_type";
        $where = 'Alw_ID';
        $this->Db_model->delete_by_id($id, $where, $table);
        echo json_encode(array("status" => TRUE));
    }


}
