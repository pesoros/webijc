@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')


@section('content')
    <div class="max-w-sm m-8">
        <div class="text-black text-5xl md:text-15xl font-black">
            <img src="{{asset('public/backEnd/img/404.png')}}" alt="" class="img img-fluid"></div>

        <div class="w-16 h-1 bg-purple-light my-3 md:my-6"></div>

        <p class="text-grey-darker text-2xl md:text-3xl font-light mb-8 leading-normal text-white" style="color: #fff; !important;">

            {{ __('Sorry, you are making too many requests to our servers.')}}
        </p>

        <a href="{{url('/')}}">
            <button
                class="primary-btn fix-gr-bg">
                Go Home
            </button>
        </a>
        <a href="{{ URL::previous() }}">
            <button
                class="primary-btn fix-gr-bg tr-bg">
                Go Back
            </button>
        </a>
    </div>
@endsection
