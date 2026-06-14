{{--
    Shared dark hero / page header banner.

    Usage:
    @include('frontend.partials.page-hero', [
        'eyebrow'     => 'OPTIONAL UPPERCASE LABEL',   // null to hide
        'title'       => 'Page Title',
        'subtitle'    => 'Optional subtitle text',      // null to hide
        'breadcrumbs' => [                              // array of crumbs
            ['label' => 'Home',  'url' => route('home')],
            ['label' => 'Blog',  'url' => route('blog.index')],
            ['label' => 'Post Title'],                  // last item — no url
        ],
        'right'       => null,  // optional raw HTML to render on the right side
        'minHeight'   => '300px',  // optional override
    ])
--}}
@php
    $heroEyebrow   = $eyebrow   ?? null;
    $heroTitle     = $title     ?? '';
    $heroSubtitle  = $subtitle  ?? null;
    $heroCrumbs    = $breadcrumbs ?? [];
    $heroRight     = $right     ?? null;
    $heroMinHeight = $minHeight ?? '320px';
@endphp

<div style="position:relative;min-height:{{ $heroMinHeight }};display:flex;align-items:flex-end;
            background:linear-gradient(160deg,#0D1B2A 0%,#1a3a5c 60%,#0d2235 100%);
            overflow:hidden;">

    {{-- Decorative diagonal pattern --}}
    <div style="position:absolute;inset:0;opacity:.035;pointer-events:none;
                background-image:repeating-linear-gradient(45deg,#C9A227 0,#C9A227 1px,transparent 0,transparent 50%);
                background-size:22px 22px;"></div>

    {{-- Gold top accent line --}}
    <div style="position:absolute;top:0;left:0;right:0;height:3px;
                background:linear-gradient(90deg,transparent 0%,#C9A227 40%,#C9A227 60%,transparent 100%);"></div>

    <div class="container"
         style="position:relative;z-index:2;padding-top:110px;padding-bottom:48px;">

        {{-- Breadcrumb --}}
        @if(!empty($heroCrumbs))
        <nav aria-label="breadcrumb" style="margin-bottom:16px;">
            <ol style="list-style:none;display:flex;align-items:center;flex-wrap:wrap;
                       gap:5px;padding:0;margin:0;font-size:.78rem;">
                @foreach($heroCrumbs as $i => $crumb)
                @if($i > 0)
                <li style="color:rgba(255,255,255,.25);">›</li>
                @endif
                @if(!empty($crumb['url']))
                <li>
                    <a href="{{ $crumb['url'] }}"
                       style="color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;"
                       onmouseover="this.style.color='#C9A227'"
                       onmouseout="this.style.color='rgba(255,255,255,.5)'">
                        @if($i === 0)<i class="bi bi-house" style="margin-right:3px;"></i>@endif
                        {{ $crumb['label'] }}
                    </a>
                </li>
                @else
                <li style="color:rgba(255,255,255,.75);">{{ $crumb['label'] }}</li>
                @endif
                @endforeach
            </ol>
        </nav>
        @endif

        {{-- Title block + optional right slot --}}
        <div class="d-flex flex-column flex-md-row align-items-md-flex-end justify-content-between gap-3">
            <div>
                @if($heroEyebrow)
                <p style="color:#C9A227;font-size:.7rem;font-weight:700;letter-spacing:.18em;
                           text-transform:uppercase;margin:0 0 8px;">{{ $heroEyebrow }}</p>
                @endif

                <h1 style="font-family:'Playfair Display',Georgia,serif;color:#fff;
                            font-size:clamp(1.8rem,3.8vw,2.8rem);font-weight:700;
                            margin:0 0 6px;line-height:1.2;">
                    {!! $heroTitle !!}
                </h1>

                @if($heroSubtitle)
                <p style="color:rgba(255,255,255,.55);margin:0;font-size:.9rem;
                           max-width:540px;line-height:1.6;">{{ $heroSubtitle }}</p>
                @endif
            </div>

            @if($heroRight)
            <div style="flex-shrink:0;">{!! $heroRight !!}</div>
            @endif
        </div>

    </div>
</div>
