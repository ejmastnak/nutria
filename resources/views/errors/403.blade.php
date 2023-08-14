@extends('errors::unauthorized')

@section('title', __('Forbidden'))
@section('code', '403')

@if ($exception->getMessage() === config('auth.free_tier_resources_exceeded'))
    @section('message', "Some logic free tier resources exceeded.")
@elseif ($exception->getMessage() === config('auth.generic_deny'))
    @section('message', "Some logic about generic deny.")
@else
    @section('message', "Some fallback error message.")
@endif
