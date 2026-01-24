<?php

namespace App\Http\Livewire\SecondaryOut;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use App\Models\SignalBit\UserPassword;
use App\Models\SignalBit\MasterPlan;
use App\Models\SignalBit\ProductType;
use App\Models\SignalBit\Defect as DefectModel;
use App\Models\SignalBit\DefectType;
use App\Models\SignalBit\DefectArea;
use App\Models\SignalBit\Rft;
use App\Models\SignalBit\Reject;
use App\Models\SignalBit\SewingSecondaryOutDefect;
use App\Models\SignalBit\SewingSecondaryIn;
use App\Models\SignalBit\SewingSecondaryOut;
use App\Models\Nds\Numbering;
use Carbon\Carbon;
use Validator;
use DB;

class Defect extends Component
{
    use WithFileUploads;

    public $lines;
    public $orders;

    public $outputInput;

    public $worksheetDefect;
    public $styleDefect;
    public $colorDefect;
    public $sizeDefect;
    public $kodeDefect;
    public $lineDefect;

    public $sizeInput;
    public $sizeInputText;
    public $noCutInput;
    public $numberingInput;
    public $defect;

    public $defectTypes;
    public $defectAreas;
    public $productTypes;

    public $defectType;
    public $defectArea;
    public $productType;

    public $defectTypeAdd;
    public $defectAreaAdd;
    public $productTypeAdd;

    public $productTypeImageAdd;
    public $defectAreaPositionX;
    public $defectAreaPositionY;

    public $rapidDefect;
    public $rapidDefectCount;

    public $selectedSecondary;
    public $selectedSecondaryText;

    protected $rules = [
        'sizeInput' => 'required',
        'noCutInput' => 'required',
        'numberingInput' => 'required',
        // 'productType' => 'required',
        'defectType' => 'required',
        'defectArea' => 'required',
        'defectAreaPositionX' => 'required',
        'defectAreaPositionY' => 'required',
    ];

    protected $messages = [
        'sizeInput.required' => 'Harap scan qr.',
        'noCutInput.required' => 'Harap scan qr.',
        'numberingInput.required' => 'Harap scan qr.',
        // 'productType.required' => 'Harap tentukan tipe produk.',
        'defectType.required' => 'Harap tentukan jenis defect.',
        'defectArea.required' => 'Harap tentukan area defect.',
        'defectAreaPositionX.required' => "Harap tentukan posisi defect area dengan mengklik tombol 'gambar' di samping 'select product type'.",
        'defectAreaPositionY.required' => "Harap tentukan posisi defect area dengan mengklik tombol 'gambar' di samping 'select product type'.",
    ];

    protected $listeners = [
        'setDefectAreaPosition' => 'setDefectAreaPosition',
        'updateOutputDefect' => 'updateOutput',
        'setAndSubmitInputDefect' => 'setAndSubmitInput',
        'toInputPanel' => 'resetError',
        'updateSelectedSecondary' => 'updateSelectedSecondary'
    ];

    private function checkIfNumberingExists($numberingInput = null): bool
    {
        $currentData = DB::table('output_secondary_out')->where('kode_numbering', ($numberingInput ?? $this->numberingInput))->first();
        if ($currentData) {
            $this->addError('numberingInput', 'Kode QR sudah discan di '.strtoupper($currentData->status).'.');

            return true;
        }

        return false;
    }

    public function mount(SessionManager $session, $selectedSecondary)
    {
        $this->lines = null;
        $this->orders = null;

        $this->outputInput = 0;

        $this->worksheetDefect = null;
        $this->styleDefect = null;
        $this->colorDefect = null;
        $this->sizeDefect = null;
        $this->kodeDefect = null;
        $this->lineDefect = null;

        $this->sizeInput = null;
        $this->sizeInputText = null;
        $this->noCutInput = null;
        $this->numberingInput = null;

        $this->defectType = null;
        $this->defectArea = null;
        $this->productType = null;

        $this->defectAreaPositionX = null;
        $this->defectAreaPositionY = null;

        $this->rapidDefect = [];
        $this->rapidDefectCount = 0;

        $this->selectedSecondary = $selectedSecondary;

        $selectedSecondaryData = DB::table("output_secondary_master")->where("id", $this->selectedSecondary)->first();

        if ($selectedSecondaryData) {
            $this->selectedSecondaryText = $selectedSecondaryData->secondary;
        }
    }

