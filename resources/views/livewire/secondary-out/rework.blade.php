<div wire:init="loadReworkPage">
    <div class="loading-container-fullscreen" wire:loading wire:target='setAndSubmitInput, submitInput, submitMassRework, submitAllRework'>
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
                    <button class="btn btn-dark" wire:click="$emit('showModal', 'rapidRework')"><i class="fa-solid fa-layer-group"></i></button>
                </div>
                <div class="card-body" wire:ignore.self>
                    @error('numberingInput')
                        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                            <strong>Error</strong> {{$message}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    {{-- <div id="rework-reader" width="600px"></div> --}}
                    <input type="text" class="qty-input" id="scannedReworkItem" name="scannedReworkItem">
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-rework text-light">
                    <p class="mb-1 fs-5">Size</p>
                    <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-1">
                        <div class="d-flex align-items-center gap-3 me-3">
                            <p class="mb-1 fs-5">REWORK</p>
                            <p class="mb-1 fs-5">:</p>
                            <p id="rework-qty" class="mb-1 fs-5">{{ $rework->sum('output') }}</p>
                        </div>
                        <button class="btn btn-dark" wire:click="$emit('preSubmitUndo', 'rework')">
                            <i class="fa-regular fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
                @error('sizeInput')
                    <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                        <strong>Error</strong> {{$message}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror
                <div class="card-body">
                    <div class="loading-container" wire:loading wire:target='setSizeInput'>
                        <div class="loading mx-auto"></div>
                    </div>
                    <div class="loading-container hidden" id="loading-rework">
                        <div class="loading mx-auto"></div>
                    </div>
                    <div class="row h-100 row-gap-3" id="content-rework">
                        <div class="col-md-6">
                            <label class="form-label">Worksheet</label>
                            <input type="text" class="form-control" id="worksheet-rework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Style</label>
                            <input type="text" class="form-control" id="style-rework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" id="color-rework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Size</label>
                            <input type="text" class="form-control" id="size-rework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode QR</label>
                            <input type="text" class="form-control" id="kode-rework" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Line</label>
                            <input type="text" class="form-control" id="line-rework" readonly>
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
            }
        });

        Livewire.on('toInputPanel', async (type) => {
            if (type == 'rework') {
                @this.updateOutput();
                scannedReworkItemInput.focus();
            }
        });

        // Livewire.on('fromInputPanel', () => {
        //     clearReworkScan();
        // });
    </script>
@endpush
