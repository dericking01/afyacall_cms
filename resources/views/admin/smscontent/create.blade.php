@extends('layouts.app')

@push('before-styles')

<link href="{{asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet" type="text/css" />

@endpush

@push('after-scripts')

<script src="{{asset('assets/plugins/switchery/dist/switchery.min.js')}}"></script>
<script src="{{asset('assets/plugins/select2/dist/js/select2.full.min.js')}}" type="text/javascript"></script>

<script>
    jQuery(document).ready(function() {
        $(".select2").select2();
    });
</script>

@endpush

@section('content')
<script src='https://cdn.jsdelivr.net/npm/vue'></script>
<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Sms content</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("admin.home") }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a  href="{{ route("admin.contents.index") }}">Sms content</a></li>
            <li class="breadcrumb-item active">Sms create</li>
        </ol>
    </div>
  
</div>
<div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
    <h3 class="text-info"><i class="fa fa-exclamation-circle"></i> Information</h3>  Create a Message type before create new Message.
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="row"  id='myapp'>
                <div class="col-xlg-2 col-lg-4 col-md-5" >
                    <div class="card-body inbox-panel"><a href="#" class="btn btn-danger mb-3 p-2 btn-block waves-effect waves-light">160 Characters = 1 SMS</a>
                        <ul class="list-group list-group-full">
                            <span>@{{ totalcharacter }} characters</span>
                        </ul>
                    </div>
                </div>
                <div class="col-xlg-10 col-lg-8 col-md-7">
                  <form action="{{ route('admin.contents.store') }}" method="post" >   
                    {{ csrf_field() }}
                    <div class="card-body">
                        <h3 class="card-title">Create New Message</h3>
                       
                        <div class="form-group">
                            <select class="form-control select2" name="content_type" id="content_type">
                                @foreach($contenttypes as $key => $type)
                                  <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group" >
                            <textarea class="textarea_editor form-control" id="message" name="message" rows="5" placeholder="Enter text in sw ..."  v-model='message' @keyup='charCount()'></textarea>
                        </div>

                        <div class="form-group" >
                            <textarea class="textarea_editor form-control" id="eng_message" name="eng_message" rows="5" placeholder="Enter text in en..." ></textarea>
                        </div>

                        
                      
                        <button type="submit" class="btn btn-success mt-3"><i class="fa fa-envelope-o"></i> Save</button>
                        <button class="btn btn-inverse mt-3"><i class="fa fa-times"></i> Discard</button>
                    </div>
                </form>
               </div>
            </div>
        </div>
    </div>
</div>


<script>
    var app = new Vue({
      el: '#myapp',
      data: {
        message: "",
        totalcharacter: 0
      },
      methods: {
        charCount: function(){
 
          this.totalcharacter = this.message.length;
        //   this.englishcount = this.eng_message.length;
 
        } 
      }
    })
    </script>
@endsection