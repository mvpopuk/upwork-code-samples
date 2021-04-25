<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Customers;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
 {

       $customers = Customers::orderBy('FirstName', 'asc')->paginate(20);
       return view('customers.index')->with('customers', $customers);
    }

    public function search(Request $request)
    {

        /*  $request->validate([
         *   'query' => 'required|min:3',
         *  ]);
         */

        $query = $request->input('query');

     /**
      * The search logic before using 'nicolaslopezj/searchable' package [ Marian Pop - 03.05.2020 ]
      *
      * $customers = Customers::where(DB::raw("CONCAT(`FirstName`, ' ' ,`LastName`)"), 'like', "%$query%")
      *                        ->orWhere(DB::raw("CONCAT(`LastName`, ' ' ,`FirstName`)"), 'like', "%$query%")
      *                         ->orWhere('id', 'like', "%$query%")
      *                         ->orderBy('id', 'asc')
      *                         ->paginate(10);
      */

    // In order this to work I set 'strict' = false; in config/database.php [ Marian - 03.05.2020 ]
        $customers = Customers::search($query)->orderBy('FirstName', 'asc')->paginate(20);

    // If the user deletes the input text and press ENTER, we want to redirect him to /customers route [ Marian Pop - 03.05.2020 ]
        if($query == '') { return redirect('/customers'); }

        return view('customers.search')->with('customers', $customers);
    }

    public function autocomplete()
    {
        return view('panel.dashboard');
    }

    public function instantSearch(Request $request)
    {
        $customers = Customers::where('FirstName','LIKE',$request->instantSearch.'%')
            ->orWhere('LastName','LIKE',$request->instantSearch.'%')
            ->orWhere('id','LIKE',$request->instantSearch.'%')
            ->get();
        return response()->json($customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Create customer record

        $customer = new Customers;

        // Personal Information
        $customer->FirstName = $request->input('FirstName');
        $customer->LastName = $request->input('LastName');
        $customer->Mobile = $request->input('Mobile');
        $customer->Phone = $request->input('Phone');
        $customer->Email = $request->input('Email');
        $customer->Gender = $request->input('Gender');

        // Address
        $customer->Country = $request->input('Country');
        $customer->County = $request->input('County');
        $customer->City = $request->input('City');
        $customer->PostCode = $request->input('PostCode');
        $customer->Address = $request->input('Address');

        $customer->save();

        return redirect('/customers')->with('success', 'Customer added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customers::find($id);
        return view('customers.edit')->with('customer', $customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = Customers::find($id);

        // Personal Information
        $customer->FirstName = $request->input('FirstName');
        $customer->LastName = $request->input('LastName');
        $customer->Mobile = $request->input('Mobile');
        $customer->Phone = $request->input('Phone');
        $customer->Email = $request->input('Email');
        $customer->Gender = $request->input('Gender');

        // Address
        $customer->Country = $request->input('Country');
        $customer->County = $request->input('County');
        $customer->City = $request->input('City');
        $customer->PostCode = $request->input('PostCode');
        $customer->Address = $request->input('Address');

        $customer->save();

        return redirect('/customers')->with('success', 'Customer record updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customers::find($id);
        $customer->delete();

        return redirect('/customers')->with('success', 'Customer removed');
    }
}
