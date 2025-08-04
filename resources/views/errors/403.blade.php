@extends('errors::custom')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __('Akses Ditolak'))
@section('description', 'Anda tidak memiliki hak akses untuk melihat halaman ini. Hubungi administrator jika Anda merasa ini adalah kesalahan.')