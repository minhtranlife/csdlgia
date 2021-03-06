<?php

namespace App\Http\Controllers;

use App\District;
use App\HsThamDinhGia;
use App\ThamDinhGia;
use App\ThamDinhGiaDefault;
use App\ThamDinhGiaH;
use App\TtPhongBan;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class HsThamDinhGiaController extends Controller
{
    public function index($nam)
    {
        if(Session::has('admin')){
            //dd(session('admin')->mahuyen);

            $model = HsThamDinhGia::where('nam',$nam)
                ->where('mahuyen',session('admin')->mahuyen)
                ->get();

            //dd($model);

            $modelpb = District::all();
            foreach($model as $tt){
                $this->getTtPhongBan($modelpb,$tt);
            }

            return view('manage.hhdv.thamdinhgia.index')
                ->with('model',$model)
                ->with('modelpb',$modelpb)
                ->with('nam',$nam)
                ->with('pageTitle','Thông tin hồ sơ thẩm định giá');

        }else
            return view('errors.notlogin');
    }

    public function showindex($nam,$pb){
        if(Session::has('admin')){

            //dd(session('admin')->level);

            if($pb == 'all')
                $model = HsThamDinhGia::where('nam',$nam)
                    ->where('trangthai','Hoàn tất')
                    ->get();

            else
                $model = HsThamDinhGia::where('nam',$nam)
                    ->where('trangthai','Hoàn tất')
                    ->where('mahuyen',$pb)
                    ->get();
            $modelpb = District::all();

            foreach($model as $tt){
                $this->getTtPhongBan($modelpb,$tt);
            }

            return view('manage.hhdv.thamdinhgia.showindex')
                ->with('model',$model)
                ->with('modelpb',$modelpb)
                ->with('nam',$nam)
                ->with('pb',$pb)
                ->with('pageTitle','Thông tin hồ sơ thẩm định giá');

        }else
            return view('errors.notlogin');
    }

    public function getTtPhongBan($pbs,$array){
        foreach($pbs as $pb){
            if($pb->mahuyen == $array->mahuyen)
                $array->tenpb = $pb->tendv;
        }
    }

    public function create()
    {
        if(Session::has('admin')){
            $modeldelete = ThamDinhGiaDefault::where('mahuyen',session('admin')->mahuyen)
                ->delete();
            return view('manage.hhdv.thamdinhgia.create')
                ->with('pageTitle','Hồ sơ thẩm định giá thêm mới');

        }else
            return view('errors.notlogin');
    }

    public function create_dk()
    {
        if(Session::has('admin')){
            return view('manage.hhdv.thamdinhgia.create_dk')
                ->with('pageTitle','Hồ sơ thẩm định giá thêm mới');
        }else
            return view('errors.notlogin');
    }

    public function store(Request $request)
    {
        if(Session::has('admin')){
            $insert = $request->all();
            $date = date_create(getDateToDb($insert['thoidiem']));
            $thang = date_format($date,'m');
            $mahs = getdate()[0];

            $model = new HsThamDinhGia();
            $model->diadiem = $insert['diadiem'];
            $model->thoidiem = getDateToDb($insert['thoidiem']);
            $model->ppthamdinh = $insert['ppthamdinh'];
            $model->mucdich = $insert['mucdich'];
            $model->dvyeucau = $insert['dvyeucau'];
            $model->thoihan = getDateToDb($insert['thoihan']);
            $model->sotbkl = $insert['sotbkl'];
            $model->hosotdgia = $insert['hosotdgia'];
            $model->thang = date_format($date,'m');
            $model->phanloai = 'CHITIET';
            if($thang == 1 || $thang == 2 || $thang == 3)
                $model->quy = 1;
            elseif($thang == 4 || $thang == 5 || $thang == 6)
                $model->quy = 2;
            elseif($thang == 7 || $thang == 8 || $thang == 9)
                $model->quy = 3;
            else
                $model->quy = 4;
            $model->nam = date_format($date,'Y');
            $model->mahuyen = session('admin')->mahuyen;
            $model->nguonvon = $insert['nguonvon'];
            $model->thuevat = $insert['thuevat'];
            $model->songaykq = $insert['songaykq'];
            $model->trangthai = 'Đang làm';
            $model->mahs = $mahs;
            if($model->save()){
                $this->createts($mahs);
                $modelh = new ThamDinhGiaH();
                $modelh->thaotac = 'Tạo mới hồ sơ thẩm định';
                $modelh->name = session('admin')->name;
                $modelh->username = session('admin')->username;
                $modelh->mahs = $mahs;
                $model->datanew = json_encode($insert);
                $modelh->save();
            }

            return redirect('hoso-thamdinhgia/nam='.date_format($date,'Y'));

        }else
            return view('errors.notlogin');
    }

    public function store_dk(Request $request)
    {
        if(Session::has('admin')){
            $insert = $request->all();
            $date = date_create(getDateToDb($insert['thoidiem']));
            $thang = date_format($date,'m');
            $mahs = getdate()[0];

            $model = new HsThamDinhGia();
            $file=$request->file('filedk');
            if(isset($file)){
                $filename = $mahs.'_1_'.chuanhoatruong($file->getClientOriginalName());
                $file->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk = $filename;
            }

            $file1=$request->file('filedk1');
            if(isset($file1)){
                $filename = $mahs.'_2_'.chuanhoatruong($file1->getClientOriginalName());
                $file1->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk1 = $filename;
            }

            $file2=$request->file('filedk2');
            if(isset($file2)){
                $filename = $mahs.'_3_'.chuanhoatruong($file2->getClientOriginalName());
                $file2->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk2 = $filename;
            }

            $file3=$request->file('filedk3');
            if(isset($file3)){
                $filename = $mahs.'_4_'.chuanhoatruong($file3->getClientOriginalName());
                $file3->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk3 = $filename;
            }

            $file4=$request->file('filedk4');
            if(isset($file4)){
                $filename = $mahs.'_5_'.chuanhoatruong($file4->getClientOriginalName());
                $file4->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk4 = $filename;
            }

            $model->diadiem = $insert['diadiem'];
            $model->thoidiem = getDateToDb($insert['thoidiem']);
            $model->ppthamdinh = $insert['ppthamdinh'];
            $model->mucdich = $insert['mucdich'];
            $model->dvyeucau = $insert['dvyeucau'];
            $model->thoihan =getDateToDb($insert['thoihan']);
            $model->sotbkl = $insert['sotbkl'];
            $model->hosotdgia = $insert['hosotdgia'];
            $model->thang = date_format($date,'m');
            $model->phanloai = 'DINHKEM';
            $model->quy = Thang2Quy($thang);
            $model->nam = date_format($date,'Y');
            $model->mahuyen = session('admin')->mahuyen;
            $model->nguonvon = $insert['nguonvon'];
            $model->trangthai = 'Đang làm';
            $model->mahs = $mahs;
            $model->thuevat = $insert['thuevat'];
            $model->songaykq = $insert['songaykq'];
            $model->save();

            return redirect('hoso-thamdinhgia/nam='.date_format($date,'Y'));

        }else
            return view('errors.notlogin');
    }

    public function createts($mahs){
        $modelts = ThamDinhGiaDefault::where('mahuyen',session('admin')->mahuyen)
            ->get();
        if(count($modelts) > 0) {
            foreach ($modelts as $ts) {
                $model = new ThamDinhGia();
                $model->tents = $ts->tents;
                $model->dacdiempl = $ts->dacdiempl;
                $model->thongsokt = $ts->thongsokt;
                $model->nguongoc = $ts->nguongoc;
                $model->dvt = $ts->dvt;
                $model->sl = $ts->sl;
                $model->nguyengiadenghi = $ts->nguyengiadenghi;
                $model->giadenghi = $ts->giadenghi;
                $model->nguyengiathamdinh = $ts->nguyengiathamdinh;
                $model->giaththamdinh = $ts->giaththamdinh;
                $model->giakththamdinh = $ts->giakththamdinh;
                $model->giatritstd = $ts->giatritstd;
                $model->gc = $ts->gc;
                $model->mahs = $mahs;
                $model->save();
            }
        }
    }

    public function show($id)
    {
        if(Session::has('admin')){
            $model = HsThamDinhGia::findOrFail($id);
            $modelts = ThamDinhGia::where('mahs',$model->mahs)->get();

            return view('manage.hhdv.thamdinhgia.show')
                ->with('model',$model)
                ->with('modelts',$modelts)
                ->with('pageTitle','Thông tin hồ sơ thẩm định');
        }else
            return view('errors.notlogin');
    }

    public function view($id)
    {
        if(Session::has('admin')){
            $model = HsThamDinhGia::findOrFail($id);

            $modelts = ThamDinhGia::where('mahs',$model->mahs)
                ->get();

            return view('manage.hhdv.thamdinhgia.view')
                ->with('model',$model)
                ->with('modelts',$modelts)
                ->with('pageTitle','Thông tin hồ sơ thẩm định');
        }else
            return view('errors.notlogin');
    }

    public function edit($id)
    {
        if(Session::has('admin')){
            $model = HsThamDinhGia::findOrFail($id);
            $modelts = ThamDinhGia::where('mahs',$model->mahs)
                ->get();

            return view('manage.hhdv.thamdinhgia.edit')
                ->with('model',$model)
                ->with('modelts',$modelts)
                ->with('pageTitle','Hồ sơ thẩm định giá chỉnh sửa');
        }else
            return view('errors.notlogin');
    }

    public function edit_dk($id)
    {
        if(Session::has('admin')){
            $model = HsThamDinhGia::findOrFail($id);
            return view('manage.hhdv.thamdinhgia.edit_dk')
                ->with('model',$model)
                ->with('pageTitle','Hồ sơ thẩm định giá chỉnh sửa');
        }else
            return view('errors.notlogin');
    }

    public function update(Request $request, $id)
    {
        if(Session::has('admin')){
            $update = $request->all();

            $date = date_create(getDateToDb($update['thoidiem']));
            $thang = date_format($date,'m');

            $model = HsThamDinhGia::findOrFail($id);
            /* add history*/
            $arraymodel = $model->toarray();
            $arrayold = array_intersect_key($arraymodel,$update);
            $arraynew = array_intersect_key($update,$arrayold);


            $model->diadiem = $update['diadiem'];
            $model->thoidiem = getDateToDb($update['thoidiem']);
            $model->ppthamdinh = $update['ppthamdinh'];
            $model->mucdich = $update['mucdich'];
            $model->dvyeucau = $update['dvyeucau'];
            $model->thoihan = getDateToDb($update['thoihan']);
            $model->sotbkl = $update['sotbkl'];
            $model->hosotdgia = $update['hosotdgia'];
            $model->thang = date_format($date,'m');
            if($thang == 1 || $thang == 2 || $thang == 3)
                $model->quy = 1;
            elseif($thang == 4 || $thang == 5 || $thang == 6)
                $model->quy = 2;
            elseif($thang == 7 || $thang == 8 || $thang == 9)
                $model->quy = 3;
            else
                $model->quy = 4;
            $model->nguonvon = $update['nguonvon'];
            $model->nam = date_format($date,'Y');
            $model->thuevat = $update['thuevat'];
            $model->songaykq = $update['songaykq'];
            if($model->save()) {
                $this->updateh($arrayold, $arraynew, $model->mahs);
            }

            return redirect('hoso-thamdinhgia/nam='.date_format($date,'Y'));

        }else
            return view('errors.notlogin');
    }

    public function update_dk(Request $request, $id)
    {
        if(Session::has('admin')){
            $update = $request->all();

            $date = date_create(getDateToDb($update['thoidiem']));
            $thang = date_format($date,'m');

            $model = HsThamDinhGia::findOrFail($id);
            if(isset($request->filedk)){
                if(file_exists(public_path() . '/data/uploads/attack/'.$model->filedk)){
                    File::Delete(public_path() . '/data/uploads/attack/'.$model->filedk);
                }
                $file=$request->file('filedk');
                $filename = $update['mahs'].'_1_'.chuanhoatruong($file->getClientOriginalName());
                $file->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk=$filename;
            }

            if(isset($request->filedk1)){
                if(file_exists(public_path() . '/data/uploads/attack/'.$model->filedk1)){
                    File::Delete(public_path() . '/data/uploads/attack/'.$model->filedk1);
                }
                $file=$request->file('filedk1');
                $filename = $update['mahs'].'_2_'.chuanhoatruong($file->getClientOriginalName());
                $file->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk1=$filename;
            }

            if(isset($request->filedk2)){
                if(file_exists(public_path() . '/data/uploads/attack/'.$model->filedk2)){
                    File::Delete(public_path() . '/data/uploads/attack/'.$model->filedk2);
                }
                $file=$request->file('filedk2');
                $filename = $update['mahs'].'_3_'.chuanhoatruong($file->getClientOriginalName());
                $file->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk2=$filename;
            }

            if(isset($request->filedk3)){
                if(file_exists(public_path() . '/data/uploads/attack/'.$model->filedk3)){
                    File::Delete(public_path() . '/data/uploads/attack/'.$model->filedk3);
                }
                $file=$request->file('filedk3');
                $filename = $update['mahs'].'_4_'.chuanhoatruong($file->getClientOriginalName());
                $file->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk3=$filename;
            }

            if(isset($request->filedk4)){
                if(file_exists(public_path() . '/data/uploads/attack/'.$model->filedk4)){
                    File::Delete(public_path() . '/data/uploads/attack/'.$model->filedk4);
                }
                $file=$request->file('filedk4');
                $filename = $update['mahs'].'_5_'.chuanhoatruong($file->getClientOriginalName());
                $file->move(public_path() . '/data/uploads/attack/', $filename);
                $model->filedk4=$filename;
            }

            /* add history*/
            $arraymodel = $model->toarray();
            $arrayold = array_intersect_key($arraymodel,$update);
            $arraynew = array_intersect_key($update,$arrayold);

            $model->diadiem = $update['diadiem'];
            $model->thoidiem = getDateToDb($update['thoidiem']);
            $model->ppthamdinh = $update['ppthamdinh'];
            $model->mucdich = $update['mucdich'];
            $model->dvyeucau = $update['dvyeucau'];
            $model->thoihan = getDateToDb($update['thoihan']);
            $model->sotbkl = $update['sotbkl'];
            $model->hosotdgia = $update['hosotdgia'];
            $model->thang = date_format($date,'m');
            $model->quy = Thang2Quy($thang);
            $model->nguonvon = $update['nguonvon'];
            $model->nam = date_format($date,'Y');
            $model->thuevat = $update['thuevat'];
            $model->songaykq = $update['songaykq'];
            if($model->save()) {
                $this->updateh($arrayold, $arraynew, $model->mahs);
            }

            return redirect('hoso-thamdinhgia/nam='.date_format($date,'Y'));

        }else
            return view('errors.notlogin');
    }

    public function updateh($dataold,$datanew,$mahs){
        $arrysosanh = array_diff_assoc($datanew,$dataold);
        //dd(empty($arrysosanh));
        if(!empty($arrysosanh)) {
            $thaydoi = '';
            foreach ($arrysosanh as $key => $value) {
                foreach ($dataold as $keyold => $valueold) {
                    if ($key == $keyold) {
                        $thaydoi = $thaydoi . $key . ':' . $valueold . '=>' . $value . '; ';
                    }
                }
            }

            $model = new ThamDinhGiaH();
            $model->thaotac = 'Cập nhật, Thay đổi chi tiết hồ sơ thẩm định';
            $model->dataold = json_encode($dataold);
            $model->datanew = json_encode($datanew);
            $model->thaydoi = $thaydoi;
            $model->name = session('admin')->name;
            $model->username = session('admin')->username;
            $model->mahs = $mahs;
            $model->save();
        }
    }

    public function destroy(Request $request)
    {
        if(Session::has('admin')){
            //$input = $request->all();
            $model = HsThamDinhGia::where('id',$request['iddelete'])
                ->first();
            $nam =$model->nam;
            if($model->delete()){
                $modelts = ThamDinhGia::where('mahs',$model->mahs)
                    ->delete();
                $modelh = ThamDinhGiaH::where('mahs',$model->mahs)
                    ->delete();
            }
            return redirect('hoso-thamdinhgia/nam='.$nam);


        }else
            return view('errors.notlogin');
    }

    public function hoantat(Request $request){
        if(Session::has('admin')){
            $model = HsThamDinhGia::where('id',$request['idhoantat'])
                ->first();
            //dd($model);
            $nam =$model->nam;
            $model->trangthai = 'Hoàn tất';
            if($model->save()){
                $modelh = new ThamDinhGiaH();
                $modelh->mahs = $model->mahs;
                $modelh->thaotac = 'Hoàn tất hồ sơ';
                $modelh->name = session('admin')->name;
                $modelh->username = session('admin')->username;
                $modelh->save();
            }
            return redirect('hoso-thamdinhgia/nam='.$nam);
        }else
            return view('errors.notlogin');
    }

    public function huy(Request $request){
        if(Session::has('admin')){
            $model = HsThamDinhGia::where('id',$request['idhuy'])
                ->first();
            //dd($model);
            $nam =$model->nam;
            $model->trangthai = 'Đang làm';
            if($model->save()){
                $modelh = new ThamDinhGiaH();
                $modelh->mahs = $model->mahs;
                $modelh->thaotac = 'Hủy hoàn tất hồ sơ';
                $modelh->name = session('admin')->name;
                $modelh->username = session('admin')->username;
                $modelh->save();
            }
            return redirect('thongtin-thamdinhgia/nam='.$nam.'&pb=all');
        }else
            return view('errors.notlogin');
    }

    public function history($mahs){
        if(Session::has('admin')){
            $model = ThamDinhGiaH::where('mahs',$mahs)
                ->get();
            return view('manage.hhdv.thamdinhgia.history.index')
                ->with('model',$model)
                ->with('pageTitle','Thông tin lịch sử hồ sơ thẩm định giá');
        }else
            return view('errors.notlogin');
    }

}
