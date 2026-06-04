<div style="background:linear-gradient(135deg,#0D1B2A 0%,#1a3a5c 100%);
            padding:120px 0 64px;text-align:center;color:#fff;">
    <div class="container">
        @if(!empty($config['eyebrow']))
        <p style="color:#C9A227;font-size:0.78rem;font-weight:700;
                  letter-spacing:.14em;text-transform:uppercase;margin-bottom:14px;">
            {{ $config['eyebrow'] }}
        </p>
        @endif

        @if(!empty($config['title']))
        <h1 style="font-family:'Playfair Display',Georgia,serif;
                   font-size:clamp(2rem,4vw,3rem);font-weight:700;
                   margin-bottom:16px;line-height:1.2;">
            {{ $config['title'] }}
        </h1>
        @endif

        @if(!empty($config['subtitle']))
        <p style="opacity:.8;font-size:1.05rem;max-width:520px;margin:0 auto;line-height:1.7;">
            {{ $config['subtitle'] }}
        </p>
        @endif
    </div>
</div>
