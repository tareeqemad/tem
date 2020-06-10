@extends('layouts.dashboard.app')
@section('title', 'Invoice List')
@section('content-header', 'Invoice List')
@section('content-actions')
    <a href="" class="btn btn-primary">Create Product</a>
@endsection

@section('content')

 <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex">
                    <h2>{{ __('Frontend/frontend.invoices') }}</h2>
                    <a href="{{ route('invoice.create') }}" class="btn btn-primary ml-auto"><i class="fa fa-plus"></i> {{ __('Frontend/frontend.create_invoice') }}</a>
                </div>

                <div class="table-responsive">
                    <table class="table card-table">
                        <thead>
                        <tr>
                            <th>{{ __('Frontend/frontend.customer_name') }}</th>
                            <th>{{ __('Frontend/frontend.invoice_date') }}</th>
                            <th>{{ __('Frontend/frontend.total_due') }}</th>
                            <th>{{ __('Frontend/frontend.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tareq</td>
                                <td>sasda</td>
                                <td>1212.00</td>
                                <td>
                                    <a href="" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                    <a href="javascript:void(0)" onclick="if (confirm('{{ __('Frontend/frontend.r_u_sure') }}')) { alert(1)} else { return false; }" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>

                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4">
                                <div class="float-right">
                        112
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>








            </div>
        </div>
    </div>







@endsection
