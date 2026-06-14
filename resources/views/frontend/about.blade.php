@extends('layouts.app')

@section('content')

@include('frontend.partials.page-hero', [
    'eyebrow'     => 'OUR STORY',
    'title'       => 'About Bellevie Hotel',
    'subtitle'    => 'Luxury redefined — discover the story behind one of the world\'s finest hotel experiences.',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'About'],
    ],
])

<div style="padding:60px 0;">
    <div class="container">
        <p>About content here</p>
    </div>
</div>
@endsection
