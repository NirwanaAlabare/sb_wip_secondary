<div wire:poll.visible.30000ms>
    <div class="loading-container-fullscreen" wire:loading wire:target="toRft, toDefect, toDefectHistory, toReject, toRework, toProductionPanel, preSubmitUndo, submitUndo, updateOrder, toProductionPanel, setAndSubmitInput, submitInput">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>

    <div class="loading-container-fullscreen d-none" id="loading">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>

    {{-- No Connection --}}
    <div class="alert alert-danger alert-dismissible fade show" role="alert" wire:offline>
        <strong>Koneksi Terputus.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    {{-- Production Info --}}
    <div class="production-info row row-gap-1 align-items-center mb-3">
        <div class="col-md">
            <table class="table w-100">
                <thead>
                    <th>WIP</th>
                    <th>DEFECT</th>
                </thead>
                <tbody>
                    <td>...</td>
                    <td>...</td>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Production Panels --}}
    <div class="production-panel row row-gap-3" id="production-panel">
        @if ($panels)
            <div class="row row-gap-3">
                @if ($orderDate == date("Y-m-d"))
                    <div class="col-md-6" id="rft-panel">
                        <div class="d-flex h-100">
                            <div class="card-custom bg-rft d-flex justify-content-between align-items-center w-75 h-100" {{-- onclick="toRft()" --}} wire:click='toRft'>
                                <div class="d-flex flex-column gap-3">
                                    <p class="text-light"><i class="fa-regular fa-circle-check fa-2xl"></i></p>
                                    <p class="text-light">RFT</p>
                                </div>
                                <p class="text-light fs-1">{{ $outputRft }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="defect-panel">
                        <div class="d-flex h-100">
                            <div class="card-custom bg-defect d-flex justify-content-between align-items-center w-75 h-100" {{-- onclick="toDefect()" --}} wire:click='toDefect'>
                                <div class="d-flex flex-column gap-3">
                                    <p class="text-light"><i class="fa-regular fa-circle-exclamation fa-2xl"></i></p>
                                    <p class="text-light">DEFECT</p>
                                </div>
                                <p class="text-light fs-1">{{ $outputDefect }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-6" id="reject-panel">
                    <div class="d-flex h-100">
                        <div class="card-custom bg-reject d-flex justify-content-between align-items-center w-75 h-100" {{-- onclick="toReject()" --}} wire:click='toReject'>
                            <div class="d-flex flex-column gap-3">
                                <p class="text-light"><i class="fa-regular fa-circle-xmark fa-2xl"></i></p>
                                <p class="text-light">REJECT</p>
                            </div>
                            <p class="text-light fs-1">{{ $outputReject }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" id="rework-panel">
                    <div class="d-flex h-100">
                        <div class="card-custom bg-rework d-flex justify-content-between align-items-center w-75 h-100" {{-- onclick="toRework()" --}} wire:click='toRework'>
                            <div class="d-flex flex-column gap-3">
                                <p class="text-light"><i class="fa-regular fa-arrows-rotate fa-2xl"></i></p>
                                <p class="text-light">REWORK</p>
                            </div>
                            <p class="text-light fs-1">{{ $outputRework }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Rft --}}
        {{-- @if ($rft) --}}
        <div class="{{ $rft ? '' : 'd-none' }}">
            @livewire('secondary-out.rft')
        </div>
        {{-- @endif --}}

        {{-- Defect --}}
        {{-- @if ($defect) --}}
        <div class="{{ $defect ? '' : 'd-none' }}">
            @livewire('secondary-out.defect')
        </div>
        {{-- @endif --}}

        {{-- Reject --}}
        {{-- @if ($reject) --}}
        <div class="{{ $reject ? '' : 'd-none' }}">
            @livewire('secondary-out.reject')
        </div>
        {{-- @endif --}}

        {{-- Rework --}}
        <div class="{{ $rework ? '' : 'd-none' }}">
            @livewire('secondary-out.rework')
        </div>
    </div>

    {{-- Select Output Type --}}
    <div class="modal" tabindex="-1" id="select-output-type-modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-sb text-light">
                    <h5 class="modal-title">PILIH TIPE OUTPUT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="hideOutputTypeModal()"></button>
                </div>
                <div class="modal-body">
                    @error('numberingInput')
                        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                            <strong>Error</strong> {{$message}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="btn btn-rft w-100 py-5" wire:click="setAndSubmitInput('rft')" onclick="hideOutputTypeModal();">
                                <h3><b>RFT</b></h3>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-defect w-100 py-5" wire:click="setAndSubmitInput('defect')" onclick="hideOutputTypeModal();">
                                <h3><b>DEFECT</b></h3>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-reject w-100 py-5" wire:click="setAndSubmitInput('reject')" onclick="hideOutputTypeModal();">
                                <h3><b>REJECT</b></h3>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-rework w-100 py-5" wire:click="setAndSubmitInput('rework')" onclick="hideOutputTypeModal();">
                                <h3><b>REWORK</b></h3>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="hideOutputTypeModal()">Batal</button>
                    {{-- <button type="button" class="btn btn-success" wire:click='submitDefectArea'>Tambahkan</button> --}}
                </div>
            </div>
        </div>
    </div>

    @if ($panels)
        <div class="w-100">
            <p class="mt-4 text-center opacity-50"><small><i>{{ date('Y') }} &copy; Nirwana Digital Solution</i></small></p>
        </div>
    @endif

    @if (!$panels)
        {{-- Back --}}
        <a wire:click="toProductionPanel" class="back bg-sb text-light text-center w-auto" id="back-button">
            <i class="fa-regular fa-reply"></i>
        </a>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            restrictYesterdayMasterPlan();
        });

        window.addEventListener("focus", () => {
            document.getElementById("loading").classList.remove("d-none");

            $('#scannedItemRft').attr("disabled", true);
            $('#scannedDefectItem').attr("disabled", true);
            $('#scannedRejectItem').attr("disabled", true);
            $('#scannedReworkItem').attr("disabled", true);

            restrictYesterdayMasterPlan();

            $('#defect-modal').modal("hide");
            $('#reject-modal').modal("hide");

            Livewire.emit('updateOrder');
        });

        // Pad 2 Digits
        function pad(n) {
            return n < 10 ? '0' + n : n
        }

        // Restrict Yesterday Master Plan
        function restrictYesterdayMasterPlan() {
            let date = new Date();
            let day = pad(date.getDate());
            let month = pad(date.getMonth() + 1);
            let year = date.getFullYear();

            // This arrangement can be altered based on how we want the date's format to appear.
            let currentDate = `${year}-${month}-${day}`;

            console.log(@this.orderDate, currentDate);

            if (@this.orderDate != currentDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Anda sedang mengakses Master Plan yang sudah berlalu',
                    html: `Master Plan yang anda akses berasal dari tanggal <br> <b>'`+ document.getElementById('tanggal').value +`'</b> <br> `,
                    showConfirmButton: true,
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#6531a0'
                }).then((result) => {
                    // window.location.href = '{{ route('index') }}';
                });
            }
        }

        var scannedQrCode = "";
        document.addEventListener("keydown", function(e) {
            let textInput = e.key || String.fromCharCode(e.keyCode);
            let targetName = e.target.localName;

            if (targetName != 'input') {
                if (textInput && textInput.length === 1) {
                    scannedQrCode = scannedQrCode+textInput;

                    if (scannedQrCode.length > 3) {
                        let i = 0;
                        let j = 1;
                        let k = 2;

                        if (scannedQrCode.includes('WIP')) {
                            @this.scannedNumberingCode = scannedQrCode;
                        } else {
                            // break decoded text
                            let breakDecodedText = scannedQrCode.split('-');

                            console.log(breakDecodedText);

                            // set kode_numbering
                            @this.scannedNumberingInput = breakDecodedText[i];

                            // set so_det_id
                            @this.scannedSizeInput = breakDecodedText[j];

                            // set size
                            @this.scannedSizeInputText = breakDecodedText[k];
                        }
                    }
                }

                if (@this.panels && textInput == "Enter") {
                    // open dialog
                    $("#select-output-type-modal").show();
                }
            }
        });

        function hideOutputTypeModal() {
            $("#select-output-type-modal").hide();
            scannedQrCode = '';
        }

        Livewire.on('reloadPage', () => {
            location.reload();
        })
    </script>
@endpush
