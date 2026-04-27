@extends('front_box.default', ['title' => ($modalidade ?? \App\Models\Troca::MODALIDADE_TROCA) === \App\Models\Troca::MODALIDADE_DEVOLUCAO_PDV ? 'Nova devolução (PDV)' : 'Nova troca' ])
@section('content')

@php
    $pageModalidade = $modalidade ?? \App\Models\Troca::MODALIDADE_TROCA;
@endphp
{!!Form::open()
->post()
->route('trocas.store')->id('form-troca')
!!}
<div class="pl-lg-4" data-troca-modalidade="{{ $pageModalidade }}">
    @include('trocas._forms')
</div>
{!!Form::close()!!}
@include('modals._novo_cliente')
@include('front_box.partials.modal_codigo_unico')

@endsection
