<div>
    <div class="loading-container-fullscreen" wire:loading wire:target="changeMode, submitDefectIn, refreshComponent, secondaryInLine, showDefectAreaImage, secondaryInFilterKode, secondaryInFilterWaktu, secondaryInFilterLine, secondaryInFilterMasterPlan, secondaryInFilterSize, secondaryInFilterSecondary">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    <div class="loading-container-fullscreen hidden" id="loading-sewing-secondary-in-out">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    <div class="row g-3">
        <div class="d-flex justify-content-center">
            <div class="d-flex justify-content-end gap-1 w-50">
                <button type="button" class="btn btn-sm btn-sb-outline {{ $mode == "sum" ? "active" : "" }}" {{ $mode == "sum" ? "disabled" : "" }} id="button-in-out">SUM</button>
                <button type="button" class="btn btn-sm btn-defect {{ $mode == "in" ? "active" : "" }}" {{ $mode == "in" ? "disabled" : "" }} id="button-in">IN</button>
            </div>
            <div class="d-flex justify-content-end w-50" wire:ignore>
                <select class="form-select select2 w-auto" name="selectedSecondary" id="selectedSecondary">
                    @foreach ($secondaryMaster as $secondary)
                        <option value="{{ $secondary->id }}">{{ $secondary->secondary }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- SECONDARY SEWING IN --}}
        <div class="col-12 col-md-12 {{ $mode != "in" ? 'd-none' : ''}}" wire:poll.30000ms>
            <div class="card">
                <div class="card-header bg-defect">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title text-light text-center fw-bold">{{ $selectedSecondaryText }} Input</h5>
                        <div class="d-flex align-items-center">
                            <h5 class="px-3 mb-0 text-light" id="total-secondary-in" wire:ignore>Total : <b>0</b></h5>
                            <button class="btn btn-dark float-end" wire:click="refreshComponent()" onclick="reloadSecondaryInListTable()">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4" wire:ignore>
                            <input type="text" class="qty-input border h-100" id="scannedSecondaryIn" name="scannedSecondaryIn">
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Kode QR</label>
                                    <input type="text" class="form-control" id="kodeNumbering" name="kodeNumbering" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Line</label>
                                    <input type="text" class="form-control" id="sewingLine" name="sewingLine" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Worksheet</label>
                                    <input type="text" class="form-control" id="worksheet" name="worksheet" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Style</label>
                                    <input type="text" class="form-control" id="style" name="style" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Color</label>
                                    <input type="text" class="form-control" id="color" name="color" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Size</label>
                                    <input type="text" class="form-control" id="size" name="size" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table w-100" id="secondary-in-list-table" wire:ignore>
                            <thead>
                                <tr>
                                    <th>No. </th>
                                    <th>Kode QR</th>
                                    <th>Line</th>
                                    <th>Worksheet</th>
                                    <th>Style</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Secondary</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                </tr>
                                <tr class="text-center align-middle">
                                    <td>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterKode" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterLine" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterWS" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterStyle" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterColor" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterSize" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterSecondary" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterAuthor" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="secondaryInFilterWaktu" onkeyup="reloadSecondaryInListTable()">
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECONDARY SEWING IN SUM --}}
        <div class="col-12 col-md-12 {{ $mode != "sum" ? 'd-none' : ''}}">
            <div class="card">
                <div class="card-header bg-sb">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title text-light text-center fw-bold">{{ $selectedSecondaryText }} Summary</h5>
                        <div class="d-flex align-items-center">
                            <h5 class="px-3 mb-0 text-light">Total : <b>{{ $totalSecondaryInOut }}</b></h5>
                            <button class="btn btn-dark float-end" wire:click="refreshComponent()" onclick="secondaryInOutReload()">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-end">
                            <div class="d-flex align-items-end gap-3 mb-3">
                                <div>
                                    <label class="form-label">From</label>
                                    <input type="date" class="form-control" value="{{ date("Y-m-d", strtotime("-7 days")) }}" id="dateFrom" wire:model="secondaryInOutFrom" onchange="secondaryInOutReload()">
                                </div>
                                <span class="mb-2">-</span>
                                <div>
                                    <label class="form-label">To</label>
                                    <input type="date" class="form-control" value="{{ date("Y-m-d") }}" id="dateTo" wire:model="secondaryInOutTo" onchange="secondaryInOutReload()">
                                </div>
                            </div>
                            <div class="mb-3" wire:ignore>
                                <button class="btn btn-success" onclick="exportExcel(this)"><i class="fa fa-file-excel"></i> Export</button>
                            </div>
                        </div>
                        <div class="table-responsive" wire:ignore>
                            <table class="table table-bordered w-100" id="secondary-in-out-table">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Date</th>
                                        <th>Total IN</th>
                                        <th>Total PROCESS</th>
                                        <th>Total RFT</th>
                                        <th>Total DEFECT</th>
                                        <th>Total REJECT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECONDARY SEWING IN SUM MODAL --}}
    <div class="modal" tabindex="-1" id="secondary-in-out-modal" wire:ignore>
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-sb text-light fw-bold">
                    <h5 class="modal-title"><span id="secondary-in-out-modal-title"></span> SUMMARY</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="text" class="form-control" id="secondaryInOutDetailDate" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-3 gap-1">
                                <div class="w-auto">
                                    <label class="form-label fw-bold">IN</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailIn" readonly>
                                </div>
                                <div class="w-auto">
                                    <label class="form-label fw-bold">OUTPUT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailRft" readonly>
                                </div>
                                <div class="w-auto">
                                    <label class="form-label fw-bold">DEFECT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailDefect" readonly>
                                </div>
                                <div class="w-auto">
                                    <label class="form-label fw-bold">REJECT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailReject" readonly>
                                </div>
                                <div class="w-auto">
                                    <label class="form-label fw-bold">WIP</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailProcess" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Secondary</label>
                                <select class="form-select select2-secondary-in-out-modal w-auto" name="selectedSecondaryModal" id="selectedSecondaryModal" >
                                    @foreach ($secondaryMaster as $secondary)
                                        <option value="{{ $secondary->id }}">{{ $secondary->secondary }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered w-100" id="secondary-in-out-detail-table">
                                    <thead>
                                        <tr>
                                            <th>Time IN</th>
                                            <th>Time OUT</th>
                                            <th>QR</th>
                                            <th>Line</th>
                                            <th>No. WS</th>
                                            <th>Style</th>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>Area</th>
                                            <th>Image</th>
                                            <th>IN By</th>
                                            <th>OUT By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Show Defect Area --}}
    <div class="show-defect-area" id="show-defect-area" wire:ignore>
        <div class="position-relative d-flex flex-column justify-content-center align-items-center">
            <button type="button" class="btn btn-lg btn-light rounded-0 hide-defect-area-img" onclick="onHideDefectAreaImage()">
                <i class="fa-regular fa-xmark fa-lg"></i>
            </button>
            <div class="defect-area-img-container mx-auto">
                <div class="defect-area-img-point" id="defect-area-img-point-show"></div>
                <img src="" alt="" class="img-fluid defect-area-img" id="defect-area-img-show">
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('datatables/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/datatables-rowgroup/css/rowGroup.bootstrap4.min.css') }}">

    {{-- DataTables --}}
    <script src="{{ asset('datatables/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('datatables/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('datatables/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", async function () {
            document.getElementById('scannedSecondaryIn').focus();

            $('.select2').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
            });

            $('.select2-secondary-in-out-modal').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                dropdownParent: $('#secondary-in-out-modal')
            });

            $('#selectedSecondary').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedSecondary = $('#selectedSecondary').val();
                let selectedSecondaryText = $('#selectedSecondary').find('option:selected').text();

                @this.set('selectedSecondary', selectedSecondary);
                @this.set('selectedSecondaryText', selectedSecondaryText);

                if ($('#selectedSecondary').val() != $('#selectedSecondaryModal').val()) {
                    $('#selectedSecondaryModal').val(selectedSecondary).trigger("change");
                }

                $("#secondary-in-out-modal-title").html(selectedSecondaryText);

                reloadSecondaryInListTable();
                secondaryInOutDetailReload();
            });

            $('#selectedSecondaryModal').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedSecondary = $('#selectedSecondaryModal').val();
                let selectedSecondaryText = $('#selectedSecondaryModal').find('option:selected').text();

                @this.set('selectedSecondary', selectedSecondary);
                @this.set('selectedSecondaryText', selectedSecondaryText);

                if ($('#selectedSecondary').val() != $('#selectedSecondaryModal').val()) {
                    $('#selectedSecondary').val(selectedSecondary).trigger("change");
                }

                $("#secondary-in-out-modal-title").html(selectedSecondaryText);

                reloadSecondaryInListTable();
                secondaryInOutDetailReload();
            });

            $('#select-secondary-in-line').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedsecondaryInLine = $('#select-secondary-in-line').val();

                @this.set('secondaryInLine', selectedsecondaryInLine);

                getMasterPlanData();

                getDefectType();
                getDefectArea();
            });

            $('#select-defect-out-line').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectOutLine = $('#select-defect-out-line').val();

                @this.set('defectOutLine', selectedDefectOutLine);

                getMasterPlanData("out");

                getDefectType("out");
                getDefectArea("out");
            });

            $('#select-defect-in-master-plan').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectInMasterPlan = $('#select-defect-in-master-plan').val();

                @this.set('defectInSelectedMasterPlan', selectedDefectInMasterPlan);

                getSizeData();

                getDefectType();
                getDefectArea();
            });

            $('#select-defect-out-master-plan').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectOutMasterPlan = $('#select-defect-out-master-plan').val();

                @this.set('defectOutSelectedMasterPlan', selectedDefectOutMasterPlan);

                getSizeData("out");

                getDefectType("out");
                getDefectArea("out");
            });

            $('#select-defect-in-size').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectInSize = $('#select-defect-in-size').val();

                @this.set('defectInSelectedSize', selectedDefectInSize);

                getDefectType();
                getDefectArea();
            });

            $('#select-defect-out-size').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectOutSize = $('#select-defect-out-size').val();

                @this.set('defectOutSelectedSize', selectedDefectOutSize);

                getDefectType("out");
                getDefectArea("out");
            });

            $('#select-defect-in-type').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectInType = $('#select-defect-in-type').val();

                @this.set('defectInSelectedType', selectedDefectInType);

                getDefectArea();
            });

            $('#select-defect-out-type').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectOutType = $('#select-defect-out-type').val();

                @this.set('defectOutSelectedType', selectedDefectOutType);

                getDefectArea("out");
            });

            $('#select-defect-in-area').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectInType = $('#select-defect-in-area').val();

                @this.set('defectInSelectedArea', selectedDefectInType);

                getDefectType();
            });

            $('#select-defect-out-area').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectOutType = $('#select-defect-out-area').val();

                @this.set('defectOutSelectedArea', selectedDefectOutType);

                getDefectType("out");
            });

            $('#button-in').on('click', async function (e) {
                @this.changeMode("in")
            })

            $('#button-in-out').on('click', async function (e) {
                @this.changeMode("sum")
            })

            $('#selectedSecondary').trigger('change');
        });

        // Defect In List
        let defectInListTable = $("#secondary-in-list-table").DataTable({
            serverSide: true,
            processing: true,
            ordering: false,
            searching: false,
            paging: true,
            lengthChange: false,
            ajax: {
                url: '{{ route('in-get-secondary-in-list') }}',
                dataType: 'json',
                data: function (d) {
                    d.selectedSecondary = $("#selectedSecondary").val();
                    d.secondaryInSearch = $("#secondary-in-search").val();
                    d.secondaryInLine = $("#secondary-in-line").val();
                    d.secondaryInFilterKode = $("#secondaryInFilterKode").val();
                    d.secondaryInFilterWaktu = $("#secondaryInFilterWaktu").val();
                    d.secondaryInFilterLine = $("#secondaryInFilterLine").val();
                    d.secondaryInFilterWS = $("#secondaryInFilterWS").val();
                    d.secondaryInFilterStyle = $("#secondaryInFilterStyle").val();
                    d.secondaryInFilterColor = $("#secondaryInFilterColor").val();
                    d.secondaryInFilterSize = $("#secondaryInFilterSize").val();
                    d.secondaryInFilterSecondary = $("#secondaryInFilterSecondary").val();
                }
            },
            columns: [
                {
                    data: 'id',
                },
                {
                    data: 'kode_numbering',
                },
                {
                    data: 'sewing_line',
                },
                {
                    data: 'ws',
                },
                {
                    data: 'style',
                },
                {
                    data: 'color',
                },
                {
                    data: 'size',
                },
                {
                    data: 'secondary',
                },
                {
                    data: 'created_by_username',
                },
                {
                    data: 'secondary_in_time',
                },
            ],
            columnDefs: [
                // {
                //     targets: [0],
                //     className: "text-nowrap text-center align-middle",
                //     render: (data, type, row, meta) => {
                //         return meta.row+1;
                //     }
                // },
                {
                    targets: "_all",
                    className: "text-nowrap text-center align-middle"
                },
            ],
            rowCallback: function (row, data, iDisplayIndex) {
                var info = this.api().page.info();
                var page = info.page;
                var length = info.length;
                var index = (page * length + (iDisplayIndex + 1));
                $('td:eq(0)', row).html(index); // Assuming the first column is for the index
            }
        });

        $("#secondary-in-list-table").DataTable().on('draw.dt', function (e, settings, json, xhr) {
            var info = $("#secondary-in-list-table").DataTable().page.info();
            var totalEntries = info.recordsDisplay;
            $('#total-secondary-in b').text(totalEntries);
        });

        function reloadSecondaryInListTable() {
            $("#secondary-in-list-table").DataTable().ajax.reload(() => {
                var info = $("#secondary-in-list-table").DataTable().page.info();
                var totalEntries = info.recordsDisplay;
                $('#total-secondary-in b').text(totalEntries);
            });
        }

        function submitSecondaryIn() {
            document.getElementById("loading").classList.remove("d-none");

            $("#kodeNumbering").val("");
            $("#sewingLine").val("");
            $("#worksheet").val("");
            $("#style").val("");
            $("#color").val("");
            $("#size").val("");

            $.ajax({
                type: "post",
                url: "{{ route('in-submit-secondary-in') }}",
                data: {
                    scannedSecondaryIn: $("#scannedSecondaryIn").val(),
                    selectedSecondary: $("#selectedSecondary").val(),
                },
                dataType: "json",
                success: function (response) {
                    document.getElementById("loading").classList.add("d-none");

                    $("#scannedSecondaryIn").focus();

                    if (response) {
                        showNotification(response.status, response.message);

                        if (response.data) {
                            $("#kodeNumbering").val(response.data.kode_numbering);
                            $("#sewingLine").val(response.data.sewing_line);
                            $("#worksheet").val(response.data.ws);
                            $("#style").val(response.data.style);
                            $("#color").val(response.data.color);
                            $("#size").val(response.data.size);
                        }

                        reloadSecondaryInListTable();
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    console.error(jqXHR);
                }
            });
        }

        var scannedSecondaryIn = document.getElementById("scannedSecondaryIn");
        scannedSecondaryIn.addEventListener("change", async function () {
            @this.scannedSecondaryIn = this.value;

            // submit
            // @this.submitSecondaryIn();
            submitSecondaryIn();

            this.value = '';
        });

        // init scan
        Livewire.on('qrInputFocus', async (mode) => {
            console.log(mode);

            if (mode == "in") {
                document.getElementById('scannedSecondaryIn').focus();

                reloadSecondaryInListTable();
            }
        });

        function getMasterPlanData(type) {
            if (type != "in" && type != "out") {
                type = 'in';
            }
            console.log(type, $("#secondary-in-date").val());
            $.ajax({
                url: "{{ route("in-get-master-plan") }}",
                method: "GET",
                data: {
                    date: $("#secondary-in-date").val(),
                    line: $("#select-secondary-in-line").val(),
                },
                success: function (res) {
                    document.getElementById("select-secondary-in-master-plan").innerHTML = "";

                    let selectElement = document.getElementById("select-secondary-in-master-plan")

                    let option = document.createElement("option");
                    option.value = "";
                    option.innerText = "All Master Plan";
                    selectElement.appendChild(option);

                    $("#select-secondary-in-master-plan").val("").trigger("change");

                    if (res && res.length > 0) {
                        res.forEach(item => {
                            let option = document.createElement("option");
                            option.value = item.id;
                            option.innerText = item.no_ws + " - " + item.style + " - " + item.color;

                            selectElement.appendChild(option);
                        });
                    }
                }
            });
        }

        function getSizeData(type) {
            if (type != "in" && type != "out") {
                type = 'in';
            }
            $.ajax({
                url: "{{ route("in-get-size") }}",
                method: "GET",
                data: {
                    master_plan: $("#select-defect-" + type + "-master-plan").val(),
                },
                success: function (res) {
                    document.getElementById("select-defect-" + type + "-size").innerHTML = "";

                    let selectElement = document.getElementById("select-defect-" + type + "-size")

                    let option = document.createElement("option");
                    option.value = "";
                    option.innerText = "Select Size";
                    selectElement.appendChild(option);

                    $("#select-defect-" + type + "-size").val("").trigger("change");

                    if (res && res.length > 0) {
                        res.forEach(item => {
                            let option = document.createElement("option");
                            option.value = item.id;
                            option.innerText = item.size;

                            selectElement.appendChild(option);
                        });
                    }
                }
            });
        }

        function defectInCheck(element) {
            Livewire.emit("loadingStart");

            if (element.checked) {
                @this.addDefectInSelectedList(element.value);
            } else {
                @this.removeDefectInSelectedList(element.value);
                element.removeAttribute("checked");
            }
        }

        function defectOutCheck(element) {
            Livewire.emit("loadingStart");

            if (element.checked) {
                @this.addDefectOutSelectedList(element.value);
            } else {
                @this.removeDefectOutSelectedList(element.value);
                element.removeAttribute("checked");
            }
        }

        function onShowDefectAreaImage(defectAreaImage, x, y) {
            Livewire.emit('showDefectAreaImage', defectAreaImage, x, y);
        }

        Livewire.on('showDefectAreaImage', async function (defectAreaImage, x, y) {
            await showDefectAreaImage(defectAreaImage);

            let defectAreaImageElement = document.getElementById('defect-area-img-show');
            let defectAreaImagePointElement = document.getElementById('defect-area-img-point-show');

            defectAreaImageElement.style.display = 'block'

            if (x && y) {
                let rect = await defectAreaImageElement.getBoundingClientRect();

                let pointWidth = null;
                if (rect.width == 0) {
                    pointWidth = 35;
                } else {
                    pointWidth = 0.03 * rect.width;
                }

                defectAreaImagePointElement.style.width = pointWidth + 'px';
                defectAreaImagePointElement.style.height = defectAreaImagePointElement.style.width;
                defectAreaImagePointElement.style.left = 'calc(' + x + '% - ' + 0.5 * pointWidth + 'px)';
                defectAreaImagePointElement.style.top = 'calc(' + y + '% - ' + 0.5 * pointWidth + 'px)';
                defectAreaImagePointElement.style.display = 'block';
            }
        });

        function onHideDefectAreaImage() {
            hideDefectAreaImage();

            Livewire.emit('hideDefectAreaImageClear');
        }

        let secondaryInOutDatatable = $("#secondary-in-out-table").DataTable({
            serverSide: true,
            processing: true,
            ordering: false,
            pageLength: 50,
            ajax: {
                url: '{{ route('in-get-secondary-in-out-daily') }}',
                dataType: 'json',
                data: function (d) {
                    d.dateFrom = $("#dateFrom").val();
                    d.dateTo = $("#dateTo").val();
                    d.secondary = $("#selectedSecondary").val();
                }
            },
            columns: [
                {
                    data: 'tanggal',
                },
                {
                    data: 'tanggal',
                },
                {
                    data: 'total_in',
                },
                {
                    data: 'total_process',
                },
                {
                    data: 'total_rft',
                },
                {
                    data: 'total_defect',
                },
                {
                    data: 'total_reject',
                }
            ],
            columnDefs: [
                {
                    targets: [0],
                    render: (data, type, row, meta) => {
                        return `<button type='button' class='btn btn-sb-secondary btn-sm' onclick='getSecondaryInOutDetail("` + data + `")'><i class='fa fa-search'></i></button>`
                    }
                },
                {
                    targets: "_all",
                    className: "text-nowrap align-middle"
                },
            ],
        });

        function secondaryInOutReload() {
            $("#secondary-in-out-table").DataTable().ajax.reload();
        }

        let secondaryInOutDetailDatatable = $("#secondary-in-out-detail-table").DataTable({
            serverSide: true,
            processing: true,
            ordering: false,
            pageLength: 50,
            ajax: {
                url: '{{ route('in-get-secondary-in-out-detail') }}',
                data: function (d) {
                    d.tanggal = $("#secondaryInOutDetailDate").val();
                    d.secondary = $("#selectedSecondaryModal").val();
                },
                dataType: 'json',
            },
            columns: [
                {
                    data: 'time_in',
                },
                {
                    data: 'time_out',
                },
                {
                    data: 'kode_numbering',
                },
                {
                    data: 'sewing_line',
                },
                {
                    data: 'no_ws',
                },
                {
                    data: 'style',
                },
                {
                    data: 'color',
                },
                {
                    data: 'size',
                },
                {
                    data: 'status',
                },
                {
                    data: 'defect_type',
                },
                {
                    data: 'defect_area',
                },
                {
                    data: 'gambar',
                },
                {
                    data: 'user_in',
                },
                {
                    data: 'user_out',
                },
            ],
            columnDefs: [
                {
                    targets: [2],
                    className: "disabled"
                },
                {
                    targets: [3],
                    render: (data, type, row, meta) => {
                        return data ? data.replace("_", " ").toUpperCase() : '-';
                    }
                },
                {
                    targets: [11],
                    render: (data, type, row, meta) => {
                        return `<button class="btn btn-dark" onclick="onShowDefectAreaImage('` + row.gambar + `', ` + row.defect_area_x + `, ` + row.defect_area_y + `)"><i class="fa fa-image"></i></button>`
                    }
                },
                {
                    targets: "_all",
                    className: "text-nowrap align-middle"
                },
            ],
        });

        function secondaryInOutDetailReload() {
            $("#secondary-in-out-detail-table").DataTable().ajax.reload(() => {
                $("#secondaryInOutDetailIn").val(0);
                $("#secondaryInOutDetailProcess").val(0);
                $("#secondaryInOutDetailRft").val(0);
                $("#secondaryInOutDetailDefect").val(0);
                $("#secondaryInOutDetailReject").val(0);

                $.ajax({
                    url: "{{ route("in-get-secondary-in-out-detail-total") }}",
                    type: "get",
                    data: {
                        tanggal: $("#secondaryInOutDetailDate").val(),
                        selectedSecondary: $("#selectedSecondary").val(),
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        if (response) {
                            $("#secondaryInOutDetailIn").val(response.secondaryIn);
                            $("#secondaryInOutDetailProcess").val(response.secondaryProcess);
                            $("#secondaryInOutDetailRft").val(response.secondaryRft);
                            $("#secondaryInOutDetailDefect").val(response.secondaryDefect);
                            $("#secondaryInOutDetailReject").val(response.secondaryReject);
                        }
                    },
                    error: function (jqXHR) {
                        console.error(jqXHR);
                    }
                });

                secondaryInOutReload();
            });
        }

        async function getSecondaryInOutDetail(tanggal) {
            $("#secondaryInOutDetailDate").val(tanggal);

            secondaryInOutDetailReload();

            $("#secondary-in-out-modal").modal("show");
        }

        function exportExcel(elm) {
            elm.setAttribute('disabled', 'true');
            elm.innerText = "";
            let loading = document.createElement('div');
            loading.classList.add('loading-small');
            elm.appendChild(loading);

            iziToast.info({
                title: 'Exporting...',
                message: 'Data sedang di export. Mohon tunggu...',
                position: 'topCenter'
            });

            $.ajax({
                url: "{{ route("in-export-secondary-in-out") }}",
                type: 'post',
                data: {
                    dateFrom: $("#dateFrom").val(),
                    dateTo: $("#dateTo").val(),
                    selectedSecondary: $("#selectedSecondary").val(),
                },
                xhrFields: { responseType: 'blob' },
                success: function (res) {
                    elm.removeAttribute('disabled');
                    elm.innerText = "Export ";
                    let icon = document.createElement('i');
                    icon.classList.add('fa-solid');
                    icon.classList.add('fa-file-excel');
                    elm.appendChild(icon);

                    iziToast.success({
                        title: 'Success',
                        message: 'Data berhasil di export.',
                        position: 'topCenter'
                    });

                    var blob = new Blob([res]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "Secondary In Out " + $("#selectedSecondary").val() + " " + $("#dateFrom").val() + " - " + $("#dateTo").val() + ".xlsx";
                    link.click();
                }, error: function (jqXHR) {
                    elm.removeAttribute('disabled');
                    elm.innerText = "Export ";
                    let icon = document.createElement('i');
                    icon.classList.add('fa-solid');
                    icon.classList.add('fa-file-excel');
                    elm.appendChild(icon);

                    let res = jqXHR.responseJSON;
                    let message = '';
                    console.log(res.message);
                    for (let key in res.errors) {
                        message += res.errors[key] + ' ';
                        document.getElementById(key).classList.add('is-invalid');
                    };
                    iziToast.error({
                        title: 'Error',
                        message: message,
                        position: 'topCenter'
                    });
                }
            });
        }
    </script>
@endpush
