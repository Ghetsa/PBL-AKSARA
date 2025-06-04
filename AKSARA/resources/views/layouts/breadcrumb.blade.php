{{-- <div class="page-header">
<div class="page-block">
    <div class="row align-items-center">
    <div class="col-md-12">
        <div class="page-header-title">
        <h5 class="m-b-10">Sample Page</h5>
        </div>
        <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard/index.html">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript: void(0)">Other</a></li>
        <li class="breadcrumb-item" aria-current="page">Sample Page</li>
            @foreach($breadcrumb->list as $key => $value)
                @if($key == count($breadcrumb->list) - 1)
                    <li class="breadcrumb-item active">{{ $value }}</li>
                @else
                    <li class="breadcrumb-item">{{ $value }}</li>
                @endif
            @endforeach 
        </ul>
    </div>
    </div>
</div>
</div> --}}

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">@yield($breadcrumb->title)</h5>
                </div>
                <ul class="breadcrumb">
                    @if (Auth::check())
                        @if (Auth::user()->role == 'admin')
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        @elseif (Auth::user()->role == 'dosen')
                            <li class="breadcrumb-item"><a href="{{ route('dashboardDSN') }}">Home</a></li>
                        @elseif (Auth::user()->role == 'mahasiswa')
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.mahasiswa') }}">Home</a></li>
                        @else
                            {{-- Default Home link if role is not recognized or user is not any of the above --}}
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        @endif
                    @else
                        {{-- Default Home link for guests or if Auth is not checked --}}
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    @endif
                    {{-- <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li> --}}
                        @foreach($breadcrumb->list as $key => $value)
                            @if($key == count($breadcrumb->list) - 1)
                                <li class="breadcrumb-item active">{{ $value }}</li>
                            @else
                                <li class="breadcrumb-item">{{ $value }}</li>
                            @endif
                        @endforeach 
                    @yield('breadcrumb')
                </ul>
            </div>
        </div>
    </div>
</div>