<?php

namespace App\Http\Controllers;

use App\Company;
use App\District;
use App\Register;
use App\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    public function index(Request $request){
        if (Session::has('admin')) {
            if (session('admin')->sadmin == 'ssa' || session('admin')->sadmin == 'sa'
                || session('admin')->sadmin == 'satc' || session('admin')->sadmin == 'sagt' || session('admin')->sadmin == 'sact'){
                $inputs = $request->all();
                if(session('admin')->sadmin == 'satc')
                    $inputs['level'] =  isset($inputs['level']) ? $inputs['level'] : 'DVLT';
                elseif( session('admin')->sadmin == 'sagt')
                    $inputs['level'] =  isset($inputs['level']) ? $inputs['level'] : 'DVVT';
                elseif(session('admin')->sadmin == 'sact')
                    $inputs['level'] = isset($inputs['level']) ? $inputs['level'] : 'DVGS';
                else
                    $inputs['level'] = isset($inputs['level']) ? $inputs['level'] : 'DVLT';
                //Check quyền
                if($inputs['level'] == 'DVLT' && can('ttdn','dvlt') || $inputs['level'] == 'DVVT' && can('ttdn','dvvt')
                    || $inputs['level'] == 'DVGS' && can('ttdn','dvgs') || $inputs['level'] == 'DVTACN' && can('ttdn','dvtacn')) {
                    $model = Register::where('level', $inputs['level'])
                        ->get();
                    return view('system.register.xetduyet.index')
                        ->with('model', $model)
                        ->with('level', $inputs['level'])
                        ->with('pageTitle', 'Xét duyệt tài khoản đăng ký');
                }else
                    return view('errors.noperm');
            }else
                return view('errors.noperm');
        }else
            return view('errors.notlogin');
    }

    public function edit($id){
        if (Session::has('admin')) {
            if (session('admin')->sadmin == 'ssa' || session('admin')->sadmin == 'sa'
                || session('admin')->sadmin == 'satc' || session('admin')->sadmin == 'sagt' || session('admin')->sadmin == 'sact'){
                $model = Register::findOrFail($id);
                if($model->level == 'DVLT' && can('ttdn','dvlt') || $model->level== 'DVVT' && can('ttdn','dvvt')
                    || $model->level == 'DVGS' && can('ttdn','dvgs') || $model->level == 'DVTACN' && can('ttdn','dvtacn')) {
                    if ($model->level == 'DVLT' || $model->level == 'TACN')
                        $phanloaiql = 'TC';
                    elseif ($model->level == 'DVVT')
                        $phanloaiql = 'VT';
                    elseif ($model->level == 'DVGS')
                        $phanloaiql = 'CT';
                    $cqcq = District::where('phanloaiql', $phanloaiql)
                        ->get();
                    $settingdvvt = !empty($model->settingdvvt) ? json_decode($model->settingdvvt) : '';
                    return view('system.register.xetduyet.edit')
                        ->with('model', $model)
                        ->with('cqcq', $cqcq)
                        ->with('settingdvvt', $settingdvvt)
                        ->with('pageTitle', 'Chỉnh sửa thông tin đăng ký tài khoản dịch vụ lưu trú');
                }else
                    return view('errors.noperm');
            }else
                return view('errors.noperm');
        }else
            return view('errors.notlogin');
    }

    public function update(Request $request,$id){
        if (Session::has('admin')) {
            if (session('admin')->sadmin == 'ssa' || session('admin')->sadmin == 'sa'
                || session('admin')->sadmin == 'satc' || session('admin')->sadmin == 'sagt' || session('admin')->sadmin == 'sact'){
                $inputs = $request->all();
                $model = Register::findOrFail($id);
                $inputs['settingdvvt'] = isset($inputs['roles']) ? json_encode($inputs['roles']) : '';
                $model->update($inputs);
                return redirect('register?&level='.$model->level);
            }else
                return view('errors.noperm');
        }else
            return view('errors.notlogin');
    }

    public function show($id){
        if (Session::has('admin')) {
            if (session('admin')->sadmin == 'ssa' || session('admin')->sadmin == 'sa'
                || session('admin')->sadmin == 'satc' || session('admin')->sadmin == 'sagt' || session('admin')->sadmin == 'sact'){
                $model = Register::findOrFail($id);
                if($model->level == 'DVLT' && can('ttdn','dvlt') || $model->level== 'DVVT' && can('ttdn','dvvt')
                    || $model->level == 'DVGS' && can('ttdn','dvgs') || $model->level == 'DVTACN' && can('ttdn','dvtacn')) {
                    $dvcq = District::where('mahuyen', $model->mahuyen)->first()->tendv;
                    $settingdvvt = !empty($model->settingdvvt) ? json_decode($model->settingdvvt) : '';
                    return view('system.register.xetduyet.show')
                        ->with('model', $model)
                        ->with('dvcq', $dvcq)
                        ->with('settingdvvt', $settingdvvt)
                        ->with('pageTitle', 'Thông tin doanh nghiệp đăng ký tài khoản');
                }else
                    return view('errors.noperm');
            }else
                return view('errors.noperm');
        }else
            return view('errors.notlogin');
    }

    public function tralai(Request $request){
        if (Session::has('admin')) {
            if (session('admin')->sadmin == 'ssa' || session('admin')->sadmin == 'sa'
                || session('admin')->sadmin == 'satc' || session('admin')->sadmin == 'sagt' || session('admin')->sadmin == 'sact'){
                $inputs = $request->all();
                $id = $inputs['idhs'];
                $model = Register::findOrFail($id);
                $inputs['trangthai'] = 'Bị trả lại';
                if($model->update($inputs)){
                    $tencqcq = District::where('mahuyen',$model->mahuyen)->first();
                    $data=[];
                    $data['tendn'] = $model->tendn;
                    $data['tg'] = Carbon::now()->toDateTimeString();
                    $data['tencqcq'] = $tencqcq->tendv;
                    $data['masothue'] = $model->maxa;
                    $data['user'] = $model->username;
                    $data['madk'] = $model->ma;
                    $data['lydo'] = $inputs['lydo'];
                    $a = $model->email;
                    $b  =  $model->tendn;
                    Mail::send('mail.replyregister',$data, function ($message) use($a,$b) {
                        $message->to($a,$b )
                            ->subject('Thông báo trả lại thông tin đăng ký ');
                        $message->from('phanmemcsdlgia@gmail.com','Phần mềm CSDL giá');
                    });
                }
                return redirect('register?&level='.$model->level);
            }else
                return view('errors.noperm');
        }else
            return view('errors.notlogin');
    }

    public function searchregister(){
        return view('system.register.search.index')
            ->with('pageTitle','Kiểm tra tài khoản!!!');
    }

    public function checksearchregister(Request $request){
        $input = $request->all();

        $check1 = Register::where('maxa',$input['maxa'])
            ->where('level',$input['level'])
            ->first();
        if(isset($check1)){
            if($check1->trangthai == 'Chờ duyệt'){
                return view('system.register.view.register-choduyet');
            }else
                return view('system.register.view.register-tralai')
                    ->with('lydo',$check1->lydo);
        }else{
            $check2 = Users::where('maxa',$input['maxa'])
                ->first();
            if(isset($check2)){
                return view('system.register.view.register-usersuccess');
            }else{
                return view('system.register.view.register-nouser');
            }
        }
    }

    public function showttdktk(){
        return view('system.register.search.show');
    }

    public function chinhsuadktk(Request $request){
        $input = $request->all();
        $model = Register::where('ma',$input['ma'])
            ->first();
        if(isset($model)){
            if($model->trangthai == 'Bị trả lại') {

                if($model->level == 'DVLT' || $model->level =='TACN')
                    $phanloaiql = 'TC';
                elseif($model->level  == 'DVVT')
                    $phanloaiql = 'VT';
                elseif($model->level  == 'DVGS')
                    $phanloaiql = 'CT';
                $cqcq = District::where('phanloaiql', $phanloaiql)
                    ->get();
                $settingdvvt = !empty($model->settingdvvt) ? json_decode($model->settingdvvt) : '';
                return view('system.register.search.edit')
                    ->with('cqcq', $cqcq)
                    ->with('model', $model)
                    ->with('settingdvvt',$settingdvvt)
                    ->with('pageTitle', 'Chỉnh sửa thông tin đăng ký tài khoản');
            }else{
                return view('system.register.view.register-edit-errors');
            }
        }else{
            return view('system.register.view.register-edit-errors');
        }
    }

    public function dangkytaikhoan(Request $request){
        $inputs = $request->all();
        if($inputs['level'] == 'DVLT' || $inputs['level']=='DVTACN')
            $phanloaiql = 'TC';
        elseif($inputs['level'] == 'DVVT')
            $phanloaiql = 'VT';
        elseif($inputs['level'] == 'DVGS')
            $phanloaiql = 'CT';
        $model = District::where('phanloaiql',$phanloaiql)
            ->get();

        return view('system.register.register')
            ->with('model',$model)
            ->with('level',$inputs['level'])
            ->with('pageTitle','Đăng ký thông tin tài khoản doanh nghiệp');
    }

    public function dangkytaikhoanstore(Request $request)
    {
        $inputs = $request->all();

        //Bỏ captcha phần vĩnh phúc
        //if ($inputs['g-recaptcha-response'] != '') {
            $check = Company::where('maxa', $inputs['maxa'])
                ->where('level', $inputs['level'])
                ->first();
            if (count($check) > 0) {
                return view('errors.register-errors');
            } else {
                $checkuser = Users::where('username', $inputs['username'])->first();
                if (count($checkuser) > 0) {
                    return view('errors.register-errors');
                } else {
                    $inputs['ma'] = getdate()[0];
                    if(isset($inputs['roles'])){
                        $inputs['settingdvvt'] = json_encode($inputs['roles']);
                        $x = $inputs['roles'];
                        $inputs['vtxk'] = isset($inputs['dvvt']['vtxk']) ? 1 : 0;
                        $inputs['vtxb'] = isset($x['dvvt']['vtxb']) ? 1 : 0;
                        $inputs['vtxtx'] = isset($x['dvvt']['vtxtx']) ? 1 : 0;
                        $inputs['vtch'] = isset($x['dvvt']['vtch']) ? 1 : 0;
                    }else {
                        $inputs['settingdvvt'] = '';
                        $inputs['vtxk'] = 0;
                        $inputs['vtxb'] = 0;
                        $inputs['vtxtx'] = 0;
                        $inputs['vtch'] = 0;
                    }

                    $inputs['trangthai'] = 'Chờ duyệt';
                    $inputs['password'] = md5($inputs['rpassword']);

                    $model = new Register();
                    if ($model->create($inputs)) {
                        $tencqcq = District::where('mahuyen', $inputs['mahuyen'])->first();
                        $data = [];
                        $data['tendn'] = $inputs['tendn'];
                        $data['tg'] = Carbon::now()->toDateTimeString();
                        $data['tencqcq'] = $tencqcq->tendv;
                        $data['masothue'] = $inputs['maxa'];
                        $data['user'] = $inputs['username'];
                        $data['madk'] = $inputs['ma'];
                        $maildn = $inputs['email'];
                        $tendn = $inputs['tendn'];
                        $mailql = $tencqcq->emailqt;
                        $tenql = $tencqcq->tendv;
                        Mail::send('mail.register', $data, function ($message) use ($maildn, $tendn, $mailql, $tenql) {
                            $message->to($maildn, $tendn)
                                ->to($mailql, $tenql)
                                ->subject('Thông báo đăng ký tài khoản');
                            $message->from('qlgiakhanhhoa@gmail.com', 'Phần mềm CSDL giá');
                        });
                    }
                    return view('system.register.view.register-success')
                        ->with('ma', $inputs['ma']);
                }
            }
        //} else {
            //return view('errors.register-errors');
        //}
    }

    public function dangkytaikhoanupdate(Request $request,$id){
        $inputs = $request->all();
        $checkuser = Users::where('username', $inputs['username'])->first();
        if (count($checkuser) > 0) {
            return view('errors.register-errors');
        } else {
            $inputs['trangthai'] = 'Chờ duyệt';
            $inputs['password'] = md5($inputs['rpassword']);
            $inputs['settingdvvt'] = isset($inputs['roles']) ? json_encode($inputs['roles']) : '';
            if(isset($inputs['roles'])){
                $inputs['settingdvvt'] = json_encode($inputs['roles']);
                $x = $inputs['roles'];
                $inputs['vtxk'] = isset($inputs['dvvt']['vtxk']) ? 1 : 0;
                $inputs['vtxb'] = isset($x['dvvt']['vtxb']) ? 1 : 0;
                $inputs['vtxtx'] = isset($x['dvvt']['vtxtx']) ? 1 : 0;
                $inputs['vtch'] = isset($x['dvvt']['vtch']) ? 1 : 0;
            }else {
                $inputs['settingdvvt'] = '';
                $inputs['vtxk'] = 0;
                $inputs['vtxb'] = 0;
                $inputs['vtxtx'] = 0;
                $inputs['vtch'] = 0;
            }
            $model = Register::findOrFail($id);
            if ($model->update($inputs)) {
                $tencqcq = District::where('mahuyen', $inputs['mahuyen'])->first();
                $data = [];
                $data['tendn'] = $inputs['tendn'];
                $data['tg'] = Carbon::now()->toDateTimeString();
                $data['tencqcq'] = $tencqcq->tendv;
                $data['masothue'] = $inputs['maxa'];
                $data['user'] = $inputs['username'];
                $data['madk'] = $model->ma;
                $maildn = $inputs['email'];
                $tendn = $inputs['tendn'];
                $mailql = $tencqcq->emailqt;
                $tenql = $tencqcq->tendv;
                Mail::send('mail.register', $data, function ($message) use ($maildn, $tendn, $mailql, $tenql) {
                    $message->to($maildn, $tendn)
                        ->to($mailql, $tenql)
                        ->subject('Thông báo đăng ký tài khoản');
                    $message->from('phanmemcsdlgia@gmail.com', 'Phần mềm CSDL giá');
                });

            }
            return view('system.register.view.register-success')
                ->with('ma',$model->ma);
        }
    }

    public function checkmasothue(Request $request){
        $inputs = $request->all();
        $model = Company::where('maxa',$inputs['maxa'])
            ->where('level',$inputs['level'])
            ->first();
        $modelrg = Register::where('maxa',$inputs['maxa'])
            ->where('level',$inputs['level'])
            ->first();
        if(isset($model)) {
            echo 'cancel';
        }else{
            if(isset($modelrg)){
                echo 'cancel';
            }else
                echo 'ok';
        }
    }

    public function checkuser(Request $request){
        $inputs = $request->all();
        $model = Users::where('username', $inputs['user'])
            ->first();
        $modelrg = Register::where('username', $inputs['user'])
            ->first();
        if(isset($model)) {
            echo 'cancel';
        }else{
            if(isset($modelrg)){
                echo 'cancel';
            }else
                echo 'ok';
        }
    }

    public function createtk(Request $request){
        if (Session::has('admin')) {
            $input = $request->all();
            $id = $input['idregister'];
            $model = Register::findOrFail($id);
            $inputs = $model->toArray();
            $inputs['avatar'] = 'no-image-available.jpg';
            unset($inputs['id']);
            if(session('admin')->sadmin == 'ssa' || session('admin')->sadmin == 'sa' ) {
                $check = Company::where('maxa',$model->maxa)
                    ->where('level',$model->level)
                    ->first();

                if(count($check)>0){
                    return view('errors.notcrregisterlt');
                }else {
                    $modeldn = new Company();
                    if($modeldn->create($inputs)){
                        $modeluser = new Users();
                        $modeluser->name = $model->tendn;
                        $modeluser->username = $model->username;
                        $modeluser->password = $model->password;
                        $modeluser->email = $model->email;
                        $modeluser->status = 'Kích hoạt';
                        $modeluser->mahuyen = $model->mahuyen;
                        $modeluser->level = $model->level;
                        $modeluser->maxa = $model->maxa;
                        $modeluser->ttnguoitao = session('admin')->name.'('.session('admin')->username.') - '. getDateTime(Carbon::now()->toDateTimeString());
                        $modeluser->save();
                    }
                    $tencqcq = District::where('mahuyen', $model->mahuyen)->first();
                    $data = [];
                    $data['tendn'] = $model->tendn;
                    $data['tg'] = Carbon::now()->toDateTimeString();
                    $data['tencqcq'] = $tencqcq->tendv;
                    $data['masothue'] = $model->maxa;
                    $data['username'] = $model->username;
                    $maildn = $model->email;
                    $tendn = $model->tendn;
                    $mailql = $tencqcq->emailqt;
                    $tenql = $tencqcq->tendv;

                    Mail::send('mail.successregister', $data, function ($message) use ($maildn,$tendn,$mailql,$tenql) {
                        $message->to($maildn,$tendn)
                            ->to($mailql,$tenql)
                            ->subject('Thông báo thông tin đăng ký đã được xét duyệt');
                        $message->from('phanmemcsdlgia@gmail.com', 'Phần mềm CSDL giá');
                    });
                    $delete = Register::findOrFail($id)->delete();
                    return redirect('register?&level='.$model->level);
                }

            }else{
                return view('errors.noperm');
            }

        } else
            return view('errors.notlogin');
    }

    public function delete(Request $request){
        if (Session::has('admin')) {
            if(session('admin')->sadmin == 'ssa' || session('admin') == 'sa'){
                $inputs = $request->all();
                $model = Register::where('id',$inputs['iddelete'])
                    ->first();
                $level = $model->level;
                $model->delete();
                return redirect('register?&level='.$level);
            }else{
                return view('errors.noperm');
            }

        }else
            return view('errors.notlogin');
    }
}
