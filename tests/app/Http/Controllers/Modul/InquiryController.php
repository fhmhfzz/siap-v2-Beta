<?php

namespace App\Http\Controllers\Modul;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

use DataTables;
use DateTime;
use Validator;
use Carbon\Carbon;

use Illuminate\Support\Facades\Crypt;
use Session;

class InquiryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function __construct()
    {
        $this->middleware('cek:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getData(Request $request)
    {
        $output = "";
        $vlevel = Session::get('login')[0]->level;
        $userid = Session::get('login')[0]->username;
        $cari = "%".$request->search."%";
        //data kolom yang akan di tampilkan
        $aColumns = array( 'asuransi','cabang','produk','createby','regid','policyno','nama','tgllahir','mulai','up','premi','status','aksi' );

        $sIndexColumn = "regid";
        
        //nama table database
		
        // DB::table('tr_sppa')
        $sTable = "SELECT SQL_CALC_FOUND_ROWS asuransi,cabang,produk,createby,regid,policyno,nama,tgllahir,mulai,up,premi,status,aksi FROM ( SELECT mc.msdesc cabang, ma.msdesc asuransi, mp.msdesc produk, aa.createby, regid, policyno, nama, tgllahir, mulai, up, premi, ms.msdesc status, Concat(aa.regid, '-', aa.status, '-', (IF(aa.policyno IS NOT NULL, aa.policyno, 'null'))) aksi FROM   
		(select * from tr_sppa where createby='$userid' ) aa LEFT JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'ASURANSI') ma ON ma.msid = aa.asuransi INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'CAB') mc ON mc.msid = aa.cabang INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'STREQ') ms ON ms.msid = aa.status INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'PRODUK') mp ON mp.msid = aa.produk WHERE  aa.up != '' AND aa.premi != 0";

        // if ($vlevel == 'mkt' or $vlevel == 'smkt') {
        //     $sTable = $sTable . "AND 'aa.createby' IN ( SELECT CASE WHEN 'a.parent'='a.username' THEN 'a.parent' ELSE 'a.username' END FROM   ms_admin a WHERE  ( 'a.username'='$userid' OR 'a.parent'='$userid'))";
            
        // } elseif ($vlevel=="schecker" or $vlevel=="checker") {
        //     $sTable = $sTable . "AND 'aa.cabang' like (SELECT CASE WHEN cabang='ALL' THEN '%%' ELSE cabang END cabang FROM ms_admin WHERE username='$userid')";
                                              
        // } else if ($vlevel=="broker") {
        //     $sTable = $sTable . " and length('aa.regid') > 10 ";
            
                                            
        // } else if ($vlevel=="insurance") {
        //     $sTable = $sTable . "AND 'aa.asuransi' LIKE (SELECT cabang FROM ms_admin WHERE level='insurance' AND username='$userid' )";
        // }

        // $mitra = (Session::get('login')[0]->mitra == NULL)?('NOM'):(Session::get('login')[0]->mitra);
        // if ($mitra !== 'NOM') {
        //     $sTable = $sTable . " AND 'aa.mitra' = '$mitra' "; 
        // }

        $sTable = $sTable .  ") t_baru WHERE policyno LIKE '$cari' OR produk LIKE '$cari' OR cabang LIKE '$cari' OR asuransi LIKE '$cari' OR regid LIKE '$cari' OR nama LIKE '$cari' OR up LIKE '$cari' OR premi LIKE '$cari' OR status LIKE '$cari'  OR status  LIKE '$cari' order by aa.regid desc ";

        $data = DB::select($sTable);

        if($data){

            foreach ($data as $key => $p) {
                                    
                $output.='<div class="col-md-6 card shadow-sm mb-4">'.
                            '<div class="card-body">'.
                                '<div class="row container">'.
                                    '<div class="col align-self-center">'.
                                        '<h5 class="mb-0 text-color-theme">'.$p->nama.'</h5>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.

                            '<div class="card border-0">'.
                                '<div class="card-body">'.
                                    '<div class="row container mb-2">'.
                                        '<div class="col align-self-center">'.
                                            '<p class="text-muted size-12 mb-0">UP : '.number_format(floatval($p->up)).'</p>'.
                                            '<p class="text-muted size-12">Primi : '.number_format(floatval($p->premi)).'</p>'.
                                        '</div>'.
                                        '<div class="col align-self-top text-end">'.
                                            '<p class="text-muted size-12 mb-0">Jakarta, 1 Januari</p>'.
                                            '<p class="text-muted size-12">' .$p->status.'</p>'.
                                        '</div>'.
                                    '</div>'.
                                    '<a href="" class="btn btn-default w-100 shadow small">Detail</a>'.
                                '</div>'.

                            '</div>'.
                        '</div> ';
            }


            return Response($output);

        }
        
    }

    public function getDesktop(Request $request)
    {
        if($request->ajax())
        {
            $vlevel = Session::get('login')[0]->level;
            $userid = Session::get('login')[0]->username;
            $cari = "%".$request->search."%";
            // $cari = "";
            $aColumns = array( 'asuransi','cabang','produk','createby','regid','policyno','nama','tgllahir','mulai','up','premi','status','aksi' );

        $sIndexColumn = "regid";
        
      

        // if ($vlevel == 'mkt' or $vlevel == 'smkt')
		// 	{
		// 	$sTable = "SELECT SQL_CALC_FOUND_ROWS asuransi,cabang,produk,createby,regid,policyno,nama,tgllahir,mulai,up,premi,status,aksi FROM ( SELECT mc.msdesc cabang, ma.msdesc asuransi, mp.msdesc produk, aa.createby, regid, policyno, nama, tgllahir, mulai, up, premi, ms.msdesc status, Concat(aa.regid, '-', aa.status, '-', (IF(aa.policyno IS NOT NULL, aa.policyno, 'null'))) aksi FROM   
		// 	(select * from tr_sppa where createby='$userid' ) aa LEFT JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'ASURANSI') ma ON ma.msid = aa.asuransi INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'CAB') mc ON mc.msid = aa.cabang INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'STREQ') ms ON ms.msid = aa.status INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'PRODUK') mp ON mp.msid = aa.produk WHERE  aa.up != '' AND aa.premi != 0";
        //     /* $sTable = $sTable . " AND 'aa.createby' IN ( SELECT CASE WHEN 'a.parent'='a.username' THEN 'a.parent' ELSE 'a.username' END FROM   ms_admin a WHERE  ( 'a.username'='$userid' OR 'a.parent'='$userid')) "; */
		// 	} 
        //  if ($vlevel == 'smkt')
		// 	{
		// 	$sTable = "SELECT SQL_CALC_FOUND_ROWS asuransi,cabang,produk,createby,regid,policyno,nama,tgllahir,mulai,up,premi,status,aksi FROM ( SELECT mc.msdesc cabang, ma.msdesc asuransi, mp.msdesc produk, aa.createby, regid, policyno, nama, tgllahir, mulai, up, premi, ms.msdesc status, Concat(aa.regid, '-', aa.status, '-', (IF(aa.policyno IS NOT NULL, aa.policyno, 'null'))) aksi FROM   
		// 	(select * from tr_sppa where createby' IN ( SELECT CASE WHEN 'a.parent'='a.username' THEN 'a.parent' ELSE 'a.username' END FROM   ms_admin a WHERE  ( 'a.username'='$userid' OR 'a.parent'='$userid') ) aa LEFT JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'ASURANSI') ma ON ma.msid = aa.asuransi INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'CAB') mc ON mc.msid = aa.cabang INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'STREQ') ms ON ms.msid = aa.status INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'PRODUK') mp ON mp.msid = aa.produk WHERE  aa.up != '' AND aa.premi != 0";
           
		// 	} 
       
                                              

		// if ($vlevel=="broker") 
		// {
        //  $sTable = "SELECT SQL_CALC_FOUND_ROWS asuransi,cabang,produk,createby,regid,policyno,nama,tgllahir,mulai,up,premi,status,aksi FROM ( SELECT mc.msdesc cabang, ma.msdesc asuransi, mp.msdesc produk, aa.createby, regid, policyno, nama, tgllahir, mulai, up, premi, ms.msdesc status, Concat(aa.regid, '-', aa.status, '-', (IF(aa.policyno IS NOT NULL, aa.policyno, 'null'))) aksi FROM   
		// (select * from tr_sppa where premi != 0 ) aa LEFT JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'ASURANSI') ma ON ma.msid = aa.asuransi INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'CAB') mc ON mc.msid = aa.cabang INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'STREQ') ms ON ms.msid = aa.status INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'PRODUK') mp ON mp.msid = aa.produk WHERE  aa.up != '' AND aa.premi != 0"; 
		// /* $sTable = $sTable . " and length('aa.regid') > 10 "; */
                                                   
		// } 
		// if ($vlevel=="insurance") 
		// {
		// 	$sTable = "SELECT SQL_CALC_FOUND_ROWS asuransi,cabang,produk,createby,regid,policyno,nama,tgllahir,mulai,up,premi,status,aksi FROM ( SELECT mc.msdesc cabang, ma.msdesc asuransi, mp.msdesc produk, aa.createby, regid, policyno, nama, tgllahir, mulai, up, premi, ms.msdesc status, Concat(aa.regid, '-', aa.status, '-', (IF(aa.policyno IS NOT NULL, aa.policyno, 'null'))) aksi FROM   
		// 	(select * from tr_sppa where asuransi='$userid' ) aa LEFT JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'ASURANSI') ma ON ma.msid = aa.asuransi INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'CAB') mc ON mc.msid = aa.cabang INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'STREQ') ms ON ms.msid = aa.status INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'PRODUK') mp ON mp.msid = aa.produk WHERE  aa.up != '' AND aa.premi != 0";
		// }
			// $sTable = $sTable .  ") t_baru WHERE policyno LIKE '$cari' OR produk LIKE '$cari' OR cabang LIKE '$cari' OR asuransi LIKE '$cari' OR regid LIKE '$cari' OR nama LIKE '$cari' OR up LIKE '$cari' OR premi LIKE '$cari' OR status LIKE '$cari'  OR status  LIKE '$cari' LIMIT 10000";

            // $sTable = "SELECT SQL_CALC_FOUND_ROWS asuransi,cabang,produk,createby,regid,policyno,nama,tgllahir,mulai,up,premi,status,aksi FROM ( SELECT mc.msdesc cabang, ma.msdesc asuransi, mp.msdesc produk, aa.createby, regid, policyno, nama, tgllahir, mulai, up, premi, ms.msdesc status, Concat(aa.regid, '-', aa.status, '-', (IF(aa.policyno IS NOT NULL, aa.policyno, 'null'))) aksi FROM (select * from tr_sppa where createby='$userid' ) aa LEFT JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'ASURANSI') ma ON ma.msid = aa.asuransi INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'CAB') mc ON mc.msid = aa.cabang INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'STREQ') ms ON ms.msid = aa.status INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'PRODUK') mp ON mp.msid = aa.produk WHERE  aa.up != '' AND aa.premi != 0";
            

            // if ($vlevel == 'mkt' or $vlevel == 'smkt') {
            //     $sTable = $sTable . "AND 'aa.createby' IN ( SELECT CASE WHEN 'a.parent'='a.username' THEN 'a.parent' ELSE 'a.username' END FROM   ms_admin a WHERE  ( 'a.username'='$userid' OR 'a.parent'='$userid')";
                
            // } 
            // if ($vlevel=="schecker" or $vlevel=="checker") {
            //     $sTable = $sTable . "AND 'aa.cabang' like (SELECT CASE WHEN cabang='ALL' THEN '%%' ELSE cabang END cabang FROM ms_admin WHERE username='$userid')";
                                                
            // } 
            //  if ($vlevel=="broker") {
            //     $sTable = $sTable . " and length('aa.regid') > 10 ";
                
                                                
            // } else if ($vlevel=="insurance") {
            //     $sTable = $sTable . "AND 'aa.asuransi' LIKE (SELECT cabang FROM ms_admin WHERE level='insurance' AND username='$userid' )";
            // }

            // $mitra = (Session::get('login')[0]->mitra == NULL)?('NOM'):(Session::get('login')[0]->mitra);
            // if ($mitra !== 'NOM') {
            //     $sTable = $sTable . " AND 'aa.mitra' = '$mitra' "; 
            // }

            // $sTable = $sTable .  ") t_baru WHERE policyno LIKE '$cari' OR produk LIKE '$cari' OR cabang LIKE '$cari' OR asuransi LIKE '$cari' OR regid LIKE '$cari' OR nama LIKE '$cari' OR up LIKE '$cari' OR premi LIKE '$cari' OR status LIKE '$cari'  OR status  LIKE '$cari' ";
            $sTable = "SELECT SQL_CALC_FOUND_ROWS asuransi,cabang,produk,createby,regid,policyno,nama,tgllahir,mulai,up,premi,status,aksi FROM ( SELECT mc.msdesc cabang, 
    ma.msdesc                  asuransi, 
    mp.msdesc                  produk, 
    aa.createby, 
    regid, 
    policyno, 
    nama, 
    tgllahir, 
    mulai, 
    up, 
    premi, 
    ms.msdesc                  status,
    regid     reg_encode,						
    Concat(aa.regid, '-', aa.status, '-', (IF(aa.policyno IS NOT NULL, aa.policyno, ''))) aksi ,
    policyno spolicyno
    FROM   tr_sppa aa 
    LEFT JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'ASURANSI') ma 
            ON ma.msid = aa.asuransi 
    INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'CAB') mc 
            ON mc.msid = aa.cabang 
    INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'STREQ') ms 
            ON ms.msid = aa.status 
    INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'PRODUK') mp 
            ON mp.msid = aa.produk 
    WHERE  aa.up != '' 
    AND aa.premi != 0  ";

    if ($vlevel == 'mkt' or $vlevel == 'smkt') {
        $sTable .= "AND aa.createby IN ( SELECT CASE WHEN a.parent=a.username THEN a.parent ELSE a.username END FROM   ms_admin a WHERE  ( a.username='$userid' OR a.parent='$userid'))";
        
    } elseif ($vlevel=="schecker" or $vlevel=="checker") {
        $sTable .= "AND aa.cabang like (SELECT CASE WHEN cabang='ALL' THEN '%%' ELSE cabang END cabang FROM ms_admin WHERE username='$userid')";
                                        
    } else if ($vlevel=="broker") {
        $sTable .= " and length(aa.regid)>10 ";
        
                                        
    } else if ($vlevel=="insurance") {
        $sTable .= "AND aa.asuransi LIKE (SELECT cabang FROM ms_admin WHERE level='insurance' AND username='$userid' )";
                                        
    }

    $mitra = (Session::get('login')[0]->mitra == NULL)?('NOM'):(Session::get('login')[0]->mitra);
    if ($mitra !== 'NOM') {
        $sTable .= " AND aa.mitra = '$mitra' ";
    }

    $sTable = $sTable .  ") t_baru WHERE policyno LIKE '$cari' OR produk LIKE '$cari' OR cabang LIKE '$cari' OR asuransi LIKE '$cari' OR regid LIKE '$cari' OR nama LIKE '$cari' OR up LIKE '$cari' OR premi LIKE '$cari' OR status LIKE '$cari'  OR status  LIKE '$cari' LIMIT 100";
            $data = DB::select($sTable);
            // dd($data);

            return DataTables::of($data)
                ->addColumn('action', function($data){
                    $button = '<a href="'.'inquiry/view/'. Crypt::encryptString($data->regid).'"  class="btn btn-default btn-sm" style="display:inline !important;">View</a>&nbsp;';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
            }
    }

    public function data_cancel(Request $request)
    {
        if($request->ajax())
        {
            $vlevel = Session::get('login')[0]->level;
            $userid = Session::get('login')[0]->username;
            $cari = "%".$request->search."%";
            // $cari = "";
            $sTable = "SELECT regid,produk,nama,tgllahir,cabang,mulai,up,premi,status,aksi FROM (SELECT aa.regid, aa.nama, ac.msdesc 'cabang', ad.msdesc 'produk', aa.tgllahir, aa.mulai, aa.up, aa.premi, ab.msdesc 'status', concat(aa.regid,'-',aa.status) aksi FROM (select * from tr_sppa where status in ('5','6','20')";

            // $sTable = $sTable .  " WHERE policyno LIKE '$cari' OR produk LIKE '$cari' OR cabang LIKE '$cari' OR asuransi LIKE '$cari' OR regid LIKE '$cari' OR nama LIKE '$cari' OR up LIKE '$cari' OR premi LIKE '$cari' OR status LIKE '$cari'  OR status  LIKE '$cari' ";

            if ($vlevel=="schecker" or $vlevel=="checker") {
                $sTable .= " AND aa.cabang LIKE (SELECT CASE WHEN cabang='ALL' THEN '%%' ELSE cabang END cabang FROM ms_admin WHERE username='$userid') ";
                                                  
            } 

            $sTable .= " ) ) t_baru LIMIT 10";


            $data = DB::select($sTable);

            // dd($data);

            return DataTables::of($data)
                ->addColumn('action', function($data){
                    $button = '<a href="'.'inquiry/view/'. Crypt::encryptString($data->regid).'"  class="btn btn-default btn-sm" style="display:inline !important;">View</a>&nbsp;';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
            }
    }


    public function index()
    {
        // dd(Session::get('login')[0]->id_member);
        // $data = DB::select("SELECT * FROM md_product LEFT JOIN md_brand ON md_product.id_brand=md_brand.id_brand LEFT JOIN md_category ON md_product.id_category=md_category.id_category LEFT JOIN md_category_tokopedia ON md_product.id_category_tokopedia=md_category_tokopedia.id_category_tokopedia LEFT JOIN md_category_shopee ON md_product.id_category_shopee=md_category_shopee.id_category_shopee");

        //primary key
        // dd("ok");

        $vlevel = Session::get('login')[0]->level;
        $userid = Session::get('login')[0]->username;

        //data kolom yang akan di tampilkan
        
        //nama table database

        // DB::table('tr_sppa')

        

        // dd($inquiry);

        $data = [
            'judul' => 'Inquiry',
        ];
        $agent = new \Jenssegers\Agent\Agent;
        if($agent->isDesktop()){
            return view('master.inquiry.index_desktop', compact('data'));
        }else{

            $aColumns = array( 'asuransi','cabang','produk','createby','regid','policyno','nama','tgllahir','mulai','up','premi','status','aksi' );

            $sIndexColumn = "regid";
            $sTable = "SELECT SQL_CALC_FOUND_ROWS asuransi,cabang,produk,createby,regid,policyno,nama,tgllahir,mulai,up,premi,status,aksi FROM ( SELECT mc.msdesc cabang, ma.msdesc asuransi, mp.msdesc produk, aa.createby, regid, policyno, nama, tgllahir, mulai, up, premi, ms.msdesc status, Concat(aa.regid, '-', aa.status, '-', (IF(aa.policyno IS NOT NULL, aa.policyno, 'null'))) aksi FROM   tr_sppa aa LEFT JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'ASURANSI') ma ON ma.msid = aa.asuransi INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'CAB') mc ON mc.msid = aa.cabang INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'STREQ') ms ON ms.msid = aa.status INNER JOIN ( SELECT msid, msdesc FROM ms_master where mstype = 'PRODUK') mp ON mp.msid = aa.produk WHERE  aa.up != '' AND aa.premi != 0";
        // }

            $sTable = $sTable .  ") t_baru LIMIT 10";

            $inquiry = DB::select($sTable);
            return view('master.inquiry.index', compact('data','inquiry'));
        }
        
    }

    public function cancel()
    {
        $vlevel = Session::get('login')[0]->level;
        $userid = Session::get('login')[0]->username;

        $data = [
            'judul' => 'Inquiry Cancel',
        ];

        $inquiry = "    ";

        $agent = new \Jenssegers\Agent\Agent;
        if($agent->isDesktop()){
            return view('master.inquiry.inquiry_cancel', compact('data','inquiry'));
        }else{
            return view('master.inquiry.index', compact('data','inquiry'));
        }
        // return view('master.pengajuan.add');
    }

    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'product_name'   => 'required',
        //     'product_code' => 'required',
        // ]);
        // return Validator::make($data, [
        //     'product_name' => ['required', 'string', 'max:255'],
        //     'product_code' => ['required', 'string', 'max:255'],
        // ]);
        $brand = DB::select("SELECT * FROM md_brand WHERE id_brand='$request->id_brand'");
        $kode = DB::select("SELECT max(id_sku_detail) as kodeTerbesar FROM md_product");
        $urutan = (int) substr($kode[0]->kodeTerbesar, -5);
        $urutan++;
        $depan = $brand[0]->brand_code. $request->varian_code;
        $sku_no = $depan . sprintf("%05s", $urutan);
        // dd($sku_no);


        $sku_name = $request->product_name.' - '.$request->varian_name;

        $sku_no_digit = strlen($sku_no);
        $sku_name_digit = strlen($sku_name);

        $sku_status = $request->sku_status;
        $product_name = $request->product_name;
        $varian_name = $request->varian_name;
        $varian_code = $request->varian_code;
        $id_brand = $request->id_brand;
        $id_category = $request->id_category;
        $id_category_tokopedia = $request->id_category_tokopedia;
        $id_category_shopee = $request->id_category_shopee;
        $length = $request->length;
        $width = $request->width;
        $height = $request->height;
        $volume_weight = ($length * $width * $height) /6;
        $actual_weight = $request->actual_weight;
        $retail_price = $request->retail_price;
        $username = Session::get('login')[0]->username;
        // dd($product_code);

        DB::select("INSERT INTO md_product VALUES ('','$sku_no','$sku_name','$sku_status','$product_name','$varian_name','$varian_code','$id_brand','$sku_no_digit','$sku_name_digit','$id_category','$id_category_tokopedia','$id_category_shopee','$length','$width','$height','$volume_weight','$actual_weight','$retail_price','$username')");
        // return view('master.product.add');
        return redirect()->intended('product')->with('success', 'Data Berhasil ditambahkan');
    }

    public function view($id)
    {
        // dd(Session::get('login')[0]->id_member);
        $sid = Crypt::decryptString($id);

        $data = DB::select("select aa.regid,aa.nama,aa.noktp,aa.jkel,aa.pekerjaan,aa.cabang,aa.tgllahir,aa.mulai, aa.akhir,aa.masa,aa.up,aa.status,aa.createdt,aa.createby,aa.editdt,aa.editby,aa.validby, aa.validdt,aa.nopeserta,aa.usia,aa.premi,aa.epremi,aa.tpremi, aa.bunga,aa.tunggakan, aa.produk,aa.mitra,aa.comment,aa.asuransi,aa.policyno,aa.asuransi,ab.msdesc tstatus,aa.hubungan_ahli_waris from tr_sppa aa  left join ms_master ab on aa.status=ab.msid and ab.mstype='STREQ' where aa.regid='$sid'");
        $produk = DB::select("select ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='produk' order by ms.mstype");
        $cabang = DB::select("select   ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='cab'  order by ms.mstype");
        $pekerjaan = DB::select("select   ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='kerja'  order by ms.mstype");   
		$asuransi = DB::select("select ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='asuransi' and ms.msid<>'ALL' order by ms.mstype");		
		$mitra = DB::select("select ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='mitra' and ms.msid<>'ALL' order by ms.mstype");
		$jkel = DB::select("select   ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='jkel'  order by ms.mstype");      		
        $file = DB::select("SELECT a.regid,a.tglupload,a.nama_file,a.tipe_file,a.ukuran_file,a.file,a.pages,a.seqno,a.jnsdoc from tr_document a  where regid='$id' ");
        $dokumen = DB::select("SELECT regid, tglupload, nama_file, tipe_file, ukuran_file,file,pages,createby,createdt seqno,jnsdoc,catdoc FROM tr_document WHERE regid='$sid'");
        $log = DB::select("SELECT a.regid,a.status,a.comment,a.createdt,a.createby ,b.msdesc stdesc from tr_sppa_log a inner join ms_master b on a.status=b.msid and b.mstype='streq' where regid='$sid' order by a.createdt desc");
        // dd($dokumen);
        return view('master.inquiry.view', compact('data','produk','cabang','pekerjaan','asuransi','mitra','jkel','file','dokumen','log'));
    }

    public function detail_inquiry_claim($id)
    {
        // dd(Session::get('login')[0]->id_member);
        $sid = Crypt::decryptString($id);

        $data = DB::select("select aa.*,ab.msdesc tstatus  from tr_sppa aa  inner join ms_master ab on aa.status=ab.msid and ab.mstype='STREQ' where aa.regid='$sid'");
        $produk = DB::select("select ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='produk' order by ms.mstype");
        $cabang = DB::select("select   ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='cab'  order by ms.mstype");
        $pekerjaan = DB::select("select   ms.msid comtabid,msdesc comtab_nm from ms_master ms where ms.mstype='kerja'  order by ms.mstype");        
        $file = DB::select("SELECT a.regid,a.tglupload,a.nama_file,a.tipe_file,a.ukuran_file,a.file,a.pages,a.seqno,a.jnsdoc from tr_document a  where regid='$id' ");
        $dokumen = DB::select("SELECT a.* from tr_document a  where regid='$sid'");
        // dd($dokumen);
        return view('master.inquiry.view_cancel', compact('data','produk','cabang','pekerjaan','file','dokumen'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'product_name'   => 'required',
            'product_code' => 'required',
        ]);
        $product_name = $request->product_name;
        $product_code = $request->product_code;
        $username = Session::get('login')[0]->username;
        // dd(Session::get('login')[0]->id_member);
        $data = DB::select("UPDATE md_product SET product_name='$product_name', product_code='$product_code', created_by='$username' WHERE id_product='$request->id'");

        return redirect()->intended('product')->with('success', 'Data Berhasil diubah');
    }

    public function delete($id)
    {
        DB::select("DELETE FROM md_product WHERE id_product='$id'");
        // return view('master.product.add');
        return redirect()->intended('product')->with('success', 'Data Berhasil dihapus');
    }
}
