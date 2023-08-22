@extends('layouts.app')
    @section('content')
<main class="h-100 has-header">
        <!-- Header -->
        <header class="header position-fixed">
            <div class="row">
                <div class="col-auto">
                    <button class="btn btn-light back-btn" >
                        <i class="bi bi-arrow-left"> </i>Cancel
                    </button>
                </div>
                <div class="col align-self-center text-center">
                    <h5>Pengajuan</h5>
                </div>
                <div class="col-auto">
                    <a href="{{ url('notification') }}" target="_self" class="btn btn-light btn-44">
                        <i class="bi bi-bell"></i>
                        <span class="count-indicator"></span>
                    </a>
                </div>
            </div>
        </header>
        <!-- Header ends -->
        <!-- main page content -->
        <div class="main-container container">
            <!-- user information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto">
                            <figure class="avatar avatar-50 shadow rounded-10 text-white" style="background-color: #E73712;">
                                <?php 
                                    $thumbnail = "";
                                    $teks = $data[0]->nama;
                                    $teks = explode(",", $teks);
                                    foreach($teks as $t){
                                        $kata = substr($t,0,1);
                                        $thumbnail .= $kata." ";
                                    }
                                    if (isset($teks[0])) {
                                        echo substr($teks[0],0,1);
                                    }
                                    if (isset($teks[1])) {
                                        echo substr($teks[1],0,1);
                                    }
                                ?>
                            </figure>
                        </div>
                        <div class="col px-0 align-self-center">
                            <h3 class="mb-0 text-color-theme">{{ $data[0]->nama }}</h3>
                            <p class="text-muted ">Tgl Lahir : {{ $data[0]->tgllahir }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted ">
                        {{ $data[0]->comment }}
                    </p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <h6>Action</h6>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 px-0">
                    <!-- swiper users connections -->
                    <div class="swiper-container connectionwiper" style="overflow: scroll;">
                        <!-- STATISTIC marriage  -->
                        <div class="swiper-wrapper" id="statistic_family">
                             
                            <div class="swiper-slide">
                                <a href="{{ url('pengajuan/approve/'.Crypt::encryptString($data[0]->regid)) }}" class="card text-center">
                                    <div class="card-body">
                                        <div class="avatar avatar-50 shadow-sm mb-2 rounded-10 theme-bg text-white">
                                            <i class="bi bi-check2-circle size-32"></i>
                                        </div>
                                        <p class="text-color-theme size-12 small">Approve</p>
                                    </div>
                                </a>
                            </div>
                             
                             
                            <div class="swiper-slide">
                                <a href="{{ url('pengajuan/doc/'.Crypt::encryptString($data[0]->regid).'/DTPGJ') }}" class="card text-center">
                                    <div class="card-body">
                                        <div class="avatar avatar-50 shadow-sm mb-2 rounded-10 theme-bg text-white">
                                            <i class="bi bi-file-earmark-arrow-up size-32"></i>
                                        </div>
                                        <p class="text-color-theme size-12 small">Document</p>
                                    </div>
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="{{ url('pengajuan/salin/'.Crypt::encryptString($data[0]->regid)) }}" class="card text-center">
                                    <div class="card-body">
                                        <div class="avatar avatar-50 shadow-sm mb-2 rounded-10 theme-bg text-white">
                                            <i class="bi bi-clipboard size-32"></i>
                                        </div>
                                        <p class="text-color-theme size-12 small">Salin</p>
                                    </div>
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="{{ url('pengajuan/log/'.Crypt::encryptString($data[0]->regid)) }}" class="card text-center">
                                    <div class="card-body">
                                        <div class="avatar avatar-50 shadow-sm mb-2 rounded-10 theme-bg text-white">
                                            <i class="bi bi-door-closed size-32"></i>
                                        </div>
                                        <p class="text-color-theme size-12 small">Log</p>
                                    </div>
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="{{ url('pengajuan/report/'.Crypt::encryptString($data[0]->regid)) }}" class="card text-center">
                                    <div class="card-body">
                                        <div class="avatar avatar-50 shadow-sm mb-2 rounded-10 theme-bg text-white">
                                            <i class="bi bi-door-closed size-32"></i>
                                        </div>
                                        <p class="text-color-theme size-12 small">Report</p>
                                    </div>
                                </a>
                            </div>
                             
                        </div>
                    </div>
                </div>
            </div>
            <!-- connectionwiper -->
            <!-- profile information -->
            <div class="row mb-3">
                <div class="col">
                    <h6>Basic Information</h6>
                </div>
            </div>
            
            <form method="POST" action="{{ url('verifikasi/update')}}">
                @csrf
                <div class="row h-100 mb-4">
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <select class="form-control" readonly name="produk" readonly id="country" >
                                <!-- <option value="" ></option> -->
                                @foreach($produk as $p)
                                <option value="{{ $p->comtabid }}" @if($data[0]->produk == $p->comtabid) selected @endif>{{ $p->comtab_nm }}</option>
                                @endforeach
                            </select>
                            <label for="country">Produk</label>
                           
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="regid" value="{{ $data[0]->regid }}" placeholder="Name" id="regid">
                            <label for="names">No Register</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="nama" value="{{ $data[0]->nama }}" placeholder="Name" id="nama">
                            <label for="names">Nama</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="tgllahir" value="{{ $data[0]->tgllahir }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">Tanggal Lahir</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="usia" value="{{ $data[0]->usia }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">Usia Masuk</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="noktp" value="{{ $data[0]->noktp }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">No KTP</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <select readonly name="jkel" class="form-control">
                                <option value="L" @if($data[0]->jkel == "L") selected @endif>Laki-laki</option>
                                <option value="P" @if($data[0]->jkel == "P") selected @endif>Perempuan</option>
                            </select>
                            <label for="surnames">Jenis Kelamin</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <select readonly class="form-control" id="country" name="pekerjaan">
                                <option value="" ></option>
                                @foreach($pekerjaan as $k)
                                <option value="{{ $k->comtabid }}" @if($data[0]->pekerjaan == $k->comtabid) selected @endif>{{ $k->comtab_nm }}</option>
                                @endforeach
                            </select>
                            <label for="surnames">Pekerjaan</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <select readonly class="form-control" id="country" name="cabang" >
                                <option value="" ></option>
                                @foreach($cabang as $c)
                                <option value="{{ $c->comtabid }}" @if($data[0]->cabang == $c->comtabid) selected @endif>{{ $c->comtab_nm }}</option>
                                @endforeach
                            </select>
                            <label for="surnames">Cabang</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <select readonly class="form-control" id="country" name="mitra" >
                                <option value="" ></option>
                                @foreach($mitra as $m)
                                <option value="{{ $m->comtabid }}" @if($data[0]->mitra == $m->comtabid) selected @endif>{{ $m->comtab_nm }}</option>
                                @endforeach
                            </select>
                            <label for="surnames">Mitra</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="masa" value="{{ $data[0]->masa }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">Masa Asuransi</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="mulai" value="{{ $data[0]->mulai }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">Mulai Asuransi</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="akhir" value="{{ $data[0]->akhir }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">Akhir Asuransi</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="up" value="{{ $data[0]->up }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">UP</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="premi" value="{{ $data[0]->premi }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">Premi</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="epremi" value="{{ $data[0]->epremi }}" placeholder="Tempat Lahir" id="names">
                            <label for="names">EPremi</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" readonly name="tunggakan" value="{{ $data[0]->tunggakan }}" placeholder="tunggakan" id="names">
                            <label for="names">Grace Period (khusus MPP)</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="form-floating mb-3">
                            <select readonly class="form-control" id="country" name="asuransi" id="asuransi">
                                <option value="" ></option>
                                @foreach($asuransi as $h)
                                <option value="{{ $h->comtabid }}" @if($data[0]->hubungan_ahli_waris == $h->comtab_nm) selected @endif>{{ $h->comtab_nm }}</option>
                                @endforeach
                            </select>
                            <label for="country">Asuransi</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group form-floating  mb-3">
                            <textarea class="form-control" readonly name="catatan">{{ $data[0]->comment }}</textarea>
                            <label for="names">Catatan</label>
                        </div>
                    </div>
                    
                </div>
                <input type="hidden" name="regid" value="{{ $data[0]->regid }}">
                <!-- add edit address form -->
                <div class="row mb-3">
                    <div class="col">
                        <h6>Track Changes</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" value="{{ $data[0]->createby }}" readonly  placeholder="Surname" id="surnames">
                            <label for="surnames">Created By</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" value="{{ $data[0]->createdt }}" readonly  placeholder="Surname" id="surnames">
                            <label for="surnames">Created Date</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" value="{{ $data[0]->editby }}" readonly  placeholder="Surname" id="surnames">
                            <label for="surnames">Updated By</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="form-group form-floating  mb-3">
                            <input type="text" class="form-control" value="{{ $data[0]->editdt }}" readonly  placeholder="Surname" id="surnames">
                            <label for="surnames">Updated Date</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card card-body table-responsive">
                            <h5>Dokumen</h5>
                            <table class="mt-3 table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama file </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($file as $d)
                                        <tr>
                                            <td><a href="{{ url($d->file.'/'.$d->nama_file) }}" target="pdf-frame">{{ $d->nama_file }} </a>
                                            <th>
                                                <a href="{{ url($d->file.'/'.$d->nama_file) }}" target="pdf-frame" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o"></i> Document</a>
                                            </th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="card card-body table-responsive">
                            <h5>Log Status</h5>
                            <table class="mt-3 table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code </th>
                                        <th>Status </th>
                                        <th>User </th>
                                        <th>Tanggal </th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log as $item)
                                        <tr>
                                            <td>{{ $item->status }}</td>
                                            <td>{{ $item->stdesc }}</td>
                                            <td>{{ $item->createby }}</td>
                                            <td>{{ $item->createdt }}</td>
                                            <td>{{ $item->comment }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="position-fixed bottom-0 start-50 translate-middle-x  z-index-10" id="konfirmasi">
                    <div class="toast toast-save mb-3 fade hide" role="alert" aria-live="assertive" aria-atomic="true" id="toastSimpan"
                        data-bs-animation="true">
                        <div class="toast-header">
                            <img src="{{ asset('assets/img/logo_inch.png') }}"  width="20px" class="rounded me-2" alt="...">
                            <strong class="me-auto">PERHATIAN!</strong>
                            <!-- <small>now</small> -->
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            <div class="row">
                                <div class="col save">
                                    Apakah Anda yakin ingin menyimpan ini?
                                </div>
                                <div class="col delete">
                                    Apakah Anda yakin ingin menghapus ini?
                                </div>
                                <div class="col-auto align-self-center ps-0">
                                    <button class="btn btn-sm btn-default btn-save" type="submit">Save</button>
                                    <a href="{{ url('pengajuan/delete/'.$data[0]->regid) }}" class="btn btn-sm btn-default btn-delete" >Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="row h-100 ">
                    <div class="col-md-6 mb-1">
                        <!-- <button class="btn btn-lg btn-default shadow btn-block" type="submit">Save</button> -->
                        <button class="btn btn-lg btn-default w-100 shadow" type="button" onclick="return save()">Save</button>
                    </div>
                    <div class="col-md-6 mb-1">
                        <button class="btn btn-lg btn-danger w-100 shadow text-white" type="button" onclick="return hapus()">Delete</button>
                    </div>
                </div>
                
            </form>
 
        <!-- </div> -->
        <!-- main page content ends -->
    </main>
    @endsection
    @section('script')
        <script>
            function save(){
                $('.toast-save').toast('show');
                $(".btn-save").show();
                $(".save").show();
                $(".btn-delete").hide();
                $(".delete").hide();
            }
            function hapus(){
                $('.toast-save').toast('show');
                $(".btn-save").hide();
                $(".save").hide();
                $(".btn-delete").show();
                $(".delete").show();
            }
            function cekUsia() {
                var a = moment($('#mulai').val());
                var b = moment("{{ $data[0]->tgllahir }}");
                
                var years = a.diff(b, 'year');
                b.add(years, 'years');
                
                var months = a.diff(b, 'months');
                b.add(months, 'months');
                
                var days = a.diff(b, 'days');
                
                // console.log(years + ' years ' + months + ' months ' + days + ' days');
                var age = (months >= 6) ? (years+1) : (years);
                // console.log(age);
                // var tgl_mulai = moment();
                if (age > {{ $data[0]->usia }}) {
                    if ([56, 61, 66, 70].includes(age)) {
                        $('#masa-error').show();
                        $('#masa-text').text('Usia debitur berubah dari {{ $data[0]->usia }} tahun menjadi '+age+' tahun, premi akan berubah, harap mengkoreksi kembali rate yang berlaku');
                        $('#div-mulai').addClass('has-error');
                    } else {
                        $('#masa-error').hide();
                        $('#div-mulai').removeClass('has-error');
                    }
                } else if (age < {{ $data[0]->usia }}) {
                    if ([55, 60, 65, 69].includes(age)) {
                        $('#masa-error').show();
                        $('#masa-text').text('Usia debitur berubah dari {{ $data[0]->usia }} tahun menjadi '+age+' tahun, premi akan berubah, harap mengkoreksi kembali rate yang berlaku');
                        $('#div-mulai').addClass('has-error');
                    } else {
                        $('#div-mulai').removeClass('has-error');
                        $('#masa-error').hide();
                    }
                } else {
                    $('#masa-error').hide();
                }
            }
        </script>
        
    @endsection
