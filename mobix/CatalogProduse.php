<?php

namespace App\Http\Livewire;
use Livewire\WithPagination;
use Livewire\Component;
use App\Produse;

class CatalogProduse extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'denumire';
    public $sortDirection = 'asc';

    // protected $queryString = ['sortField', 'sortDirection'];

    public function sortBy($field) {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function render()
    {
        return view('livewire.catalog-produse', [
            'produse' => Produse::search('denumire', $this->search)
                        ->orderBy($this->sortField, $this->sortDirection)
                        ->paginate(5),
        ]);
    }

     /**
     *  Livewire Lifecycle Hook
     */
    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }

}
