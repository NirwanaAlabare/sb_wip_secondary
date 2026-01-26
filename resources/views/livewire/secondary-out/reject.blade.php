<div>
    <div class="loading-container-fullscreen" wire:loading wire:target="selectRejectAreaPosition">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    {{-- Production Input --}}
    <div class="production-input row row-gap-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-reject text-light">
                    <p class="mb-0 fs-5">Qty</p>
                </div>
                <div class="card-body" wire:ignore.self>
                    <input type="number" class="qty-input" id="reject-input" value="{{ $outputInput }}" wire:model.defer='outputInput'>
                    <div class="d-flex justify-content-between gap-1 mt-3">
                        <button class="btn btn-danger w-50 fs-3" id="decrement" wire:click="outputDecrement">-1</button>
                        <button class="btn btn-success w-50 fs-3" id="increment" wire:click="outputIncrement">+1</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-reject text-light">
                    <div class="d-flex justify-content-end align-items-center gap-1">
                        <div class="d-flex align-items-center gap-3 me-3">
                            <p class="mb-1 fs-5">REJECT</p>
                        </div>
                        <button class="btn btn-dark" wire:click="$emit('preSubmitUndo', 'reject')" disabled>
                            <i class="fa-regular fa-rotate-left"></i>
                        </button>
                        {{-- <button class="btn btn-dark">
                            <i class="fa-regular fa-gear"></i>
                        </button> --}}
                    </div>
                </div>
                @error('sizeInput')
                    <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                        <strong>Error</strong> {{$message}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror
                <div class="card-body">
                    <div class="loading-container hidden" id="loading-reject">
                        <div class="loading mx-auto"></div>
                    </div>
                    <div class="row h-100 row-gap-3" id="content-reject">
                        <div class="col-md-12">
                            <div class="row g-3" wire:ignore>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Worksheet</label>
                                    <select class="form-control select2" id="worksheetReject" name="worksheet">
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
                                    <input type="text" class="form-control" id="styleReject" name="style" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Color</label>
                                    <select class="form-control select2" id="colorReject" name="color">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Size</label>
                                    <select class="form-control select2" id="sizeReject" name="size">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Line</label>
                                    <select class="form-control select2" id="sewingLineReject" name="sewingLine">
                                        <option value="">Pilih Line</option>
                                        @if ($lines)
                                            @foreach ($lines as $line)
                                                <option value="{{ $line->username }}">{{ $line->username }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">WIP Qty</label>
                                    <input type="number" class="form-control" id="secondaryInQtyReject" name="secondaryInQty" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Defect Qty</label>
                                    <input type="number" class="form-control" id="secondaryInDefectQtyReject" name="secondaryInDefectQty" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Reject Qty</label>
                                    <input type="number" class="form-control" id="secondaryInRejectQtyReject" name="secondaryInRejectQty" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" wire:ignore>
                            @error('rejectType')
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> {{$message}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @enderror
                            <div class="d-flex align-items-center mb-1">
                                <button type="button" class="btn btn-sm btn-light rounded-0 me-1" wire:click="$emit('showModal', 'addDefectType')">
                                    <i class="fa-regular fa-plus fa-xs"></i>
                                </button>
                                <label class="form-label me-1 mb-0 fw-bold">Defect Type</label>
                            </div>
                            <div wire:ignore id="select-reject-type-container">
                                <select class="form-select @error('rejectType') is-invalid @enderror" id="reject-type-select2" wire:model='rejectType'>
                                    <option value="" selected>Select defect type</option>
                                    @foreach ($defectTypes as $defectType)
                                        <option value="{{ $defectType->id }}">
                                            {{ $defectType->defect_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" wire:ignore>
                            @error('defectArea')
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> {{$message}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @enderror
                            <div class="d-flex align-items-center mb-1">
                                <button type="button" class="btn btn-sm btn-light rounded-0 me-1" wire:click="$emit('showModal', 'addDefectArea')">
                                    <i class="fa-regular fa-plus fa-xs"></i>
                                </button>
                                <label class="form-label me-1 mb-0 fw-bold">Defect Area</label>
                            </div>
                            <div class="d-flex gap-1">
                                <div class="w-75" wire:ignore id="select-reject-area-container">
                                    <select class="form-select @error('rejectArea') is-invalid @enderror" id="reject-area-select2" wire:model='rejectArea'>
                                        <option value="" selected>Select defect area</option>
                                        @foreach ($defectAreas as $defect)
                                            <option value="{{ $defect->id }}">
                                                {{ $defect->defect_area }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-25">
                                    <button type="button" wire:click="selectRejectAreaPosition" class="btn btn-dark w-100">
                                        <i class="fa-regular fa-image"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @if ($errors->has('rejectAreaPositionX') || $errors->has('rejectAreaPositionY'))
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> Harap tentukan posisi defect area dengan mengklik tombol <button type="button"class="btn btn-dark btn-sm"><i class="fa-regular fa-image fa-2xs"></i></button> di samping 'select defect area'.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @endif
                            <div class="d-none">
                                <label class="form-label me-1 mb-2">Defect Area Position</label>
                                <div class="row">
                                    <div class="col d-flex justify-content-center align-items-center">
                                        <label class="form-label me-1 mb-0">X </label>
                                        <div class="d-flex">
                                            <input class="form-control @error('rejectAreaPositionX') is-invalid @enderror" id="reject-area-position-x-livewire" wire:model='rejectAreaPositionX' readonly>
                                        </div>
                                    </div>
                                    <div class="col d-flex justify-content-center align-items-center">
                                        <label class="form-label me-1 mb-1">Y </label>
                                        <div class="d-flex">
                                            <input class="form-control @error('rejectAreaPositionY') is-invalid @enderror" id="reject-area-position-y-livewire" wire:model='rejectAreaPositionY' readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-12">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-danger" wire:click='clearForm'>Batal</button>
                                <div id="regular-submit" wire:ignore.self>
                                    <button type="button" class="btn btn-success" wire:click='submitInput'>SIMPAN</button>
                                </div>
                            </div>
                        </div> --}}
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
                        <input type="date" class="form-control" id="defect-reject-log-date" name="defect-reject-log-date">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" id="defect-reject-secondary-out-list-table">
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
                                    <th>Reject</th>
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
                <div class="card-header bg-reject text-light">
                    <h5 class="card-title">Reject</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="date" class="form-control" id="reject-log-date" name="reject-log-date">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" id="reject-secondary-out-list-table">
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

    {{-- Footer --}}
    <footer class="footer fixed-bottom py-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-end">
                <button class="btn btn-dark btn-lg ms-auto fs-3" onclick="submitSecondaryReject()">SIMPAN</button>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            updateSecondaryOutQty("Reject", "defect", "secondaryInDefectQtyReject");
            updateSecondaryOutQty("Reject", "reject", "secondaryInRejectQtyReject");

            Livewire.on('clearSelectRejectAreaPoint', () => {
                $('#reject-type-select2').val("").trigger('change');
                $('#reject-area-select2').val("").trigger('change');
            });
        });

        // On Livewire Render
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', (message, component) => {
                // Reject Type
                $('#reject-type-select2').select2({
                    theme: "bootstrap-5",
                    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                    placeholder: $( this ).data( 'placeholder' ),
                });

                $('#reject-type-select2').on('change', function (e) {
                    var rejectType = $('#reject-type-select2').select2("val");
                    @this.set('rejectType', rejectType);
                });

                // Reject Area
                $('#reject-area-select2').select2({
                    theme: "bootstrap-5",
                    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                    placeholder: $( this ).data( 'placeholder' ),
                });

                $('#reject-area-select2').on('change', function (e) {
                    var rejectArea = $('#reject-area-select2').select2("val");
                    @this.set('rejectArea', rejectArea);
                });
            });
        });

        // Event
        $("#worksheetReject").on("change", function (event) {
            @this.worksheetReject = this.value;

            let selectedOption = this.options[this.selectedIndex];

            $("#styleReject").val(selectedOption.getAttribute("data-style"));

            updateColorList("Reject");
        });

        $("#colorReject").on("change", function (event) {
            updateSizeList("Reject");
        });

        $("#sizeReject").on("change", function (event) {
            updateSecondaryInQty("Reject");
        });

        $("#sewingLineReject").on("change", function (event) {
            updateSecondaryInQty("Reject");
        });

        // DEFECT Secondary Out List
        let defectRejectSecondaryOutListTable = $("#defect-reject-secondary-out-list-table").DataTable({
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
                    d.date = $("#defect-reject-log-date").val();
                    d.status = "defect";
                    d.selectedSecondary = $("#selectedSecondary").val();
                    d.worksheet = $("#worksheetReject").val();
                    d.style = $("#styleReject").val();
                    d.color = $("#colorReject").val();
                    d.size = $("#sizeReject").val();
                    d.sewingLine = $("#sewingLineReject").val();
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
                            <button class="btn btn-sm btn-reject fw-bold w-100" onclick="preSubmitRejectDefect(`+row.id+`, '`+row.size+`', '`+row.defect_type+`', '`+row.defect_area+`', '`+row.gambar+`', '`+row.defect_area_x+`', '`+row.defect_area_y+`')">
                                REJECT
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

        function defectRejectSecondaryOutListReload() {
            $("#defect-reject-secondary-out-list-table").DataTable().ajax.reload();
        }

        $("#defect-reject-secondary-out-list-table").DataTable().on('draw.dt', function (e, settings, json, xhr) {
            var info = $("#defect-reject-secondary-out-list-table").DataTable().page.info();
            var totalEntries = info.recordsDisplay;
            $('#secondaryInDefectQtyReject').val(totalEntries);
        });

        // REJECT Secondary Out List
        let rejectSecondaryOutListTable = $("#reject-secondary-out-list-table").DataTable({
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
                    d.date = $("#reject-log-date").val();
                    d.status = "reject";
                    d.selectedSecondary = $("#selectedSecondary").val();
                    d.worksheet = $("#worksheetReject").val();
                    d.style = $("#styleReject").val();
                    d.color = $("#colorReject").val();
                    d.size = $("#sizeReject").val();
                    d.sewingLine = $("#sewingLineReject").val();
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
                    data: 'reject_type',
                },
                {
                    data: 'reject_area',
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
                        return `<button class="btn btn-dark" onclick="onShowDefectAreaImage('` + row.gambar + `', ` + row.reject_area_x + `, ` + row.reject_area_y + `)"><i class="fa fa-image"></i></button>`
                    }
                },
                {
                    targets: [12],
                    render: (data, type, row, meta) => {
                        let button = '';
                        if (row.reject_status == 'defect') {
                            button = `
                                <button class="btn btn-sm btn-defect fw-bold w-100" onclick="preSubmitCancelReject(`+row.id+`, '`+row.size+`', '`+row.reject_type+`', '`+row.reject_area+`', '`+row.gambar+`', '`+row.reject_area_x+`', '`+row.reject_area_y+`')">
                                    CANCEL
                                </button>
                            `;
                        } else {
                            button = `
                                <button class="btn btn-sm btn-defect fw-bold w-100" disabled>
                                    MATI
                                </button>
                            `;
                        }

                        return button;
                    }
                }
            ]
        });

        function rejectSecondaryOutListReload() {
            $("#reject-secondary-out-list-table").DataTable().ajax.reload();
        }

        $("#reject-secondary-out-list-table").DataTable().on('draw.dt', function (e, settings, json, xhr) {
            var info = $("#reject-secondary-out-list-table").DataTable().page.info();
            var totalEntries = info.recordsDisplay;
            $('#secondaryInRejectQtyReject').val(totalEntries);
        });

        // Submit Secondary Reject
        function submitSecondaryReject() {
            document.getElementById("loading").classList.remove("d-none");

            $.ajax({
                type: "post",
                url: "{{ route('out-submit-secondary-out-reject') }}",
                data: {
                    selectedSecondary: $("#selectedSecondary").val(),
                    sewingLine : $("#sewingLineReject").val(),
                    worksheet : $("#worksheetReject").val(),
                    style : $("#styleReject").val(),
                    color : $("#colorReject").val(),
                    size : $("#sizeReject").val(),
                    qty : $("#reject-input").val(),
                    defectType: $("#reject-type-select2").val(),
                    defectArea: $("#reject-area-select2").val(),
                    defectAreaPositionX: $("#reject-area-position-x-livewire").val(),
                    defectAreaPositionY: $("#reject-area-position-y-livewire").val(),
                },
                dataType: "json",
                success: function (response) {
                    document.getElementById("loading").classList.add("d-none");

                    if (response) {
                        // Success
                        if (response.success > 0) {
                            showNotification("success", response.success+" reject berhasil disimpan.");
                        }

                        // fail
                        if (response.fail > 0) {
                            showNotification("error", response.fail+" reject gagal disimpan/tidak ditemukan.");
                        }

                        // exist
                        if (response.exist > 0) {
                            showNotification("warning", response.exist+" output sudah ada .");
                        }

                        rejectSecondaryOutListReload();

                        defectRejectSecondaryOutListReload();

                        updateSecondaryInQty("Reject");

                        clearForm("Reject");
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    let res = jqXHR.responseJSON;

                    showValidationError(res, 'Reject')
                }
            });
        }

        // Submit Secondary Defect Reject
        function preSubmitRejectDefect(defectId, defectSize, defectType, defectArea, defectImage, defectX, defectY) {
            Swal.fire({
                icon: 'warning',
                title: 'REJECT defect ini?',
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
                confirmButtonText: 'Reject',
                confirmButtonColor: '#fa4456',
                denyButtonText: 'Batal',
                denyButtonColor: '#212529',
            }).then((result) => {
                if (result.isConfirmed) {
                    submitSecondaryRejectDefect(defectId);
                } else if (result.isDenied) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Submit REJECT dibatalkan',
                        confirmButtonText: 'Ok',
                        confirmButtonColor: '#fa4456',
                    });
                }
            });
        }

        function submitSecondaryRejectDefect(id) {
            document.getElementById("loading").classList.remove("d-none");

            $.ajax({
                type: "post",
                url: "{{ route('out-submit-secondary-out-reject-defect') }}",
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

                        defectRejectSecondaryOutListReload();
                        rejectSecondaryOutListReload();

                        updateSecondaryInQty("Reject");

                        updateSecondaryOutQty("Reject", "defect", "secondaryInDefectQtyReject");
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    let res = jqXHR.responseJSON;
                }
            });
        }

        // Cancel Reject
        function preSubmitCancelReject(defectId, defectSize, defectType, defectArea, defectImage, defectX, defectY) {
            Swal.fire({
                icon: 'info',
                title: 'CANCEL REJECT ini?',
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
                    cancelSecondaryReject(defectId);
                } else if (result.isDenied) {
                    Swal.fire({
                        icon: 'info',
                        title: 'CANCEL REJECT dibatalkan',
                        confirmButtonText: 'Ok',
                        confirmButtonColor: '#d88202',
                    });
                }
            });
        }

        function cancelSecondaryReject(id) {
            document.getElementById("loading").classList.remove("d-none");

            $.ajax({
                type: "post",
                url: "{{ route('out-camcel-secondary-out-reject-defect') }}",
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

                        defectRejectSecondaryOutListReload();
                        rejectSecondaryOutListReload();

                        updateSecondaryInQty("Reject");

                        updateSecondaryOutQty("Reject", "defect", "secondaryInDefectQtyReject");
                        updateSecondaryOutQty("Reject", "status", "secondaryInRejectQtyReject");
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    let res = jqXHR.responseJSON;
                }
            });
        }

        Livewire.on("toInputPanel", function () {
            defectRejectSecondaryOutListReload();
            rejectSecondaryOutListReload();
        })
    </script>
@endpush
