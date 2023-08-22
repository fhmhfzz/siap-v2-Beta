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
use Jenssegers\Agent\Agent;
use PDF;
use Session;

class DashboardController extends Controller
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
        $this->middleware(function ($request, $next) {
            // $user = Auth::user();

            if(Session::get('login')[0]->level == 'broker'){
                // View::share( 'loggedInUser' ,  $user );

                return $next($request);
            }
            return redirect()->intended('index')->with('error', 'Maaf kamu tidak dapat mengakses ini');
            
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getData(Request $request)
    {
        // if($request->ajax())
        // {
            $vlevel = Session::get('login')[0]->level;
            $userid = Session::get('login')[0]->username;
            $cari = "%".$request->search."%";
            // Session::get('login')[0]->id_member;
            $output = "";
            $sTable = "SELECT SQL_CALC_FOUND_ROWS regid,nama,up,premi,sts,comment,reg_encode,status FROM (SELECT aa.regid,aa.nama,aa.up,aa.premi,aa.comment,ad.msdesc sts,aa.regid reg_encode,aa.status FROM tr_sppa aa LEFT JOIN ms_admin ac ON ac.username = aa.createby LEFT JOIN ms_master ad ON ad.msid = aa.status AND ad.mstype = 'STREQ'";

            // if ($vlevel == 'smkt') {
            //     $sTable .= "AND aa.createby IN ( SELECT CASE WHEN a.parent=a.username THEN a.parent ELSE a.username END FROM ms_admin a WHERE ( a.username='$userid' OR a.parent='$userid')) ";
                
            // } else {
            //     $sTable .= "AND aa.createby IN ('$userid') ";
                                                  
            // }
            
            // $rate = (Session::get('login')[0]->rate == NULL)?('NOM'):(Session::get('login')[0]->rate);
            // if ($rate !== 'NOM') {
            //     $sTable .= " AND aa.rate = '$rate' ";
            // }
            
            // if ($_POST['sSearch'] == '') {
            //     $sTable .= " ORDER BY aa.createdt DESC";
            // }
            $sTable .= " ) t_baru WHERE regid LIKE '$cari' OR nama LIKE '$cari' OR up LIKE '$cari' OR premi LIKE '$cari' OR sts LIKE '$cari' OR comment LIKE '$cari' OR reg_encode LIKE '$cari' OR status  LIKE '$cari' LIMIT 10";


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
                                                '<p class="text-muted size-12">' .$p->sts.'</p>'.
                                            '</div>'.
                                        '</div>'.
                                        '<a href="'.'pengajuan/edit/'. Crypt::encryptString($p->regid).'" class="btn btn-default w-100 shadow small">Detail</a>'.
                                    '</div>'.

                                '</div>'.
                            '</div> ';
                }


                return Response($output);

           }
            // dd($data);
            // return DataTables::of($data)
            //         ->addColumn('action', function($data){
            //             $button = '<a id=""  class="btn btn-default btn-sm" style="display:inline !important;">Edit</a>&nbsp;';
            //             $button .= '<a id=""  class="btn btn-default btn-sm" style="display:inline !important;">Salin</a>&nbsp;';
            //             $button .= '<a id=""  class="btn btn-default btn-sm" style="display:inline !important;">Log</a>&nbsp;';
            //             $button .= '<a id=""  class="btn btn-default btn-sm" style="display:inline !important;">Doc</a>&nbsp;';
            //             $button .= '<a id=""  class="btn btn-default btn-sm" style="display:inline !important;">Approve</a>';
            //             return $button;
            //         })
            //         ->rawColumns(['action'])
            //         ->make(true);
        // }
        // return view('master.pengajuan.index');
    }

    public function getDesktop(Request $request)
    {
        if($request->ajax())
        {
            $vlevel = Session::get('login')[0]->level;
            $userid = Session::get('login')[0]->username;
            $cari = "%".$request->search."%";

            $sTable = "SELECT a.*,b.msdesc sproduk,concat(produk,jkel,umurb,umura,insperiodyy,insperiodmm)  from tr_rates a left join ms_master b on a.produk=b.msid and b.mstype='produk' order by produk,jkel,umurb,insperiodmm,gpb ASC  ";


        $sTable .= " LIMIT 10";
            $data = DB::select($sTable);

            // dd($data);

            return DataTables::of($data)
                // ->addColumn('action', function($data){
                //     $button = '<a href="'.'pengajuan/edit/'. Crypt::encryptString($data->id_contact).'"  class="btn btn-default btn-sm" style="display:inline !important;">Edit</a>&nbsp;';
                //     return $button;
                // })
                ->rawColumns(['action'])
                ->make(true);
            }
    }



    public function index(Request $request)
    {
        $slevel = Session::get('login')[0]->level;
        $level = Session::get('login')[0]->level;
        // $userid = Session::get('login')[0]->id_;
        $userid = Session::get('login')[0]->username;
        // $sTable = "SELECT distinct date_format(now(),'%Y')  sdate ,a.username,level FROM ms_admin a  where a.username='$userid'  ";

        // dd(Session::get('login')[0]);
        // $sTable .= " LIMIT 10";
        // $rate = DB::select($sTable);

        $lsp2="";
        $lsp3="";
        $ssql = "";

        if ($slevel=="broker" or $slevel=="smon" )
        {
            $lsp2="Sertifikat";
            $lsp3="Realisasi";
            $ssql=" select sum(sp1) s1,sum(sp2) s2,sum(sp3) s3,sum(sp4) s4,sum(sp5) s5,sum(sp6) s6";
            $ssql=$ssql . " from  ";
            $ssql=$ssql . " ( ";
            $ssql=$ssql . " SELECT count(1) sp1,0 sp2, 0 sp3,0 sp4,0 sp5, 0 sp6 FROM tr_sppa where status in (1)  and cabang<>''  ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,count(1) sp2, 0 sp3,0 sp4,0 sp5, 0 sp6  FROM tr_sppa where status in (5)  and cabang<>'' ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, count(1)  sp3,0 sp4,0 sp5, 0 sp6   FROM tr_sppa where status in (3) and cabang<>'' ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,count(1) sp4,0 sp5,0 sp6  FROM tr_sppa where status in (10)  and cabang<>'' ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,0 sp4 ,count(1) sp5,0 sp6  FROM tr_sppa where status in (11)  and cabang<>''  ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,0 sp4 ,0 sp5,count(1) sp6  FROM tr_sppa  inner join tr_sppa_paid  on tr_sppa.regid=tr_sppa_paid.regid  where  cabang<>'' ";
            $ssql=$ssql . " ) aa ";
        }

        if ($slevel=="checker" or $level=="schecker" )
        {
            $lsp2="Active";
            $lsp3="Realisasi";
            $ssql=" select sum(sp1) s1,sum(sp2) s2,sum(sp3) s3,sum(sp4) s4,sum(sp5) s5 ,sum(sp6) s6";
            $ssql=$ssql . " from  ";
            $ssql=$ssql . " ( ";
            $ssql=$ssql . " SELECT count(1) sp1,0 sp2, 0 sp3,0 sp4,0 sp5, 0 sp6 FROM tr_sppa where status in (1)  and cabang like (select case when cabang='ALL' THEN '%%' ELSE cabang END cabang  from ms_admin  where username='$userid' )  ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,count(1) sp2, 0 sp3,0 sp4,0 sp5, 0 sp6  FROM tr_sppa where status in (2)  and cabang like (select case when cabang='ALL' THEN '%%' ELSE cabang END cabang  from ms_admin  where username='$userid' )  ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, count(1)  sp3,0 sp4,0 sp5, 0 sp6   FROM tr_sppa where status in (3)  and cabang like (select case when cabang='ALL' THEN '%%' ELSE cabang END cabang from ms_admin  where username='$userid' )  ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,count(1) sp4,0 sp5,0 sp6  FROM tr_sppa where status in (10,4)  and cabang like (select case when cabang='ALL' THEN '%%' ELSE cabang END cabang from ms_admin  where username='$userid' )  ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,0 sp4 ,count(1) sp5,0 sp6  FROM tr_sppa where status in (11,5)  and cabang like (select case when cabang='ALL' THEN '%%' ELSE cabang END cabang from ms_admin  where username='$userid' )  ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,0 sp4 ,0 sp5,count(1) sp6  FROM tr_sppa  inner join tr_sppa_paid  on tr_sppa.regid=tr_sppa_paid.regid   ";
            $ssql=$ssql . " where tr_sppa.cabang like (select case when cabang='ALL' THEN '%%' ELSE cabang END cabang from ms_admin  where username='$userid' )  ";
            $ssql=$ssql . " ) aa ";
        }

        if ($slevel=="insurance" )
        {
            $lsp2="Sertifikat";
            $lsp3="Realisasi";
            $ssql=" select sum(sp1) s1,sum(sp2) s2,sum(sp3) s3,sum(sp4) s4,sum(sp5) s5 ,sum(sp6) s6";
            $ssql=$ssql . " from  ";
            $ssql=$ssql . " ( ";
            $ssql=$ssql . " SELECT count(1) sp1,0 sp2, 0 sp3,0 sp4,0 sp5, 0 sp6 FROM tr_sppa where status in (1)  and asuransi in (select cabang  from ms_admin  where username='$userid' )  ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,count(1) sp2, 0 sp3,0 sp4,0 sp5, 0 sp6  FROM tr_sppa where status in (5)  and asuransi in (select cabang  from ms_admin  where username='$userid' ) ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, count(1)  sp3,0 sp4,0 sp5, 0 sp6   FROM tr_sppa where status in (3)  and asuransi in (select cabang  from ms_admin  where username='$userid' ) ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,count(1) sp4,0 sp5,0 sp6  FROM tr_sppa where status in (10)  and asuransi in (select cabang  from ms_admin  where username='$userid' ) ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,0 sp4 ,count(1) sp5,0 sp6  FROM tr_sppa where status in (11)  and asuransi in (select cabang  from ms_admin  where username='$userid' ) ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,0 sp4 ,0 sp5,count(1) sp6  FROM tr_sppa  inner join tr_sppa_paid  on tr_sppa.regid=tr_sppa_paid.regid   ";
            $ssql=$ssql . " where tr_sppa.asuransi in (select cabang  from ms_admin  where username='$userid' ) ";
            $ssql=$ssql . " ) aa ";
        }

        if ($slevel=="mkt" or $level=="smkt" )
        {
            $lsp2="Active";
            $lsp3="Realisasi";
            $ssql=" select sum(sp1) s1,sum(sp2) s2,sum(sp3) s3,sum(sp4) s4,sum(sp5) s5 ,sum(sp6) s6";
            $ssql=$ssql . " from  ";
            $ssql=$ssql . " ( ";
            $ssql=$ssql . " SELECT count(1) sp1,0 sp2, 0 sp3,0 sp4,0 sp5, 0 sp6 FROM tr_sppa where status in (1)    ";
            $ssql=$ssql . " and createby in (select  a.uname from vw_msadmin_mkt a ";
            $ssql=$ssql . " where a.username='$userid' or a.parent='$userid') ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,count(1) sp2, 0 sp3,0 sp4,0 sp5, 0 sp6  FROM tr_sppa where status in (2)   ";
            $ssql=$ssql . " and createby in (select  a.uname from vw_msadmin_mkt a ";
            $ssql=$ssql . " where a.username='$userid' or a.parent='$userid') ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, count(1)  sp3,0 sp4,0 sp5, 0 sp6   FROM tr_sppa where status in (3) ";
            $ssql=$ssql . " and createby in (select  a.uname from vw_msadmin_mkt a ";
            $ssql=$ssql . " where a.username='$userid' or a.parent='$userid') ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,count(1) sp4,0 sp5,0 sp6  FROM tr_sppa where status in (10,4)  ";
            $ssql=$ssql . " and createby in (select  a.uname from vw_msadmin_mkt a ";
            $ssql=$ssql . " where a.username='$userid' or a.parent='$userid') ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,0 sp4 ,count(1) sp5,0 sp6  FROM tr_sppa where status in (11,5)   ";
            $ssql=$ssql . " and createby in (select  a.uname from vw_msadmin_mkt a ";
            $ssql=$ssql . " where a.username='$userid' or a.parent='$userid') ";
            $ssql=$ssql . " union  ";
            $ssql=$ssql . " SELECT  0 sp1,0 sp2, 0 sp3,0 sp4 ,0 sp5,count(1) sp6  FROM tr_sppa a inner join tr_sppa_paid  b on a.regid=b.regid   ";
            $ssql=$ssql . " and a.createby in (select  a.uname from vw_msadmin_mkt a ";
            $ssql=$ssql . " where a.username='$userid' or a.parent='$userid') ";
            $ssql=$ssql . " ) aa ";
        }


        $query = DB::select($ssql);

        // $custSelect = "LEFT(p.sg,7) grup, ";
        // $custGroup = "GROUP BY LEFT(p.sg,7) ";
        // $custOrder = "ORDER BY LEFT(p.sg,7) ";

        $label = ['premi','sertifikat'];

        $sqlPremi = "SELECT Sum(sj1) premi, sum(sj2) sertifikat FROM (SELECT b.paiddt sg ,sum(a.premi) sj1,0 sj2 FROM tr_sppa a inner join tr_sppa_paid b ON a.regid=b.regid GROUP BY b.paiddt UNION ALL SELECT b.tgllapor sg ,0 sj1,sum(nilaios) sj2 FROM tr_sppa a inner join tr_claim b ON a.regid=b.regid where b.statclaim not in ('94') GROUP BY b.tgllapor ) P ";

        $datapremi = DB::select($sqlPremi);

        // dd($datapremi);

        $data = [
            'judul' => 'Dashboard',
        ];

        $agent = new \Jenssegers\Agent\Agent;
            return view('modul.dashboard.index_desktop', compact('data','query','label','datapremi'));
        // if($agent->isDesktop()){
        // }else{
        //     return view('modul.dashboard.index', compact('data','query','label','datapremi'));
        // }
    }

    public function add()
    {
        // dd(Session::get('login')[0]->id_member);
        // $brand = DB::select("SELECT * FROM md_brand");
        // $category = DB::select("SELECT * FROM md_category");
        // $tokopedia = DB::select("SELECT * FROM md_category_tokopedia");
        // $shopee = DB::select("SELECT * FROM md_category_shopee");
        // return view('master.product.add', compact('brand','tokopedia','shopee','category'));
        $produk = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='produk' and ms.msid<>'ALL' order by ms.msdesc  asc");
        $jkel = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='JKEL'  order by ms.msdesc  asc");
        $kerja = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='KERJA' order by ms.msdesc  asc");
        $cab = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='CAB' and ms.msid<>'ALL' order by ms.msdesc  asc");
        $rate = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='rate'  order by ms.msdesc  asc");
        $hubungan = DB::select("select ms.msid comtabid,left(msdesc,50) comtab_nm from ms_master ms where ms.mstype='hubungan' order by ms.msid");

        // dd($jkel);

        return view('master.pengajuan.add', compact('produk','jkel','kerja','cab','rate','hubungan'));
    }

    function hitung_umur($tanggal_lahir, $tanggal_mulai, $result = null, $naik = true)
	{
	    $d1     = new DateTime($tanggal_lahir);
	    $d2     = new DateTime($tanggal_mulai);
	    $diff   = $d2->diff($d1);
	    $tahun  = $diff->y;
	    $bulan  = $diff->m;
	    $hari   = $diff->d;
	    if ($naik) {
	        if ($diff->m >= 6) {
	            $tahun++;
	            $bulan = 0;
	            $hari = 0;
	        }
	    }
	    
	    if ($result == null) {
	        return $tahun;
	    } else if ($result == "bulan") {
	        return $bulan;
	    } else if ($result == "hari") {
	        return $hari;
	    }
	    // return "Usia ".$diff->y." tahun, ".$diff->m." bulan, ".$diff->d." hari";
	}

    public function store(Request $request)
    {

        $sup = str_replace('.', '', $request->up);
        $sproduk = $request->produk;
        $sjkel = $request->jkel;
        $snama      = str_replace("'", "`", $request->nama);
        $susia = $request->usia;
        $snoktp = $request->noktp;
        $stmplahir = $request->tmplahir;
        $smasa = $request->masa;
        $salamat = $request->alamat;
        $spekerjaan = $request->pekerjaan;
        $scabang = $request->cabang;
        $snopeserta = $request->nopeserta;
        $scabang = $request->cabang;
        $stunggakan = $request->tunggakan;
        $stgllahir = $request->tgllahir;
        $smulai = $request->mulai;
        $srate = $request->rate;
        $sakhir     = date('Y-m-d', strtotime('+' . $smasa . 'months', strtotime($smulai))); 
        $hubunganahli  = $request->hubungan;
        $hubunganahli2 = $request->ket_hubungan;
        $nmahli = $request->nama_ahli;
        $notelpahli = $request->notelp_ahli;
		$scomment = $request->catatan;
        $status     = '0';
        $userid = Session::get('login')[0]->username;
        $sdate      = date('Y-m-d H:i:s');
        // $stgllahir = $request->tgllahir;

        $prevno = DB::select("SELECT CONCAT(CONCAT(prevno, DATE_FORMAT(NOW(), '%y%m')),RIGHT(CONCAT(formseqno, tbl_lastno_trans.lastno),formseqlen)) AS seqno FROM tbl_lastno_form LEFT JOIN tbl_lastno_trans ON tbl_lastno_form.trxid = tbl_lastno_trans.trxid WHERE tbl_lastno_form.trxid = 'regid'");

        $regid = $prevno[0]->seqno;

        DB::select("UPDATE tbl_lastno_trans SET lastno = lastno +1 WHERE trxid = 'regid'");


        $susia      = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $smulai);
        $sbulan     = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $smulai, "bulan");
        $shari      = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $smulai, "hari");
    //  $total_usia = ($susia + ($smasa / 12));
        
        // belum naik / reset bulan hari
        $stgla = date('Y-m-d', strtotime('+' . $smasa . 'months', strtotime($smulai))); 

        $busia      = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $sakhir, null, false);
        $bbulan     = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $sakhir, "bulan", false);
        $bhari      = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $sakhir, "hari", false);

        $term = DB::select("SELECT umurb, umura, maxup FROM tr_term WHERE produk = '$sproduk' ");
        $sbumurb = $term[0]->umurb;
        $sbumura = $term[0]->umura;
        $smaxup  = $term[0]->maxup;
        // $susia= 20;
        // echo $susia. "susia <br>";
        // echo $stunggakan. "stunggakan <br>";
        // $rates = DB::select("SELECT rates,ratesother,tunggakan,bunga,umurb,umura,$susia FROM tr_rates WHERE produk = '$sproduk' AND jkel = '$sjkel' AND '$susia' BETWEEN umurb AND umura AND insperiodmm = '$smasa' AND '$stunggakan' BETWEEN gpb AND gpa AND $sup BETWEEN minup AND maxup ");
        $rates = DB::select("SELECT * FROM tr_rates WHERE produk = '$sproduk' AND jkel = '$sjkel' AND '$susia' BETWEEN umurb AND umura AND insperiodmm = '$smasa' AND '$stunggakan' BETWEEN gpb AND gpa AND $sup BETWEEN minup AND maxup");
        
        $srates      = "";
        $sratesoth   = "";
        $stunggakan1 = "";
        $sbunga      = "";
        $sumurb      = "";
        $sumura      = "";

        if ($rates) {
        	$srates      = $rates[0]->rates;
	        $sratesoth   = $rates[0]->ratesother;
	        $stunggakan1 = $rates[0]->tunggakan;
	        $sbunga      = $rates[0]->bunga;
	        $sumurb      = $rates[0]->umurb;
	        $sumura      = $rates[0]->umura;
        	
        }

       $spremi = 0;
       $sepremi = 0;
        // $susia      = $rates[0]->susia;

        // dd($srates);
       if ($srates == "") {
           $spremi      = $sup / 100;
       }else{
            $spremi      = ($sup * $srates) / 100;
       }

        if ($sratesoth == "") {
           $spremi      = $sup / 100;
       }else{
            $sepremi     = ($sup * $sratesoth) / 100;
       }
        

        
        $stpremi     = ($spremi + $sepremi);
        // dd()
        // echo "<br> spremi = ".$spremi;
        // echo "<br> sup = ".$sup;

        if ($susia < $sbumurb and $stpremi == 0) {
            $scomment   .= " Usia minimum($sbumurb tahun) belum tercapai, harap cek kembali tanggal lahir dan tanggal mulai. ";
            $spremi     = 0;
            $sepremi    = 0;
            $stpremi    = 0;
        }

        if ($susia > $sbumura or ($susia >= $sbumura and ($sbulan > 0 or $shari > 0)) and $stpremi == 0) {
            $scomment   .= " Usia maksimum($sbumura tahun) terlampaui, harap cek kembali tanggal lahir dan tanggal mulai. ";
            $spremi     = 0;
            $sepremi    = 0;
            $stpremi    = 0;
        }

        if ($sup > $smaxup) {
            $scomment   .= " Plafond melebih maksimum pinjaman(" . number_format($smaxup, 0, ".", ",")."). ";
            $spremi     = 0;
            $sepremi    = 0;
            $stpremi    = 0;
        }

        if ($busia > $sbumura or ($busia >= $sbumura and ($bbulan > 0 or $bhari > 0))) {
            $scomment   .= " Usia debitur ditambah masa pinjaman adalah ".$busia." tahun $bbulan bulan $bhari hari (melebihi maksimal usia pinjaman $sbumura tahun 0 bulan 0 hari)";
            $spremi     = 0;
            $sepremi    = 0;
            $stpremi    = 0;
        }
        
        if ($susia == '') {
            $susia = $susia;
        }
        
        if ($sbunga == '') {
            $sbunga = 0;
        }
        
        if ($stunggakan == '') {
            $stunggakan = 0;
        }
        
        if ($hubunganahli == 'LAINNYA') {
            $hubungan = $hubunganahli2;
        } else {
            $result = DB::select("SELECT * FROM ms_master WHERE mstype='hubungan' AND msid='$hubunganahli'");
            $hubungan = $result[0]->msdesc;
        }
        // echo $regid." regid <br>".$snama." snama <br>".$snoktp." snoktp <br>".$sjkel." sjkel <br>".$stmplahir." stmplahir <br>".$salamat." salamat <br>".$spekerjaan." spekerjaan <br>".$scabang." scabang <br>".$stgllahir." stgllahir <br>".$smulai." smulai <br>".$sakhir." sakhir <br>".$smasa." smasa <br>".$sup ." sup<br>".$snopeserta." snopeserta <br>".$status." status <br>".$userid." userid <br>".$sdate." sdate <br>".$spremi." spremi <br>".$sepremi." sepremi <br>".$stpremi." stpremi <br>".$susia." susia <br>".$sproduk." sproduk <br>".$stunggakan." stunggakan <br>".$sbunga." sbunga <br>".$srate." srate <br>".$nmahli." nmahli <br>".$notelpahli." notelpahli <br>".$hubungan." hubungan <br>".$scomment." scomment <br>";


        DB::select("INSERT INTO tr_sppa (regid, nama, noktp, jkel, tempat_lahir, alamat, pekerjaan, cabang, tgllahir, mulai, akhir, masa, up, nopeserta, status, createby,createdt, premi, epremi,tpremi, usia, produk, tunggakan, bunga, rate, nama_ahli_waris, notelp_ahli_waris, hubungan_ahli_waris, comment)VALUES('$regid','$snama','$snoktp','$sjkel','$stmplahir','$salamat','$spekerjaan','$scabang','$stgllahir','$smulai','$sakhir','$smasa','$sup','$snopeserta','$status','$userid','$sdate','$spremi','$sepremi','$stpremi','$susia','$sproduk','$stunggakan','$sbunga','$srate','$nmahli','$notelpahli','$hubungan','$scomment')");

        return redirect()->intended('pengajuan')->with('success', 'Data Berhasil ditambahkan');
    }

    public function edit($id)
    {
        // dd(Session::get('login')[0]->id_member);
        $sid = Crypt::decryptString($id);
        $data = DB::select("SELECT sp.*,sl.tgl_approve FROM tr_sppa sp LEFT JOIN (SELECT regid, MAX(createdt) as tgl_approve FROM tr_sppa_log WHERE regid = '$sid') sl ON  sp.regid = sl.regid WHERE sp.regid = '$sid'");
        // dd($data);
        $produk = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='produk' and ms.msid<>'ALL' order by ms.msdesc  asc");
        $jkel = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='JKEL'  order by ms.msdesc  asc");
        $kerja = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='KERJA' order by ms.msdesc  asc");
        $cab = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='CAB' and ms.msid<>'ALL' order by ms.msdesc  asc");
        $rate = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='rate'  order by ms.msdesc  asc");
        $hubungan = DB::select("select ms.msid comtabid,left(msdesc,50) comtab_nm from ms_master ms where ms.mstype='hubungan' order by ms.msid");

        $dokumen = DB::select("SELECT a.regid,a.tglupload,a.nama_file,a.tipe_file,a.ukuran_file,a.file,a.pages,a.seqno,a.jnsdoc from tr_document a  where regid='$sid'");

        $file = DB::select("SELECT a.regid,a.tglupload,a.nama_file,a.tipe_file,a.ukuran_file,a.file,a.pages,a.seqno,a.jnsdoc from tr_document a  where regid='$sid'");

        return view('master.pengajuan.edit', compact('data','produk','jkel','kerja','cab','rate','hubungan','dokumen','file','sid'));
    }

    public function approve($id){
        $sregid = Crypt::decryptString($id);
        $sdate = date('Y-m-d H:i:s');
        $userid = Session::get('login')[0]->username; 
        // dd(DB::select("SELECT * FROM tr_sppa_log WHERE regid = '$sregid'"));

        // DB::select("UPDATE tr_sppa SET status = '1',editby = '$userid',editdt = '$sdate' WHERE regid = '$sregid' AND premi <> 0 AND masa <> 0 AND usia <> 0");
        DB::select("UPDATE tr_sppa SET status = '1',editby = '$userid',editdt = '$sdate' WHERE regid = '$sregid'");

        DB::select("INSERT INTO tr_sppa_log (regid, status, createby, createdt, comment) VALUES('$sregid','1','$userid','$sdate','approve by ao spv')");

 
        return redirect()->intended('pengajuan')->with('success', 'Data Berhasil disimpan');
    }

    public function log($id)
    {
        $regid = Crypt::decryptString($id);
        $data = DB::select("SELECT a.*  FROM tr_sppa a WHERE regid='$regid'");
        // $type = 'LTPGJ';

        // if ($type == 'LTPGJ') {
        //     $status = "NOT IN ( '7','71','72','73','8','81','82','83','84','85','90','91','92','93','94','95','96' )";
        // } elseif ($type == 'LTBTL') {
        //     $status = "IN ( '7','71','72','73' )";
        // } elseif ($type == 'LTRFN') {
        //     $status = "IN ( '8','81','82','83','84','85' )";
        // } elseif ($type == 'LTCLM') {
        //     $status = "IN ( '90','91','92','93','94','95','96' )";
        // } else {
        //     $status = "LIKE '%%'";
        // }
        
        // $sqll=" SELECT a.regid,a.status,a.comment,a.createdt,a.createby,b.msdesc statpol FROM tr_sppa_log a INNER JOIN ms_master b ON a.status=b.msid AND b.mstype='STREQ' WHERE a.regid='$regid' AND status $status ORDER BY a.createdt DESC ";
        $data2 = DB::select(" SELECT a.regid,a.status,a.comment,a.createdt,a.createby,b.msdesc statpol FROM tr_sppa_log a INNER JOIN ms_master b ON a.status=b.msid AND b.mstype='STREQ' WHERE a.regid='$regid' ORDER BY a.createdt DESC ");
        // dd($data2);
        return view('master.pengajuan.log', compact('data','data2'));
    }

    public function report($id)
    {
        // dd(Session::get('login')[0]->id_member);
        $regid = Crypt::decryptString($id);

        return view('master.pengajuan.report', compact('regid'));


    }

    public function reportlpfk($id)
    {
        // dd(Session::get('login')[0]->id_member);
        // $regid = Crypt::decryptString($id);
        $regid = "";

            $pdf = PDF::loadview('master.pengajuan.lpfk', compact('regid'));
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed'=> TRUE,
                        'verify_peer' => FALSE,
                        'verify_peer_name' => FALSE,
                    ]
                ])
            );
            return $pdf->stream('laporan-pegawai-pdf.pdf');
        
        return view('master.pengajuan.lpfk', compact('regid'));


    }
    public function reportskkt($id)
    {
        // dd(Session::get('login')[0]->id_member);
        // $regid = Crypt::decryptString($id);
        $regid = "";

            $pdf = PDF::loadview('master.pengajuan.skkt', compact('regid'));
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed'=> TRUE,
                        'verify_peer' => FALSE,
                        'verify_peer_name' => FALSE,
                    ]
                ])
            );
            return $pdf->stream('laporan-pegawai-pdf.pdf');
        
        return view('master.pengajuan.skkt', compact('regid'));


    }

    public function reportspa($id)
    {
        // dd(Session::get('login')[0]->id_member);
        // $regid = Crypt::decryptString($id);
        $regid = "";

            $pdf = PDF::loadview('master.pengajuan.spa', compact('regid'));
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed'=> TRUE,
                        'verify_peer' => FALSE,
                        'verify_peer_name' => FALSE,
                    ]
                ])
            );
            return $pdf->stream('laporan-pegawai-pdf.pdf');
        
        return view('master.pengajuan.spa', compact('regid'));


    }

    public function reportspkk($id)
    {
        // dd(Session::get('login')[0]->id_member);
        // $regid = Crypt::decryptString($id);
        $regid = "";

            $pdf = PDF::loadview('master.pengajuan.spkk', compact('regid'));
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed'=> TRUE,
                        'verify_peer' => FALSE,
                        'verify_peer_name' => FALSE,
                    ]
                ])
            );
            return $pdf->stream('laporan-pegawai-pdf.pdf');
        
        return view('master.pengajuan.spkk', compact('regid'));


    }

    public function reportspm($id)
    {
        // dd(Session::get('login')[0]->id_member);
        // $regid = Crypt::decryptString($id);
        $regid = "";

            $pdf = PDF::loadview('master.pengajuan.spm', compact('regid'));
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed'=> TRUE,
                        'verify_peer' => FALSE,
                        'verify_peer_name' => FALSE,
                    ]
                ])
            );
            return $pdf->stream('laporan-pegawai-pdf.pdf');
        
        return view('master.pengajuan.spm', compact('regid'));


    }

    public function doc($id)
    {
        // dd(Session::get('login')[0]->id_member);
        $regid = Crypt::decryptString($id);
        $data = DB::select("SELECT a.*  FROM tr_sppa a WHERE regid='$regid'");
        // dd($data);
        $jenis = "DTPGJ";
        $vlevel = Session::get('login')[0]->level;
        $custWhere = "";

        if ($jenis == 'DTCLM') {
            $custWhere = " WHERE  mstype = (SELECT doctype
                                     FROM   tr_claim
                                     WHERE  regid = '$regid') ";
        }
        elseif ($jenis == 'DTBTL') {
            $custWhere = " WHERE  mstype = 'FRMBATAL' ";
        }
        elseif ($jenis == 'DTRFN') {
            $custWhere = " WHERE  mstype = 'FRMREFUND' ";
        }
        elseif ($jenis == 'DTPGJ') {
            $custWhere = " WHERE mstype = concat('PGJ',(SELECT produk
                                     FROM   tr_sppa
                                     WHERE  regid = '$regid')) ";
            $foto = DB::select("SELECT * FROM tr_document WHERE regid = '$regid' AND nama_file like '%FOT%'");
            if ($foto) {
                $i = 0;
                foreach($foto as $row) {
                    $custWhere .= " UNION 
                                    SELECT 
                                        'UPLOADED PHOTO $i',
                                        '".$row->tipe_file."',
                                        '".$row->ukuran_file."',
                                        '".$row->tglupload."',
                                        concat('$regid".$row->seqno."',
                                               '-',
                                               'UPLOADED PHOTO $i',
                                               '-',
                                               '".$row->ukuran_file."',
                                               '-',
                                               '".$row->tipe_file."',
                                               '-',
                                               'UPLOADED PHOTO $i',
                                               '-',
                                               '".$row->file."',
                                               '-',
                                               'null,'
                                               '-',
                                               'null,') 'aksi'
                                    ";
                    $i++;
                }
            }
            
            
        }
        else {
            $custWhere = " WHERE mstype IN (
                            ( SELECT doctype
                              FROM   tr_claim
                              WHERE  regid = '$regid'),
                            'FRMBATAL',
                            'FRMREFUND',
                            concat('PGJ',( SELECT produk
                                           FROM   tr_sppa
                                           WHERE  regid = '$regid'))) ";
            $foto = DB::select("SELECT * FROM tr_document WHERE regid = '$regid' AND nama_file like '%FOT%'");
            if ($foto) {
                $i = 0;
                foreach($foto as $row) {
                    $custWhere .= " UNION 
                                    SELECT 
                                        'UPLOADED PHOTO $i',
                                        '".$row->tipe_file."',
                                        '".$row->ukuran_file."',
                                        '".$row->tglupload."',
                                        concat('$regid".$row->seqno."',
                                               '-',
                                               'UPLOADED PHOTO $i',
                                               '-',
                                               '".$row->ukuran_file."',
                                               '-',
                                               '".$row->tipe_file."',
                                               '-',
                                               'UPLOADED PHOTO $i',
                                               '-',
                                               '".$row->file."',
                                               '-',
                                               'null,'
                                               '-',
                                               'null,') 'aksi'
                                    ";
                    $i++;
                }
            }
        }

        $dokumen = DB::select("SELECT SQL_CALC_FOUND_ROWS dokumen,tipe,ukuran,tglupload,aksi FROM (SELECT a.msdesc 'dokumen', b.tipe_file 'tipe',b.ukuran_file 'ukuran',b.tglupload,concat(IF (concat(b.regid,b.seqno) IS NOT NULL, concat(b.regid,b.seqno), 'null'),'-', a.msid,'-',IF (b.ukuran_file IS NOT NULL, b.ukuran_file, 'null'), '-', IF (b.tipe_file IS NOT NULL, b.tipe_file, 'null'), '-', a.msdesc,'-',IF (b.file IS NOT NULL, b.file, 'null'),'-',IF (a.createby IS NOT NULL, a.createby, 'null'),'-',IF (a.editby IS NOT NULL, a.editby, 'null')) 'aksi' FROM   ms_master a LEFT JOIN (SELECT regid, seqno, file, jnsdoc, tipe_file, ukuran_file, tglupload FROM tr_document where regid = '$regid') b ON a.msid = b.jnsdoc $custWhere ) t_baru");
        // $jkel = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='JKEL'  order by ms.msdesc  asc");
        // $kerja = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='KERJA' order by ms.msdesc  asc");
        // $cab = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='CAB' and ms.msid<>'ALL' order by ms.msdesc  asc");
        // $rate = DB::select("select ms.msid comtabid,ms.msdesc comtab_nm from ms_master ms   where ms.mstype='rate'  order by ms.msdesc  asc");
        // $hubungan = DB::select("select ms.msid comtabid,left(msdesc,50) comtab_nm from ms_master ms where ms.mstype='hubungan' order by ms.msid");

        // $dokumen = DB::select("SELECT a.regid,a.tglupload,a.nama_file,a.tipe_file,a.ukuran_file,a.file,a.pages,a.seqno,a.jnsdoc from tr_document a  where regid='$sid'");

        // $file = DB::select("SELECT * FROM tr_document where regid = '$regid'");
        // dd($dokumen);
 
        return view('master.pengajuan.doc', compact('data','dokumen','vlevel','regid'));
    }

    public function upload(Request $request){
        // $this->validate($request, [
        // //     'product_name'   => 'required',
        // //     'product_code' => 'required',
        // // ]);

        $sdate = date('Y-m-d H:i:s');
        $userid = Session::get('login')[0]->username; 

        //     echo "OK";

        $regid = Crypt::decryptString($request->regid);
        DB::select("SELECT * FROM tr_document WHERE regid='$regid'");
        if ($request->file("upload") != null) 
        {
            $file = $request->file("upload");
            $imageName = time().$request->jnsdoc.$file->getClientOriginalName(); 
            $tgl = date("Y-m-d");
            $file_ext = $file->getClientOriginalExtension();
            $file_size = $file->getSize();;
            $jnsdoc = $request->jnsdoc;
            $catdoc = $request->jnsdoc;
            $status = "berhasil";
            $pesan = "Data berhasil di simpan";
            
            if ($file->move('assets/upload/'.$regid, $imageName)) {
                $lokasi = "assets/upload/".$regid;
                $sqlq=" INSERT INTO tr_document (regid,tglupload,nama_file,tipe_file,ukuran_file,file,jnsdoc,catdoc) VALUES('$regid','$tgl','$imageName','$file_ext','$file_size','$lokasi','$jnsdoc','$catdoc')";
                $query = DB::select($sqlq);
                if ($catdoc == 'clm') {
                    $cekDoc = DB::select(" SELECT b.jmldokumen,c.uploaded FROM tr_claim a LEFT JOIN (SELECT mstype,Count(msid) 'jmldokumen' FROM   ms_master GROUP  BY mstype) b ON b.mstype = a.doctype LEFT JOIN (SELECT regid, Count(regid) 'uploaded' FROM   tr_document WHERE  catdoc = 'clm' GROUP  BY regid) c ON c.regid = a.regid WHERE  a.regid = '$regid' ");
                    $d  = DB::select($cekDoc);
                    if ($d[0]->uploaded >= $d[0]->jmldokumen) {
                        $updClm = "UPDATE tr_claim SET softcopydt = '$sdate' WHERE regid ='$regid' AND `softcopydt` = 0"; //cegah update, saat dokumen diupload ulang
                        if (DB::select($updClm)) { 
                            echo "berhasil"; 
                        } else {
                            $status = "error";
                            $pesan = "Data gagal di simpan";
                        }
                    } else {
                        echo "berhasil";
                    }
                } else {
                    echo "berhasil";
                }
            }else {
                $status = "error";
                $pesan = "Data gagal di simpan";
            }

        return redirect()->intended('pengajuan/edit/'.$request->regid)->with('success', 'Data Berhasil dihapus');
        // dd(DB::select("SELECT * FROM tr_document WHERE regid='$regid'")); 
        }
    }

    public function update(Request $request)
    {
        // $this->validate($request, [
        //     'product_name'   => 'required',
        //     'product_code' => 'required',
        // ]);

        $sproduk = $request->produk;
        $sjkel = $request->jkel;
        $snama      = str_replace("'", "`", $request->nama);
        $susia = $request->usia;
        $snoktp = $request->noktp;
        $stmplahir = $request->tempat_lahir;
        $smasa = $request->masa;
        $salamat = $request->alamat;
        $spekerjaan = $request->pekerjaan;
        $scabang = $request->cabang;
        $snopeserta = $request->nopeserta;
        $scabang = $request->cabang;
        $stunggakan = $request->tunggakan;
        $stgllahir = $request->tgllahir;
        $smulai = $request->mulai;
        $srate = $request->rate;
        $sakhir     = date('Y-m-d', strtotime('+' . $smasa . 'months', strtotime($smulai))); 
        $hubunganahli  = $request->hubungan;
        $hubunganahli2 = $request->ket_hubungan;
        $nmahli = $request->nama_ahli_waris;
        $notelpahli = $request->notelp_ahli_waris;
        $scomment = $request->catatan;
        $status     = '0';
        $userid = Session::get('login')[0]->username;

        $sdate      = date('Y-m-d H:i:s');
        $sregid     = $request->regid;
        $sup        = str_replace(',', '', $request->up);

        $susia      = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $smulai);
        $sbulan     = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $smulai, "bulan");
        $shari      = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $smulai, "hari");
    //  $total_usia = ($susia + ($smasa / 12));
        
        // belum naik / reset bulan hari
        $sakhir     = date('Y-m-d', strtotime('+' . $smasa . 'months', strtotime($smulai)));
        $busia      = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $sakhir, null, false);
        $bbulan     = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $sakhir, "bulan", false);
        $bhari      = app('App\Http\Controllers\Master\PengajuanController')->hitung_umur($stgllahir, $sakhir, "hari", false);

        $rates = DB::select("SELECT rates,ratesother,tunggakan,bunga,umurb,umura,$susia FROM tr_rates WHERE produk = '$sproduk' AND jkel = '$sjkel' AND $susia BETWEEN umurb AND umura AND insperiodmm = '$smasa' AND '$stunggakan' BETWEEN gpb AND gpa AND $sup BETWEEN minup AND maxup");

        $srates      = "";
        $sratesoth   = "";
        $stunggakan1 = "";
        $sbunga      = "";
        $sumurb      = "";
        $sumura      = "";

        if ($rates) {
            $srates      = $rates[0]->rates;
            $sratesoth   = $rates[0]->ratesother;
            $stunggakan1 = $rates[0]->tunggakan;
            $sbunga      = $rates[0]->bunga;
            $sumurb      = $rates[0]->umurb;
            $sumura      = $rates[0]->umura;
            
        }

       $spremi = 0;
       $sepremi = 0;
        // $susia      = $rates[0]->susia;

        // dd($srates);
       if ($srates == "") {
           $spremi      = $sup / 100;
       }else{
            $spremi      = ($sup * $srates) / 100;
       }

        if ($sratesoth == "") {
           $spremi      = $sup / 100;
       }else{
            $sepremi     = ($sup * $sratesoth) / 100;
       }


        $produk = DB::select("SELECT umurb, umura, maxup FROM tr_term WHERE produk='$sproduk'");
        
        $sbumurb    = $produk[0]->umurb;
        $sbumura    = $produk[0]->umura;
        $smaxup     = $produk[0]->maxup;

        $stpremi        = ($spremi + $sepremi);
        $hubungan = "";

        if ($susia < $sbumurb and $stpremi == 0) {
            $scomment   .= " Usia minimum(".$sbumurb." tahun) belum tercapai, harap cek kembali tanggal lahir dan tanggal mulai. ";
            $spremi     = 0;
            $sepremi    = 0;
            $stpremi    = 0;
        }

        if ($susia > $sbumura or ($susia >= $sbumura and ($sbulan > 0 or $shari > 0)) and $stpremi == 0) {
            $scomment   .= " Usia maksimum(".$sbumura." tahun) terlampaui, harap cek kembali tanggal lahir dan tanggal mulai. ";
            $spremi     = 0;
            $sepremi    = 0;
            $stpremi    = 0;
        }

        if ($sup > $smaxup) {
            $scomment   .= " Plafond melebih maksimum pinjaman(" . number_format($smaxup, 0, ".", ",")."). ";
            $spremi     = 0;
            $sepremi    = 0;
            $stpremi    = 0;
        }

        if ($busia > $sbumura or ($busia >= $sbumura and ($bbulan > 0 or $bhari > 0))) {
            $scomment   .= " Usia debitur ditambah masa pinjaman adalah ".$busia." tahun ".$bbulan." bulan ".$bhari." hari (melebihi maksimal usia pinjaman ".$sbumura." tahun 0 bulan 0 hari)";
            $spremi     = 0;
            $sepremi    = 0;
            $stpremi    = 0;
        }
        
        if ($susia == '') {
            $susia = $susia;
        }
        
        if ($sbunga == '') {
            $sbunga = 0;
        }
        
        if ($stunggakan == '') {
            $stunggakan = 0;
        }
        
        if ($hubunganahli == 'LAINNYA') {
            $hubungan = $hubunganahli2;
        } else {
            $result = DB::select("SELECT * FROM ms_master WHERE mstype='hubungan' AND msid='$hubunganahli'");
            foreach ($result as $r) {
                $hubungan = $r->msdesc;
            }
        }

        $hasil =DB::select("UPDATE tr_sppa SET nama= '$snama',cabang = '$scabang',rate = '$srate',usia = '$susia',tgllahir = '$stgllahir',up = '$sup', jkel = '$sjkel', pekerjaan = '$spekerjaan', tempat_lahir = '$stmplahir', alamat = '$salamat', noktp = '$snoktp', tpremi = '$spremi', premi = '$spremi', epremi = 0, status = 0, editby = '$userid', editdt = '$sdate', masa = '$smasa', akhir = '$sakhir', mulai = '$smulai', tunggakan = '$stunggakan', nama_ahli_waris = '$nmahli',notelp_ahli_waris = '$notelpahli',hubungan_ahli_waris = '$hubungan', COMMENT = '$scomment' WHERE regid = '$sregid'");
        // dd($hasil);

        return redirect()->intended('pengajuan')->with('success', 'Data Berhasil diubah');
    }

    public function delete($id)
    {
        DB::select("DELETE FROM md_product WHERE id_product='$id'");
        // return view('master.product.add');
        return redirect()->intended('product')->with('success', 'Data Berhasil dihapus');
    }
}
