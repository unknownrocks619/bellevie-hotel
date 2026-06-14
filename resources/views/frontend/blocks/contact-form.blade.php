@php $nested = $config['_nested'] ?? false; @endphp
@if(!$nested)<section style="padding:60px 0;background:#f8f9fa;"><div class="container"><div class="row justify-content-center"><div class="col-lg-8">@endif
                <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                    <div class="card-body p-4 p-md-5">

                        @if(!empty($config['title']))
                        <h2 style="font-family:'Playfair Display',Georgia,serif;
                                   font-size:1.6rem;color:#0D1B2A;margin-bottom:6px;">
                            {{ $config['title'] }}
                        </h2>
                        @endif

                        @if(!empty($config['description']))
                        <p class="text-muted mb-4" style="font-size:.92rem;">
                            {{ $config['description'] }}
                        </p>
                        @endif

                        @if(session('contact_success'))
                        <div class="d-flex align-items-center gap-2 mb-4 p-3"
                             style="border-left:4px solid #C9A227;background:#fdf8ea;
                                    border-radius:6px;color:#5a4500;">
                            <i class="bi bi-check-circle-fill" style="color:#C9A227;font-size:1.1rem;flex-shrink:0;"></i>
                            <div>{{ session('contact_success') }}</div>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('contact.send') }}" method="POST" novalidate>
                            @csrf

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                                        Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder="John Smith" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                                        Email Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="john@example.com" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="+1 234 567 8900" required>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                                        Subject <span class="text-danger">*</span>
                                    </label>
                                    <select name="subject"
                                            class="form-select @error('subject') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('subject') ? '' : 'selected' }}>
                                            Choose a subject…
                                        </option>
                                        @foreach(['Room Reservation','General Enquiry','Restaurant Booking',
                                                  'Conference & Events','Feedback','Other'] as $opt)
                                        <option value="{{ $opt }}" {{ old('subject') == $opt ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="font-size:.85rem;">
                                    Message <span class="text-danger">*</span>
                                </label>
                                <textarea name="message" rows="6"
                                          class="form-control @error('message') is-invalid @enderror"
                                          placeholder="Tell us how we can help you…"
                                          required>{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn w-100 py-3 fw-semibold"
                                    style="background:#C9A227;color:#fff;border:none;border-radius:6px;
                                           font-size:1rem;letter-spacing:.04em;transition:background .2s;"
                                    onmouseover="this.style.background='#b08c20'"
                                    onmouseout="this.style.background='#C9A227'">
                                <i class="bi bi-send me-2"></i>Send Message
                            </button>
                        </form>

                    </div>
                </div>
@if(!$nested)</div></div></div></section>@endif
