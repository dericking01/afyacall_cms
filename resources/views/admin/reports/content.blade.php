@extends('layouts.app')

@push('before-styles')


@endpush

@section('content')

<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Keyword Categories</h4>
            <div>
                <canvas id="keywordcontent" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('after-scripts')
<script src="{{asset('assets/plugins/Chart.js/chartjs.init.js')}}"></script>
<script src="{{asset('assets/plugins/Chart.js/Chart.min.js')}}"></script>
<script type="text/javascript">

    $(document).ready(function () {
    
        $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
    
           $.ajax({
                  type:'GET',
                  url:"{{ route('admin.contenttype_list')}}",
                  success:function(response){
                    var datalabels = []
                    var countcontent = []
                    $.each(response, function(k, v)  {
                        datalabels.push(v['name'])
                        countcontent.push(v['contents_count'])
                        
                    });
                    new Chart(document.getElementById("keywordcontent"),
                            {
                                "type":"line",
                                "data":{"labels":datalabels,
                                "datasets":[{
                                                "label":"Total Contents",
                                                "data":countcontent,
                                                "fill":false,
                                                "borderColor":"rgb(86, 192, 216)",
                                                "lineTension":0.1
                                            }]
                            },"options":{}});
                  },
               });		
     });    
    
    </script>
<script>
       
</script>


@endpush