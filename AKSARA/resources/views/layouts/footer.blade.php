{{-- <footer class="pc-footer">
  <div class="footer-wrapper container-fluid">
    <div class="row">
      <div class="col-sm my-1">
        <p class="m-0"
          >Mantis &#9829; crafted by Team <a href="https://themeforest.net/user/codedthemes" target="_blank">Codedthemes</a> Distributed by <a href="https://themewagon.com/">ThemeWagon</a>.</p
        >
      </div>
      <div class="col-auto my-1">
        <ul class="list-inline footer-link mb-0">
          <li class="list-inline-item"><a href="../index.html">Home</a></li>
        </ul>
      </div>
    </div>
  </div>
</footer> --}}

<footer class="pc-footer">
  <div class="footer-wrapper container-fluid">
      <div class="row">
          <div class="col-sm my-1">
              <p class="m-0">Mantis &#9829; crafted by Team <a href="[https://themeforest.net/user/codedthemes](https://themeforest.net/user/codedthemes)" target="_blank">Codedthemes</a> Distributed by <a href="[https://themewagon.com/](https://themewagon.com/)">ThemeWagon</a>.</p>
          </div>
          <div class="col-auto my-1">
              <ul class="list-inline footer-link mb-0">
                   @if (Auth::check())
                      @if (Auth::user()->role == 'admin')
                          <li class="list-inline-item"><a href="{{ route('dashboard') }}">Home</a></li>
                      @elseif (Auth::user()->role == 'dosen')
                          <li class="list-inline-item"><a href="{{ route('dashboardDSN') }}">Home</a></li>
                      @elseif (Auth::user()->role == 'mahasiswa')
                          <li class="list-inline-item"><a href="{{ route('dashboard.mahasiswa') }}">Home</a></li>
                      @else
                          <li class="list-inline-item"><a href="{{ url('/') }}">Home</a></li>
                      @endif
                  @else
                      <li class="list-inline-item"><a href="{{ url('/') }}">Home</a></li>
                  @endif
              </ul>
          </div>
      </div>
  </div>
</footer>