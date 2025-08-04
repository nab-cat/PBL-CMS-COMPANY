@extends('errors::custom')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Kesalahan Server'))
@section('description', 'Terjadi kesalahan internal pada server. Tim kami telah diberitahu dan sedang menangani masalah ini. Silakan coba lagi nanti.')