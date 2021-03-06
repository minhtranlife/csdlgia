<?php

namespace App\Http\Controllers;

use App\DmHangHoa;
use App\DmHhTn;
use App\DmHhXnk;
use App\DmLoaiGia;
use App\DmLoaiHh;
use App\DmThiTruong;
use App\DmThoiDiem;
use App\GeneralConfigs;
use App\GiaHangHoa;
use App\GiaHhTn;
use App\GiaHhXnk;
use App\HsGiaHangHoa;
use App\HsGiaHhTn;
use App\HsGiaHhTt;
use App\HsGiaHhXnk;
use App\HsThamDinhGia;
use App\ThamDinhGia;
use App\TsNhaDat;
use App\TsOtoKhac;
use App\TtTsNhaDat;
use App\TtTsOtoKhac;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TT1422015BtcController extends Controller
{

    public function index()
    {
        if(Session::has('admin')){
            $modelthoidiemtn = DmThoiDiem::where('plbc','Hàng hóa, dịch vụ')
                ->get();
            $modelthoidiemxnk = DmThoiDiem::where('plbc','Hàng hóa xuất nhập khẩu')
                ->get();
            $modelthoidiemtdg = DmThoiDiem::where('plbc','Tài sản thẩm định giá')
                ->get();
            $loaihh = DmLoaiHh::all();
            $thitruong= DmThiTruong::all();
            return view('reports.hhdv.tt142-2015-btc.index')
                ->with('modelthoidiemtn',$modelthoidiemtn)
                ->with('modelthoidiemxnk',$modelthoidiemxnk)
                ->with('modelthoidiemtdg',$modelthoidiemtdg)
                ->with('loaihh',$loaihh)
                ->with('thitruong',$thitruong)
                ->with('pageTitle','Thông tư 142/2015/BTC');
        }else
            return view('errors.notlogin');
    }

    //Phụ lục 2
    public function PL2(Request $request)
    {
        if (Session::has('admin')) {
            $input = $request->all();
            $thoigian = HsGiaHangHoa::where('mathoidiem',$input['mathoidiem'])
                ->where('thitruong',$input['thitruong'])
                ->where('maloaihh',$input['maloaihh'])
                ->get();

            $arrayidtg = '';
            foreach($thoigian as $tg){
                $arrayidtg = $arrayidtg.$tg->mahs.',';
            }
            $model = GiaHangHoa::selectraw('mahh, sum(giatu) as giatu, sum(giaden) as giaden')->wherein('mahs',explode(',',$arrayidtg))->groupby('mahh')
                ->get();

            $modeldmhh = DmHangHoa::select('mahh','tenhh','dacdiemkt','dvt')->get()->toarray();
            $modelloaihh = DmLoaiHh::all();
            $modelloaigia = DmLoaiGia::all();

            foreach($model as $hh)
            {
                $hh->giagiaodich = ($hh->giatu + $hh->giaden)/2;
                $this->getTtHhPL2($modeldmhh,$hh);
                $hh->thitruong=$input['thitruong'];
                $hh->maloaihh=$input['maloaihh'];
                //$this->getTtTgPL2($thoigian,$hh);
                $this->getDmLoaiHhPL2($modelloaihh,$hh);
                //$this->getDmLoaiGiaPL2($modelloaigia,$hh);
            }


            return view('reports.hhdv.tt142-2015-btc.PL2')
                ->with('model',$model)
                ->with('pageTitle','Phụ lục 2');

        }else
            return view('errors.notlogin');

    }

    public function getDmLoaiHhPL2($ttdm,$array){
        foreach($ttdm as $dm){
            if($dm->maloaihh === $array->maloaihh){
                $array->loaihh = $dm->tenloaihh;
            }
        }
    }

    public function getDmLoaiGiaPL2($ttdm,$array){
        foreach($ttdm as $dm){
            if($dm->maloaigia == $array->maloaigia){
                $array->loaigia = $dm->tenloaigia;
            }
        }
    }

    public function getTtHhPL2($tthh,$array){
        foreach($tthh as $hh){
            if($hh['mahh'] === $array->mahh){
                $array->tenhh = $hh['tenhh'];
                $array->dacdiemkt = $hh['dacdiemkt'];
                $array->dvt = $hh['dvt'];
                break;
            }
        }
    }

    public function getTtTgPL2($tttg,$array){
        foreach($tttg as $tg){
            if($tg->id == $array->idtg){
                $array->tgnhap = $tg->tgnhap;
                $array->thitruong = $tg->thitruong;
                $array->maloaihh = $tg->maloaihh;
                $array->maloaigia = $tg->maloaigia;
            }
        }
    }

    //Phụ lục 3
    public function PL3(Request $request){
        if (Session::has('admin')) {
            $input = $request->all();

            $modelthoidiem = DmThoiDiem::where('mathoidiem',$input['mathoidiem'])->first();
            //dd($modelthoidiem);
            $thoigian = HsGiaHhXnk::where('mathoidiem',$input['mathoidiem'])
                ->where('maloaigia','GXK')
                ->get();
            //dd($thoigian);
            $arraytg = '';
            foreach($thoigian as $tg){
                $arraytg = $arraytg.$tg->mahs.',';
            }
            $model = GiaHhXnk::wherein('mahs',explode(',',$arraytg))->get();
            //dd($model);
            $modelhh = DmHhXnk::all();
            foreach($model as $hh)
            {
                $hh->kimngach = ($hh->giatu + $hh->giaden)/2;
                $this->getTtHhPL3($modelhh,$hh);
            }
            $config = GeneralConfigs::first();
            //dd($config);
            //dd($model);
            return view('reports.hhdv.tt142-2015-btc.PL3')
                ->with('config',$config)
                ->with('model',$model)
                ->with('modelthoidiem',$modelthoidiem)
                ->with('pageTitle','Phụ lục 3');

        }else
            return view('errors.notlogin');
    }

    public function getTtHhPL3($tthh,$array){
        foreach($tthh as $tt){
            if($tt->mahh == $array->mahh && $tt->masoloai == $array->masoloai){
                $array->tenhh = $tt->tenhh;
                $array->nsx = $tt->nsx;
                $array->dvt = $tt->dvt;
            }
        }
    }

    //Phụ lục 4
    public function PL4(Request $request){
        if (Session::has('admin')) {
            $input = $request->all();

            $modelthoidiem = DmThoiDiem::where('mathoidiem',$input['mathoidiem'])->first();

            $thoigian = HsGiaHhXnk::where('mathoidiem',$input['mathoidiem'])
                ->where('maloaigia','GNK')
                ->get();
            //dd($thoigian);
            $arraytg = '';
            foreach($thoigian as $tg){
                $arraytg = $arraytg.$tg->mahs.',';
            }
            $model = GiaHhXnk::wherein('mahs',explode(',',$arraytg))->get();
            $modelhh = DmHhXnK::all();
            foreach($model as $hh)
            {
                $hh->kimngach = ($hh->giatu + $hh->giaden)/2;
                $this->getTtHhPL4($modelhh,$hh);
            }
            $config = GeneralConfigs::first();

            return view('reports.hhdv.tt142-2015-btc.PL4')
                ->with('config',$config)
                ->with('model',$model)
                ->with('modelthoidiem',$modelthoidiem)
                ->with('pageTitle','Phụ lục 4');

        }else
            return view('errors.notlogin');
    }

    public function getTtHhPL4($tthh,$array){
        foreach($tthh as $tt){
            if($tt->mahh == $array->mahh && $tt->masoloai == $array->masoloai){
                $array->tenhh = $tt->tenhh;
                $array->nsx = $tt->nsx;
                $array->dvt = $tt->dvt;
            }
        }
    }

    //Phụ lục 5
    public function PL5(Request $request){
        if (Session::has('admin')) {
            $input = $request->all();

            $input = $request->all();
            $modelhs = HsThamDinhGia::whereBetween('thoidiem',array($input['ngaytu'],$input['ngayden']))
                ->get();
            //dd($model);
            $arrayid='';
            foreach($modelhs as $hs){
                $arrayid = $arrayid.$hs->mahs.',';
            }
            //dd($arrayid);

            $model = ThamDinhGia::wherein('mahs',explode(',',$arrayid))->get();
            //dd($model);
            foreach($model as $tths){
                $this->getTtTgPL5($modelhs,$tths);
            }


            return view('reports.hhdv.TT142-2015-BTC.PL5')
                ->with('model',$model)
                ->with('pageTitle','Phụ lục 5');

        }else
            return view('errors.notlogin');
    }

    public function getTtTgPL5($tgs,$array){
        foreach($tgs as $tg){
            if($tg->id == $array->idhs){
                $array->thoidiem = $tg->thoidiem;
                $array->diadiem = $tg->diadiem;
                $array->ppthamdinh = $tg->ppthamdinh;
                $array->mucdich = $tg->mucdich;
                $array->dvyeucau = $tg->dvyeucau;
                $array->thoihan = $tg->thoihan;
            }
        }
    }

    //Phụ lục 6
    public function PL6(Request $request){
        if (Session::has('admin')) {
            $input = $request->all();
            $nam = $input['nam'];
            $modelhs = TsNhaDat::where('nam',$nam)
                ->get();
            $arrayid ='';
            foreach($modelhs as $hs){
                $arrayid = $arrayid.$hs->mahs.',';
            }
            $model = TtTsNhaDat::wherein('mahs',explode(',',$arrayid))
                ->get();
            return view('reports.hhdv.TT142-2015-BTC.PL6')
                ->with('nam',$nam)
                ->with('model',$model)
                ->with('pageTitle','Phụ lục 6');
        }else
            return view('errors.notlogin');
    }

    //Phụ lục 7
    public function PL7(Request $request){
        if (Session::has('admin')) {
            $input = $request->all();
            $nam = $input['nam'];
            $modelhs = TsOtoKhac::where('nam',$nam)
                ->get();
            $arrayid ='';
            foreach($modelhs as $hs){
                $arrayid = $arrayid.$hs->mahs.',';
            }
            $model = TtTsOtoKhac::wherein('mahs',explode(',',$arrayid))
                ->get();
            return view('reports.hhdv.TT142-2015-BTC.PL7')
                ->with('nam',$nam)
                ->with('model',$model)
                ->with('pageTitle','Phụ lục 7');
        }else
            return view('errors.notlogin');
    }
}
