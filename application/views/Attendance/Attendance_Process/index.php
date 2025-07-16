<!DOCTYPE html>


<!--Description of Attendance Collection page

@author Ashan Rathsara-->


<html lang="en">


<head>
    <!-- Styles -->
    <?php $this->load->view('template/css.php'); ?>

</head>

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
                        <ol class="breadcrumb">

                            <li class=""><a href="index.html">HOME</a></li>
                            <li class="active"><a href="index.html">ATTENDANCE PROCESS</a></li>

                        </ol>


                        <div class="page-tabs">
                            <ul class="nav nav-tabs">

                                <li class="active"><a data-toggle="tab" href="#tab1">ATTENDANCE PROCESS</a></li>


                            </ul>
                        </div>
                        <div class="container-fluid">


                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">

                                    <div class="row">
                                        <div class="col-xs-12">


                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div style="" class="panel ">
                                                        <div style="background: rgb(59,105,129);
                                                                 background: linear-gradient(60deg, rgba(59,105,129,1) 0%, rgba(54,120,150,0.644782913165266) 100%); "
                                                            class="panel-heading">
                                                            <h2 style="color: #ffffff">ATTENDANCE PROCESS   <?php echo 0 ?> Count <?php echo $WData ?></h2>
                                                        </div>
                                                        <div class="panel-body">

                                                        <div class="form-horizontal">
                                                            <form
                                                                action="<?php echo base_url(); ?>index.php/Attendance/Attendance_Process_New/emp_attendance_process"
                                                                class="form-horizontal" id="frmBackup" name="frmBackup"
                                                                method="POST">

                                                                <!--success Message-->
                                                                <?php if (isset($_SESSION['success_message']) && $_SESSION['success_message'] != '') { ?>
                                                                    <div id="spnmessage"
                                                                        class="alert alert-dismissable alert-success success_redirect">
                                                                        <strong>Success !</strong>
                                                                        <?php echo $_SESSION['success_message'] ?>
                                                                    </div>
                                                                <?php } ?>

                                                                <!--Error Message-->
                                                                <?php if (isset($_SESSION['error_message']) && $_SESSION['error_message'] != '') { ?>
                                                                    <div id="spnmessage"
                                                                        class="alert alert-dismissable alert-danger error_redirect">
                                                                        <strong>Error !</strong>
                                                                        <?php echo $_SESSION['error_message'] ?>
                                                                    </div>
                                                                <?php } ?>
                                                                
                                                                <?php
                                                                    if ($WData == 0) {
                                                                ?>


                                                                <!--Employees without allocate shift-->
                                                                <?php if (count($sh_employees) > 0) { ?>
                                                                    <h4 style="color: #000">* Please Allocate Shift for
                                                                        following employees for First Time </h4>
                                                                    <div style="color: red; font-size: 20px; ">

                                                                        <?php
                                                                        foreach ($sh_employees as $t_data) {
                                                                            echo $t_data->EmpNo . ", ";
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                <?php }
                                                                
                                                                    } else {
                                                                ?>
                                                                <h4 style="color: #000">* Since there are more
                                                                    attendances to be processed, please click the
                                                                    <span style="color: red">'Process Attendances'</span> button again. </h4>
                                                                    <div style="color: red; font-size: 20px; ">
                                                                    <?php
                                                                        echo $WData;
                                                                    ?> records have not been processed yet.
                                                                </div>  
                                                                <?php } ?>
                                                                
                                                                <br><br>




                                                                <div id="search_body">

                                                                </div>

                                                                <div class="form-group col-sm-12">
                                                                    <div class="col-sm-8">
                                                                        <img class=""
                                                                            style="width: 200px; margin-left: 20%;"
                                                                            src="<?php echo base_url(); ?>assets/images/ethics_4474254.png">
                                                                    </div>
                                                                </div>
                                                                <br><br><br><br><br><br><br><br>

                                                           <?php
                                                                    if ($WData == 0) {
                                                                ?>

                                                                <div class="form-group col-sm-6">
                                                                    <label for="focusedinput"
                                                                        class="col-sm-4 control-label">Month</label>
                                                                    <div class="col-sm-8">
                                                                        <select required="" class="form-control"
                                                                            id="cmb_month" name="cmb_month">
                                                                            <option value="">--Select--</option>
                                                                            <option value="1">January</option>
                                                                            <option value="2">February</option>
                                                                            <option value="3">March</option>
                                                                            <option value="4">April</option>
                                                                            <option value="5">May</option>
                                                                            <option value="6">June</option>
                                                                            <option value="7">July</option>
                                                                            <option value="8">August</option>
                                                                            <option value="9">September</option>
                                                                            <option value="10">October</option>
                                                                            <option value="11">November</option>
                                                                            <option value="12">December</option>
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                                <?php 
                                                                
                                                                    } else {
                                                                ?>
                                                                <div class="form-group col-sm-6">
                                                                    <label for="focusedinput"
                                                                        class="col-sm-4 control-label">Month</label>
                                                                    <div class="col-sm-8">
                                                                          <select required="" class="form-control"
                                                                            id="cmb_month" name="cmb_month">

                                                                            <?php
                                                                                if ($MData == 1) {
                                                                                    ?>
                                                                            <option value="1" selected>January</option>
                                                                            <?php
                                                                                } else if ($MData == 2) {
                                                                                    ?>
                                                                            <option value="2" selected>February</option>
                                                                            <?php
                                                                                } else if ($MData == 3) {
                                                                                    ?>
                                                                            <option value="3" selected>March</option>
                                                                            <?php
                                                                                } else if ($MData == 4) {
                                                                                    ?>
                                                                            <option value="4" selected>April</option>
                                                                                <?php
                                                                                    } else if ($MData == 5) {
                                                                                        ?>
                                                                            <option value="5" selected>May</option>
                                                                                <?php
                                                                                    } else if ($MData == 6) {
                                                                                        ?>
                                                                            <option value="6" selected>June</option>
                                                                                <?php
                                                                                    } else if ($MData == 7) {
                                                                                        ?>
                                                                            <option value="7" selected>July</option>
                                                                            <?php
                                                                                } else if ($MData == 8) {
                                                                                    ?>
                                                                            <option value="8" selected>August</option>
                                                                            <?php
                                                                                } else if ($MData == 9) {
                                                                                    ?>
                                                                            <option value="9" selected>September</option>
                                                                            <?php
                                                                                } else if ($MData == 10) {
                                                                                    ?>
                                                                            <option value="10" selected>October</option>
                                                                            <?php
                                                                                } else if ($MData == 11) {
                                                                                    ?>
                                                                            <option value="11" selected>November</option>
                                                                            <?php
                                                                                } else if ($MData == 12) {
                                                                                    ?>
                                                                            <option value="12" selected>December</option>
                                                                            <?php
                                                                                } else {
                                                                                    ?>
                                                                            <option value="">--Select--</option>
                                                                                <?php
                                                                                    }
                                                                                    ?>
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                                
                                                                <?php
                                                                    }
                                                                ?>

                                                                <br><br><br>
                                                                <!--submit button-->
                                                                <div class="row">
                                                                    <div class="col-sm-8 col-sm-offset-2">
                                                                        <button type="submit" id="submit" name="submit"
                                                                            class="btn-success btn-lg fa fa-check">&nbsp;&nbsp;ATTENDANCE
                                                                            PROCESS</button>
                                                                        <!--<button type="button" id="Cancel" name="Cancel" class="btn btn-danger-alt fa fa-times-circle">&nbsp;&nbsp;CANCEL</button>-->
                                                                    </div>
                                                                </div>
                                                                <!--end submit-->


                                                            </form>
                                                        </div>
                                                           

                                                            <hr>

                                                            <div id="divmessage" class="">
                                                                <button type="button" class="close" data-dismiss="alert"
                                                                    aria-hidden="true">&times;</button>
                                                                <div id="spnmessage"> </div>

                                                            </div>

                                                            <div id="search_body">

                                                            </div>


                                                        </div>

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

        <!-- End loading page level scripts-->



        <script src="<?php echo base_url(); ?>assets/plugins/form-jasnyupload/fileinput.min.js"></script>
        <!-- File Input -->

        <!--JQuary Validation-->
        <script type="text/javascript">
            $(document).ready(function () {
                $("#frm_leave_entry").validate();
                $("#spnmessage").hide(5500);
            });
        </script>


        <script>

            $("#success_message_my").hide("bounce", 2000, 'fast');


            $("#submit").click(function () {
                $('#search_body').html('<center><p><img style="width: 50;height: 50;" src="<?php echo base_url(); ?>assets/images/processing.gif" /></p><center>');

            });


        </script>


</body>


</html>