    public function dehydrate()
    {
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function resetError() {
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function updatedproductTypeImageAdd()
    {
        $this->validate([
            'productTypeImageAdd' => 'image',
        ]);
    }

    public function submitProductType()
    {
        if ($this->productTypeAdd && $this->productTypeImageAdd) {

            $productTypeImageAddName = md5($this->productTypeImageAdd . microtime()).'.'.$this->productTypeImageAdd->extension();
            $this->productTypeImageAdd->storeAs('public/images', $productTypeImageAddName);

            $createProductType = ProductType::create([
                'product_type' => $this->productTypeAdd,
                'image' => $productTypeImageAddName,
            ]);

            if ($createProductType) {
                $this->emit('alert', 'success', 'Product Type : '.$this->productTypeAdd.' berhasil ditambahkan.');

                $this->productTypeAdd = null;
                $this->productTypeImageAdd = null;
            } else {
                $this->emit('alert', 'error', 'Terjadi kesalahan.');
            }
        } else {
            $this->emit('alert', 'error', 'Harap tentukan nama tipe produk beserta gambarnya');
        }
    }

    public function submitDefectType()
    {
        if ($this->defectTypeAdd) {
            $createDefectType = DefectType::create([
                'defect_type' => $this->defectTypeAdd
            ]);

            if ($createDefectType) {
                $this->emit('alert', 'success', 'Defect type : '.$this->defectTypeAdd.' berhasil ditambahkan.');

                $this->defectTypeAdd = '';
            } else {
                $this->emit('alert', 'error', 'Terjadi kesalahan.');
            }
        } else {
            $this->emit('alert', 'error', 'Harap tentukan nama defect type');
        }
    }

    public function submitDefectArea()
    {
        if ($this->defectAreaAdd) {

            $createDefectArea = DefectArea::create([
                'defect_area' => $this->defectAreaAdd,
            ]);

            if ($createDefectArea) {
                $this->emit('alert', 'success', 'Defect area : '.$this->defectAreaAdd.' berhasil ditambahkan.');

                $this->defectAreaAdd = null;
            } else {
                $this->emit('alert', 'error', 'Terjadi kesalahan.');
            }
        } else {
            $this->emit('alert', 'error', 'Harap tentukan nama defect area');
        }
    }

    public function clearInput()
    {
        $this->sizeInput = '';
    }

    public function selectDefectAreaPosition()
    {
        $masterPlan = DB::connection('mysql_sb')->table('output_secondary_in')->
            select("master_plan.gambar")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
            where("output_secondary_in.kode_numbering", $this->numberingInput)->
            first();

        if ($masterPlan) {
            $this->emit('showSelectDefectArea', $masterPlan->gambar);
        } else {
            $this->emit('alert', 'error', 'Harap pilih tipe produk terlebih dahulu');
        }
    }

    public function setDefectAreaPosition($x, $y)
    {
        $this->defectAreaPositionX = $x;
        $this->defectAreaPositionY = $y;
    }

    public function clearForm() {
        $this->worksheetDefect = "";
        $this->styleDefect = "";
        $this->colorDefect = "";
        $this->sizeDefect = "";
        $this->kodeDefect = "";
        $this->lineDefect = "";
    }

    public function outputIncrement()
    {
        $this->outputInput++;
    }

    public function outputDecrement()
    {
        if (($this->outputInput-1) < 1) {
            $this->emit('alert', 'warning', "Kuantitas output tidak bisa kurang dari 1.");
        } else {
            $this->outputInput--;
        }
    }

    public function updateSelectedSecondary($selectedSecondary) {
        $this->selectedSecondary = $selectedSecondary;

        $selectedSecondaryData = DB::table("output_secondary_master")->where("id", $this->selectedSecondary)->first();

        if ($selectedSecondaryData) {
            $this->selectedSecondaryText = $selectedSecondaryData->secondary;
        }
    }

    public function render(SessionManager $session)
    {
        $this->lines = UserPassword::where("Groupp", "SEWING")->orderBy("line_id", "asc")->get();

        $this->orders = DB::connection('mysql_sb')->
            table('act_costing')->
            selectRaw('
                id as id_ws,
                kpno as no_ws,
                styleno as style
            ')->
            where('status', '!=', 'CANCEL')->
            where('cost_date', '>=', '2023-01-01')->
            where('type_ws', 'STD')->
            orderBy('cost_date', 'desc')->
            orderBy('kpno', 'asc')->
            groupBy('kpno')->
            get();

        // if (isset($this->errorBag->messages()['numberingInput']) && collect($this->errorBag->messages()['numberingInput'])->contains(function ($message) {return Str::contains($message, 'Kode QR sudah discan');})) {
        //     foreach ($this->errorBag->messages()['numberingInput'] as $message) {
        //         $this->emit('alert', 'warning', $message);
        //     }
        // } else if ((isset($this->errorBag->messages()['numberingInput']) && collect($this->errorBag->messages()['numberingInput'])->contains("Harap scan qr.")) || (isset($this->errorBag->messages()['sizeInput']) && collect($this->errorBag->messages()['sizeInput'])->contains("Harap scan qr."))) {
        //     $this->emit('alert', 'error', "Harap scan QR.");
        // }
        if (isset($this->errorBag->messages()['numberingInput'])) {
            foreach ($this->errorBag->messages()['numberingInput'] as $message) {
                $this->emit('alert', 'error', $message);
            }
        }

        // Defect types
        $this->productTypes = ProductType::orderBy('product_type')->get();

        // Defect types
        $this->defectTypes = DefectType::whereRaw("(hidden IS NULL OR hidden != 'Y')")->orderBy('defect_type')->get();

        // Defect areas
        $this->defectAreas = DefectArea::whereRaw("(hidden IS NULL OR hidden != 'Y')")->orderBy('defect_area')->get();

        return view('livewire.secondary-out.defect', ["defectTypesOpt" => $this->defectTypes, "defectAreasOpt" => $this->defectAreas]);
    }
}
