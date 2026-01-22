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
        <div class="d-flex flex-column align-items-center justify-content-center gap-3">
            <div class="d-flex justify-content-center w-50" wire:ignore>
                <select class="form-select form-select-sm select2 w-auto" name="selectedSecondary" id="selectedSecondary">
                    @foreach ($secondaryMaster as $secondary)
                        <option value="{{ $secondary->id }}">{{ $secondary->secondary }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex justify-content-center gap-1 w-50">
                <button type="button" class="btn btn-sm btn-sb-outline {{ $mode == "sum" ? "active" : "" }}" {{ $mode == "sum" ? "disabled" : "" }} id="button-in-out">SUM</button>
                <button type="button" class="btn btn-sm btn-defect {{ $mode == "in" ? "active" : "" }}" {{ $mode == "in" ? "disabled" : "" }} id="button-in">IN</button>
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
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end">
                                <div>
                                    <label class="form-label fw-bold w-auto">Total Qty IN</label>
                                    <input type="number" class="form-control" id="total-secondary-in-1" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row g-3" wire:ignore>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Line</label>
                                    <select class="form-control select2" id="sewingLine" name="sewingLine">
                                        <option value="">Pilih Line</option>
                                        @foreach ($lines as $line)
                                            <option value="{{ $line->username }}">{{ $line->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Worksheet</label>
                                    <select class="form-control select2" id="worksheet" name="worksheet">
                                        <option value="">Pilih Worksheet</option>
                                        @foreach ($orders as $order)
                                            <option value="{{ $order->id_ws }}" data-style="{{ $order->style }}">{{ $order->no_ws }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Style</label>
                                    <input type="text" class="form-control" id="style" name="style" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Color</label>
                                    <select class="form-control select2" id="color" name="color">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Size</label>
                                    <select class="form-control select2" id="size" name="size">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Sewing Qty</label>
                                    <input type="number" class="form-control" id="sewingQty" name="sewingQty" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Input Qty</label>
                                    <input type="number" class="form-control" id="secondaryInQty" name="secondaryInQty">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-defect btn-block w-100" onclick="submitSecondaryIn()">SIMPAN</button>
                    <div class="table-responsive mt-3" wire:ignore>
                        <table class="table w-100" id="secondary-in-list-table">
                            <thead>
                                <tr>
                                    <th>No. </th>
                                    <th>Line</th>
                                    <th>Worksheet</th>
                                    <th>Style</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Secondary</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Total</th>
                                </tr>
                                <tr class="text-center align-middle">
                                    <td>
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
                                    <td></td>
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
    <script>
        document.addEventListener("DOMContentLoaded", async function () {
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

            $('#button-in').on('click', async function (e) {
                @this.changeMode("in")
            })

            $('#button-in-out').on('click', async function (e) {
                @this.changeMode("sum")
            })

            $('#selectedSecondary').trigger('change');
        });

        function updateColorList() {
            updateSewingQty();

            let selectElement = document.getElementById("color");

            if (selectElement) {
                selectElement.innerHTML = null;

                $.ajax({
                    type: "get",
                    url: "{{ route('get-color') }}",
                    data: {
                        "worksheet": $("#worksheet").val()
                    },
                    dataType: "json",
                    success: function (response)  {
                        console.log(response);

                        if (response && response.length > 0) {
                            let initOption = document.createElement("option");
                            initOption.value = "";
                            initOption.text = "Pilih Color";

                            if (selectElement) {
                                selectElement.appendChild(initOption);

                                response.forEach(item => {
                                    let option = document.createElement("option");
                                    option.value = item.color;
                                    option.text = item.color;

                                    selectElement.appendChild(option);

                                    console.log(item);
                                });
                            }
                        }
                    }
                });
            }
        }

        function updateSizeList() {
            updateSewingQty();

            let selectElement = document.getElementById("size");

            if (selectElement) {
                selectElement.innerHTML = null;

                $.ajax({
                    type: "get",
                    url: "{{ route('get-size') }}",
                    data: {
                        "worksheet": $("#worksheet").val(),
                        "color": $("#color").val(),
                    },
                    dataType: "json",
                    success: function (response)  {
                        console.log(response);

                        if (response && response.length > 0) {
                            let initOption = document.createElement("option");
                            initOption.value = "";
                            initOption.text = "Pilih Size";

                            if (selectElement) {
                                selectElement.appendChild(initOption);

                                response.forEach(item => {
                                    let option = document.createElement("option");
                                    option.value = item.size;
                                    option.text = item.size;

                                    selectElement.appendChild(option);

                                    console.log(item);
                                });
                            }
                        }
                    }
                });
            }
        }

        function updateSewingQty() {
            $.ajax({
                type: "get",
                url: "{{ route('get-sewing-qty') }}",
                data: {
                    "worksheet": $("#worksheet").val(),
                    "color": $("#color").val(),
                    "size": $("#size").val(),
                    "sewingLine" : $("#sewingLine").val(),
                },
                success: function (response) {
                    console.log(response);
                    $("#sewingQty").val(response);
                },
                error: function (jqXHR) {
                    console.error(jqXHR);
                }
            });
        }

        $("#worksheet").on("change", function (event) {
            let selectedOption = this.options[this.selectedIndex];

            $("#style").val(selectedOption.getAttribute("data-style"));

            updateColorList();
        });

        $("#color").on("change", function (event) {
            updateSizeList();
        });

        $("#size").on("change", function (event) {
            updateSewingQty();
        });

        $("#sewingLine").on("change", function (event) {
            updateSewingQty();
        });

        // Secondary In List
        let secondaryInListTable = $("#secondary-in-list-table").DataTable({
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
                {
                    data: 'secondary_in_qty',
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

        function updateSecondaryInListTotal() {
            $.ajax({
                type: "get",
                url: "{{ route('in-get-secondary-in-list-total') }}",
                data: {
                    selectedSecondary : $("#selectedSecondary").val(),
                    secondaryInSearch : $("#secondary-in-search").val(),
                    secondaryInLine : $("#secondary-in-line").val(),
                    secondaryInFilterKode : $("#secondaryInFilterKode").val(),
                    secondaryInFilterWaktu : $("#secondaryInFilterWaktu").val(),
                    secondaryInFilterLine : $("#secondaryInFilterLine").val(),
                    secondaryInFilterWS : $("#secondaryInFilterWS").val(),
                    secondaryInFilterStyle : $("#secondaryInFilterStyle").val(),
                    secondaryInFilterColor : $("#secondaryInFilterColor").val(),
                    secondaryInFilterSize : $("#secondaryInFilterSize").val(),
                    secondaryInFilterSecondary : $("#secondaryInFilterSecondary").val(),
                },
                dataType: "json",
                success: function (response) {
                    if (response) {
                        $("#total-secondary-in b").text(response);
                        $("#total-secondary-in-1").val(response);
                    }
                },
                error: function (jqXHR) {
                    console.log(jqXHR)
                }
            });
        }

        $("#secondary-in-list-table").DataTable().on('draw.dt', function (e, settings, json, xhr) {
            // var info = $("#secondary-in-list-table").DataTable().page.info();
            // var totalEntries = info.recordsDisplay;
            // $('#total-secondary-in b').text(totalEntries);
            // $('#total-secondary-in-1').val(totalEntries);

            updateSecondaryInListTotal();
        });

        function reloadSecondaryInListTable() {
            $("#secondary-in-list-table").DataTable().ajax.reload(() => {
                // var info = $("#secondary-in-list-table").DataTable().page.info();
                // var totalEntries = info.recordsDisplay;
                // $('#total-secondary-in b').text(totalEntries);
                // $('#total-secondary-in-1').val(totalEntries);

                updateSecondaryInListTotal();
            });
        }

        function submitSecondaryIn() {
            document.getElementById("loading").classList.remove("d-none");

            $.ajax({
                type: "post",
                url: "{{ route('in-submit-secondary-in') }}",
                data: {
                    selectedSecondary: $("#selectedSecondary").val(),
                    sewingLine : $("#sewingLine").val(),
                    worksheet : $("#worksheet").val(),
                    style : $("#style").val(),
                    color : $("#color").val(),
                    size : $("#size").val(),
                    qty : $("#secondaryInQty").val(),
                },
                dataType: "json",
                success: function (response) {
                    document.getElementById("loading").classList.add("d-none");

                    if (response) {
                        // Success
                        if (response.success > 0) {
                            showNotification("success", response.success+" output berhasil disimpan.");
                        }

                        // fail
                        if (response.fail > 0) {
                            showNotification("error", response.fail+" output gagal disimpan/tidak ditemukan.");
                        }

                        // exist
                        if (response.exist > 0) {
                            showNotification("warning", response.exist+" output sudah ada .");
                        }

                        reloadSecondaryInListTable();

                        updateSewingQty();
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    console.error(jqXHR);
                }
            });
        }

        // init scan
        Livewire.on('qrInputFocus', async (mode) => {
            console.log(mode);

            if (mode == "in") {
                reloadSecondaryInListTable();
            }
        });

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
                    render: (data, type, row, meta) => {
                        return data ? data.replace("_", " ").toUpperCase() : '-';
                    }
                },
                {
                    targets: [10],
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
