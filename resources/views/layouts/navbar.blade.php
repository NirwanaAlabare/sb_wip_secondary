@php
    $thisOrderDate = date('Y-m-d');
    $disableDate = false;
    if (isset($orderDate) && $orderDate) {
        $thisOrderDate = $orderDate;
        $disableDate = true;
    }
@endphp
<header>
    <nav class="navbar bg-body-secondary navbar-expand">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('images/Frame 1.png') }}" alt="" width="130"></a>
                <p class="mb-0 bg-sb-secondary p-1 text-light rounded-3 fw-bold">SECONDARY {{ $mode ? $mode : "IN"}}</p>
            </div>
            <ul class="navbar-nav align-items-center gap-3">
                <div class="row justify-content-end align-items-center">

                    <div class="col-md-auto">
                        <li class="nav-item w-100">
                            <p id="input-type"></p>
                        </li>
                    </div>
                    @role ("in_out")
                        <div class="col-md-auto">
                            <li class="nav-item bg-light dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $mode }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('in') }}">IN</a></li>
                                    <li><a class="dropdown-item" href="{{ route('out') }}">OUT</a></li>
                                </ul>
                            </li>
                        </div>
                    @endrole
                    <div class="col-md-auto">
                        <li class="nav-item w-100">
                            <input type="date" class="form-control form-control-sm" id="tanggal" name="tanggal" value="{{ $thisOrderDate }}" {{ $disableDate ? "readonly" : "" }}>
                        </li>
                    </div>
                    <div class="col-md-auto">
                        <li class="nav-item w-100">
                            <input type="text" class="form-control form-control-sm text-center" id="jam" name="jam" readonly>
                        </li>
                    </div>
                    <div class="col-md-auto">
                        <li class="nav-item dropdown w-100">
                            <button class="btn btn-sm bg-white dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-regular fa-gear"></i>
                                <span>{{ strtoupper(substr(Auth::user()->FullName, 0, 5)).(strlen(Auth::user()->FullName) > 5 ? '...' : '') }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profile"><i class="fa-regular fa-gear"></i> {{ strtoupper(Auth::user()->FullName) }}</a></li>
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#history"><i class="fa-regular fa-clock-rotate-left"></i> History</a></li>
                                {{-- <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#undo"><i class="fa-regular fa-trash"></i> Latest Undo</a></li> --}}
                                <li><a class="dropdown-item" onclick="logout('{{ url('/login/unauthenticate') }}')"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</a></li>
                            </ul>
                        </li>
                    </div>
                    <div class="col-md-auto">
                        <li class="nav-item w-100">
                            <a class="btn btn-no btn-sm" onclick="logout('{{ url('/login/unauthenticate') }}')">Log Out</a>
                        </li>
                    </div>
                </div>
            </ul>
        </div>
    </nav>
</header>

@push('scripts')
    <script>
        // Set Date Event
        if (document.getElementById("tanggal")) {
            let tanggalValue = document.getElementById("tanggal").value;
            let isChanged = function() {
                if(document.getElementById("tanggal").value !== tanggalValue){
                    tanggalValue=tanggal.value;
                    return true;
                };

                return false;
            };

            document.getElementById("tanggal").addEventListener("change", function() {
                if(isChanged()) {
                    Livewire.emit('setDate', tanggalValue);
                }
            });
        }

        function changeInOutMode(mode) {
            if (mode == "IN") {
                window.location.href = "{{ url('/in') }}";
            } else if (mode == "OUT") {
                window.location.href = "{{ url('/out') }}";
            }
        }
    </script>
@endpush
