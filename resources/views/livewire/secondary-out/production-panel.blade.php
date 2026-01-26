<div wire:poll.visible.30000ms>
    <div class="loading-container-fullscreen" wire:loading wire:target="toRft, toDefect, toReject, toRework, toProductionPanel, setAndSubmitInput, submitInput">
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
    {{-- <div class="production-info row row-gap-3 justify-content-end align-items-center mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title text-center">WIP</h5>
                </div>
                <div class="card-body">
                    <h5 class="text-center">0</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title text-center">DEFECT</h5>
                </div>
                <div class="card-body">
                    <h5 class="text-center">0</h5>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Production Panels --}}
    <div class="production-panel row row-gap-3 mb-3" id="production-panel">
        @if ($panels)
            <div class="production-panel-info row row-gap-3 justify-content-end align-items-center mb-3">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-center">WIP</h5>
                        </div>
                        <div class="card-body">
                            <h5 class="text-center">{{ $inWip }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-center">DEFECT</h5>
                        </div>
                        <div class="card-body">
                            <h5 class="text-center">{{ $outDefect }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-gap-3">
                <div class="col-md-6" id="rft-panel">
                    <div class="d-flex h-100">
                        <div class="card-custom bg-rft d-flex justify-content-between align-items-center w-100 h-100" {{-- onclick="toRft()" --}} wire:click='toRft'>
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
                        <div class="card-custom bg-defect d-flex justify-content-between align-items-center w-100 h-100" {{-- onclick="toDefect()" --}} wire:click='toDefect'>
                            <div class="d-flex flex-column gap-3">
                                <p class="text-light"><i class="fa-regular fa-circle-exclamation fa-2xl"></i></p>
                                <p class="text-light">DEFECT</p>
                            </div>
                            <p class="text-light fs-1">{{ $outputDefect }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" id="reject-panel">
                    <div class="d-flex h-100">
                        <div class="card-custom bg-reject d-flex justify-content-between align-items-center w-100 h-100" {{-- onclick="toReject()" --}} wire:click='toReject'>
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
                        <div class="card-custom bg-rework d-flex justify-content-between align-items-center w-100 h-100" {{-- onclick="toRework()" --}} wire:click='toRework'>
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
            @livewire('secondary-out.rft', ['selectedSecondary' => $selectedSecondary])
        </div>
        {{-- @endif --}}

        {{-- Defect --}}
        {{-- @if ($defect) --}}
        <div class="{{ $defect ? '' : 'd-none' }}">
            @livewire('secondary-out.defect', ['selectedSecondary' => $selectedSecondary])
        </div>
        {{-- @endif --}}

        {{-- Reject --}}
        {{-- @if ($reject) --}}
        <div class="{{ $reject ? '' : 'd-none' }}">
            @livewire('secondary-out.reject', ['selectedSecondary' => $selectedSecondary])
        </div>
        {{-- @endif --}}

        {{-- Rework --}}
        <div class="{{ $rework ? '' : 'd-none' }}">
            @livewire('secondary-out.rework', ['selectedSecondary' => $selectedSecondary])
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

    <div class="card {{ $panels ? '' : 'd-none' }}">
            <div class="card-header bg-rework text-light">
                <h5 class="card-title">Total Output {{ $selectedSecondaryText }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <input type="date" class="form-control w-auto" value="{{ $date }}" id="secondary-out-date" onchange="secondaryOutTotalReload()">
                </div>
                <div class="table-responsive" wire:ignore>
                    <table class="table table-bordered w-100" id="secondary-out-total-table">
                        <thead>
                            <tr>
                                <th>Tanggal Plan</th>
                                <th>Master Plan</th>
                                <th>RFT</th>
                                <th>DEFECT</th>
                                <th>REWORK</th>
                                <th>REJECT</th>
                            </tr>
                        </thead>
                    </table>
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
        window.addEventListener("focus", () => {
            // Reset State when losing focus

            $('#defect-modal').modal("hide");
            $('#reject-modal').modal("hide");
        });

        // Pad 2 Digits
        function pad(n) {
            return n < 10 ? '0' + n : n
        }

        // When Scanned on Production Panel
        var scannedQrCode = "";
        document.addEventListener("keydown", function(e) {
            let textInput = e.key || String.fromCharCode(e.keyCode);
            let targetName = e.target.localName;

            if (targetName != 'input') {
                if (textInput && textInput.length === 1) {
                    scannedQrCode = scannedQrCode+textInput;

                    if (scannedQrCode.length > 3) {
                        let i = 0;

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

        let secondaryOutTotal = $("#secondary-out-total-table").DataTable({
            serverSide: true,
            processing: true,
            ordering: false,
            searching: false,
            paging: true,
            lengthChange: false,
            ajax: {
                url: '{{ route('out-get-secondary-out-total') }}',
                dataType: 'json',
                data: function (d) {
                    d.date = $("#secondary-out-date").val();
                    d.selectedSecondary = $("#selectedSecondary").val();
                }
            },
            columns: [
                {
                    data: 'master_plan_tanggal',
                },
                {
                    data: 'master_plan_id',
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
                },
            ],
            columnDefs: [
                {
                    targets: "_all",
                    className: "text-nowrap text-center align-middle"
                },
                {
                    targets: [1],
                    render: (data, type, row, meta) => {
                        return row.master_plan_ws + "<br>" + row.master_plan_style + "<br>" + row.master_plan_color;
                    }
                }
            ],
        });

        function secondaryOutTotalReload() {
            $("#secondary-out-total-table").DataTable().ajax.reload();
        }

        Livewire.on("updateSelectedSecondary", function () {
            secondaryOutTotalReload();
        })

        Livewire.on('toInputPanel', (type) => {
            secondaryOutTotalReload();
        });

        function clearForm(suffix= '') {
            // $("#worksheet"+suffix).val(null).trigger("change");
            // $("#style"+suffix).val(null).trigger("change");
            // $("#color"+suffix).val(null).trigger("change");
            // $("#size"+suffix).val(null).trigger("change");
            // $("#sewingLine"+suffix).val(null).trigger("change");

            $("#rft-input").val(0);
            $("#defect-input").val(0);
            $("#reject-input").val(0);

            if (suffix == "Defect") {
                $("#defect-type-select2").val(null).trigger("change");
                $("#defect-area-select2").val(null).trigger("change");
                $("#defect-area-position-x-livewire").val(null).trigger("change");
                $("#defect-area-position-y-livewire").val(null).trigger("change");
            }

            if (suffix == "Reject") {
                $("#reject-type-select2").val(null).trigger("change");
                $("#reject-area-select2").val(null).trigger("change");
                $("#reject-area-position-x-livewire").val(null).trigger("change");
                $("#reject-area-position-y-livewire").val(null).trigger("change");
            }

            $('.is-invalid').removeClass('is-invalid');
        }
    </script>
@endpush
