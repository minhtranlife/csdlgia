@extends('main')

@section('custom-style')
    <link rel="stylesheet" type="text/css" href="{{url('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{url('assets/global/plugins/select2/select2.css')}}"/>
    <!--Date-->
    <link type="text/css" rel="stylesheet" href="{{ url('vendors/bootstrap-datepicker/css/datepicker.css') }}">
    <!--End Date-->
@stop


@section('custom-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->

    <script type="text/javascript" src="{{url('assets/global/plugins/select2/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{url('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{url('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js')}}"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script src="{{url('assets/admin/pages/scripts/table-managed.js')}}"></script>

    <!--Date>
    <script type="text/javascript" src="{{ url('js/jquery-1.10.2.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('vendors/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ url('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/main.js') }}"></script>

    <End Date-->

    !--Date new-->
    <!--script src="{{url('minhtran/jquery.min.js')}}"></script-->
    <script src="{{url('minhtran/jquery.inputmask.bundle.min.js')}}"></script>

    <script>
        $(document).ready(function(){
            $(":input").inputmask();
        });
    </script>
    <!--End date new-->

    <script>
        jQuery(document).ready(function() {
            TableManaged.init();
        });
        function InputMask(){
            //$(function(){
            // Input Mask
            if($.isFunction($.fn.inputmask))
            {
                $("[data-mask]").each(function(i, el)
                {
                    var $this = $(el),
                            mask = $this.data('mask').toString(),
                            opts = {
                                numericInput: attrDefault($this, 'numeric', false),
                                radixPoint: attrDefault($this, 'radixPoint', ''),
                                rightAlignNumerics: attrDefault($this, 'numericAlign', 'left') == 'right'
                            },
                            placeholder = attrDefault($this, 'placeholder', ''),
                            is_regex = attrDefault($this, 'isRegex', '');


                    if(placeholder.length)
                    {
                        opts[placeholder] = placeholder;
                    }

                    switch(mask.toLowerCase())
                    {
                        case "phone":
                            mask = "(999) 999-9999";
                            break;

                        case "currency":
                        case "rcurrency":

                            var sign = attrDefault($this, 'sign', '$');;

                            mask = "999,999,999.99";

                            if($this.data('mask').toLowerCase() == 'rcurrency')
                            {
                                mask += ' ' + sign;
                            }
                            else
                            {
                                mask = sign + ' ' + mask;
                            }

                            opts.numericInput = true;
                            opts.rightAlignNumerics = false;
                            opts.radixPoint = '.';
                            break;

                        case "email":
                            mask = 'Regex';
                            opts.regex = "[a-zA-Z0-9._%-]+@[a-zA-Z0-9-]+\\.[a-zA-Z]{2,4}";
                            break;

                        case "fdecimal":
                            mask = 'decimal';
                            $.extend(opts, {
                                autoGroup		: true,
                                groupSize		: 3,
                                radixPoint		: attrDefault($this, 'rad', '.'),
                                groupSeparator	: attrDefault($this, 'dec', ',')
                            });
                    }

                    if(is_regex)
                    {
                        opts.regex = mask;
                        mask = 'Regex';
                    }

                    $this.inputmask(mask, opts);
                });
            }
            //});
        }

    </script>
    <script>
        function clearForm(){
            $('#tenhhcreate').val('');
            $('#qcclcreate').val('');
            $('#dvtcreate').val('');
            $('#ghichucreate').val('');
        }
        function createttp(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/kkgdvgs/boxungtt',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    tenhh: $('input[name="tenhhcreate"]').val(),
                    qccl: $('textarea[name="qcclcreate"]').val(),
                    dvt: $('input[name="dvtcreate"]').val(),
                    ghichu: $('textarea[name="ghichucreate"]').val(),
                    maxa: $('input[name="maxa"]').val(),
                    mahs: $('input[name="mahs"]').val(),
                    mahuyen: $('input[name="mahuyen"]').val()
                },
                dataType: 'JSON',
                success: function (data) {
                    if(data.status == 'success') {
                        toastr.success("Bổ xung thông tin thành công!");
                        $('#dsts').replaceWith(data.message);
                        jQuery(document).ready(function() {
                            TableManaged.init();
                        });
                        $('#modal-create').modal("hide");

                    }
                }
            })
        }
        function kkgia(id){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            //alert(id);
            $.ajax({
                url: '/kkgdvgs/kkgiahhedit',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    id: id,
                },
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 'success') {
                        $('#ttkkgia').replaceWith(data.message);
                        InputMask();
                    }
                    else
                        toastr.error("Không thể chỉnh sửa thông tin giá!", "Lỗi!");
                }
            })
        }
        function kkgialk(id){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            //alert(id);
            $.ajax({
                url: '/kkgdvgs/kkgiahhlkedit',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    id: id,
                },
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 'success') {
                        $('#ttkkgialk').replaceWith(data.message);
                        InputMask();
                    }
                    else
                        toastr.error("Không thể chỉnh sửa thông tin giá phòng nghỉ!", "Lỗi!");
                }
            })
        }
        function upkkgia(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/kkgdvgs/upkkgiaedit',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    id: $('input[name="idkkgia"]').val(),
                    giaQ: $('input[name="giaQ"]').val(),
                    giaC: $('input[name="giaC"]').val(),
                    giaCtt: $('input[name="giaCtt"]').val(),
                    giaCvt: $('input[name="giaCvt"]').val(),
                    giaCnc: $('input[name="giaCnc"]').val(),
                    giaCkh: $('input[name="giaCkh"]').val(),
                    giaCk: $('input[name="giaCk"]').val(),
                    giaCc: $('input[name="giaCc"]').val(),
                    giaCcm: $('input[name="giaCcm"]').val(),
                    giaCtc: $('input[name="giaCtc"]').val(),
                    giaCbh: $('input[name="giaCbh"]').val(),
                    giaCql: $('input[name="giaCql"]').val(),
                    giaTC: $('input[name="giaTC"]').val(),
                    giaCP: $('input[name="giaCP"]').val(),
                    giaZ: $('input[name="giaZ"]').val(),
                    giaZdv: $('input[name="giaZdv"]').val(),
                    mahs: $('input[name="mahs"]').val()
                },
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 'success') {
                        toastr.success("Cập nhật giá thành công", "Thành công!");
                        $('#dsts').replaceWith(data.message);
                        jQuery(document).ready(function() {
                            TableManaged.init();
                        });
                        $('#modal-kkgia').modal("hide");

                    } else
                        toastr.error("Bạn cần kiểm tra lại thông tin vừa nhập!", "Lỗi!");
                }
            })

        }
        function upkkgialk(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/kkgdvgs/upkkgialkedit',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    id: $('input[name="idkkgialk"]').val(),
                    giaQlk: $('input[name="giaQlk"]').val(),
                    giaClk: $('input[name="giaClk"]').val(),
                    giaCttlk: $('input[name="giaCttlk"]').val(),
                    giaCvtlk: $('input[name="giaCvtlk"]').val(),
                    giaCnclk: $('input[name="giaCnclk"]').val(),
                    giaCkhlk: $('input[name="giaCkhlk"]').val(),
                    giaCklk: $('input[name="giaCklk"]').val(),
                    giaCclk: $('input[name="giaCclk"]').val(),
                    giaCcmlk: $('input[name="giaCcmlk"]').val(),
                    giaCtclk: $('input[name="giaCtclk"]').val(),
                    giaCbhlk: $('input[name="giaCbhlk"]').val(),
                    giaCqllk: $('input[name="giaCqllk"]').val(),
                    giaTClk: $('input[name="giaTClk"]').val(),
                    giaCPlk: $('input[name="giaCPlk"]').val(),
                    giaZlk: $('input[name="giaZlk"]').val(),
                    giaZdvlk: $('input[name="giaZdvlk"]').val(),
                    mahs: $('input[name="mahs"]').val()
                },
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 'success') {
                        toastr.success("Cập nhật thông tin giá thành công", "Thành công!");
                        $('#dsts').replaceWith(data.message);
                        jQuery(document).ready(function() {
                            TableManaged.init();
                        });
                        $('#modal-kkgialk').modal("hide");

                    } else
                        toastr.error("Bạn cần kiểm tra lại thông tin vừa nhập!", "Lỗi!");
                }
            })

        }
        function editTtPh(id) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            //alert(id);
            $.ajax({
                url: '/kkgdvgs/chinhsuatt',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    id: id,
                },
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 'success') {
                        $('#ttpedit').replaceWith(data.message);
                    }
                    else
                        toastr.error("Không thể chỉnh sửa thông tin phòng nghỉ!", "Lỗi!");
                }
            })
        }

        function updatets() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/kkgdvgs/capnhattt',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    id: $('input[name="idedit"]').val(),
                    tenhh: $('input[name="tenhhedit"]').val(),
                    qccl: $('textarea[name="qccledit"]').val(),
                    dvt: $('input[name="dvtedit"]').val(),
                    ghichu: $('textarea[name="ghichuedit"]').val(),
                    mahs: $('input[name="mahs"]').val()
                },
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 'success') {
                        toastr.success("Chỉnh sửa thông tin phòng nghỉ thành công", "Thành công!");
                        $('#dsts').replaceWith(data.message);
                        jQuery(document).ready(function() {
                            TableManaged.init();
                        });
                        $('#modal-edit').modal("hide");

                    } else
                        toastr.error("Bạn cần kiểm tra lại thông tin vừa nhập!", "Lỗi!");
                }
            })
        }

        function getid(id){
            document.getElementById("iddelete").value=id;
        }
        function deleteRow() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/kkgdvgs/xoatt',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    id: $('input[name="iddelete"]').val(),
                    mahs:  $('input[name="mahs"]').val()
                },
                dataType: 'JSON',
                success: function (data) {
                    //if(data.status == 'success') {
                    toastr.success("Bạn đã xóa thông tin phòng nghỉ thành công!", "Thành công!");
                    $('#dsts').replaceWith(data.message);
                    jQuery(document).ready(function() {
                        TableManaged.init();
                    });

                    $('#modal-delete').modal("hide");

                    //}
                }
            })
        }
        function checkngay(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/ajax/checkngay',
                type: 'GET',
                data: {
                    _token: CSRF_TOKEN,
                    ngaynhap: $('input[name="ngaynhap"]').val(),
                    ngayhieuluc: $('input[name="ngayhieuluc"]').val(),
                    plhs: $('select[name="plhs"]').val()
                },
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 'success') {
                        toastr.success("Ngày hiệu lực có thể sử dụng được", "Thành công!");
                    }else {
                        toastr.error("Bạn cần kiểm tra lại ngày có hiệu lực!", "Lỗi!");
                        $('input[name="ngayhieuluc"]').val('');
                    }
                }
            })

        }
        function clearngay(){
            $('input[name="ngaynhap"]').val('');
            $('input[name="ngayhieuluc"]').val('');
        }
        function clearngayhieuluc(){
            $('input[name="ngayhieuluc"]').val('');
        }


    </script>

