<!DOCTYPE html>


<!--Description of dashboard page

@author Ashan Rathsara-->

<div class="panel panel-primary">
    <div class="panel panel-default">
        <div class="panel-body panel-no-padding">
            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>EMP NO</th>
                        <th>NAME</th>
                        <th>LOAN</th>
                        <th>DATE</th>
                        <th>YEAR</th>
                        <th>AMOUNT</th>
                        <th>PAID</th>
                        <th>BALANCE</th>
                        <th>SETTLE</th>


                        <th>DELETE</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data_set as $data) {



                        echo "<tr class='odd gradeX'>";

                        echo "<td width='100'>" . $data->EmpNo . "</td>";
                        echo "<td width='100'>" . $data->Emp_Full_Name . "</td>";
                        echo "<td width='100'>" . $data->loan_name . "</td>";
                        echo "<td width='100'>" . $data->Loan_date . "</td>";
                        echo "<td width='100'>" . $data->Year . "</td>";
                        echo "<td width='100'>" . $data->FullAmount . "</td>";
                        echo "<td width='100'>" . $data->Paid_Amount . "</td>";
                        echo "<td width='100'>" . $data->Balance_amount . "</td>";
                        echo "<td width='100'>" . $data->Is_Settled . "</td>";




                        echo "<td width='15'>";
                        echo "<button  class=' btn btn-danger' data-toggle='modal' href='javascript:void()' title='DELETE' onclick='delete_id($data->ID)'><i class='fa fa-times-circle'></i></a>";
                        echo "</td>";

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="panel-footer"></div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title">EDIT ALLOWANCE</h2>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="<?php echo base_url(); ?>Payroll/Allowance/edit" method="post">
                    <div class="form-group col-sm-12">
                        <label for="focusedinput" class="col-sm-4 control-label">ID</label>
                        <div class="col-sm-8">
                            <input value="<?php echo $data->ID; ?>" type="text" class="form-control" readonly="readonly" name="id" id="id" class="m-wrap span3" >
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <label for="focusedinput" class="col-sm-4 control-label">NAME</label>
                        <div class="col-sm-8">
                            <input value="<?php echo $data->Emp_Full_Name; ?>" type="text" name="Name" id="Name"  class="form-control m-wrap span6"><br>
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <label for="focusedinput" class="col-sm-4 control-label">ALLOWANCE NAME</label>
                        <div class="col-sm-8">
                            <input value="<?php echo $data->Allowance_name; ?>" type="text" name="allowance" id="allowance"  class="form-control m-wrap span6"><br>
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <label for="focusedinput" class="col-sm-4 control-label">AMOUNT</label>
                        <div class="col-sm-8">
                            <input value="<?php echo $data->Amount; ?>" type="text" name="amount" id="amount"  class="form-control m-wrap span6"><br>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>

            </div>

            <br>
            <!--<input class="btn green" type="submit" value="submit" id="submit">-->

        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
    //Get  Data
    $(".get_data").click(function () {

        var ID = $(this).attr("data-id");
        $.ajax({
            type: "POST",
            url: baseurl + "index.php/Payroll/Allowance/get_details",
            data: {'id': ID},
            dataType: "JSON",
            success: function (response) {
//                    alert(response);
                for (var i = 0; i < response.length; i++) {
                    $('#id').val(response[i].ID);
                    $('#Name').val(response[i].Emp_Full_Name);
                    $('#allowance').val(response[i].Allowance_name);
                    $('#amount').val(response[i].Amount);

                }
            }
        });
    });
</script>


<script type="text/javascript">
    function delete_id(id)
    {
        swal({title: "Are you sure?", text: "You will not be able to recover this data!", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "Yes, Delete This!", cancelButtonText: "No, Cancel This!", closeOnConfirm: false, closeOnCancel: false},
                function (isConfirm) {
                    if (isConfirm) {

                        $.ajax({
                            url: baseurl + "index.php/Pay/Loan_Entry/ajax_delete/" + id,
                            type: "POST",
                            dataType: "JSON",
                            success: function (data)
                            {

                                //if success reload ajax table
                                $('#modal_form').modal('hide');
                                reload_table();
                            }

                        });


                        swal("Deleted!", "Selected data has been deleted.", "success");


                        $(document).ready(function () {
                            setTimeout(function () {
                                window.location.replace(baseurl + "Pay/Loan_Entry");
                            }, 1000);
                        });


                    } else {
                        swal("Cancelled", "Selected data Cancelled", "error");

                    }

                });

    }
</script>