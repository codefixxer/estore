@extends('layouts.admin.app')

@section('title',translate('messages.Order_Invoice'))

@section('content')
<style>
    @media print {
    
            #printableArea {
                margin: 0 auto !important;
                width: 320px !important;
            }
        }
</style>
    @include('new_invoice')
@endsection
