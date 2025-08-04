@extends('errors::custom')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Layanan Tidak Tersedia'))
@section('description', 'Situs sedang dalam maintenance atau mengalami gangguan sementara. Silakan coba lagi dalam beberapa menit.')