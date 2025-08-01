<!DOCTYPE html>


<!--Add Employee

@author Ashan Rathsara-->


<html lang="en">


<head>
    <title><?php echo $title ?></title>
    <!-- Styles -->
    <?php $this->load->view('template/css.php'); ?>


</head>

<style type="text/css">
    .thumb-image {
        float: left;
        width: 200px;
        height: 250px;
        position: relative;
        padding: 5px;
    }
</style>

<body class="infobar-offcanvas">

    <!--header-->

    <?php $this->load->view('template/header.php'); ?>

    <!--end header-->

    <div id="wrapper">
        <div id="layout-static">

            <!--dashboard side-->

            <?php $this->load->view('template/dashboard_side.php'); ?>

            <!--dashboard side end-->

            <div class="static-content-wrapper">
                <div class="static-content">
                    <div class="page-content">
                        <ol class="">

                            <!--                                <li class=""><a href="#">HOME</a></li>
                                                                <li class="active"><a href="#">EMPLOYEES</a></li>-->

                        </ol>


                        <!--                            <div class="page-tabs">
                                                            <ul class="nav nav-tabs">
                            
                                                                <li class="active"><a data-toggle="tab" href="#tab1">EMPLOYEES</a></li>
                            
                            
                            
                                                            </ul>
                                                        </div>-->
                        <div class="container-fluid">


                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">

                                    <div class="row">
                                        <div class="col-xs-12">


                                            <div class="panel">
                                                <div style="background: rgb(59,105,129);
                                                         background: linear-gradient(60deg, rgba(59,105,129,1) 0%, rgba(54,120,150,0.644782913165266) 100%);border-radius: 30px;" class="panel-heading">
                                                    <img style="height: 70px; float: left" src="<?php echo base_url(); ?>assets/images/user-group.png">
                                                    <h2 style="color: #ffffff">EMPLOYEE</h2>
                                                    <!--                                                        <div class="options">
                                                                                                                    <ul class="nav nav-tabs">
                                                        
                                                                                                                        <li class="active"><a href="#horizontal-form" data-toggle="tab">MASTER</a></li>
                                                                                                                        <li ><a href="#vertical-form" data-toggle="tab">PERSONAL DETAILS</a></li>
                                                                                                                        <li><a href="#bordered-row" data-toggle="tab">OTHER DETAILS</a></li>
                                                                                                                        <li><a href="#login" data-toggle="tab">LOGIN DETAILS</a></li>
                                                                                                                        <li><a href="#tabular-form" data-toggle="tab">DOCUMENTS</a></li>
                                                                                                                    </ul>
                                                                                                                </div>-->
                                                </div>
                                                <div class="panel-body ">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="horizontal-form">
                                                            <form class="form-horizontal" id="frm_employee" name="frm_employee" action="<?php echo base_url(); ?>Employee_Management/ADD_Employees/insert_Data" method="POST" enctype="multipart/form-data">

                                                                <!--success Message-->
                                                                <?php if (isset($_SESSION['success_message']) && $_SESSION['success_message'] != '') { ?>
                                                                    <div id="spnmessage" class="alert alert-dismissable alert-success">
                                                                        <strong>Success !</strong> <?php echo $_SESSION['success_message'] ?>
                                                                    </div>
                                                                <?php } ?>

                                                                <!--Error Message-->
                                                                <?php if (isset($_SESSION['error_message']) && $_SESSION['error_message'] != '') { ?>
                                                                    <div id="spnmessage" class="alert alert-dismissable alert-danger error_redirect">
                                                                        <strong>Error !</strong> <?php echo $_SESSION['error_message'] ?>
                                                                    </div>
                                                                <?php } ?>

                                                                <!--                                                                    <div class="form-group col-sm-12">
                                                                                                                                            <div class="col-sm-8">
                                                                                                                                                <img class="imagecss" src="<?php echo base_url(); ?>assets/images/user-group.png" >
                                                                                                                                            </div>
                                                                                                                                        </div>-->

                                                                <div class="form-group col-md-12">
                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Employee No <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_emp_no" name="txt_emp_no" placeholder="Ex: 00001" required="">
                                                                            <div id="username_availability_result"></div>
                                                                            <!--<input class="btn btn-green" type="button" id="check_username_availability" value="Check Availability">-->
                                                                        </div>

                                                                    </div>

                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Enroll No <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_enroll_no" name="txt_enroll_no" placeholder="Ex: 1" required="">
                                                                        </div>

                                                                    </div>

                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">EPF No <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_epf_no" required="" name="txt_epf_no" placeholder="Ex: 1">
                                                                        </div>

                                                                    </div>



                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">EPF Category</label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" id="cmb_epf_cat" name="cmb_epf_cat">


                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_epf as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->EPF_CAT; ?>"><?php echo $t_data->EPF_CAT_Name; ?></option>

                                                                                <?php }
                                                                                ?>

                                                                            </select>
                                                                        </div>

                                                                    </div>


                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Employee Status</label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" id="cmb_emp_status" name="cmb_emp_status">


                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_status as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->EMP_ST_ID; ?>"><?php echo $t_data->EMP_ST_Name; ?></option>

                                                                                <?php }
                                                                                ?>

                                                                            </select>
                                                                        </div>

                                                                    </div>


                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">EPF Liable</label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" id="cmb_if_epf" name="cmb_if_epf">

                                                                                <option value="1">Yes</option>
                                                                                <option value="0">No</option>

                                                                            </select>
                                                                        </div>

                                                                    </div>



                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Occupation Code</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_ocp_code" name="txt_ocp_code" placeholder="Ex: 15">
                                                                        </div>

                                                                    </div>


                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Title</label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" id="cmb_emp_title" name="cmb_emp_title">

                                                                                <option value="Unknown.">Unknown</option>
                                                                                <option value="Mr.">Mr.</option>
                                                                                <option value="Mrs.">Mrs.</option>
                                                                                <option value="Miss.">Miss.</option>
                                                                                <option value="Dr.">Dr.</option>


                                                                            </select>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-md-12">

                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Full Name <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_emp_name" name="txt_emp_name" required="" placeholder="Ex: Nimal">
                                                                        </div>

                                                                    </div>


                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Initials <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_emp_name_init" name="txt_emp_name_init" required="" placeholder="Ex: L.A.R">
                                                                        </div>

                                                                    </div>


                                                                    <div class="form-group col-sm-6">

                                                                        <label for="focusedinput" class="col-sm-4 control-label">Image</label>
                                                                        <div class="col-sm-6">
                                                                            <div class="fileinput fileinput-new" style="width: 100%;" data-provides="fileinput">
                                                                                <div class="fileinput-preview thumbnail mb20" data-trigger="fileinput" style="width: 100%; height: 150px;"></div>
                                                                                <div>
                                                                                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                                                    <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="img_employee" id="img_employee"></span>

                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                    </div>




                                                                </div>

                                                                <div class="form-group col-md-12">

                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Gender</label>
                                                                        <div class="col-sm-8">
                                                                            <select required="" class="form-control" id="cmb_gender" name="cmb_gender">

                                                                                <option value="Male">Male</option>
                                                                                <option value="Female">Female</option>
                                                                            </select>
                                                                        </div>

                                                                    </div>

                                                                    <!--                                                                        <div class="form-group col-sm-6 ">
                                                                                                                                                    <label class="col-sm-4 control-label">Status</label>
                                                                                                                                                    <div class="col-sm-8">
                                                                                                                                                        <label class="radio-inline icheck">
                                                                                                                                                            <input type="radio" id="inlineradio1" required="" value="Active" name="employee_status" > Active
                                                                                                                                                        </label>
                                                                                                                                                        <label class="radio-inline icheck">
                                                                                                                                                            <input type="radio" id="inlineradio2" value="Inactive" name="employee_status" > Inactive
                                                                                                                                                        </label>
                                                                                                                                                    </div>
                                                                                                                                                </div>-->

                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Designation <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" required="" id="cmb_desig" name="cmb_desig">


                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_desig as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->Des_ID; ?>"><?php echo $t_data->Desig_Name; ?></option>

                                                                                <?php }
                                                                                ?>

                                                                            </select>
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Department <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" required="" id="cmb_dep" name="cmb_dep">


                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_dep as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->Dep_ID; ?>"><?php echo $t_data->Dep_Name; ?></option>

                                                                                <?php }
                                                                                ?>

                                                                            </select>
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Employee Group <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" required="" id="cmb_group" name="cmb_group">


                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_grp as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->Grp_ID; ?>"><?php echo $t_data->EmpGroupName; ?></option>

                                                                                <?php }
                                                                                ?>

                                                                            </select>
                                                                        </div>

                                                                    </div>


                                                                    <!-- <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Roster Pattern <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" required="" id="cmb_roster_pattern" name="cmb_roster_pattern">

                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_Rstr as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->RosterCode; ?>"><?php echo $t_data->RosterName; ?></option>

                                                                                <?php }
                                                                                ?>
                                                                            </select>
                                                                        </div>

                                                                    </div> -->

                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">OT Pattern <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" required="" id="cmb_ot_pattern" name="cmb_ot_pattern">

                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_ot as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->OTCode; ?>"><?php echo $t_data->OTName; ?></option>

                                                                                <?php }
                                                                                ?>



                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <!--                                                                        <div class="form-group col-sm-6">
                                                                                                                                                    <label for="focusedinput" class="col-sm-4 control-label">OT Pattern</label>
                                                                                                                                                    <div class="col-sm-8">
                                                                                                                                                        <select class="form-control" id="cmb_ot_pattern" name="cmb_ot_pattern">
                                                                        
                                                                                                                                                            <option >--Select--</option>
                                                                                                                                                            <option value="HIGH">HIGH</option>
                                                                                                                                                            <option value="MEDIUM">MEDIUM</option>
                                                                                                                                                            <option value="LOW">LOW</option>
                                                                                                                                                        </select>
                                                                                                                                                    </div>
                                                                                                                                                </div>-->

                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Branch</label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" required="" id="cmb_branch" name="cmb_branch">
                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_branch as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->B_id; ?>"><?php echo $t_data->B_name; ?></option>

                                                                                <?php }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>



                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Appoint Date <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_appoint_date" name="txt_appoint_date" required="" placeholder="Ex: Select Date">
                                                                        </div>

                                                                    </div>


                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Permanent Date</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_permanent_date" name="txt_permanent_date" placeholder="Select Date">
                                                                        </div>

                                                                    </div>





                                                                    <div class="form-group col-sm-6 icheck-flat">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">EPF Liable</label>
                                                                        <div class="col-sm-8 icheck-flat">

                                                                            <label class="checkbox-inline icheck col-sm-5">
                                                                                <input type="checkbox" id="chk_epf" name="chk_epf">
                                                                            </label>
                                                                        </div>
                                                                    </div>

                                                                </div>







                                                                <div class="row">

                                                                </div>




                                                                <div class="tab-pane" id="vertical-form">

                                                                    <label style="font-weight: bold; color: #000">Payroll Details</label>
                                                                    <hr>



                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Basic Salary <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_basic_sal" name="txt_basic_sal" required="" placeholder="Ex: 25500">
                                                                        </div>

                                                                    </div>



                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Incentive <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_Incentive" name="txt_Incentive" required="" placeholder="Ex: 15500">
                                                                        </div>

                                                                    </div>


                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Budgetary Allowance 1 <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_BG_Allowance" name="txt_BG_Allowance1" required="" placeholder="Ex: 5500">
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Budgetary Allowance 2 <span style="color: red;">*</span></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_BG_Allowance" name="txt_BG_Allowance2" required="" placeholder="Ex: 5500">
                                                                        </div>

                                                                    </div>




                                                                    <!-- <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Bank</label>
                                                                        <div class="col-sm-8">
                                                                            <select class="form-control" id="cmb_bank" name="cmb_bank">

                                                                                <option value="" default>-- Select --</option>
                                                                                <?php foreach ($data_bank as $t_data) { ?>
                                                                                    <option value="<?php echo $t_data->Bnk_ID; ?>"><?php echo $t_data->bank_name; ?></option>

                                                                                <?php }
                                                                                ?>

                                                                            </select>
                                                                        </div>
                                                                    </div> -->

                                                                    <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">Bank ID</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="cmb_bank" name="cmb_bank" placeholder="Ex: 023">
                                                                            </div>
                                                                    </div>


                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Branch ID</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_B_Branch" name="txt_B_Branch" placeholder="Ex: 023">
                                                                        </div>
                                                                    </div>



                                                                    <div class="form-group col-sm-6">
                                                                        <label for="focusedinput" class="col-sm-4 control-label">Account No</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="txt_account" name="txt_account" placeholder="Ex: 112457854">
                                                                        </div>
                                                                    </div>

                                                                </div>


                                                                <div class="row">

                                                                </div>


                                                                <!--</div>-->


                                                                <div class="tab-pane" id="vertical-form">

                                                                    <label style="font-weight: bold; color: #000">Personal Details</label>
                                                                    <hr>

                                                                    <div class="form-group col-md-12">

                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">Full Address</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_address" name="txt_address" placeholder="Ex: No: 123, Street, City">
                                                                            </div>

                                                                        </div>

                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">City</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_city" name="txt_city" placeholder="Ex: No: 123, Street, City">
                                                                            </div>

                                                                        </div>

                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">District</label>
                                                                            <div class="col-sm-8">
                                                                                <select class="form-control" id="cmb_district" name="cmb_district">

                                                                                    <option>--Select--</option>
                                                                                    <option value="Unknown">Unknown</option>
                                                                                    <option value="Ampara">Ampara</option>
                                                                                    <option value="Anuradhapura">Anuradhapura</option>
                                                                                    <option value="Badulla">Badulla</option>
                                                                                    <option value="Batticaloa">Batticaloa</option>
                                                                                    <option value="Colombo">Colombo</option>
                                                                                    <option value="Galle">Galle</option>
                                                                                    <option value="Gampaha">Gampaha</option>
                                                                                    <option value="Hambantota">Hambantota</option>
                                                                                    <option value="Jaffna">Jaffna</option>
                                                                                    <option value="Kalutara">Kalutara</option>
                                                                                    <option value="Kegalle">Kegalle</option>
                                                                                    <option value="Kilinochchi">Kilinochchi</option>
                                                                                    <option value="Kurunegala">Kurunegala</option>
                                                                                    <option value="Kandy">Kandy</option>
                                                                                    <option value="Mannar">Mannar</option>
                                                                                    <option value="Matale">Matale</option>
                                                                                    <option value="Moneragala">Moneragala</option>
                                                                                    <option value="Mullaitivu">Mullaitivu</option>
                                                                                    <option value="NuwaraEliya">NuwaraEliya</option>
                                                                                    <option value="Polonnaruwa">Polonnaruwa</option>
                                                                                    <option value="Puttalam">Puttalam</option>
                                                                                    <option value="Ratnapura">Ratnapura</option>
                                                                                    <option value="Trincomalee">Trincomalee</option>
                                                                                    <option value="Vavuniya">Vavuniya</option>

                                                                                </select>
                                                                            </div>

                                                                        </div>
                                                                        <!--
                                                                                                                                                <div class="form-group col-sm-6">
                                                                                                                                                    <label for="focusedinput" class="col-sm-4 control-label">Provincetown</label>
                                                                                                                                                    <div class="col-sm-8">
                                                                                                                                                        <input type="text" class="form-control" id="txt_address" name="txt_address" placeholder="Ex: No: 123, Street, City">
                                                                                                                                                    </div>
                                                                            
                                                                                                                                                </div>-->

                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">Contact No (Home)</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_cont_home" name="txt_cont_home" placeholder="Ex: 0112 234 567">
                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">Contact No (Mobile) <span style="color: red;">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" required="" class="form-control" id="txt_cont_mobile" name="txt_cont_mobile" placeholder="Ex: 071 733 8110">
                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">E Mail</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_email" name="txt_email" placeholder="Ex: ashan.rathsara@gmail.com">
                                                                            </div>

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group col-md-12">

                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">NIC <span style="color: red;">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" required="" class="form-control" id="txt_nic" name="txt_nic" placeholder="Ex: 123456789V">
                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">Passport No</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_passport" name="txt_passport" placeholder="Ex: 1234567890">
                                                                            </div>

                                                                        </div>

                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">Date of Birth <span style="color: red;">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_dob" placeholder="Select Date" name="txt_dob" required="">
                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                    <div class="form-group col-md-12">
                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">Religion</label>
                                                                            <div class="col-sm-8">
                                                                                <select class="form-control" id="cmb_religin" name="cmb_religin">
                                                                                    <option value="Catholic">Unknown</option>
                                                                                    <option value="Buddhist">Buddhist</option>
                                                                                    <option value="Catholic">Catholic</option>
                                                                                    <option value="Catholic">Hindu</option>
                                                                                    <option value="Catholic">ISLAM</option>
                                                                                    <option value="Catholic">Other</option>

                                                                                </select>
                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                    <div class="form-group col-md-12">
                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-4 control-label">Civil Status</label>
                                                                            <div class="col-sm-8">
                                                                                <select class="form-control" id="cmb_civil_status" name="cmb_civil_status">

                                                                                    <option value="SINGLE">SINGLE</option>
                                                                                    <option value="MARRIED">MARRIED</option>
                                                                                    <option value="MARRIED">DIVORCED</option>
                                                                                </select>
                                                                            </div>

                                                                        </div>

                                                                    </div>








                                                                </div>



                                                                <div class="row">

                                                                </div>

                                                                <div class="tab-pane" id="bordered-row">

                                                                    <label style="font-weight: bold; color: #000">Family Details</label>
                                                                    <hr>
                                                                    <div class="form-horizontal">

                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-2 control-label">Blood Group</label>
                                                                            <div class="col-sm-8">
                                                                                <select class="form-control" id="cmb_blood" name="cmb_blood">

                                                                                    <option>--Select--</option>
                                                                                    <option value="A+">A+</option>
                                                                                    <option value="A-">A-</option>
                                                                                    <option value="B+">B+</option>
                                                                                    <option value="B-">B-</option>
                                                                                    <option value="AB">AB</option>
                                                                                    <option value="AB+">AB+</option>
                                                                                    <option value="O">O</option>
                                                                                    <option value="O+">O+</option>
                                                                                </select>
                                                                            </div>

                                                                        </div>


                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-2 control-label">Relation's Name</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_rel_name" name="txt_rel_name" placeholder="Mr. Ashan Rathsara">
                                                                            </div>

                                                                        </div>

                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-2 control-label">Relation's Contact No</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_rel_cont" name="txt_rel_cont" placeholder="Mr. 071 111 222">
                                                                            </div>

                                                                        </div>


                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-2 control-label">No of Children</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="txt_no_child" name="txt_no_child" placeholder="Ex: 4">
                                                                            </div>

                                                                        </div>


                                                                    </div>
                                                                </div>




                                                                <div class="row">

                                                                </div>


                                                                <div class="tab-pane" id="login">

                                                                    <label style="font-weight: bold; color: #000">Login Details</label>
                                                                    <hr>
                                                                    <div class="form-horizontal">

                                                                        <div class="form-group col-md-12">
                                                                            <!--                                                                                <div class="form-group col-sm-6">
                                                                                                                                                                    <div class="form-group icheck-flat">
                                                                                                                                                                        <label class="col-sm-2 control-label">Is Allow Login</label>
                                                                                                                                                                        <div class="col-sm-8 icheck-flat">
                                                                                
                                                                                                                                                                            <label class="checkbox-inline icheck ">
                                                                                                                                                                                <input type="checkbox" id="Is_Allow" name="Is_Allow">
                                                                                                                                                                            </label>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>-->


                                                                            <div class="form-group col-sm-6">
                                                                                <label for="focusedinput" class="col-sm-2 control-label">User Name <span style="color: red;">*</span></label>
                                                                                <div class="col-sm-8">
                                                                                    <input type="text" required="" class="form-control" id="txt_user_name" name="txt_user_name" placeholder="ashan">
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                        <!--                                                                            <div class="form-group col-md-12">
                                                                            
                                                                                                                                                            <div class="form-group col-sm-6">
                                                                                                                                                                <label for="focusedinput" class="col-sm-2 control-label">Password</label>
                                                                                                                                                                <div class="col-sm-8">
                                                                                                                                                                    <input type="password" class="form-control" id="txt_password" name="txt_password" placeholder="Mr. user login password">
                                                                                                                                                                </div>
                                                                            
                                                                                                                                                            </div>
                                                                            
                                                                            
                                                                            -->
                                                                        <div class="form-group col-sm-6">
                                                                            <label for="focusedinput" class="col-sm-2 control-label">User Level <span style="color: red;">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <select class="form-control" required="" id="cmb_user_level" name="cmb_user_level">


                                                                                    <option value="" default>-- Select --</option>
                                                                                    <?php foreach ($data_u_lvl as $t_data) { ?>
                                                                                        <option value="<?php echo $t_data->user_level_id; ?>"><?php echo $t_data->user_level_name; ?></option>

                                                                                    <?php }
                                                                                    ?>

                                                                                </select>
                                                                            </div>

                                                                        </div>

                                                                    </div>


                                                                </div>






                                                                <div class="row">

                                                                </div>


                                                                <div class="tab-pane" id="tabular-form">
                                                                    <div class="form-horizontal tabular-form">


                                                                        <div class="form-group col-md-12">
                                                                            <div class="form-group col-sm-6">
                                                                                <label for="focusedinput" class="col-sm-4 control-label">Remarks</label>
                                                                                <div class="col-sm-8">
                                                                                    <textarea type="text" class="form-control" id="txt_remarks" name="txt_remarks" placeholder="Ex: Remarks"></textarea>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <div class="form-group col-sm-6">
                                                                                <label for="focusedinput" class="col-sm-4 control-label">Highlights</label>
                                                                                <div class="col-sm-8">
                                                                                    <textarea type="text" class="form-control" id="txt_high" name="txt_high" placeholder="Ex:"></textarea>
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                </div>



                                                        </div>


                                                        <!--submit button-->
                                                        <?php $this->load->view('template/btn_submit.php'); ?>
                                                        <!--end submit-->

                                                        </form>

                                                        <br>

                                                        <div id="divmessage" class="">

                                                            <div id="spnmessage"> </div>
                                                        </div>

                                                    </div>
                                                </div>



                                            </div>
                                        </div>

                                    </div>


                                </div>

                            </div> <!-- .container-fluid -->
                        </div>
                        <!--Footer-->
                        <?php $this->load->view('template/footer.php'); ?>
                        <!--End Footer-->
                    </div>
                </div>
            </div>



            <!-- Load site level scripts -->

            <?php $this->load->view('template/js.php'); ?> <!-- Initialize scripts for this page-->


            <!-- Initialize scripts for this page-->
            <script src="<?php echo base_url(); ?>assets/plugins/form-jasnyupload/fileinput.min.js"></script>
            <!-- End loading page level scripts-->
            <!-- End loading page level scripts-->
            <!--Ajax-->
            <!--<script src="<?php echo base_url(); ?>system_js/Master/Employee.js"></script>-->


            <script>
                $('#txt_appoint_date').datepicker({
                    format: "dd/mm/yyyy",
                    "todayHighlight": true,
                    autoclose: true,
                    format: 'yyyy/mm/dd'
                }).on('changeDate', function(ev) {
                    $(this).datepicker('hide');
                });

                $('#txt_permanent_date').datepicker({
                    format: "dd/mm/yyyy",
                    "todayHighlight": true,
                    autoclose: true,
                    format: 'yyyy/mm/dd'
                }).on('changeDate', function(ev) {
                    $(this).datepicker('hide');
                });

                $('#txt_dob').datepicker({
                    format: "dd/mm/yyyy",
                    "todayHighlight": true,
                    autoclose: true,
                    format: 'yyyy/mm/dd'
                }).on('changeDate', function(ev) {
                    $(this).datepicker('hide');
                });
            </script>


            <script>
                $(document).ready(function() {
                    $("#img_employee").on('change', function() {
                        //Get count of selected files
                        var countFiles = $(this)[0].files.length;
                        var imgPath = $(this)[0].value;
                        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
                        var image_holder = $("#image-holder");
                        image_holder.empty();
                        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                            if (typeof(FileReader) != "undefined") {
                                //loop for each file selected for uploaded.
                                for (var i = 0; i < countFiles; i++) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        $("<img />", {
                                            "src": e.target.result,
                                            "class": "thumb-image"
                                        }).appendTo(image_holder);
                                    }
                                    image_holder.show();
                                    reader.readAsDataURL($(this)[0].files[i]);
                                }
                            } else {
                                alert("This browser does not support FileReader.");
                            }
                        } else {
                            alert("Pls select only images");
                        }
                    });
                });
            </script>


            <!--JQuary Validation-->
            <script type="text/javascript">
                $(document).ready(function() {
                    $("#frm_employee").validate();
                    $("#spnmessage").hide("shake", {
                        times: 5
                    }, 3500);
                });
            </script>


            <script type="text/javascript">
                $(document).ready(function() {


                    //the min chars for username
                    var min_chars = 3;

                    //result texts
                    var characters_error = 'Minimum amount of chars is 3';
                    var checking_html = 'Checking...';

                    //when button is clicked
                    $('#check_username_availability').click(function() {
                        //run the character number check
                        if ($('#txt_emp_no').val().length < min_chars) {
                            //if it's bellow the minimum show characters_error text '
                            $('#username_availability_result').html(characters_error);
                        } else {
                            //else show the cheking_text and run the function to check
                            $('#username_availability_result').html(checking_html);
                            check_availability();
                        }
                    });

                });

                //function to check username availability
                function check_availability() {

                    //get the username
                    var username = $('#txt_emp_no').val();

                    //use ajax to run the check
                    $.post(baseurl + "Employee_Management/ADD_Employees/check_emp", {
                            EmpNo: username
                        },
                        function(result) {
                            //if the result is 1
                            if (result == 1) {
                                //show that the username is available
                                $('#username_availability_result').html(username + ' is Available');
                            } else {
                                //show that the username is NOT available
                                $('#username_availability_result').html(username + ' is not Available');
                            }
                        });

                }
            </script>


</body>


</html>