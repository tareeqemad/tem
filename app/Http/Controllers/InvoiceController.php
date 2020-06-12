<?php

namespace App\Http\Controllers;

use App\Invoice;
use Illuminate\Http\Request;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::orderBy('id', 'desc')->paginate(10);

        return view('invoice.index',compact('invoices'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('invoice.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data['customer_name'] = $request->customer_name;
        $data['customer_email'] = $request->customer_email;
        $data['customer_mobile'] = $request->customer_mobile;
        $data['company_name'] = $request->company_name;
        $data['invoice_number'] = $request->invoice_number;
        $data['invoice_date'] = $request->invoice_date;
        $data['sub_total'] = $request->sub_total;
        $data['discount_type'] = $request->discount_type;
        $data['discount_value'] = $request->discount_value;
        $data['vat_value'] = $request->vat_value;
        $data['shipping'] = $request->shipping;
        $data['total_due'] = $request->total_due;

        $invoice = Invoice::create($data);

        $details_list = [];
        for ($i = 0; $i < count($request->product_name); $i++) {
            $details_list[$i]['product_name'] = $request->product_name[$i];
            $details_list[$i]['unit'] = $request->unit[$i];
            $details_list[$i]['quantity'] = $request->quantity[$i];
            $details_list[$i]['unit_price'] = $request->unit_price[$i];
            $details_list[$i]['row_sub_total'] = $request->row_sub_total[$i];
        }

        $details = $invoice->details()->createMany($details_list);

        if ($details) {
            return redirect()->back()->with([
                'message' => __('Frontend/frontend.created_successfully'),
                'alert-type' => 'success'
            ]);
        } else {
            return redirect()->back()->with([
                'message' => __('Frontend/frontend.created_failed'),
                'alert-type' => 'danger'
            ]);
        }


    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('invoice.show', compact('invoice'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('invoice.edit', compact('invoice'));

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

        $invoice = Invoice::whereId($id)->first();

        $data['customer_name'] = $request->customer_name;
        $data['customer_email'] = $request->customer_email;
        $data['customer_mobile'] = $request->customer_mobile;
        $data['company_name'] = $request->company_name;
        $data['invoice_number'] = $request->invoice_number;
        $data['invoice_date'] = $request->invoice_date;
        $data['sub_total'] = $request->sub_total;
        $data['discount_type'] = $request->discount_type;
        $data['discount_value'] = $request->discount_value;
        $data['vat_value'] = $request->vat_value;
        $data['shipping'] = $request->shipping;
        $data['total_due'] = $request->total_due;

        $invoice->update($data);

        $invoice->details()->delete();


        $details_list = [];
        for ($i = 0; $i < count($request->product_name); $i++) {
            $details_list[$i]['product_name'] = $request->product_name[$i];
            $details_list[$i]['unit'] = $request->unit[$i];
            $details_list[$i]['quantity'] = $request->quantity[$i];
            $details_list[$i]['unit_price'] = $request->unit_price[$i];
            $details_list[$i]['row_sub_total'] = $request->row_sub_total[$i];
        }

        $details = $invoice->details()->createMany($details_list);

        if ($details) {
            return redirect()->back()->with([
                'message' => __('Frontend/frontend.updated_successfully'),
                'alert-type' => 'success'
            ]);
        } else {
            return redirect()->back()->with([
                'message' => __('Frontend/frontend.updated_failed'),
                'alert-type' => 'danger'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($invoice) {
            $invoice->delete();
            return redirect()->route('invoice.index')->with([
                'message' => __('Frontend/frontend.deleted_successfully'),
                'alert-type' => 'success'
            ]);
        } else {
            return redirect()->route('invoice.index')->with([
                'message' => __('Frontend/frontend.deleted_failed'),
                'alert-type' => 'danger'
            ]);
        }
    }

    public function print($id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('invoice.print', compact('invoice'));
    }

    public function pdf($id)
    {
        $invoice = Invoice::whereId($id)->first();

        $data['invoice_id']         = $invoice->id;
        $data['invoice_date']       = $invoice->invoice_date;
        $data['customer']           = [
            __('Frontend/frontend.customer_name')       => $invoice->customer_name,
            __('Frontend/frontend.customer_mobile')     => $invoice->customer_mobile,
            __('Frontend/frontend.customer_email')      => $invoice->customer_email
        ];
        $items = [];
        $invoice_details            =  $invoice->details()->get();
        foreach ($invoice_details as $item) {
            $items[] = [
                'product_name'      => $item->product_name,
                'unit'              => $item->unitText(),
                'quantity'          => $item->quantity,
                'unit_price'        => $item->unit_price,
                'row_sub_total'     => $item->product_subtotal,
            ];
        }
        $data['items'] = $items;

        $data['invoice_number']     = $invoice->invoice_number;
        $data['created_at']         = $invoice->created_at->format('Y-m-d');
        $data['sub_total']          = $invoice->sub_total;
        $data['discount']           = $invoice->discountResult();
        $data['vat_value']          = $invoice->vat_value;
        $data['shipping']           = $invoice->shipping;
        $data['total_due']          = $invoice->total_due;


        $pdf = PDF::loadView('invoice.pdf', $data);

         return $pdf->stream($invoice->invoice_number.'.pdf');


    }


    public function list_pdf(){

        $items =[];
        $invoice_list            =  Invoice::all();
        foreach ($invoice_list as $item) {
            $items[] = [
                'customer_name'      => $item->customer_name,
                'invoice_date'       => $item->invoice_date,
                'total_due'          => $item->total_due,
            ];
        }
        $data['items'] = $items;
        $pdf = PDF::loadView('invoice.list_pdf', $data);
        return $pdf->stream('list_pdf_teest.pdf');
    }

}
