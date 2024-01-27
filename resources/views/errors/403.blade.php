@extends('errors::unauthorized')

@section('title', __('Forbidden'))
@section('code', '403')

@if ($exception->getMessage() === config('auth.free_tier_resources_exceeded'))
    @section('message', "Error: free tier exceeded.")
@elseif ($exception->getMessage() === config('auth.generic_deny'))
    @section('message', "Error.")
@else
    @section('message', "Error.")
@endif
