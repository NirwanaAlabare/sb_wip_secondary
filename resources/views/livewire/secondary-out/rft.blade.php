<div>
    <div class="loading-container-fullscreen" wire:loading wire:target="setAndSubmitInput, submitInput, submitRapidInput">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    {{-- Production Input --}}
    <div class="production-input row row-gap-3 mb-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-rft text-light">
                    <p class="mb-0 fs-5">Scan QR</p>
                    {{-- <button class="btn btn-dark" wire:click="$emit('showModal', 'rapidRft')"><i class="fa-solid fa-layer-group"></i></button> --}}
                </div>
                <div class="card-body" wire:ignore.self>
                    @error('numberingInput')
                        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                            <strong>Error</strong> {{$message}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    {{-- <div id="rft-reader" width="600px"></div> --}}
                    <input type="text" class="qty-input h-100" id="scannedItemRft" name="scannedItemRft">
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
                        <div class="col-md-6">
                            <label class="form-label">Worksheet</label>
                            <input type="text" class="form-control" id="worksheet-rft" wire:model="worksheetRft" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Style</label>
                            <input type="text" class="form-control" id="style-rft" wire:model="styleRft" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" id="color-rft" wire:model="colorRft" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Size</label>
                            <input type="text" class="form-control" id="size-rft" wire:model="sizeRft" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode QR</label>
                            <input type="text" class="form-control" id="kode-rft" wire:model="kodeRft" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Line</label>
                            <input type="text" class="form-control" id="line-rft" wire:model="lineRft" readonly>
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
                            <th>Kode Numbering</th>
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

    {{-- Rapid RFT --}}
    <div class="modal" tabindex="-1" id="rapid-rft-modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-rft text-light">
                    <h5 class="modal-title"><i class="fa-solid fa-clone"></i> RFT Rapid Scan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-center">Scanned Item : <b>{{ $rapidRftCount }}</b></p>
                        <input type="text" class="qty-input" id="rapid-rft-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" wire:click='submitRapidInput'>Selesai</button>
                </div>
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
                <button class="btn btn-dark btn-lg ms-auto fs-3" onclick="triggerSubmit()" {{ $submitting ? 'disabled' : ''}}>SELESAI</button>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
    <script>
        // Scan QR
            // if (document.getElementById("rft-reader")) {
            //     function onScanSuccess(decodedText, decodedResult) {
            //         // handle the scanned code as you like, for example:

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

            //         clearRftScan();
            //     }

            //     Livewire.on('renderQrScanner', async (type) => {
            //         if (type == 'rft') {
            //             document.getElementById('back-button').disabled = true;
            //             await refreshRftScan(onScanSuccess);
            //             document.getElementById('back-button').disabled = false;
            //         }
            //     });

            //     Livewire.on('toInputPanel', async (type) => {
            //         if (type == 'rft') {
            //             document.getElementById('back-button').disabled = true;
            //             await @this.updateOutput();
            //             await initRftScan(onScanSuccess);
            //             document.getElementById('back-button').disabled = false;
            //         }
            //     });

            //     Livewire.on('fromInputPanel', () => {
            //         clearRftScan();
            //     });
            // }

        var scannedItemRftInput = document.getElementById("scannedItemRft");

        // scannedItemRftInput.addEventListener("change", async function () {
        //     @this.numberingInput = this.value;

        //     this.setAttribute("disabled", true);

        //     // submit
        //     await @this.submitInput();

        //     this.removeAttribute("disabled");
        //     this.value = '';
        // });
        scannedItemRftInput.addEventListener("change", async function () {
            const value = this.value;

            // this.setAttribute("disabled", true);

            // submit
            await @this.submitInput(value);

            // this.removeAttribute("disabled");
            this.value = '';
        });

        var scannedRapidRftInput = document.getElementById("rapid-rft-input");

        scannedRapidRftInput.addEventListener("change", function () {
            @this.pushRapidRft(this.value, null, null);

            this.value = '';
        });

        Livewire.on('qrInputFocus', async (type) => {
            if (type == 'rft') {
                scannedItemRftInput.focus();
            }
        });

        Livewire.on('toInputPanel', async (type) => {
            if (type == 'rft') {
                scannedItemRftInput.focus();
            }
        });

        // Livewire.on('fromInputPanel', () => {
        //     clearRftScan();
        // });

        function triggerSubmit() {
            if ($("#scannedItemRft").val()) {
                $("#scannedItemRft").trigger("change");
            }

            $("#scannedItemRft").focus();
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
            ],
            rowCallback: function (row, data, iDisplayIndex) {
                var info = this.api().page.info();
                var page = info.page;
                var length = info.length;
                var index = (page * length + (iDisplayIndex + 1));
                $('td:eq(0)', row).html(index); // Assuming the first column is for the index
            }
        });
    </script>
@endpush
