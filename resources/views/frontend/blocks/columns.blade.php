@php
    $bgColor    = $config['bgColor']       ?? 'transparent';
    $paddingTop = (int)($config['paddingTop']    ?? 60);
    $paddingBot = (int)($config['paddingBottom'] ?? 60);
    $gutter     = $config['gutter']        ?? '4';
    $contained  = ($config['contained'] ?? true) ? 'container' : 'container-fluid';
    $columns    = $config['columns']       ?? [];
@endphp

<section style="background:{{ $bgColor }};padding:{{ $paddingTop }}px 0 {{ $paddingBot }}px;">
    <div class="{{ $contained }}">
        <div class="row g-{{ $gutter }}">
            @foreach($columns as $col)
            @php
                // Build Bootstrap column classes
                $colSm  = $col['colSm'] ?? '12';
                $colMd  = $col['colMd'] ?? '12';
                $colLg  = $col['colLg'] ?? '6';
                $colCls = "col-sm-{$colSm} col-md-{$colMd} col-lg-{$colLg}";

                // Padding utilities (pt-N, pb-N, ps-N, pe-N)
                foreach (['pt','pb','ps','pe'] as $sp) {
                    $v = $col[$sp] ?? '0';
                    if ($v !== '0' && $v !== '') $colCls .= " {$sp}-{$v}";
                }
                // Margin utilities (mt-N, mb-N)
                foreach (['mt','mb'] as $sp) {
                    $v = $col[$sp] ?? '0';
                    if ($v !== '0' && $v !== '') $colCls .= " {$sp}-{$v}";
                }
            @endphp
            <div class="{{ $colCls }}">
                @foreach($col['blocks'] ?? [] as $child)
                @php
                    // Pass _nested=true so child blocks skip their outer section/container wrappers
                    $child['config']['_nested'] = true;
                @endphp
                @include('frontend.builder-content', ['sections' => [$child]])
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</section>
