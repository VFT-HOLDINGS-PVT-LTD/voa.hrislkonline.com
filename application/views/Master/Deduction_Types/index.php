<!DOCTYPE html>


<!--Description of dashboard page

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
                            <li class="active"><a href="index.html">DEDUCTION</a></li>

                        </ol>


                        <div class="page-tabs">
                            <ul class="nav nav-tabs">

                                <li class="active"><a data-toggle="tab" href="#tab1">DEDUCTION TYPES</a></li>
                                <li><a data-toggle="tab" href="#tab2">VIEW DEDUCTION</a></li>


                            </ul>
                        </div>
                        <div class="container-fluid">


                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">

                                    <div class="row">
                                        <div class="col-xs-12">


                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <h2>ADD DEDUCTION</h2>
                                                        </div>
                                                        <div class="panel-body">
                                                            <form class="form-horizontal" id="frm_deduction_types"
                                                                name="frm_deduction_types"
                                                                action="<?php echo base_url(); ?>Master/Deduction_Types/insert_data"
                                                                method="POST">
                                                                <div class="form-group col-sm-12">
                                                                    <div class="col-sm-8">
                                                                        <img style="margin-left: 30%; width: 100px; height: 100px;"
                                                                            src="<?php echo base_url(); ?>assets/images/deduction_types.png">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-sm-6">
                                                                    <label for="focusedinput"
                                                                        class="col-sm-4 control-label">Deduction Type
                                                                        Name</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" class="form-control"
                                                                            id="txt_deduction" name="txt_deduction"
                                                                            placeholder="Ex: Telephone Deduction">
                                                                    </div>

                                                                </div>


                                                                <div class="form-group col-sm-6 icheck-flat">
                                                                    <label class="col-sm-2 control-label"></label>
                                                                    <div class="col-sm-8 icheck-flat">
                                                                        <label class="checkbox green icheck col-sm-5"
                                                                            style="display: none;">
                                                                            <input type="checkbox" id="isFixed"
                                                                                value="1"> IS FIXED
                                                                        </label>
                                                                        <label class="checkbox-inline icheck col-sm-5"
                                                                            style="display: none;">
                                                                            <input type="checkbox" id="isFixed"
                                                                                value="1"> IS ACTIVE
                                                                        </label>
                                                                    </div>
                                                                </div>



                                                                <div class="row">
                                                                    <div class="col-sm-8 col-sm-offset-2">
                                                                        <button type="submit" id="submit"
                                                                            class="btn-primary btn fa fa-check">&nbsp;&nbsp;Submit</button>
                                                                        <button type="button" id="Cancel" name="Cancel"
                                                                            class="btn btn-danger-alt fa fa-times-circle">&nbsp;&nbsp;Cancel</button>
                                                                    </div>
                                                                </div>

                                                            </form>

                                                            <hr>
                                                            <div id="divmessage" class="">

                                                                <div id="spnmessage"> </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>


                                        </div>
                                    </div>

                                </div>
                                <!--***************************-->
                                <!-- Grid View -->
                                <div class="tab-pane" id="tab2">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="col-md-12">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h2>DEDUCTION DETAILS</h2>
                                                            <div class="panel-ctrls">
                                                            </div>
                                                        </div>
                                                        <div class="panel-body panel-no-padding">
                                                            <table id="example"
                                                                class="table table-striped table-bordered"
                                                                cellspacing="0" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>DEDUCTION NAME</th>
                                                                        <th>IS FIXED</th>

                                                                        <th>EDIT</th>
                                                                        <th>DELETE</th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($data_set as $data): ?>
                                                                        <tr class="odd gradeX">
                                                                            <td width="100"><?php echo $data->Ded_ID; ?>
                                                                            </td>
                                                                            <td width="100">
                                                                                <?php echo $data->Deduction_name; ?></td>
                                                                            <td width="100"><?php echo $data->isFixed; ?>
                                                                            </td>


                                                                            <td width="15">
                                                                                <button class="get_data btn btn-green"
                                                                                    data-toggle="modal"
                                                                                    data-target="#myModal" title="EDIT"
                                                                                    data-id="<?php echo $data->Ded_ID; ?>"
                                                                                    href=""><i
                                                                                        class="fa fa-edit"></i></button>
                                                                            </td>
                                                                            <td width="15">
                                                                                <button class="btn btn-danger"
                                                                                    data-toggle="modal"
                                                                                    href="javascript:void()" title="DELETE"
                                                                                    onclick="delete_id(<?php echo $data->Ded_ID; ?>)"><i
                                                                                        class="fa fa-times-circle"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                            <div class="panel-footer"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- End Grid View-->

                                <!-- Modal -->
                                <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-hidden="true">&times;</button>
                                                <h2 class="modal-title">DEDUCTION TYPES</h2>
                                            </div>
                                            <div class="modal-body">
                                                <form class="form-horizontal"
                                                    action="<?php echo base_url(); ?>Master/Deduction_Types/update_Data"
                                                    method="post">
                                                    <div class="form-group col-sm-12">
                                                        <label for="focusedinput"
                                                            class="col-sm-4 control-label">ID</label>
                                                        <div class="col-sm-8">
                                                            <input value="" type="text" class="form-control"
                                                                readonly="readonly" name="id" id="id"
                                                                class="m-wrap span3">
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-sm-12">
                                                        <label for="focusedinput"
                                                            class="col-sm-4 control-label">Deduction_name</label>
                                                        <div class="col-sm-8">
                                                            <input value="" type="text" name="leave_name" id="L_Name"
                                                                class="form-control m-wrap span6"><br>
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-sm-12" style="display: none;">
                                                        <label for="focusedinput" class="col-sm-4 control-label">Is
                                                            Fixed</label>
                                                        <div class="col-sm-8">
                                                            <label class="checkbox green col-sm-5">

                                                                <input type="checkbox" name="leavebf" id="leavebf">
                                                            </label>
                                                        </div>
                                                    </div>

                                            </div>

                                            <br>
                                            <!--<input class="btn green" type="submit" value="submit" id="submit">-->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" id="submit" class="btn btn-primary">Save
                                                    changes</button>
                                            </div>
                                            </form>
                                        </div>

                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->




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

            <script>
                function delete_id(id) {
                    swal({ title: "Are you sure?", text: "You will not be able to recover this data!", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "Yes, Delete This!", cancelButtonText: "No, Cancel This!", closeOnConfirm: false, closeOnCancel: false },
                        function (isConfirm) {
                            if (isConfirm) {

                                $.ajax({
                                    url: baseurl + "index.php/Master/Deduction_Types/ajax_delete/" + id,
                                    type: "POST",
                                    dataType: "JSON",
                                    success: function (data) {

                                        //if success reload ajax table
                                        $('#modal_form').modal('hide');
                                        reload_table();
                                    }

                                });


                                swal("Deleted!", "Selected data has been deleted.", "success");


                                $(document).ready(function () {
                                    setTimeout(function () {
                                        window.location.replace(baseurl + "Master/Deduction_Types/");
                                    }, 1000);
                                });


                            } else {
                                swal("Cancelled", "Selected data Cancelled", "error");

                            }

                        });

                }
            </script>

            <!--Ajax-->
            <!-- <script src="<?php echo base_url(); ?>system_js/Master/L_Types.js"></script> -->
            <script>
                $(".get_data").click(function () {

                    var id = $(this).attr("data-id");
                    $.ajax({
                        type: "POST",
                        url: baseurl + "index.php/Master/Deduction_Types/get_details",
                        data: {
                            'id': id
                        },
                        dataType: "JSON",
                        success: function (response) {

                            $('#L_Name').val(response[0].Deduction_name);

                            $('#id').val(response[0].Ded_ID);

                            if (response[0].isFixed == 1) {
                                document.getElementById("leavebf").checked = true;
                            } else {
                                document.getElementById("leavebf").checked = false;
                            }


                        }
                    });
                });
            </script>

</body>


</html>