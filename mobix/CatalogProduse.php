<?php

namespace App\Http\Livewire;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportProduse;
use Livewire\WithPagination;
use Livewire\Component;
use App\Produse;

class CatalogProduse extends Component
{
    use WithPagination;

    public $selecteazaToataPagina = false;
    public $arataFormularEditare = false;
    public $arataModalStergere = false;
    public Produse $editeazaProdus;
    public $sortField = 'denumire';
    public $sortDirection = 'asc';    public $selecteazaTot = false;
    public $selectate= [];
    public $search = '';
    
    protected $queryString = [
        'sortField', 
        'sortDirection',
    ];

    protected $rules = [
        'editeazaProdus.denumire' => 'required',
    ];

    public function mount() { $this->editeazaProdus = Produse::make(['created_at' => now()]); }

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

    public function selecteazaTot()
    {
        $this->selecteazaTot = true;
    }

    public function selecteazaToataPagina()
    {
        $this->selectate = $this->produse->pluck('id')->map(fn($id) => (string) $id);
    }

    /** CRUD **/

     /* Operațiuni CRUD (Marian Pop - 28.04.2021)
     * --------------------------------------------------*
     * Editează, Adaugă, Salvează, Șterge
     * --------------------------------------------------*
     */

    public function editeaza(Produse $produs)
    {
        if ($this->editeazaProdus->isNot($produs)) $this->editeazaProdus = $produs;
        $this->arataFormularEditare = true;
    }

    public function adaugaProdus()
    {
        if ($this->editeazaProdus->getKey()) $this->editeazaProdus = Produse::make(['created_at' => now()]);
        $this->arataFormularEditare = true;
    }

    public function salveazaProdusul()
    {
        $this->validate();
        $this->editeazaProdus->save();
        $this->arataFormularEditare = false;
    }

    public function stergeProduseSelectate()
    {
        $produse = Produse::whereKey($this->selectate);
        $produse->delete();
        $this->arataModalStergere = false;
        $this->selectate = [];
    }

     /* Exportă în format .xlsx (Marian Pop - 28.04.2021)
     * --------------------------------------------------*
     * Exporta produsele selectate in format .xlsx (excel)
     * In viitor posibil sa dorim sa exportam si in alte formate (.csv etc.)
     * --------------------------------------------------*
     */

    public function exportExcelSelectate() 
    {
       return (new ExportProduse($this->selectate))->download('mobix_produse.xlsx');
    }

    /* Database Query (Marian Pop - 28.04.2021)
     * --------------------------------------------------*
     * Interogare bază de date pentru a primi produsele
     * disponibile, sortate și paginate.
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
        if ($this->selecteazaTot) $this->selectate = $this->produse->pluck('id')->map(fn($id) => (string) $id);

        return view('livewire.catalog-produse', [
            'produse' => $this->produse,
        ]);

     /** Livewire Lifecycle Hook **/

     public function updatedSelectate()
     {
        $this->selecteazaTot = false;
        $this->selecteazaToataPagina = false;
     }

     public function updatedSelecteazaToataPagina($value)
     {

         $this->selecteazaTot = false;
        // $this->selectate = [];

        if ($value) {
            $this->selectate = $this->produse->pluck('id')->map(fn($id) => (string) $id);
        } else {
            $this->selectate = [];
        }
     }

    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }

}
