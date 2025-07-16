<!DOCTYPE html>


<!--Description of dashboard page

@author Ashan Rathsara-->


<html lang="en">



    <head>
        <title><?php echo $title ?></title>
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

                                <li class=""><a href="">HOME</a></li>
                                <li class="active"><a href="">PAY SHEET REPORT</a></li>

                            </ol>

                            <div class="container-fluid">


                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab1">

                                        <div class="row">
                                            <div class="col-xs-12">


                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="panel panel-primary">
                                                            <div class="panel-heading"><h2>PAY SHEET REPORT</h2></div>

                                                            <div class="panel-body">
                                                                <div style="font-size: 15px; font-weight: bold;" class="bg-bright">REPORT BY CATEGORIES</div>
                                                                <form action="<?php echo base_url(); ?>Reports/Payroll/Paysheet/Pay_sheet_Report_By_Cat" class="form-horizontal" id="frm_in_out_rpt" name="frm_in_out_rpt" method="POST">
    <div class="form-group col-sm-12">
        <div class="col-sm-6">
            <img class="imagecss" src="<?php echo base_url(); ?>assets/images/paysheet.png">
        </div>
    </div>

    <div class="form-group col-sm-3">
        <label for="cmb_year" class="col-sm-4 control-label">Year</label>
        <div class="col-sm-8">
            <select required class="form-control" id="cmb_year" name="cmb_year">
                <option value="" default>-- Select --</option>
                <?php for ($year = 2023; $year <= 2030; $year++) { ?>
                    <option value="<?= $year; ?>"><?= $year; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group col-sm-3">
        <label for="cmb_month" class="col-sm-4 control-label">Month</label>
        <div class="col-sm-8">
            <select required class="form-control" id="cmb_month" name="cmb_month">
                <option value="">--Select--</option>
                <?php 
                $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                foreach ($months as $index => $month) { ?>
                    <option value="<?= $index + 1; ?>"><?= $month; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group col-sm-3">
        <label for="cmb_branch" class="col-sm-4 control-label">Branch</label>
        <div class="col-sm-8">
            <select class="form-control" id="cmb_branch" name="cmb_branch">
                <option value="" default>-- Select --</option>
                <?php foreach ($data_branch as $t_data) { ?>
                    <option value="<?= $t_data->B_id; ?>"><?= $t_data->B_name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group col-sm-3">
        <label for="cmb_departments" class="col-sm-4 control-label">Departments</label>
        <div class="col-sm-8">
            <select class="form-control" id="cmb_departments" name="cmb_departments">
                <option value="" default>-- Select --</option>
                <?php foreach ($data_dep as $t_data) { ?>
                    <option value="<?= $t_data->Dep_ID; ?>"><?= $t_data->Dep_Name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group col-sm-3">
        <label for="cmb_ass_departments" class="col-sm-4 control-label">Assigned Department</label>
        <div class="col-sm-8">
            <select class="form-control" id="cmb_ass_departments" name="cmb_ass_departments">
                <option value="" default>-- Select --</option>
                <?php foreach ($data_ass_c as $t_data) { ?>
                    <option value="<?= $t_data->ass_dep_id; ?>"><?= $t_data->ass_dep_name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="col-sm-6">
        <input type="submit" id="search" name="search" formtarget="_blank" class="btn-green btn fa fa-check" value="&nbsp;&nbsp;VIEW&nbsp;REPORT">
        <button type="button" id="search1" name="search1" class="btn-green btn fa fa-check">&nbsp;&nbsp;VIEW&nbsp;EXCEL REPORT</button>
        <button type="button" id="cancel" name="cancel" class="btn-danger-alt btn fa fa-check">&nbsp;&nbsp;CLEAR</button>
    </div>
</form>

                                                                <hr>


                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                </div> <!-- .container-fluid -->
                            </div>
                        </div>
                    </div>
                    <!--Footer-->
                    <?php $this->load->view('template/footer.php'); ?>
                    <!--End Footer-->
                </div>
            </div>
        </div>



        <!-- Load site level scripts -->

        <?php $this->load->view('template/js.php'); ?>							<!-- Initialize scripts for this page-->
        
        <script>
        $(document).ready(function () {
    $("#search1").click(function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Collect form data
        var formData = {
            txt_emp: $("#txt_emp").val(),
            txt_emp_name: $("#txt_emp_name").val(),
            cmb_desig: $("#cmb_desig").val(),
            cmb_departments: $("#cmb_departments").val(), // Updated department select ID
            cmb_ass_departments: $("#cmb_ass_departments").val(), // Added assigned department
            cmb_branch: $("#cmb_branch").val(), // Corrected branch select ID
            cmb_comp: $("#cmb_comp").val(),
            cmb_year: $("#cmb_year").val(),
            cmb_month: $("#cmb_month").val() // Corrected month select ID
        };
        
        // alert (formData.cmb_year);

        // Perform the AJAX request
        $.ajax({
            url: "<?php echo base_url(); ?>Reports/Payroll/Paysheet/Pay_sheet_Report_By_Cat_excel",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                // Open a new window and inject the HTML response
                var newWindow = window.open("", "_blank");
                newWindow.document.write(response.html);
                newWindow.document.close(); // Close the document stream

                // Trigger the Excel export after the page is loaded
                newWindow.document.addEventListener('DOMContentLoaded', function () {
                    var table = newWindow.document.getElementById('pay_sheet_table'); // Updated table ID
                    var ws = XLSX.utils.table_to_sheet(table);
                    var wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Pay Sheet Summary'); // Updated sheet name
                    XLSX.writeFile(wb, 'Pay_Sheet_Report.xlsx'); // Updated file name
                });
            },
            error: function (xhr, status, error) {
                console.error("Error: " + error);
            }
        });
    });

    $("#cancel").click(function () {
        // Reset all the form fields
        $("#frm_in_out_rpt")[0].reset();
    });
});


    </script>
    <!-- End loading page level scripts-->

        <!-- End loading page level scripts-->

        <!--Clear Text Boxes-->
        <script type="text/javascript">

            $("#cancel").click(function () {

                $("#txt_emp").val("");
                $("#txt_emp_name").val("");
                $("#cmb_desig").val("");
                $("#cmb_dep").val("");
                $("#cmb_comp").val("");
                $("#txt_nic").val("");
                $("#cmb_gender").val("");
                $("#cmb_status").val("");


            });
        </script>

        <!--Date Format-->
        <script>

            $('#dpd1').datepicker({
                format: "dd/mm/yyyy",
                "todayHighlight": true,
                autoclose: true,
                format: 'yyyy/mm/dd'
            }).on('changeDate', function (ev) {
                $(this).datepicker('hide');
            });
            $('#dpd2').datepicker({
                format: "dd/mm/yyyy",
                "todayHighlight": true,
                autoclose: true,
                format: 'yyyy/mm/dd'
            }).on('changeDate', function (ev) {
                $(this).datepicker('hide');
            });


        </script>

        <!--JQuary Validation-->
        <script type="text/javascript">
            $(document).ready(function () {
                $("#frm_in_out_rpt").validate();
                $("#spnmessage").hide("shake", {times: 4}, 1500);
            });
        </script>


        <!--Auto complete-->
        <script type="text/javascript">
            $(function () {
                $("#txt_emp_name").autocomplete({
                    source: "<?php echo base_url(); ?>Reports/Attendance/Report_Attendance_In_Out/get_auto_emp_name" // path to the get_birds method
                });
            });

            $(function () {
                $("#txt_emp").autocomplete({
                    source: "<?php echo base_url(); ?>Reports/Attendance/Report_Attendance_In_Out/get_auto_emp_no" // path to the get_birds method
                });
            });
        </script>





    </body>


</html>