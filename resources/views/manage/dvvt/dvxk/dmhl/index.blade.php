<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 4/16/2016
 * Time: 10:55 PM
 */
        ?>
@extends('main')

@section('custom-style')
    <link rel="stylesheet" type="text/css" href="{{url('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{url('assets/global/plugins/select2/select2.css')}}"/>
@stop

@section('custom-script')
    <script type="text/javascript" src="{{url('assets/global/plugins/select2/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{url('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{url('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js')}}"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script src="{{url('assets/admin/pages/scripts/table-managed.js')}}"></script>
    <script>
        jQuery(document).ready(function() {
            TableManaged.init();
        });
    </script>
@stop

@section('content')
    <h3 class="page-title">
        Vận tải hành khách bằng xe ôtô<small> giá hàng lý vượt quy định</small>
    </h3>
    <input type="hidden" name="maxa" id="maxa" value="{{$maxa}}">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box">
                <div class="portlet-title">
                    <div class="actions">
                        @if($per['create'])
                            <button type="button" id="_btnThemDV" class="btn btn-default btn-sm" onclick="addDVXK()" ><i class="fa fa-plus"></i>&nbsp;Thêm mới</button>
                        @endif
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover" id="sample_3">
                            <thead>
                            <tr>
                                <th style="text-align: center">STT</th>
                                <th style="text-align: center">Mô tả dịch vụ</th>
                                <th style="text-align: center">Quy cách chất lượng</th>
                                <th style="text-align: center">Đơn vị tính</th>
                                <th style="text-align: center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody id="noidung">
                            @foreach($model as $key=>$dv)
                                <tr class="odd gradeX">
                                    <td style="text-align: center">{{$key+1}}</td>
                                    <td name="tendichvu" class="active">{{$dv->tendichvu}}</td>
                                    <td name="qccl">{{$dv->qccl}}</td>
                                    <td name="dvt" style="text-align: center">{{$dv->dvt}}</td>
                                    <td>
                                        @if($per['edit'])
                                            <button type="button" class="btn btn-default btn-xs mbs" onclick="editDVXK({{$dv->id}})"><i class="fa fa-edit"></i>&nbsp;Chỉnh sửa</button>
                                        @endif
                                        @if($per['delete'])
                                            <button type="button" onclick="confirmDel('{{$dv->id}}')" class="btn btn-default btn-xs mbs" data-target="#del-modal-confirm" data-toggle="modal"><i class="fa fa-trash-o"></i>&nbsp;
                                            Xóa</button>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>
        </div>
    </div>

    <!--Modal thông tin dịch vụ vận tải xe khách-->

    <div class="modal fade bs-modal-lg" id="dvxk-modal-confirm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Thông tin giá hàng lý</h4>
                </div>
                <div class="modal-body">
                    @include('manage.dvvt.template.dmdv')
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Hủy thao tác</button>
                    <button type="submit" id="submit" name="submit" value="submit" class="btn btn-primary" onclick="confirmDVXK()">Đồng ý</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <script>
        function confirmDVXK(){
            var valid=true;
            var message='';
            var tendichvu= $('#tendichvu').val();
            var dvt= $('#dvt').val();

            if(tendichvu==''){
                valid=false;
                message +='Tên dịch vụ không được bỏ trống \n';
            }

            if(dvt==''){
                valid=false;
                message +='Đơn vị tính không được bỏ trống \n';
            }


            //return false;
            if(valid){
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{$url}}'+'danh_muc_hl/add',
                    type: 'GET',
                    data: {
                        _token: CSRF_TOKEN,
                        maxa: $('#maxa').val(),
                        tendichvu: tendichvu,
                        qccl: $('#qccl').val(),
                        dvt: dvt,
                        ghichu: $('#ghichu').val(),
                        id: $('#iddv').val()
                    },
                    dataType: 'JSON',
                    success: function (data) {
                        //alert(data.message);
                        if (data.status == 'success') {
                            location.reload();
                            //$('#noidung').replaceWith(data.message);
                        }
                    },
                    error: function(message){
                        alert(message);
                    }
                });//
                $('#dvxk-modal-confirm').modal('hide');
            }else{
                alert(message);
            }
            return valid;
        }

        function editDVXK(id){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{$url}}'+'danh_muc_hl/get',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    id: id
                },
                dataType: 'JSON',
                success: function (data) {
                    $('#tendichvu').val(data.tendichvu);
                    $('#qccl').val(data.qccl);
                    $('#dvt').val(data.dvt);
                    $('#ghichu').val(data.ghichu);
                },
                error: function (message) {
                    toastr.error(message, 'Lỗi!');
                }
            });
            $('#iddv').attr('value',id);
            $('#dvxk-modal-confirm').modal('show');
        }

        function addDVXK(){
            $('#iddv').attr('value',0);
            $('#tendichvu').attr('value','');
            $('#qccl').attr('value','');
            $('#dvt').attr('value','');
            $('#ghichu').attr('value','');
            $('#dvxk-modal-confirm').modal('show');
        }

    </script>
    <div id="del-modal-confirm" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade">
        {!! Form::open(['url'=>$url.'danh_muc_hl/del','id' => 'frm_del','method'=>'GET'])!!}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-primary">
                    <button type="button" data-dismiss="modal" aria-hidden="true"
                            class="close">&times;</button>
                    <h4 id="modal-header-primary-label" class="modal-title">Đồng ý xoá?</h4>
                    <input type="hidden" name="iddel" id="iddel">

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Hủy thao tác</button>
                    <button type="submit" data-dismiss="modal" class="btn btn-primary" onclick="clickdel()">Đồng ý</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <script>
        function confirmDel(id) {
            document.getElementById("iddel").value=id;
        }
        function clickdel(){
            $('#frm_del').submit();
        }
    </script>
@stop


