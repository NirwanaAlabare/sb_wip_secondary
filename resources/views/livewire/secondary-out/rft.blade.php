<div>
    <div class="loading-container-fullscreen" wire:loading wire:target="submitInput">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    {{-- Production Input --}}
    <div class="production-input row row-gap-3 mb-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-rft text-light">
                    <p class="mb-0 fs-5">Qty</p>
                </div>
                <div class="card-body" wire:ignore.self>
                    @error('outputInput')
                        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                            <strong>Error</strong> {{$message}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    <input type="number" class="qty-input" id="rft-input" value="{{ $outputInput }}" wire:model.defer='outputInput'>
                    <div class="d-flex justify-content-between gap-1 mt-3">
                        <button class="btn btn-danger w-50 fs-3" id="decrement" wire:click="outputDecrement">-1</button>
                        <button class="btn btn-success w-50 fs-3" id="increment" wire:click="outputIncrement">+1</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-rft text-light">
                    <div class="d-flex justify-content-end align-items-center gap-1">
                        <div class="d-flex align-items-center gap-3 me-3">
                            <p class="mb-1 fs-5">RFT</p>
                        </div>
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
                    <div class="loading-container hidden" id="loading-rft">
                        <div class="loading mx-auto"></div>
                    </div>
                    <div class="row h-100 row-gap-3" id="content-rft">
                        <div class="col-md-12">
                            <div class="row g-3" wire:ignore>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Worksheet</label>
                                    <select class="form-control select2" id="worksheetRft" name="worksheet">
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
                                    <input type="text" class="form-control" id="styleRft" name="style" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Color</label>
                                    <select class="form-control select2" id="colorRft" name="color">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Size</label>
                                    <select class="form-control select2" id="sizeRft" name="size">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Line</label>
                                    <select class="form-control select2" id="sewingLineRft" name="sewingLine">
                                        <option value="">Pilih Line</option>
                                        @if ($lines)
                                            @foreach ($lines as $line)
                                                <option value="{{ $line->username }}">{{ $line->username }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">WIP Qty</label>
                                    <input type="number" class="form-control" id="secondaryInQtyRft" name="secondaryInQty" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Log --}}
    <div class="card" wire:ignore>
        <div class="card-body">
            <div class="mb-3">
                <input type="date" class="form-control" id="rft-log-date" name="rft-log-date">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered w-100" id="rft-secondary-out-list-table">
                    <thead>
                        <tr>
                            <th>No. WS</th>
                            <th>Style</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Line</th>
                            <th>Secondary</th>
                            <th>Total</th>
                            <th>Created By</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <p class="text-center opacity-50 my-0"><small><i>{{ date('Y') }} &copy; Nirwana Digital Solution</i></small></p>
    </div>

    {{-- Footer --}}
    <footer class="footer fixed-bottom py-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-end">
                <button class="btn btn-dark btn-lg ms-auto fs-3" onclick="submitSecondaryRft()">SELESAI</button>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
    <script>
        $("#worksheetRft").on("change", function (event) {
            let selectedOption = this.options[this.selectedIndex];

            $("#styleRft").val(selectedOption.getAttribute("data-style"));

            updateColorList("Rft");
        });

        $("#colorRft").on("change", function (event) {
            updateSizeList("Rft");
        });

        $("#sizeRft").on("change", function (event) {
            updateSecondaryInQty("Rft");
        });

        $("#sewingLineRft").on("change", function (event) {
            updateSecondaryInQty("Rft");
        });

        function submitSecondaryRft() {
            document.getElementById("loading").classList.remove("d-none");

            $.ajax({
                type: "post",
                url: "{{ route('out-submit-secondary-out-rft') }}",
                data: {
                    selectedSecondary: $("#selectedSecondary").val(),
                    sewingLine : $("#sewingLineRft").val(),
                    worksheet : $("#worksheetRft").val(),
                    style : $("#styleRft").val(),
                    color : $("#colorRft").val(),
                    size : $("#sizeRft").val(),
                    qty : $("#rft-input").val(),
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

                        rftSecondaryOutListReload();

                        updateSecondaryInQty("Rft");

                        clearForm("Rft");
                    }
                },
                error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");

                    let res = jqXHR.responseJSON;

                    showValidationError(res, 'Rft')
                }
            });
        }

        // RFT Secondary Out List
        let rftSecondaryOutListTable = $("#rft-secondary-out-list-table").DataTable({
            serverSide: true,
            processing: true,
            ordering: false,
            searching: false,
            paging: true,
            lengthChange: false,
            ajax: {
                url: '{{ route('out-get-secondary-out-log') }}',
                dataType: 'json',
                data: function (d) {
                    d.date = $("#rft-log-date").val();
                    d.status = "rft";
                    d.selectedSecondary = $("#selectedSecondary").val();
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
                    data: 'output',
                },
                {
                    data: 'created_by_username',
                },
                {
                    data: 'secondary_out_time',
                },
            ],
            columnDefs: [
                {
                    targets: "_all",
                    className: "text-nowrap text-center align-middle"
                },
            ],
        });

        function rftSecondaryOutListReload() {
            $("#rft-secondary-out-list-table").DataTable().ajax.reload();
        }

        Livewire.on("updateSelectedSecondary", function () {
            rftSecondaryOutListReload();
        })

        Livewire.on("toInputPanel", function () {
            rftSecondaryOutListReload();
        })
    </script>
@endpush
