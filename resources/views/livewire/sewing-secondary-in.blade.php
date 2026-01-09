<div>
    <div class="loading-container-fullscreen" wire:loading
        wire:target="changeMode, preSaveSelectedDefectIn, saveSelectedDefectIn, saveCheckedDefectIn, saveAllDefectIn, preSaveSelectedDefectOut, saveSelectedDefectOut, saveCheckedDefectOut, saveAllDefectOut, submitDefectIn, submitDefectOut, submitDefectOut, refreshComponent, defectInOutputType, defectInLine, defectOutOutputType, defectOutLine, showDefectAreaImage, defectOutFilterKode, defectInFilterKode, defectOutFilterWaktu, defectInFilterWaktu, defectOutFilterLine, defectInFilterLine, defectOutFilterMasterPlan, defectInFilterMasterPlan, defectOutFilterSize, defectInFilterSize, defectOutFilterType, defectInFilterType">
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
                <button type="button" class="btn btn-sm btn-sb-outline {{ $mode == "in-out" ? "active" : "" }}" {{ $mode == "in-out" ? "disabled" : "" }} id="button-in-out">SUM</button>
                <button type="button" class="btn btn-sm btn-defect {{ $mode == "in" ? "active" : "" }}" {{ $mode == "in" ? "disabled" : "" }} id="button-in">IN</button>
            </div>
            <div class="d-flex justify-content-end w-50">
                <select class="form-select select2 w-auto" name="secondaries" id="secondaries" wire>
                    <option value="">Secondary 1</option>
                    <option value="">Secondary 2</option>
                </select>
            </div>
        </div>

        {{-- SECONDARY SEWING IN --}}
        <div class="col-12 col-md-12 {{ $mode != "in" ? 'd-none' : ''}}" wire:poll.30000ms>
            <div class="card">
                <div class="card-header bg-defect">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title text-light text-center fw-bold">INPUT SECONDARY 1
                        </h5>
                        <div class="d-flex align-items-center">
                            <h5 class="px-3 mb-0 text-light" id="total-defect-in" wire:ignore>Total : <b>0</b></h5>
                            <button class="btn btn-defect float-end" wire:click="refreshComponent()" onclick="reloadSewingSecondaryInListTable()">
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
                    <div class="table-responsive my-3">
                        <table class="table" id="secondary-in-table" wire:ignore>
                            <thead>
                                <th>Kode QR</th>
                                <th>Line</th>
                                <th>Worksheet</th>
                                <th>Style</th>
                                <th>Color</th>
                                <th>Size</th>
                            </thead>
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
                        <h5 class="card-title text-light text-center fw-bold">SECONDARY SEWING IN Summary</h5>
                        <div class="d-flex align-items-center">
                            <h5 class="px-3 mb-0 text-light">Total : <b>{{ $totalDefectInOut }}</b></h5>
                            <button class="btn btn-defect float-end" wire:click="refreshComponent()"
                                onclick="secondaryInOutReload()">
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
                                        <th>Total OUT</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
                    <h5 class="modal-title">SUMMARY SECONDARY 1</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="text" class="form-control" id="secondaryInOutDetailDate" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Line</label>
                                <select class="form-select select2-secondary-in-out-modal" id="secondaryInOutDetailLine"
                                    onchange="secondaryInOutDetailReload()">
                                    <option value="" selected>All Line</option>
                                    @foreach ($lines as $line)
                                        <option value="{{ $line->username }}">{{ str_replace("_", " ", $line->username) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-select select2-secondary-in-out-modal" id="secondaryInOutDetailDepartment"
                                    onchange="secondaryInOutDetailReload()">
                                    <option value="">All Department</option>
                                    <option value="qc">QC</option>
                                    {{-- <option value="qcf">QC FINISHING</option> --}}
                                    <option value="packing">FINISHING</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row g-1 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">IN</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailIn" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">OUTPUT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailOut" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">DEFECT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailDefect" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">REJECT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailReject" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">WIP</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailProcess" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered w-100" id="secondary-in-out-detail-table">
                                    <thead>
                                        <tr>
                                            <th>Time IN</th>
                                            <th>Time OUT</th>
                                            <th>Line</th>
                                            <th>Dept.</th>
                                            <th>QR</th>
                                            <th>No. WS</th>
                                            <th>Style</th>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th>Type</th>
                                            <th>Area</th>
                                            <th>Image</th>
                                            <th>Status</th>
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
            document.getElementById('scannedItemSecondaryIn').focus();

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

            $('#select-defect-in-line').on('change', function (e) {
                Livewire.emit("loadingStart");

                let selectedDefectInLine = $('#select-defect-in-line').val();

                @this.set('defectInLine', selectedDefectInLine);

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

            $('#button-out').on('click', async function (e) {
                @this.changeMode("out")
            })

            $('#button-in-out').on('click', async function (e) {
                @this.changeMode("sum")
            })
        });

        // Defect In List
        let defectInListTable = $("#defect-in-list-table").DataTable({
            serverSide: true,
            processing: true,
            ordering: false,
            searching: false,
            paging: true,
            ajax: {
                url: '{{ route('get-defect-in-list') }}',
                dataType: 'json',
                data: function (d) {
                    d.defectInOutputType = $("#defect-in-output-type").val();
                    d.defectInSearch = $("#defect-in-search").val();
                    d.defectInLine = $("#defect-in-line").val();
                    d.defectInFilterKode = $("#defectInFilterKode").val();
                    d.defectInFilterWaktu = $("#defectInFilterWaktu").val();
                    d.defectInFilterLine = $("#defectInFilterLine").val();
                    d.defectInFilterMasterPlan = $("#defectInFilterMasterPlan").val();
                    d.defectInFilterSize = $("#defectInFilterSize").val();
                    d.defectInFilterType = $("#defectInFilterType").val();
                }
            },
            columns: [
                {
                    data: null,
                },
                {
                    data: 'kode_numbering',
                },
                {
                    data: 'defect_time',
                },
                {
                    data: 'sewing_line',
                },
                {
                    data: 'ws',
                },
                {
                    data: 'size',
                },
                {
                    data: 'defect_type',
                },
                {
                    data: 'defect_qty',
                },
                {
                    data: 'output_type',
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
                    targets: [4],
                    className: "text-nowrap text-center align-middle",
                    render: (data, type, row, meta) => {
                        return row.ws + "<br>" + row.style + "<br>" + row.color;
                    }
                },
                {
                    targets: [8],
                    className: "text-nowrap text-center align-middle",
                    render: (data, type, row, meta) => {
                        let textColor = "";
                        if (data == "packing") {
                            textColor = "text-success";
                        } else {
                            textColor = "text-danger";
                        }
                        return "<span class='fw-bold " + textColor + "'>" + (data && data == "packing" ? "finishing" : data).toUpperCase() + "</span>";
                    }
                },
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

        $("#defect-in-list-table").DataTable().on('draw.dt', function (e, settings, json, xhr) {
            var info = $("#defect-in-list-table").DataTable().page.info();
            var totalEntries = info.recordsDisplay;
            $('#total-defect-in b').text(totalEntries);
        });

        function reloadSecondaryInListTable() {
            $("#defect-in-list-table").DataTable().ajax.reload(() => {
                var info = $("#defect-in-list-table").DataTable().page.info();
                var totalEntries = info.recordsDisplay;
                $('#total-defect-in b').text(totalEntries);
            });
        }

        function submitSecondaryIn() {
            document.getElementById("loading").classList.remove("d-none");

            $.ajax({
                type: "post",
                url: "{{ route('submit-defect-in') }}",
                data: {
                    scannedDefectIn: $("#scannedItemSecondaryIn").val(),
                    defectInOutputType: $("#defect-in-output-type").val(),
                },
                dataType: "json",
                success: function (response) {
                    document.getElementById("loading").classList.add("d-none");

                    $("#scannedItemSecondaryIn").focus();

                    if (response) {
                        showNotification(response.status, response.message);

                        reloadSecondaryInListTable();
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    console.error(jqXHR);
                }
            });
        }

        var scannedItemSecondaryIn = document.getElementById("scannedItemSecondaryIn");
        scannedItemSecondaryIn.addEventListener("change", async function () {
            @this.scannedDefectIn = this.value;

            // submit
            // @this.submitSecondaryIn();
            submitSecondaryIn();

            this.value = '';
        });

        // init scan
        Livewire.on('qrInputFocus', async (mode) => {
            console.log(mode);

            if (mode == "in") {
                document.getElementById('scannedItemSecondaryIn').focus();
                document.getElementById('button-out').disabled = false;

                reloadSecondaryInListTable();
            }
        });

        function getMasterPlanData(type) {
            if (type != "in" && type != "out") {
                type = 'in';
            }
            console.log(type, $("#defect-" + type + "-date").val());
            $.ajax({
                url: "{{ route("get-master-plan") }}",
                method: "GET",
                data: {
                    date: $("#defect-" + type + "-date").val(),
                    line: $("#select-defect-" + type + "-line").val(),
                },
                success: function (res) {
                    document.getElementById("select-defect-" + type + "-master-plan").innerHTML = "";

                    let selectElement = document.getElementById("select-defect-" + type + "-master-plan")

                    let option = document.createElement("option");
                    option.value = "";
                    option.innerText = "All Master Plan";
                    selectElement.appendChild(option);

                    $("#select-defect-" + type + "-master-plan").val("").trigger("change");

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
                url: "{{ route("get-size") }}",
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
                url: '{{ route('get-defect-in-out-daily') }}',
                dataType: 'json',
                data: function (d) {
                    d.dateFrom = $("#dateFrom").val();
                    d.dateTo = $("#dateTo").val();
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
                    data: 'total_out',
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
                url: '{{ route('get-defect-in-out-detail') }}',
                data: function (d) {
                    d.tanggal = $("#secondaryInOutDetailDate").val();
                    d.line = $("#secondaryInOutDetailLine").val();
                    d.departemen = $("#defectInOutDetailDepartment").val();
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
                    targets: [8],
                    render: (data, type, row, meta) => {
                        let textColor = '';

                        if (data == "reworked") {
                            textColor = "text-rework";
                        } else {
                            textColor = "text-defect";
                        }

                        return `<span class="` + textColor + ` fw-bold">` + (data ? data.toUpperCase() : '-') + `</span>`;
                    }
                },
                {
                    targets: [11],
                    render: (data, type, row, meta) => {
                        return `<button class="btn btn-defect" onclick="onShowDefectAreaImage('` + row.gambar + `', ` + row.defect_area_x + `, ` + row.defect_area_y + `)"><i class="fa fa-image"></i></button>`
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
                $("#secondaryInOutDetailIn").val("-");
                $("#defectInOutDetailProcess").val("-");
                $("#defectInOutDetailOut").val("-");

                $.ajax({
                    url: "{{ route("get-defect-in-out-detail-total") }}",
                    type: "get",
                    data: {
                        tanggal: $("#secondaryInOutDetailDate").val(),
                        line: $("#secondaryInOutDetailLine").val(),
                        departemen: $("#defectInOutDetailDepartment").val()
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        if (response) {
                            $("#secondaryInOutDetailIn").val(response.defectIn);
                            $("#defectInOutDetailProcess").val(response.defectProcess);
                            $("#defectInOutDetailOut").val(response.defectOut);
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
                url: "{{ route("export-defect-in-out") }}",
                type: 'post',
                data: {
                    dateFrom: $("#dateFrom").val(),
                    dateTo: $("#dateTo").val(),
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
                    link.download = "Defect In Out {{ Auth::user()->Groupp }} " + $("#dateFrom").val() + " - " + $("#dateTo").val() + ".xlsx";
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
