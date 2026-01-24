<div>
    <div class="loading-container-fullscreen" wire:loading wire:target="changeMode, refreshComponent, showDefectAreaImage">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    <div class="loading-container-fullscreen hidden" id="loading-sewing-secondary-in-out">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    <div class="row justify-content-center g-3">
        <div class="col-12">
            <div class="d-flex flex-column align-items-center justify-content-center gap-3">
                <div class="d-flex justify-content-center w-50" wire:ignore>
                    <select class="form-select form-select-sm select2 w-auto" name="selectedSecondary" id="selectedSecondary">
                        @foreach ($secondaryMaster as $secondary)
                            <option value="{{ $secondary->id }}">{{ $secondary->secondary }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex justify-content-center gap-1 w-50">
                    <button type="button" class="btn btn-sm btn-sb-secondary-outline {{ $mode == "sum" ? "active" : "" }}" {{ $mode == "sum" ? "disabled" : "" }} id="button-in-out">SUM</button>
                    <button type="button" class="btn btn-sm btn-rework {{ $mode == "out" ? "active" : "" }}" {{ $mode == "out" ? "disabled" : "" }} id="button-out">OUT</button>
                </div>
            </div>
        </div>

        {{-- SECONDARY SEWING OUT --}}
        <div class="col-12 col-md-12 {{ $mode != "out" ? 'd-none' : ''}}" wire:poll.30000ms>
            @livewire('secondary-out.production-panel', ["selectedSecondary" => $selectedSecondary])
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
                                        <th>Total RFT</th>
                                        <th>Total DEFECT</th>
                                        <th>Total REWORK</th>
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
                                    <label class="form-label fw-bold">RFT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailRft" readonly>
                                </div>
                                <div class="w-auto">
                                    <label class="form-label fw-bold">DEFECT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailDefect" readonly>
                                </div>
                                <div class="w-auto">
                                    <label class="form-label fw-bold">REWORK</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailRework" readonly>
                                </div>
                                <div class="w-auto">
                                    <label class="form-label fw-bold">REJECT</label>
                                    <input type="text" class="form-control" id="secondaryInOutDetailReject" readonly>
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

                secondaryInOutDetailReload();
            });

            $('#button-out').on('click', async function (e) {
                @this.changeMode("out")
            })

            $('#button-in-out').on('click', async function (e) {
                @this.changeMode("sum")
            })

            $('#selectedSecondary').trigger('change');
        });

        // init scan
        Livewire.on('qrInputFocus', async (mode) => {
            console.log(mode);

            if (mode == "out") {
                //
            }
        });

        function getMasterPlanData(type) {
            if (type != "out" && type != "out") {
                type = 'out';
            }
            console.log(type, $("#secondary-out-date").val());
            $.ajax({
                url: "{{ route("get-master-plan") }}",
                method: "GET",
                data: {
                    date: $("#secondary-out-date").val(),
                    line: $("#select-secondary-out-line").val(),
                },
                success: function (res) {
                    document.getElementById("select-secondary-out-master-plan").innerHTML = "";

                    let selectElement = document.getElementById("select-secondary-out-master-plan")

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

        function updateColorList(suffix = '') {
            let selectElement = document.getElementById("color"+suffix);

            if (selectElement) {
                selectElement.innerHTML = null;

                $.ajax({
                    type: "get",
                    url: "{{ route('get-color') }}",
                    data: {
                        "worksheet": $("#worksheet"+suffix).val()
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

        function updateSizeList(suffix = '') {
            let selectElement = document.getElementById("size"+suffix);

            if (selectElement) {
                selectElement.innerHTML = null;

                $.ajax({
                    type: "get",
                    url: "{{ route('get-size') }}",
                    data: {
                        "worksheet": $("#worksheet"+suffix).val(),
                        "color": $("#color"+suffix).val(),
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

        function updateSecondaryInQty(suffix = '') {
            $.ajax({
                type: "get",
                url: "{{ route('get-secondary-in-wip-total') }}",
                data: {
                    "selectedSecondary": $("#selectedSecondary").val(),
                    "worksheet": $("#worksheet"+suffix).val(),
                    "color": $("#color"+suffix).val(),
                    "size": $("#size"+suffix).val(),
                    "sewingLine" : $("#sewingLine"+suffix).val(),
                },
                success: function (response) {
                    console.log(response);
                    $("#secondaryInQty"+suffix).val(response);
                },
                error: function (jqXHR) {
                    console.error(jqXHR);
                }
            });
        }

        function updateSecondaryOutQty(suffix = '', status = '', elementId  = '') {
            $.ajax({
                type: "get",
                url: "{{ route('out-get-secondary-out-log-total') }}",
                data: {
                    "selectedSecondary": $("#selectedSecondary").val(),
                    "worksheet": $("#worksheet"+suffix).val(),
                    "color": $("#color"+suffix).val(),
                    "size": $("#size"+suffix).val(),
                    "sewingLine" : $("#sewingLine"+suffix).val(),
                    "status": status
                },
                success: function (response) {
                    console.log(response);

                    if (elementId) {
                        $('#'+elementId).val(response);
                    }
                },
                error: function (jqXHR) {
                    console.error(jqXHR);
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

            console.log(x && y, x, y);

            if (x && y) {
                let rect = await defectAreaImageElement.getBoundingClientRect();

                let pointWidth = null;
                if (rect.width == 0) {
                    pointWidth = 35;
                } else {
                    pointWidth = 0.03 * rect.width;
                }

                defectAreaImagePointElement.style.visibility = 'visible';
                defectAreaImagePointElement.style.width = pointWidth + 'px';
                defectAreaImagePointElement.style.height = defectAreaImagePointElement.style.width;
                defectAreaImagePointElement.style.left = 'calc(' + x + '% - ' + 0.5 * pointWidth + 'px)';
                defectAreaImagePointElement.style.top = 'calc(' + y + '% - ' + 0.5 * pointWidth + 'px)';
                defectAreaImagePointElement.style.display = 'block';
            } else {
                Object.assign(defectAreaImagePointElement.style, {
                    width: '0px',
                    height: '0px',
                    display: 'none',
                    visibility: 'hidden',
                });
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
                url: '{{ route('out-get-secondary-in-out-daily') }}',
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
                    data: 'total_rft',
                },
                {
                    data: 'total_defect',
                },
                {
                    data: 'total_rework',
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
                url: '{{ route('out-get-secondary-in-out-detail') }}',
                data: function (d) {
                    d.tanggal = $("#secondaryInOutDetailDate").val();
                    d.secondary = $("#selectedSecondaryModal").val();
                },
                dataType: 'json',
            },
            columns: [
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
                    data: 'user_out',
                },
            ],
            columnDefs: [
                {
                    targets: [1],
                    className: "disabled"
                },
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
                $("#secondaryInOutDetailRework").val(0);
                $("#secondaryInOutDetailReject").val(0);

                $.ajax({
                    url: "{{ route("out-get-secondary-in-out-detail-total") }}",
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
                            $("#secondaryInOutDetailRework").val(response.secondaryRework);
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
                url: "{{ route("out-export-secondary-in-out") }}",
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
                    link.download = "Secondary Out " + $("#selectedSecondary").val() + " " + $("#dateFrom").val() + " - " + $("#dateTo").val() + ".xlsx";
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
