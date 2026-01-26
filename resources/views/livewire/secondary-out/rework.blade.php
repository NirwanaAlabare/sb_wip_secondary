<div wire:init="loadReworkPage">
    <div class="loading-container-fullscreen" wire:loading>
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    {{-- Production Input --}}
    {{-- <div class="loading-container hidden" id="loading-rework">
        <div class="loading mx-auto"></div>
    </div> --}}
    <div class="row row-gap-3 mb-3">

        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-rework text-light">
                    <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-1">
                        <div class="d-flex align-items-center gap-3 me-3">
                            <p class="mb-1 fs-5">REWORK</p>
                        </div>
                    </div>
                </div>
                @error('sizeInput')
                    <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                        <strong>Error</strong> {{$message}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror
                <div class="card-body">
                    <div class="loading-container hidden" id="loading-rework">
                        <div class="loading mx-auto"></div>
                    </div>
                    <div class="row h-100 justify-content-center row-gap-3" id="content-rework">
                        <div class="col-md-12">
                            <div class="row g-3" wire:ignore>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Worksheet</label>
                                    <select class="form-control select2" id="worksheetRework" name="worksheet">
                                        <option value="">Pilih Worksheet</option>
                                        @if ($orders)
                                            @foreach ($orders as $order)
                                                <option value="{{ $order->id_ws }}" data-style="{{ $order->style }}">{{ $order->no_ws }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Style</label>
                                    <input type="text" class="form-control" id="styleRework" name="style" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Color</label>
                                    <select class="form-control select2" id="colorRework" name="color">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Size</label>
                                    <select class="form-control select2" id="sizeRework" name="size">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Line</label>
                                    <select class="form-control select2" id="sewingLineRework" name="sewingLine">
                                        <option value="">Pilih Line</option>
                                        @if ($lines)
                                            @foreach ($lines as $line)
                                                <option value="{{ $line->username }}">{{ $line->username }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Defect Qty</label>
                                    <input type="number" class="form-control" id="secondaryInDefectQtyRework" name="secondaryInDefectQty" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Rework Qty</label>
                                    <input type="number" class="form-control" id="secondaryInReworkQtyRework" name="secondaryInReworkQty" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Log --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card mt-3" wire:ignore>
                <div class="card-header bg-defect text-light">
                    <h5 class="card-title">Defect</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="date" class="form-control" id="defect-rework-log-date" name="defect-rework-log-date">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" id="defect-rework-secondary-out-list-table">
                            <thead>
                                <tr>
                                    <th>No. WS</th>
                                    <th>Style</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Line</th>
                                    <th>Secondary</th>
                                    <th>Defect Type</th>
                                    <th>Defect Area</th>
                                    <th>Status</th>
                                    <th>Image</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Rework</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mt-3" wire:ignore>
                <div class="card-header bg-rework text-light">
                    <h5 class="card-title">Rework</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="date" class="form-control" id="rework-log-date" name="rework-log-date">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" id="rework-secondary-out-list-table">
                            <thead>
                                <tr>
                                    <th>No. WS</th>
                                    <th>Style</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Line</th>
                                    <th>Secondary</th>
                                    <th>Defect Type</th>
                                    <th>Defect Area</th>
                                    <th>Status</th>
                                    <th>Image</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Defect</th>
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

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            updateSecondaryOutQty("Rework", "defect", "secondaryInDefectQtyRework");
            updateSecondaryOutQty("Rework", "rework", "secondaryInReworkQtyRework");
        });

        // DEFECT Secondary Out List
        let defectReworkSecondaryOutListTable = $("#defect-rework-secondary-out-list-table").DataTable({
            serverSide: true,
            processing: true,
            ordering: false,
            searching: false,
            paging: true,
            lengthChange: false,
            ajax: {
                url: '{{ route('out-get-secondary-out-log-single') }}',
                dataType: 'json',
                data: function (d) {
                    d.date = $("#defect-rework-log-date").val();
                    d.status = "defect";
                    d.selectedSecondary = $("#selectedSecondary").val();
                    d.worksheet = $("#worksheetRework").val();
                    d.style = $("#styleRework").val();
                    d.color = $("#colorRework").val();
                    d.size = $("#sizeRework").val();
                    d.sewingLine = $("#sewingLineRework").val();
                }
            },
            columns: [
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
                    data: 'sewing_line',
                },
                {
                    data: 'secondary',
                },
                {
                    data: 'defect_type',
                },
                {
                    data: 'defect_area',
                },
                {
                    data: 'status',
                },
                {
                    data: 'gambar',
                },
                {
                    data: 'created_by_username',
                },
                {
                    data: 'secondary_out_time',
                },
                {
                    data: 'id',
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
                {
                    targets: [9],
                    render: (data, type, row, meta) => {
                        return `<button class="btn btn-dark" onclick="onShowDefectAreaImage('` + row.gambar + `', ` + row.defect_area_x + `, ` + row.defect_area_y + `)"><i class="fa fa-image"></i></button>`
                    }
                },
                {
                    targets: [12],
                    render: (data, type, row, meta) => {
                        return `
                            <button class="btn btn-sm btn-rework fw-bold w-100" onclick="preSubmitRework(`+row.id+`, '`+row.size+`', '`+row.defect_type+`', '`+row.defect_area+`', '`+row.gambar+`', '`+row.defect_area_x+`', '`+row.defect_area_y+`')">
                                REWORK
                            </button>
                        `
                    }
                }
            ],
            // rowCallback: function (row, data, iDisplayIndex) {
            //     var info = this.api().page.info();
            //     var page = info.page;
            //     var length = info.length;
            //     var index = (page * length + (iDisplayIndex + 1));
            //     $('td:eq(0)', row).html(index); // Assuming the first column is for the index
            // }
        });

        function defectReworkSecondaryOutListReload() {
            $("#defect-rework-secondary-out-list-table").DataTable().ajax.reload();
        }

        $("#defect-rework-secondary-out-list-table").DataTable().on('draw.dt', function (e, settings, json, xhr) {
            var info = $("#defect-rework-secondary-out-list-table").DataTable().page.info();
            var totalEntries = info.recordsDisplay;
            $('#secondaryInDefectQtyRework').val(totalEntries);
        });

        // REWORK Secondary Out List
        let reworkSecondaryOutListTable = $("#rework-secondary-out-list-table").DataTable({
            serverSide: true,
            processing: true,
            ordering: false,
            searching: false,
            paging: true,
            lengthChange: false,
            ajax: {
                url: '{{ route('out-get-secondary-out-log-single') }}',
                dataType: 'json',
                data: function (d) {
                    d.date = $("#rework-log-date").val();
                    d.status = "rework";
                    d.selectedSecondary = $("#selectedSecondary").val();
                    d.worksheet = $("#worksheetRework").val();
                    d.style = $("#styleRework").val();
                    d.color = $("#colorRework").val();
                    d.size = $("#sizeRework").val();
                    d.sewingLine = $("#sewingLineRework").val();
                }
            },
            columns: [
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
                    data: 'sewing_line',
                },
                {
                    data: 'secondary',
                },
                {
                    data: 'defect_type',
                },
                {
                    data: 'defect_area',
                },
                {
                    data: 'status',
                },
                {
                    data: 'gambar',
                },
                {
                    data: 'created_by_username',
                },
                {
                    data: 'secondary_out_time',
                },
                {
                    data: 'id',
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
                {
                    targets: [11],
                    render: (data, type, row, meta) => {
                        return `<button class="btn btn-dark" onclick="onShowDefectAreaImage('` + row.gambar + `', ` + row.defect_area_x + `, ` + row.defect_area_y + `)"><i class="fa fa-image"></i></button>`
                    }
                },
                {
                    targets: [12],
                    render: (data, type, row, meta) => {
                        return `
                            <button class="btn btn-sm btn-defect fw-bold w-100" onclick="preSubmitCancel(`+row.id+`, '`+row.size+`', '`+row.defect_type+`', '`+row.defect_area+`', '`+row.gambar+`', '`+row.defect_area_x+`', '`+row.defect_area_y+`')">
                                CANCEL
                            </button>
                        `
                    }
                }
            ]
        });

        function reworkSecondaryOutListReload() {
            $("#rework-secondary-out-list-table").DataTable().ajax.reload();
        }

        $("#rework-secondary-out-list-table").DataTable().on('draw.dt', function (e, settings, json, xhr) {
            var info = $("#rework-secondary-out-list-table").DataTable().page.info();
            var totalEntries = info.recordsDisplay;
            $('#secondaryInReworkQtyRework').val(totalEntries);
        });

        function preSubmitRework(defectId, defectSize, defectType, defectArea, defectImage, defectX, defectY) {
            Swal.fire({
                icon: 'info',
                title: 'REWORK defect ini?',
                html: `<table class="table text-start w-auto mx-auto">
                            <tr>
                                <td>ID<td>
                                <td>:<td>
                                <td>`+defectId+`<td>
                            <tr>
                            <tr>
                                <td>Size<td>
                                <td>:<td>
                                <td>`+defectSize+`<td>
                            <tr>
                            <tr>
                                <td>Defect Type<td>
                                <td>:<td>
                                <td>`+defectType+`<td>
                            <tr>
                            <tr>
                                <td>Defect Area<td>
                                <td>:<td>
                                <td>`+defectArea+`<td>
                            <tr>
                            <tr>
                                <td>Defect Image<td>
                                <td>:<td>
                                <td>
                                    <button type="button" class="btn btn-dark" onclick="onShowDefectAreaImage('`+defectImage+`', '`+defectX+`', '`+defectY+`')">
                                        <i class="fa-regular fa-image"></i>
                                    </button>
                                <td>
                            <tr>
                        </table>`,
                showConfirmButton: true,
                showDenyButton: true,
                confirmButtonText: 'Rework',
                confirmButtonColor: '#447efa',
                denyButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    submitSecondaryRework(defectId);
                } else if (result.isDenied) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Submit REWORK dibatalkan',
                        confirmButtonText: 'Ok',
                        confirmButtonColor: '#447efa',
                    });
                }
            });
        }

        function submitSecondaryRework(id) {
            document.getElementById("loading").classList.remove("d-none");

            $.ajax({
                type: "post",
                url: "{{ route('out-submit-secondary-out-rework') }}",
                data: {
                    selectedSecondary: $("#selectedSecondary").val(),
                    id : id,
                },
                dataType: "json",
                success: function (response) {
                    document.getElementById("loading").classList.add("d-none");

                    if (response) {
                        let notificationType = response.status == 200 ? "success" : "error";

                        showNotification(notificationType, response.message);

                        defectReworkSecondaryOutListReload();
                        reworkSecondaryOutListReload();

                        updateSecondaryInQty("Rework");

                        updateSecondaryOutQty("Rework", "defect", "secondaryInDefectQtyRework");
                        updateSecondaryOutQty("Rework", "status", "secondaryInReworkQtyRework");
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    let res = jqXHR.responseJSON;
                }
            });
        }

        // CANCEL REWORK
        function preSubmitCancel(defectId, defectSize, defectType, defectArea, defectImage, defectX, defectY) {
            Swal.fire({
                icon: 'info',
                title: 'CANCEL REWORK ini?',
                html: `<table class="table text-start w-auto mx-auto">
                            <tr>
                                <td>ID<td>
                                <td>:<td>
                                <td>`+defectId+`<td>
                            <tr>
                            <tr>
                                <td>Size<td>
                                <td>:<td>
                                <td>`+defectSize+`<td>
                            <tr>
                            <tr>
                                <td>Defect Type<td>
                                <td>:<td>
                                <td>`+defectType+`<td>
                            <tr>
                            <tr>
                                <td>Defect Area<td>
                                <td>:<td>
                                <td>`+defectArea+`<td>
                            <tr>
                            <tr>
                                <td>Defect Image<td>
                                <td>:<td>
                                <td>
                                    <button type="button" class="btn btn-dark" onclick="onShowDefectAreaImage('`+defectImage+`', '`+defectX+`', '`+defectY+`')">
                                        <i class="fa-regular fa-image"></i>
                                    </button>
                                <td>
                            <tr>
                        </table>`,
                showConfirmButton: true,
                showDenyButton: true,
                confirmButtonText: 'CANCEL',
                confirmButtonColor: '#d88202',
                denyButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    cancelSecondaryRework(defectId);
                } else if (result.isDenied) {
                    Swal.fire({
                        icon: 'info',
                        title: 'CANCEL REWORK dibatalkan',
                        confirmButtonText: 'Ok',
                        confirmButtonColor: '#d88202',
                    });
                }
            });
        }

        function cancelSecondaryRework(id) {
            document.getElementById("loading").classList.remove("d-none");

            $.ajax({
                type: "post",
                url: "{{ route('out-cancel-secondary-out-rework') }}",
                data: {
                    selectedSecondary: $("#selectedSecondary").val(),
                    id : id,
                },
                dataType: "json",
                success: function (response) {
                    document.getElementById("loading").classList.add("d-none");

                    if (response) {
                        let notificationType = response.status == 200 ? "success" : "error";

                        showNotification(notificationType, response.message);

                        defectReworkSecondaryOutListReload();
                        reworkSecondaryOutListReload();

                        updateSecondaryInQty("Rework");

                        updateSecondaryOutQty("Rework", "defect", "secondaryInDefectQtyRework");
                        updateSecondaryOutQty("Rework", "status", "secondaryInReworkQtyRework");
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    let res = jqXHR.responseJSON;
                }
            });
        }

        Livewire.on("updateSelectedSecondary", function () {
            defectReworkSecondaryOutListReload();
            reworkSecondaryOutListReload();
        })

        // EVENT
        $("#worksheetRework").on("change", function (event) {
            let selectedOption = this.options[this.selectedIndex];

            $("#styleRework").val(selectedOption.getAttribute("data-style"));

            updateColorList("Rework");
            defectReworkSecondaryOutListReload();
            reworkSecondaryOutListReload();
        });

        $("#colorRework").on("change", function (event) {
            updateSizeList("Rework");
            defectReworkSecondaryOutListReload();
            reworkSecondaryOutListReload();
        });

        $("#sizeRework").on("change", function (event) {
            updateSecondaryInQty("Rework");
            defectReworkSecondaryOutListReload();
            reworkSecondaryOutListReload();
        });

        $("#sewingLineRework").on("change", function (event) {
            updateSecondaryInQty("Rework");
            defectReworkSecondaryOutListReload();
            reworkSecondaryOutListReload();
        });

        Livewire.on("toInputPanel", function () {
            defectReworkSecondaryOutListReload();
            reworkSecondaryOutListReload();
        })
    </script>
@endpush
