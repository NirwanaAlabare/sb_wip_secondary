<div>
    <div class="loading-container-fullscreen" wire:loading wire:target="selectDefectAreaPosition, setAndSubmitInput, preSubmitInput, submitInput, submitRapidInput">
        <div class="loading-container">
            <div class="loading"></div>
        </div>
    </div>
    <div class="production-input row row-gap-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-defect text-light">
                    <p class="mb-0 fs-5">Scan QR</p>
                    {{-- <button class="btn btn-dark" wire:click="$emit('showModal', 'rapidDefect')"><i class="fa-solid fa-layer-group"></i></button> --}}
                </div>
                <div class="card-body" wire:ignore.self>
                    @error('numberingInput')
                        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                            <strong>Error</strong> {{$message}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    {{-- <div id="defect-reader" width="600px"></div> --}}
                    <input type="text" class="qty-input h-75" id="scannedDefectItem" name="scannedDefectItem">
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-defect text-light">
                    <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-1">
                        {{-- <div class="d-flex align-items-center gap-1 me-3">
                            <label class="mb-1">Type</label>
                            <select type="text" class="form-select">
                                <option value="" selected>Defect Type</option>
                                <option value="">asd</option>
                                <option value="">asd</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-1 me-3">
                            <label class="mb-1">Area</label>
                            <select type="text" class="form-select">
                                <option value="" selected>Defect Area</option>
                                <option value="">asd</option>
                                <option value="">asd</option>
                            </select>
                        </div> --}}
                        <div class="d-flex align-items-center gap-3 me-3">
                            <p class="mb-1 fs-5">DEFECT</p>
                        </div>
                        <button class="btn btn-dark" wire:click="$emit('preSubmitUndo', 'defect')" disabled>
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
                    <div class="loading-container hidden" id="loading-defect">
                        <div class="loading mx-auto"></div>
                    </div>
                    <div class="loading-container" wire:loading wire:target='setSizeInput'>
                        <div class="loading mx-auto"></div>
                    </div>
                    <div class="row h-100 row-gap-3" id="content-defect">
                        <div class="col-md-6">
                            <label class="form-label">Worksheet</label>
                            <input type="text" class="form-control" id="worksheet-defect" wire:model="worksheetDefect" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Style</label>
                            <input type="text" class="form-control" id="style-defect" wire:model="styleDefect" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" id="color-defect" wire:model="colorDefect" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Size</label>
                            <input type="text" class="form-control" id="size-defect" wire:model="sizeDefect" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode QR</label>
                            <input type="text" class="form-control" id="kode-defect" wire:model="kodeDefect" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Line</label>
                            <input type="text" class="form-control" id="line-defect" wire:model="lineDefect" readonly>
                        </div>
                        <div class="col-md-6" wire:ignore>
                            @error('defectType')
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
                                <label class="form-label me-1 mb-0">Defect Type</label>
                            </div>
                            <div wire:ignore id="select-defect-type-container">
                                <select class="form-select @error('defectType') is-invalid @enderror" id="defect-type-select2" wire:model='defectType'>
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
                                <label class="form-label me-1 mb-0">Defect Area</label>
                            </div>
                            <div class="d-flex gap-1">
                                <div class="w-75" wire:ignore id="select-defect-area-container">
                                    <select class="form-select @error('defectArea') is-invalid @enderror" id="defect-area-select2" wire:model='defectArea'>
                                        <option value="" selected>Select defect area</option>
                                        @foreach ($defectAreas as $defect)
                                            <option value="{{ $defect->id }}">
                                                {{ $defect->defect_area }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-25">
                                    <button type="button" wire:click="selectDefectAreaPosition" class="btn btn-dark w-100">
                                        <i class="fa-regular fa-image"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @if ($errors->has('defectAreaPositionX') || $errors->has('defectAreaPositionY'))
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
                                            <input class="form-control @error('defectAreaPositionX') is-invalid @enderror" id="defect-area-position-x-livewire" wire:model='defectAreaPositionX' readonly>
                                        </div>
                                    </div>
                                    <div class="col d-flex justify-content-center align-items-center">
                                        <label class="form-label me-1 mb-1">Y </label>
                                        <div class="d-flex">
                                            <input class="form-control @error('defectAreaPositionY') is-invalid @enderror" id="defect-area-position-x-livewire" wire:model='defectAreaPositionY' readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-danger" wire:click='clearForm'>Batal</button>
                                <div id="regular-submit" wire:ignore.self>
                                    <button type="button" class="btn btn-success" wire:click='submitInput'>SIMPAN</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Log --}}
    <div class="card mt-3" wire:ignore>
        <div class="card-body">
            <div class="mb-3">
                <input type="date" class="form-control" id="defect-log-date" name="defect-log-date">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered w-100" id="defect-secondary-out-list-table">
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

    {{-- Add Product Type --}}
    <div class="modal" tabindex="-1" id="product-type-modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-defect text-light">
                    <h5 class="modal-title">TAMBAH PRODUCT TYPE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            @error('defectAreaAdd')
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> {{$message}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @enderror
                            <label class="form-label me-1 mb-0">Product Type</label>
                            <input type="text" class="form-control" name="product-type-add" id="product-type-add" wire:model='productTypeAdd'>
                        </div>
                        <div class="mb-3">
                            @error('productTypeImageAdd')
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> {{$message}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @enderror
                            <label class="form-label me-1 mb-0">Product Type Image</label>
                            <input type="file" class="form-control" name="product-type-image-add" id="product-type-image-add" style="border-radius: 5px 5px 0 0;" wire:model='productTypeImageAdd'>
                            <div class="d-flex justify-content-center border" style="border-radius: 0 0 5px 5px;">
                                @if ($productTypeImageAdd)
                                    <img src="{{ $productTypeImageAdd->temporaryUrl() }}" class="img-fluid">
                                @else
                                    <p class="text-center mb-1">*Preview Gambar*</p>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" wire:click='submitProductType'>Tambahkan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Defect Type --}}
    <div class="modal" id="defect-type-modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-defect text-light">
                    <h5 class="modal-title">TAMBAH DEFECT TYPE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            @error('defectTypeAdd')
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> {{$message}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @enderror
                            <label class="form-label me-1 mb-0">Defect Type</label>
                            <input type="text" class="form-control" name="defect-type-add" id="defect-type-add" wire:model='defectTypeAdd'>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" wire:click='submitDefectType'>Tambahkan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Defect Area --}}
    <div class="modal" tabindex="-1" id="defect-area-modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-defect text-light">
                    <h5 class="modal-title">TAMBAH DEFECT AREA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            @error('defectAreaAdd')
                                <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
                                    <small>
                                        <strong>Error</strong> {{$message}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </small>
                                </div>
                            @enderror
                            <label class="form-label me-1 mb-0">Defect Area</label>
                            <input type="text" class="form-control" name="defect-area-add" id="defect-area-add" wire:model='defectAreaAdd'>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" wire:click='submitDefectArea'>Tambahkan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Rapid Defect --}}
    <div class="modal" tabindex="-1" id="rapid-defect-modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-defect text-light">
                    <h5 class="modal-title"><i class="fa-solid fa-clone"></i> Defect Rapid Scan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-center">Scanned Item : <b>{{ $rapidDefectCount }}</b></p>
                        <input type="text" class="qty-input" id="rapid-defect-input">
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
        // On DOM Render
        document.addEventListener("DOMContentLoaded", () => {
            // Product Type
            $('#product-type-select2').select2({
                theme: "bootstrap-5",
                width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                placeholder: $( this ).data( 'placeholder' ),
                dropdownParent: $('#defect-modal .modal-content #select-product-type-container')
            });

            $('#product-type-select2').on('change', function (e) {
                var productType = $('#product-type-select2').select2("val");
                @this.set('productType', productType);
            });

            Livewire.on('clearSelectDefectAreaPoint', () => {
                $('#product-type-select2').val("").trigger('change');
                $('#defect-type-select2').val("").trigger('change');
                $('#defect-area-select2').val("").trigger('change');
            });
        })

        // On Livewire Render
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', (message, component) => {
                // Defect Type
                $('#defect-type-select2').select2({
                    theme: "bootstrap-5",
                    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                    placeholder: $( this ).data( 'placeholder' ),
                });

                $('#defect-type-select2').on('change', function (e) {
                    var defectType = $('#defect-type-select2').select2("val");
                    @this.set('defectType', defectType);
                });

                // Defect Area
                $('#defect-area-select2').select2({
                    theme: "bootstrap-5",
                    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                    placeholder: $( this ).data( 'placeholder' ),
                });

                $('#defect-area-select2').on('change', function (e) {
                    var defectArea = $('#defect-area-select2').select2("val");
                    @this.set('defectArea', defectArea);
                });
            })
        })

        // Scan QR
        var scannedDefectItemInput = document.getElementById("scannedDefectItem");

        scannedDefectItemInput.addEventListener("change", async function () {
            const value = this.value;

            this.setAttribute("disabled", true);

            // submit
            await @this.preSubmitInput(value);

            this.removeAttribute("disabled");
            this.value = '';
        });

        var scannedRapidDefectInput = document.getElementById("rapid-defect-input");

        scannedRapidDefectInput.addEventListener("change", function () {
            @this.pushRapidDefect(this.value, null, null);

            this.value = '';
        });

        Livewire.on('qrInputFocus', async (type) => {
            if (type == 'defect') {
                scannedDefectItemInput.focus();

                defectSecondaryOutListReload()
            }
        });

        Livewire.on('toInputPanel', async (type) => {
            if (type == 'defect') {
                scannedDefectItemInput.focus();
            }
        });

        // Livewire.on('fromInputPanel', () => {
        //     clearDefectScan();
        // });

        $('#defect-area-modal').on('hidden.bs.modal', function () {
            scannedDefectItemInput.focus();
        })

        function triggerSubmit() {
            if ($("#scannedDefectItem").val()) {
                $("#scannedDefectItem").trigger("change");
            }

            $("#scannedDefectItem").focus();
        }

        // DEFECT Secondary Out List
        let defectSecondaryOutListTable = $("#defect-secondary-out-list-table").DataTable({
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
                    d.date = $("#defect-log-date").val();
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
        });

        function defectSecondaryOutListReload() {
            $("#defect-secondary-out-list-table").DataTable().ajax.reload();
        }
    </script>
@endpush