@stop

@section('content')

    <h3 class="page-title">
        Thông tin kê khai giá sữa<small>&nbsp;chỉnh sửa</small>
        <p><h5 style="color: blue">{{$modeldn->tendn}}&nbsp;- Mã số thuế: {{$modeldn->maxa}}</h5></p>
    </h3>

    <!-- END PAGE HEADER-->
    <div class="row">
        {!! Form::model($model, ['method' => 'PATCH', 'url'=>'kekhaigiasua/'. $model->id, 'class'=>'horizontal-form','id'=>'update_kkdvgs']) !!}
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box blue">
                <div class="portlet-body">
                    <h4 class="form-section" style="color: #0000ff">Thông tin hồ sơ</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group"><label for="selGender" class="control-label">Thực hiện theo</label>
                                <div>
                                        <textarea id="thqd" class="form-control" name="thqd" cols="30" rows="5"
                                                >{{$model->thqd}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Ngày kê khai<span class="require">*</span></label>
                                <p style="color: #000088"><b>{{getDayVn($model->ngaynhap)}}</b></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Ngày thực hiện mức giá kê khai<span class="require">*</span></label>
                                {!!Form::text('ngayhieuluc',date('d/m/Y',  strtotime($model->ngayhieuluc)), array('id' => 'ngayhieuluc','data-inputmask'=>"'alias': 'date'",'class' => 'form-control required','onchange'=>"checkngay()"))!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Số công văn<span class="require">*</span></label>
                                {!!Form::text('socv', null, array('id' => 'socv','class' => 'form-control required'))!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Số công văn liền kề</label>
                                {!!Form::text('socvlk',null, array('id' => 'socvlk','class' => 'form-control'))!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Ngày nhập số công văn liền kề<span class="require">*</span></label>
                                {!!Form::text('ngaycvlk',$model->ngaycvlk != '' ? date('d/m/Y',  strtotime($model->ngaycvlk)) : '', array('id' => 'ngaycvlk','data-inputmask'=>"'alias': 'date'",'class' => 'form-control'))!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group"><label for="selGender" class="control-label">Phân tích nguyên nhân điều chỉnh tăng/giảm giá kê khai của từng mặt hàng</label>
                                <div>
                                    <textarea id="ghichu" class="form-control" name="ghichu" cols="30" rows="5"
                                      placeholder="-Phụ thu, Thuế VAT">{{$model->ghichu}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="mahs" id="mahs" value="{{$model->mahs}}">
                    <input type="hidden" name="maxa" id="maxa" value="{{$model->maxa}}">
                    <input type="hidden" name="mahuyen" id="mahuyen" value="{{$modeldn->mahuyen}}">
                    <input type="hidden" name="tendn" id="tendn" value="{{$modeldn->tendn}}">
                    {!! Form::close() !!}
                    <!--/row-->
                    <h4 class="form-section" style="color: #0000ff">Thông tin chi tiết hồ sơ</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" data-target="#modal-create" data-toggle="modal" class="btn btn-success btn-xs" onclick="clearForm()"><i class="fa fa-plus"></i>&nbsp;Kê khai bổ sung phòng</button>
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="row" id="dsts">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-hover" id="sample_3">
                                <thead>
                                <tr>
                                    <th style="text-align: center">STT</th>
                                    <th style="text-align: center">Tên hoàng hoá<br>dịch vụ</th>
                                    <th style="text-align: center">Quy cách<br>Chất lượng</th>
                                    <th style="text-align: center">Đơn vị<br>tính</th>
                                    <th style="text-align: center">Ghi chú</th>
                                    <th style="text-align: center">Mức giá <br>liền kề</th>
                                    <th style="text-align: center">Mức giá <br>kê khai</th>
                                    <th style="text-align: center" width="20%">Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($modelct as $key=>$tt)
                                    <tr>
                                        <td align="center">{{$key + 1}}</td>
                                        <td class="active">{{$tt->tenhh}}</td>
                                        <td>{{$tt->qccl}}</td>
                                        <td>{{$tt->dvt}}</td>
                                        <td>{{$tt->ghichu}}</td>
                                        <td align="right">{{number_format($tt->giaZdvlk)}}</td>
                                        <td align="right">{{number_format($tt->giaZdv)}}</td>
                                        <td>
                                            <button type="button" data-target="#modal-kkgialk" data-toggle="modal" class="btn btn-default btn-xs mbs" onclick="kkgialk({{$tt->id}});"><i class="fa fa-edit"></i>&nbsp;Kê khai giá liền kề</button>
                                            <button type="button" data-target="#modal-kkgia" data-toggle="modal" class="btn btn-default btn-xs mbs" onclick="kkgia({{$tt->id}});"><i class="fa fa-edit"></i>&nbsp;Kê khai giá</button>
                                            <button type="button" data-target="#modal-edit" data-toggle="modal" class="btn btn-default btn-xs mbs" onclick="editTtPh({{$tt->id}});"><i class="fa fa-edit"></i>&nbsp;Chỉnh sửa thông tin</button>
                                            <button type="button" data-target="#modal-delete" data-toggle="modal" class="btn btn-default btn-xs mbs" onclick="getid({{$tt->id}});" ><i class="fa fa-trash-o"></i>&nbsp;Xóa</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
            <div style="text-align: center">
                <a href="{{url('kekhaigiasua?&masothue='.$model->maxa)}}" class="btn btn-danger"><i class="fa fa-reply"></i>&nbsp;Quay lại</a>
                <button type="reset" class="btn btn-default"><i class="fa fa-refresh"></i>&nbsp;Nhập lại</button>
                <button type="submit" class="btn green" onclick="validateForm()"><i class="fa fa-check"></i> Cập nhật</button>

            </div>
        </div>

    </div>

    <!-- BEGIN DASHBOARD STATS -->

    <!-- END DASHBOARD STATS -->
    <div class="clearfix">
    </div>

    <!--Validate Form-->
    <script type="text/javascript">
        function validateForm(){

            var validator = $("#update_kkdvgs").validate({
                rules: {
                    ten :"required"
                },
                messages: {
                    ten :"Chưa nhập dữ liệu"
                }
            });
        }
    </script>

    <!--Model them moi ttp-->
    <div class="modal fade bs-modal-lg" id="modal-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Thêm mới thông tin hàng hoá -  quy cách chất lượng</h4>
                </div>
                <div class="modal-body" id="ttpthemmoi">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><label for="selGender" class="control-label"><b>Tên hàng hoá</b><span class="require">*</span></label>
                                <div><input type="text" name="tenhhcreate" id="tenhhcreate" class="form-control" ></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label for="selGender" class="control-label"><b>Đơn vị tính</b><span class="require">*</span></label>
                                <div><input type="text" id="dvtcreate" class="form-control" name="dvtcreate"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group"><label for="selGender" class="control-label"><b>Quy cách chất lượng</b><span class="require">*</span></label>
                                <div><textarea id="qcclcreate" class="form-control" name="qcclcreate" cols="30" rows="3"></textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group"><label for="selGender" class="control-label"><b>Ghi chú</b><span class="require">*</span></label>
                                <div><textarea id="ghichucreate" class="form-control" name="ghichucreate" cols="30" rows="3"></textarea></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Thoát</button>
                    <button type="button" class="btn btn-primary" onclick="createttp()">Bổ xung</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Modal chỉnh sửa ttp-->
    <div class="modal fade bs-modal-lg" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Chỉnh sửa thông tin hàng hoá</h4>
                </div>
                <div class="modal-body" id="ttpedit">
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Thoát</button>
                    <button type="button" class="btn btn-primary" onclick="updatets()">Cập nhật</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Modal Wide Width-->
    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Đồng ý xóa thông tin hàng hoá?</h4>
                </div>
                <input type="hidden" id="iddelete" name="iddelete">
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Thoát</button>
                    <button type="button" class="btn btn-primary" onclick="deleteRow()">Đồng ý</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Modal kê khai giá-->
    <div class="modal fade" id="modal-kkgia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Kê khai giá hàng hoá</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal" id="ttkkgia">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Thoát</button>
                    <button type="button" class="btn btn-primary" onclick="upkkgia()">Đồng ý</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Model kkgialk-->
    <div class="modal fade" id="modal-kkgialk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Kê khai giá hàng hoá kiền kề</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal" id="ttkkgialk">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Thoát</button>
                    <button type="button" class="btn btn-primary" onclick="upkkgialk()">Đồng ý</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    @include('includes.script.create-header-scripts')



@stop