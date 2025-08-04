@extends('errors::custom')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Terlalu Banyak Permintaan'))
@section('description', 'Anda telah mengirim terlalu banyak permintaan dalam waktu singkat. Silakan tunggu beberapa saat sebelum mencoba lagi.')