<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Vehicles;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function trash()
    {
        $customers = Customers::onlyTrashed()->orderBy('deleted_at', 'DESC')->get();
        $vehicles = Vehicles::onlyTrashed()->orderBy('deleted_at', 'DESC')->get();
        return view('trash.index', compact('customers', 'vehicles'));
    }

    public function restoreCust($id)
    {
        $restoreCust = Customers::withTrashed()->find($id);
        $restoreCust->restore();

        return redirect('/trash')->with('success', 'Record successfully restored');
    }

    public function restoreVeh($id)
    {
        $restoreVeh = Vehicles::withTrashed()->find($id);
        $restoreVeh->restore();

        return redirect('/trash')->with('success', 'Record successfully restored');
    }

    public static function totalDeleted()
    {
        $trashedCustomers = Customers::onlyTrashed()->count();
        $trashedVehicles = Vehicles::onlyTrashed()->count();
        $totalDel = $trashedCustomers + $trashedVehicles;
        return $totalDel;

//      return view('trash.index', compact('trashedCustomers', 'trashedVehicles'))->count();
    }
}
