@extends('adminlte::page')
@section('title', 'FMDB')
@section('content')
<section class="content">
       <div class="row">
        <div class="col-xs-4">
            <span style="font-size:24px">Users</span>
        </div>
        <div class="col-xs-8" align="right">
            <span href="#" class="btn btn-sm btn-flat btn-success btn-add">&nbsp;<i class="glyphicon glyphicon-plus" title="Add new data"></i>&nbsp; Add</span>
        </div>
    </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
             <div class="box-body">
                <table id="data-table" class="table table-bordered table-hover table-condensed" width="100%">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Job Code</th>
                            <th>NIK</th>
                            <th>Area Code</th>
                            <th width="10%">Active</th>
                            <th width="8%">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- /.box-body -->
            </div>
          </div>
        </div>
      </div>
</section>
<div id="add-data-modal" class="modal fade" role="dialog">
    <div class="modal-dialog" width="900px">
		<div class="modal-content">
			<div class="modal-header">	
				<h4 class="modal-title"></h4>
			</div>
			<form id="data-form">
                <div class="modal-body">	
                    <div class="box-body">
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Username</label> 
                            <input type="text" class="form-control" name="username" id="username" maxlength="50" required>
                            <input type="hidden" name='edit_id' id="edit_id">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Nama</label> 
                            <input class="form-control" name='name' id="name" maxlength="200" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Email</label> 
                            <input type="email" class="form-control"  name='email' id="email" maxlength="250">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Job Code</label> 
                            <input type="text" class="form-control"  name='job_code' id="job_code" maxlength="150">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">NIK</label> 
                            <input type="text" class="form-control"  name='nik' id="nik" maxlength="80">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Area Code</label> 
                            <input type="text" class="form-control"  name='area_code' id="area_code" maxlength="200">
                        </div>
                    </div>	 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" style="margin-right: 5px;">Submit</button>
                </div>
            </form>
		</div>
    </div>
</div>
@stop
@section('js')
<script>
    var attribute = [];
    jQuery(document).ready(function() {
         jQuery('#data-table').DataTable({
            ajax: '{!! route('get.grid_tr_user') !!}',
            columns: [
                { data: 'USERNAME', name: 'username' },
                { data: 'NAMA', name: 'name' },
                { data: 'EMAIL', name: 'email' },
                { data: 'JOB_CODE', name: 'job_code' },
                { data: 'NIK', name: 'nik' },
                { data: 'AREA_CODE', name: 'area_code' },
                {  
                    "render": function (data, type, row) {
                        if(row.FL_ACTIVE == 1) {
                            var content = '<span class="badge bg-green">Y</span>';
                        } else{
                            var content = '<span class="badge bg-grey">N</span>';
                        }    
                        return content;
                    } 
                },
                {
                    "render": function (data, type, row) {
                        var content = '<button class="btn btn-flat btn-xs btn-success btn-action btn-edit" title="edit data ' + row.USER_ID + '" onClick="edit(' + row.USER_ID + ')"><i class="fa fa-pencil"></i></button>';
                            content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-activated ' + (row.FL_ACTIVE == 1 ? '' : 'hide') + '" onClick="inactive(' + row.USER_ID + ')"><i class="fa fa-trash"></i></button>';
                            content += '<button class="btn btn-flat btn-xs btn-success btn-action btn-inactivated ' + (row.FL_ACTIVE == 0 ? '': 'hide') + '" onClick="active(' + row.USER_ID + ')"><i class="fa fa-check"></i></button>';
                        
                        return content;
                    }
                } 
            ],
             columnDefs: [
                { targets: [7], className: 'text-center', orderable: false},
                { targets: [6], className: 'text-center'}
            ]
        }); 

        jQuery('.btn-add').on('click', function() {
            document.getElementById("data-form").reset();
            jQuery("#edit_id").val("");
            jQuery("#add-data-modal").modal({backdrop:'static', keyboard:false});		
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i> Create new data");		
            jQuery("#add-data-modal").modal("show");		
        });
        
        jQuery('.btn-edit').on('click', function() {
            jQuery("#add-data-modal").modal({backdrop:'static', keyboard:false});		
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-pencil'></i> Edit data");		
            jQuery("#add-data-modal").modal("show");		
        });

        jQuery('#data-form').on('submit', function(e) {
            e.preventDefault();
            var param = jQuery(this).serialize();
           jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            jQuery.ajax({
				url:"{{ url('users/post') }}",
				method:"POST",
				data: param,
				beforeSend:function(){ jQuery('.loading-event').fadeIn();},
				success:function(result){
                    if(result.status){
                        jQuery("#add-data-modal").modal("hide");
                        jQuery("#data-table").DataTable().ajax.reload();
                        notify({
                            type:'success',
                            message:result.message
                        });
                    }else{
                        notify({
                            type:'warning',
                            message:result.message
                        });
                    } 
				},
				complete:function(){ jQuery('.loading-event').fadeOut();}
			 }); 
            
            
        })
    });

    function edit(id) {
        document.getElementById("data-form").reset();
        jQuery("#edit_id").val(id);

        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('users/edit/?id=') }}"+id)));
        jQuery("#edit_id").val(result[0].USER_ID);
        jQuery("#username").val(result[0].USERNAME);
        jQuery("#name").val(result[0].NAMA);
        jQuery("#email").val(result[0].EMAIL);
        jQuery("#job_code").val(result[0].JOB_CODE);
        jQuery("#nik").val(result[0].NIK);
        jQuery("#area_code").val(result[0].AREA_CODE);

        jQuery("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data");			
        jQuery("#add-data-modal").modal("show");
    }

    function inactive(id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url:"{{ url('users/inactive') }}",
            method:"POST",
            data: {id:id},
            beforeSend:function(){ jQuery('.loading-event').fadeIn();},
            success:function(result){
                if(result.status){
                    jQuery("#data-table").DataTable().ajax.reload();
                    notify({
                        type:'success',
                        message:result.message
                    });
                }else{
                    notify({
                        type:'warning',
                        message:result.message
                    });
                } 
            },
            complete:function(){ jQuery('.loading-event').fadeOut();}
        }); 
    }
    
    function active(id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url:"{{ url('users/active') }}",
            method:"POST",
            data: {id:id},
            beforeSend:function(){ jQuery('.loading-event').fadeIn();},
            success:function(result){
                if(result.status){
                    jQuery("#data-table").DataTable().ajax.reload();
                    notify({
                        type:'success',
                        message:result.message
                    });
                }else{
                    notify({
                        type:'warning',
                        message:result.message
                    });
                } 
            },
            complete:function(){ jQuery('.loading-event').fadeOut();}
        }); 
    }


</script>            
@stop