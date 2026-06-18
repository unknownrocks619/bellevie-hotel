{{--
    Builder Content Renderer
    Loops through $sections and includes the matching block partial.
    Floating buttons are collected and rendered at the end (they're position:fixed).

    Usage: @include('frontend.builder-content', ['sections' => $data])
--}}
@php
    $floatingBtns = collect($sections ?? [])->where('type', 'floating-btn');
    $mainSections = collect($sections ?? [])->where('type', '!=', 'floating-btn');

@endphp

@foreach ($mainSections as $section)
    @php $config = $section['config'] ?? []; @endphp
    @if(!empty($config['_hidden'])) @continue @endif

    @switch($section['type'] ?? '')
        @case('hero')
            @include('frontend.blocks.hero', ['config' => $config])
        @break

        @case('hero-slider')
            @include('frontend.blocks.hero-slider', ['config' => $config])
        @break

        @case('about')
            @include('frontend.blocks.about', ['config' => $config])
        @break

        @case('split-content')
            @include('frontend.blocks.split-content', ['config' => $config])
        @break

        @case('why-choose')
            @include('frontend.blocks.why-choose', ['config' => $config])
        @break

        @case('rooms')
            @include('frontend.blocks.rooms', ['config' => $config])
        @break

        @case('testimonials')
            @include('frontend.blocks.testimonials', ['config' => $config])
        @break

        @case('gallery')
            @include('frontend.blocks.gallery', ['config' => $config])
        @break

        @case('text')
            @include('frontend.blocks.text', ['config' => $config])
        @break

        @case('cta')
            @include('frontend.blocks.cta', ['config' => $config])
        @break

        @case('video')
            @include('frontend.blocks.video', ['config' => $config])
        @break

        @case('contact')
            @include('frontend.blocks.contact', ['config' => $config])
        @break

        @case('divider')
            @include('frontend.blocks.divider', ['config' => $config])
        @break

        @case('embed')
            @include('frontend.blocks.embed', ['config' => $config])
        @break

        @case('contact-hero')
            @include('frontend.blocks.contact-hero', ['config' => $config])
        @break

        @case('contact-form')
            @include('frontend.blocks.contact-form', ['config' => $config])
        @break

        @case('contact-info')
            @include('frontend.blocks.contact-info', ['config' => $config])
        @break

        @case('contact-quick-links')
            @include('frontend.blocks.contact-quick-links', ['config' => $config])
        @break

        @case('parallax')
            @include('frontend.blocks.parallax', ['config' => $config])
        @break

        @case('faq')
            @include('frontend.blocks.faq', ['config' => $config])
        @break

        @case('columns')
            @include('frontend.blocks.columns', ['config' => $config])
        @break

        @default
            {{-- Unknown block type — silently skip --}}
    @endswitch
@endforeach

{{-- Floating buttons rendered at the end — position:fixed, outside the document flow --}}
@foreach ($floatingBtns as $section)
    @include('frontend.blocks.floating-btn', ['config' => $section['config'] ?? []])
@endforeach
