<?php

namespace App\Http\Livewire;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportProduse;
use App\Imports\ImportProduse;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Produse;

class CatalogProduse extends Component
{
    use WithPagination, WithFileUploads;

    public Produse $model;

    public $importExcel;
    public string $search = '';

    public bool $selectAll = false;
    public bool $selectPage = false;
    public $selected= [];

    public bool $showModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    public string $sortField = 'denumire';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'sortField',
        'sortDirection',
    ];

    function rules(): array
    {
        return [
        'model.denumire' => [
            'required',
            'unique:produse,denumire,'
        ],
    ];}

    public function mount()
    {
        $this->model = Produse::make(['created_at' => now()]);
    }

    /** Sortează Produse **/

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    /** Selectează Produse **/

    public function selectAll()
    {
        $this->selectAll = true;
    }

    public function selectPage()
    {
        $this->selected = $this->produse->pluck('id')->map(fn($id) => (string) $id);
    }

     /* Operațiuni CRUD (Marian Pop - 28.04.2021)
     * --------------------------------------------------*
     * Editează, Adaugă, Salvează, Șterge                *
     * --------------------------------------------------*
     */

    public function edit(Produse $produs)
    {
        if ($this->model->isNot($produs)) $this->model = $produs;
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function addProduct()
    {
        $this->resetValidation();
        if ($this->model->getKey()) {
            $this->model = Produse::make(['created_at' => now()]);
        }
        $this->showEditModal = true;
    }

    public function saveProduct()
    {
        $this->validate();
        $this->model->slug = Str::slug($this->model->denumire);
        $this->model->save();
        $this->showEditModal = false;
    }

    public function deleteSelected()
    {
        $produse = Produse::whereKey($this->selected);
        $produse->delete();
        $this->showDeleteModal = false;
        $this->selected = [];
        $this->model = Produse::make();
    }

     /* Exportă și importă în format .xlsx (Marian Pop - 28.04.2021)
     * ---------------------------------------------------------------------- *
     * Exporta produsele selected in format .xlsx (excel)                    *
     * In viitor posibil sa dorim sa exportam si in alte formate (.csv etc.)  *
     * ---------------------------------------------------------------------- *
     */

    public function exportExcelSelected()
    {
       return (new ExportProduse($this->selected))->download('mobix_produse.xlsx');
    }

    public function importProduseExcel()
    {
        $importProduse = $this->importExcel->store('Import-Excel-Produse');
        Excel::import(new ImportProduse, $importProduse);
        $this->showModal = false;
    }

     /* Database Query (Marian Pop - 28.04.2021)
     * --------------------------------------------------*
     * Interogare bază de date pentru a primi produsele  *
     * disponibile, sortate și paginate.                 *
     * --------------------------------------------------*
     */

    public function getProduseQueryProperty()
    {
        return Produse::query()
            ->search('denumire', $this->search)
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function getProduseProperty()
    {
       return $this->produseQuery->paginate(10);
    }

    /** RENDER **/

    public function render()
    {
        if ($this->selectAll) $this->selected = $this->produse->pluck('id')->map(fn($id) => (string) $id);

        return view('livewire.catalog-produse', [
            'produse' => $this->produse,
        ]);
    }

     /** Livewire Lifecycle Hook **/

     public function updatedSelected()
     {
        $this->selectAll = false;
        $this->selectPage = false;
     }

     public function updatedSelectPage($value)
     {

        $this->selectAll = false;
        if ($value) {
            $this->selected = $this->produse->pluck('id')->map(fn($id) => (string) $id);
        } else {
            $this->selected = [];
        }
     }

    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }

}
