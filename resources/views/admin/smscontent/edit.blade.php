@extends('layouts.app')

@push('before-styles')

<link href="/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

@endpush

@push('after-scripts')

<script src="/assets/plugins/switchery/dist/switchery.min.js"></script>
<script src="/assets/plugins/select2/dist/js/select2.full.min.js" type="text/javascript"></script>

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
        <h3 class="text-themecolor mb-0 mt-0">SMS CONTENTS</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("admin.home") }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a  href="{{ route("admin.contents.index") }}">sms content</a></li>
            <li class="breadcrumb-item active">Content Edit</li>
        </ol>
    </div>
  
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
                  <form action="{{ route('admin.contents.update',$content) }}" method="post" >   
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <h3 class="card-title">Update Content</h3>
                       
                        <div class="form-group">
                            <label class="control-label">Select Category <span class="text-danger">*</span> </label>
                            <select class="form-control select2" name="content_type" id="content_type">
                                @foreach($contenttypes as $key => $type)
                                  <option value="{{ $key }}" {{$content->content_type == $key ? "selected" : "" }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group" >
                            <label class="control-label">Content in Swahili <span class="text-danger">*</span> </label>
                            <textarea class="textarea_editor form-control" id="message" name="message" rows="5"  placeholder="Enter text in en..." >{{$content->message}}</textarea>
                        </div>

                        <div class="form-group" >
                            <label class="control-label">Content in English <span class="text-danger">*</span> </label>
                            <textarea class="textarea_editor form-control" id="eng_message" name="eng_message" rows="5" placeholder="Enter text in en..." >{{$content->eng_message}}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Priority</label>
                            <input type="number" id="priority" name="priority" class="form-control" value="{{$content->length}}">
                        </div>
                      
                        <button type="submit" class="btn btn-success mt-3"><i class="fa fa-envelope-o"></i> Update</button>
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