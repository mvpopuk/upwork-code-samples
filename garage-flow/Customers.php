<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
// use Laravel\Scout\Searchable;
use Nicolaslopezj\Searchable\SearchableTrait;

class Customers extends Model
{
    // use Searchable;
    use SoftDeletes;
    use SearchableTrait;

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'customers.FirstName' => 10,
            'customers.LastName' => 10,
            'customers.id' => 1,
            'customers.Email' => 1,
            'customers.Phone' => 1,
            'customers.Mobile' => 1,
            'customers.PostCode' => 1,
        ],
    ];

    // Table Name
    protected $table = 'customers';
    protected $dates = ['deleted_at'];
  
    /**
     * CONCAT 'FirstName' and 'LastName' to be    
     * used as 'FullName' in CustomersTable.php
     */

    public function getFullNameAttribute() { return $this->FirstName . $this->LastName; }
    protected $appends = [ 'FullName' ];

    public static function totalCustomersToday()
    {
        $totalCustomers = Customers::whereDate('created_at', Carbon::today())->count();
        return $totalCustomers;
    }

    public static function totalDeleted()
    {
        $trashedCustomers = Customers::onlyTrashed()->get();
        return $trashedCustomers->count();
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicles::class, 'cust_id');
    }

}
