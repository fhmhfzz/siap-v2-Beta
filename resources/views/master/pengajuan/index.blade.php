@extends('layouts.app')

    @section('content')
    <main class="h-100">
        <!-- Header -->
        <header class="header position-fixed">
            <div class="row">
                <div class="col-auto">
                    <a href="javascript:void(0)" target="_self" class="btn btn-light btn-44 menu-btn">
                        <i class="bi bi-list"></i>
                    </a>
                </div>
                <div class="col align-self-center text-center">
                    <div class="logo-small">
                        <img src="{{ asset(config('app.logo', 'assets/img/logo.png')) }}" alt="">
                        <h5>{{ config('app.title', 'SIAPLAKU') }}</h5>
                    </div>
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
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <!-- <figure class="avatar avatar-44 rounded-10">
                                <img src="assets/img/user1.jpg" alt="">
                            </figure> -->
                        </div>
                        <div class="col px-0 align-self-center">
                            <h5 class="mb-0 text-color-theme">{{ $data['judul']}}</h5>
                            <p class="text-muted size-12"></p>
                        </div>
                        <div class="col-auto">
                            <!-- <button name="create_record" tool-tip="Tambah data" id="create_record" class="btn btn-44 btn-light shadow-sm">
                                <i class="bi bi-plus-circle"></i>
                            </button> -->
                            <a href="{{ url('/pengajuan/add')}}" tooltip="Tambah data" id="create_record" class="btn btn-44 btn-light shadow-sm">
                                <i class="bi bi-plus-circle"></i>
                            </a>
                            <a href="{{ url('Formulir_SPPA.docx') }}" class="btn btn-44 btn-default shadow-sm ms-1">
                                <i class="bi bi-arrow-down-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group form-floating mb-3">
                <input type="text" class="form-control " id="searchdata" placeholder="Search">
                <label class="form-control-label" for="search">Cari Data</label>
                <button type="button" class="text-color-theme tooltip-btn">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <!-- wallet balance -->
            <div id="tbody" class="main-container container row">
                
                @foreach($pengajuan as $p)
                    <div class="col-md-6 card shadow mb-4 ">
                        <div class="card-body">
                            <div class="row container">
                                <div class="col align-self-center">
                                    <h5 class="mb-0 text-color-theme">{{ $p->nama }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card border-0">
                            <div class="card-body">
                                <div class="row container mb-2">
                                    <div class="col align-self-center">
                                        <p class="text-muted size-12 mb-0">{{ 'UP : '. number_format(floatval($p->up)) }}</p>
                                        <p class="text-muted size-12">{{ 'Primi : '. number_format(floatval($p->premi)) }}</p>
                                    </div>
                                    <div class="col align-self-top text-end">
                                        <p class="text-muted size-12 mb-0">{{ 'Jakarta, 1 Januari' }}</p>
                                        <p class="text-muted size-12">{{ $p->sts }}</p>
                                    </div>
                                </div>
                                <a href="{{ url('pengajuan/edit/'. Crypt::encryptString($p->regid)) }}" class="btn btn-default w-100 shadow small">Detail</a>
                                
                            </div>
                        </div>
                    </div> 
                @endforeach
                
            </div>
        </div>
        <!-- main page content ends -->

    </main>

</div>


    @endsection
    @section('script')
    <script>
        $(function() {
            // console.log("ok");
            // $('#allData').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     ajax: '{{ url("/pengajuan/data")}}',
            //     columns: [
            //         {
            //             data: 'regid',
            //             name: 'regid'
            //         },
            //         {
            //             data: 'nama',
            //             name: 'nama'
            //         },
            //         {
            //             data: 'up',
            //             name: 'up'
            //         },
            //         {
            //             data: 'premi',
            //             name: 'premi'
            //         },
            //         {
            //             data: 'status',
            //             name: 'status'
            //         },
            //         {
            //             data: 'comment',
            //             name: 'comment'
            //         },
            //         {
            //             data: 'action',
            //             name: 'action',
            //             orderable: false
            //         }
            //     ]
            // });
        });

        $('#create_record').click(function(){
        $('.modal-title').text('Add New Record');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formModal').modal('show');
        });

        $('#sample_form').on('submit', function(event){
            event.preventDefault();
            var action_url = '';
            if($('#action').val() == 'Add')
            {
            // action_url = "{{ url('area.store') }}";
            }
            if($('#action').val() == 'Edit')
            {
            // action_url = "{{ url('area.update') }}";
            }
            $.ajax({
            url: action_url,
            method:"POST",
            data:$(this).serialize(),
            dataType:"json",
            success:function(data)
            {
                var html = '';
                if(data.errors)
                {
                html = '<div class="alert alert-danger">';
                for(var count = 0; count < data.errors.length; count++)
                {
                html += '<p>' + data.errors[count] + '</p>';
                }
                html += '</div>';
                }
                if(data.success)
                {
                html = '<div class="alert alert-success">' + data.success + '</div>';
                $('#sample_form')[0].reset();
                $('#allData').DataTable().ajax.reload();
                }
                $('#form_result').html(html);
            }
            });
            });
            $(document).on('click', '.edit', function(){
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                url :"{{ url('') }}/area/edit/"+id,
                dataType:"json",
                success:function(data)
                {
                    // console.log(data.result[0].namearea);
                    $('#namearea').val(data.result[0].namearea);
                    $('#notearea').val(data.result[0].notearea);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Record');
                    $('#action_button').val('Edit');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
                })
                });
            var user_id;
            $(document).on('click', '.delete', function(){
                user_id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });
            $('#ok_button').click(function(){
                $.ajax({
                    url:"area/destroy/"+user_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                        $('#confirmModal').modal('hide');
                        $('#allData').DataTable().ajax.reload();
                        alert('Data Deleted');
                        }, 2000);
                    }
                })
            });
    </script>
        <script type="text/javascript">
            $('#searchdata').on('keyup',function(){
                // console.log('ok')
                $value=$(this).val();
                    $.ajax({
                        type : 'get',
                        url : '{{ url("pengajuan/data") }}',
                        data:{'search':$value},
                        success:function(data){
                        $('#tbody').html(data);
                    }
                });
            })
        </script>
        <script type="text/javascript">
            $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
        </script>
    
    
    @endsection