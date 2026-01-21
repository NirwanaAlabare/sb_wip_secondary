<div wire:init="loadReworkPage">
    <div class="loading-container-fullscreen" wire:loading wire:target='setAndSubmitInput, submitInput'>
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    {{-- Production Input --}}
    {{-- <div class="loading-container hidden" id="loading-rework">
        <div class="loading mx-auto"></div>
    </div> --}}
    <div class="row row-gap-3 mb-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-rework text-light">
                    <p class="mb-0 fs-5">Scan QR</p>
                </div>
                <div class="card-body" wire:ignore.self>
                    @error('numberingInput')
                        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                            <strong>Error</strong> {{$message}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    {{-- <div id="rework-reader" width="600px"></div> --}}
                    <input type="text" class="qty-input h-75" id="scannedReworkItem" name="scannedReworkItem">
                </div>
            </div>
        </div>
        <div class="col-md-8">
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
                    <div class="row h-100 row-gap-3" id="content-rework">
                        <div class="col-md-6">
                            <label class="form-label">Worksheet</label>
                            <input type="text" class="form-control" id="worksheet-rework" wire:model="worksheetRework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Style</label>
                            <input type="text" class="form-control" id="style-rework" wire:model="styleRework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" id="color-rework" wire:model="colorRework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Size</label>
                            <input type="text" class="form-control" id="size-rework" wire:model="sizeRework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode QR</label>
                            <input type="text" class="form-control" id="kode-rework" wire:model="kodeRework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Line</label>
                            <input type="text" class="form-control" id="line-rework" wire:model="lineRework" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rapid Rework --}}
    <div class="modal" tabindex="-1" id="rapid-rework-modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-rework text-light">
                    <h5 class="modal-title"><i class="fa-solid fa-clone"></i> Rework Rapid Scan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-center">Scanned Item : <b>{{ $rapidReworkCount }}</b></p>
                        <input type="text" class="qty-input" id="rapid-rework-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sb-secondary" data-bs-dismiss="modal" wire:click='submitRapidInput'>Selesai</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Log --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card mt-3" wire:ignore>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="date" class="form-control" id="defect-rework-log-date" name="defect-rework-log-date">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" id="defect-rework-secondary-out-list-table">
                            <thead>
                                <tr>
                                    <th>Kode Numbering</th>
                                    <th>No. WS</th>
                                    <th>Style</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Line</th>
                                    <th>Secondary</th>
                                    <th>Total</th>
                                    <th>Defect Type</th>
                                    <th>Defect Area</th>
                                    <th>Status</th>
                                    <th>Image</th>
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
        </div>
        <div class="col-md-6">
            <div class="card mt-3" wire:ignore>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="date" class="form-control" id="rework-log-date" name="rework-log-date">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" id="rework-secondary-out-list-table">
                            <thead>
                                <tr>
                                    <th>Kode Numbering</th>
                                    <th>No. WS</th>
                                    <th>Style</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Line</th>
                                    <th>Secondary</th>
                                    <th>Total</th>
                                    <th>Defect Type</th>
                                    <th>Defect Area</th>
                                    <th>Status</th>
                                    <th>Image</th>
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
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Scan QR
        // if (document.getElementById("rework-reader")) {
        //     function onScanSuccess(decodedText, decodedResult) {
        //         // handle the scanned code as you like, for example:
        //         console.log(`Code matched = ${decodedText}`, decodedResult);

        //         // break decoded text
        //         let breakDecodedText = decodedText.split('-');

        //         console.log(breakDecodedText);

        //         // set kode_numbering
        //         @this.numberingInput = breakDecodedText[3];

        //         // set so_det_id
        //         @this.sizeInput = breakDecodedText[4];

        //         // set size
        //         @this.sizeInputText = breakDecodedText[5];

        //         // submit
        //         @this.submitInput();

        //         clearReworkScan();
        //     }

        //     Livewire.on('renderQrScanner', async (type) => {
        //         if (type == 'rework') {
        //             document.getElementById('back-button').disabled = true;
        //             await refreshReworkScan(onScanSuccess);
        //             document.getElementById('back-button').disabled = false;
        //         }
        //     });

        //     Livewire.on('toInputPanel', async (type) => {
        //         if (type == 'rework') {
        //             document.getElementById('back-button').disabled = true;
        //             await @this.updateOutput();
        //             await initReworkScan(onScanSuccess);
        //             document.getElementById('back-button').disabled = false;
        //         }
        //     });

        //     Livewire.on('fromInputPanel', () => {
        //         clearReworkScan();
        //     });
        // }

        var scannedReworkItemInput = document.getElementById("scannedReworkItem");

        scannedReworkItemInput.addEventListener("change", async function () {
            const value = this.value;

            this.setAttribute("disabled", true);

            // submit
            await @this.submitInput(value);

            this.removeAttribute("disabled");
            this.value = '';
        });

        var scannedRapidReworkInput = document.getElementById("rapid-rework-input");

        scannedRapidReworkInput.addEventListener("change", function () {
            @this.pushRapidRework(this.value, null, null);

            this.value = '';
        });

        Livewire.on('qrInputFocus', async (type) => {
            if (type == 'rework') {
                scannedReworkItemInput.focus();

                defectReworkSecondaryOutListReload();
                reworkSecondaryOutListReload();
            }
        });

        Livewire.on('toInputPanel', async (type) => {
            if (type == 'rework') {
                scannedReworkItemInput.focus();
            }
        });

        // Livewire.on('fromInputPanel', () => {
        //     clearReworkScan();
        // });

        // DEFECT Secondary Out List
        let defectReworkSecondaryOutListTable = $("#defect-rework-secondary-out-list-table").DataTable({
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
                    d.date = $("#defect-rework-log-date").val();
                    d.status = "defect";
                    d.selectedSecondary = $("#selectedSecondary").val();
                }
            },
            columns: [
                {
                    data: 'kode_numbering',
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
                    data: 'sewing_line',
                },
                {
                    data: 'secondary',
                },
                {
                    data: 'output',
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

        // REWORK Secondary Out List
        let reworkSecondaryOutListTable = $("#rework-secondary-out-list-table").DataTable({
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
                    d.date = $("#rework-log-date").val();
                    d.status = "rework";
                    d.selectedSecondary = $("#selectedSecondary").val();
                }
            },
            columns: [
                {
                    data: 'kode_numbering',
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
                    data: 'sewing_line',
                },
                {
                    data: 'secondary',
                },
                {
                    data: 'output',
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
                }
            ]
        });

        function reworkSecondaryOutListReload() {
            $("#rework-secondary-out-list-table").DataTable().ajax.reload();
        }
    </script>
@endpush
