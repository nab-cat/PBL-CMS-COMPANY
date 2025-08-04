@extends('errors::custom')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Tidak Diizinkan'))
@section('description', 'Anda tidak memiliki izin untuk mengakses halaman ini. Silakan login terlebih dahulu atau hubungi administrator.')