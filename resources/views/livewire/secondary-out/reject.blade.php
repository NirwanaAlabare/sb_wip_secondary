<div>
    <div class="loading-container-fullscreen" wire:loading wire:target="selectDefectAreaPosition, setAndSubmitInput, preSubmitInput, submitInput, updateOrder, submitRapidInput">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    {{-- Production Input --}}
    <div class="production-input row row-gap-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-reject text-light">
                    <p class="mb-0 fs-5">Scan QR</p>
                    {{-- <button class="btn btn-dark" wire:click="$emit('showModal', 'rapidReject')"><i class="fa-solid fa-layer-group"></i></button> --}}
                </div>
                <div class="card-body" wire:ignore.self>
                    @error('numberingInput')
                        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                            <strong>Error</strong> {{$message}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    {{-- <div id="reject-reader" width="600px"></div> --}}
                    <input type="text" class="qty-input" id="scannedRejectItem" name="scannedRejectItem">
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-reject text-light">
                    <p class="mb-0 fs-5">Size</p>
                    <div class="d-flex justify-content-end align-items-center gap-1">
                        <div class="d-flex align-items-center gap-3 me-3">
                            <p class="mb-1 fs-5">REJECT</p>
                            <p class="mb-1 fs-5">:</p>
                            <p id="reject-qty" class="mb-1 fs-5">{{ $reject->sum('output') }}</p>
                        </div>
                        <button class="btn btn-dark"  wire:click="$emit('preSubmitUndo', 'reject')" disabled>
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
                        <div class="col-md-6">
                            <label class="form-label">Worksheet</label>
                            <input type="text" class="form-control" id="worksheet-reject" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Style</label>
                            <input type="text" class="form-control" id="style-reject" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" id="color-reject" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Size</label>
                            <input type="text" class="form-control" id="size-reject" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode QR</label>
                            <input type="text" class="form-control" id="kode-reject" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Line</label>
                            <input type="text" class="form-control" id="line-reject" readonly>
                        </div>
                        <div class="col-md-6">
                            @error('rejectType')
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> {{$message}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @enderror
                            <div class="d-flex align-items-center mb-1">
                                <label class="form-label me-1 mb-0">Reject Type</label>
                            </div>
                            <div wire:ignore id="select-reject-type-container">
                                <select class="form-select @error('rejectType') is-invalid @enderror" id="reject-type-select2" wire:model='rejectType'>
                                    <option value="" selected>Select reject type</option>
                                    @foreach ($defectTypes as $defect)
                                        <option value="{{ $defect->id }}">
                                            {{ $defect->defect_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @error('rejectArea')
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> {{$message}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @enderror
                            <div class="d-flex align-items-center mb-1">
                                <label class="form-label me-1 mb-0">Reject Area</label>
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
                                        <strong>Error</strong> Harap tentukan posisi reject area dengan mengklik tombol <button type="button"class="btn btn-dark btn-sm"><i class="fa-regular fa-image fa-2xs"></i></button> di samping 'select defect area'.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @endif
                            <div class="d-none">
                                <label class="form-label me-1 mb-2">Reject Area Position</label>
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
                                            <input class="form-control @error('rejectAreaPositionY') is-invalid @enderror" id="reject-area-position-x-livewire" wire:model='rejectAreaPositionY' readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <div id="regular-submit-reject" wire:ignore.self>
                        <button type="button" class="btn btn-success" wire:click="submitInput">Selesai</button>
                    </div>
                    {{-- <div id="rapid-submit-reject" wire:ignore.self>
                        <button type="button" class="btn btn-success" wire:click='submitRapidInput'>Selesai</button>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Rapid Reject --}}
    <div class="modal" tabindex="-1" id="rapid-reject-modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-reject text-light">
                    <h5 class="modal-title"><i class="fa-solid fa-clone"></i> Reject Rapid Scan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-center">Scanned Item : <b>{{ $rapidRejectCount }}</b></p>
                        <input type="text" class="qty-input" id="rapid-reject-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" wire:click='preSubmitRapidInput'>Lanjut</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="footer fixed-bottom py-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-end">
                <button class="btn btn-dark btn-lg ms-auto fs-3" onclick="triggerSubmit()">LANJUT</button>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Reject Type
            $('#reject-type-select2').select2({
                theme: "bootstrap-5",
                width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                placeholder: $( this ).data( 'placeholder' ),
                dropdownParent: $('#reject-modal .modal-content #select-reject-type-container')
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
                dropdownParent: $('#reject-modal .modal-content #select-reject-area-container')
            });

            $('#reject-area-select2').on('change', function (e) {
                var rejectArea = $('#reject-area-select2').select2("val");
                @this.set('rejectArea', rejectArea);
            });

            Livewire.on('clearSelectRejectAreaPoint', () => {
                $('#reject-type-select2').val("").trigger('change');
                $('#reject-area-select2').val("").trigger('change');
            });
        })

        // Scan QR
        // if (document.getElementById("reject-reader")) {
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

        //         // set sisze
        //         @this.sizeInputText = breakDecodedText[5];

        //         // submit
        //         @this.submitInput();

        //         clearRejectScan()
        //     }

        //     Livewire.on('renderQrScanner', async (type) => {
        //         if (type == 'reject') {
        //             document.getElementById('back-button').disabled = true;
        //             await refreshRejectScan(onScanSuccess);
        //             document.getElementById('back-button').disabled = false;
        //         }
        //     });

        //     Livewire.on('toInputPanel', async (type) => {
        //         if (type == 'reject') {
        //             document.getElementById('back-button').disabled = true;
        //             await @this.updateOutput();
        //             await initRejectScan(onScanSuccess);
        //             document.getElementById('back-button').disabled = false;
        //         }
        //     });

        //     Livewire.on('fromInputPanel', () => {
        //         clearRejectScan();
        //     });
        // }

        var scannedRejectItemInput = document.getElementById("scannedRejectItem");

        scannedRejectItemInput.addEventListener("change", async function () {
            const value = this.value;

            this.setAttribute("disabled", true);

            // submit
            await @this.preSubmitInput(value);

            this.removeAttribute("disabled");
            this.value = '';
        });

        var scannedRapidRejectInput = document.getElementById("rapid-reject-input");

        scannedRapidRejectInput.addEventListener("change", function () {
            @this.pushRapidReject(this.value, null, null);

            this.value = '';
        });

        Livewire.on('qrInputFocus', async (type) => {
            if (type == 'reject') {
                scannedRejectItemInput.focus();
            }
        });

        Livewire.on('toInputPanel', async (type) => {
            if (type == 'reject') {
                @this.updateOutput();
                scannedRejectItemInput.focus();
            }
        });

        // Livewire.on('fromInputPanel', () => {
        //     clearRejectScan();
        // });

        $('#reject-area-modal').on('hidden.bs.modal', function () {
            scannedRejectItemInput.focus();
        })

        function triggerSubmit() {
            if ($("#scannedRejectItem").val()) {
                $("#scannedRejectItem").trigger("change");
            }

            $("#scannedRejectItem").focus();
        }
    </script>
@endpush
