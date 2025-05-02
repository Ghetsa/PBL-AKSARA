<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold py-3 mb-0">
            {{ $breadcrumb->title ?? 'Judul Halaman' }}
        </h4>

        @if(isset($breadcrumb->list) && is_array($breadcrumb->list))
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach($breadcrumb->list as $key => $value)
                    @if($key == count($breadcrumb->list) - 1)
                        <li class="breadcrumb-item active" aria-current="page">{{ $value }}</li>
                    @else
                        <li class="breadcrumb-item">{{ $value }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>
        @endif
    </div>
</div>
