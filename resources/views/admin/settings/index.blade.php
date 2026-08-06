@extends('layouts.admin')
@section('page-title', 'Settings')
@section('content')
    <div class="card">
        <div class="card-header">Hotel Settings</div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" role="tablist" id="settingsTabs">
                <li class="nav-item px-1" role="presentation">
                    <button class="nav-link px-3 text-dark active" id="general-tab" data-bs-toggle="tab"
                        data-bs-target="#general" type="button" role="tab">General</button>
                </li>
                <li class="nav-item px-1" role="presentation">
                    <button class="nav-link px-3 text-dark" id="appearance-tab" data-bs-toggle="tab"
                        data-bs-target="#appearance" type="button" role="tab">Appearance</button>
                </li>
                <li class="nav-item px-1" role="presentation">
                    <button class="nav-link px-3 text-dark" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing"
                        type="button" role="tab">Pricing</button>
                </li>
                <li class="nav-item px-1" role="presentation">
                    <button class="nav-link px-3 text-dark" id="email-tab" data-bs-toggle="tab" data-bs-target="#email"
                        type="button" role="tab">Email / SMTP</button>
                </li>
                <li class="nav-item px-1" role="presentation">
                    <button class="nav-link px-3 text-dark" id="social-tab" data-bs-toggle="tab" data-bs-target="#social"
                        type="button" role="tab">Social Media</button>
                </li>
                <li class="nav-item px-1" role="presentation">
                    <button class="nav-link px-3 text-dark" id="security-tab" data-bs-toggle="tab" data-bs-target="#security"
                        type="button" role="tab">Security</button>
                </li>
            </ul>

            <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
                @csrf
                <div class="tab-content">
                    <!-- General Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Hotel Name</label>
                            <input type="text" name="hotel_name"
                                class="form-control @error('hotel_name') is-invalid @enderror"
                                value="{{ old('hotel_name', $settings['hotel_name'] ?? '') }}" required>
                            @error('hotel_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hotel Tagline</label>
                            <input type="text" name="hotel_tagline"
                                class="form-control @error('hotel_tagline') is-invalid @enderror"
                                value="{{ old('hotel_tagline', $settings['hotel_tagline'] ?? '') }}"
                                placeholder="e.g., Luxury Beachfront Resort">
                            @error('hotel_tagline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hotel Description</label>
                            <textarea name="hotel_description" class="form-control @error('hotel_description') is-invalid @enderror" rows="4"
                                placeholder="Brief description of your hotel...">{{ old('hotel_description', $settings['hotel_description'] ?? '') }}</textarea>
                            @error('hotel_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Hotel Email</label>
                                    <input type="email" name="hotel_email"
                                        class="form-control @error('hotel_email') is-invalid @enderror"
                                        value="{{ old('hotel_email', $settings['hotel_email'] ?? '') }}" required>
                                    @error('hotel_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Hotel Phone</label>
                                    <input type="tel" name="hotel_phone"
                                        class="form-control @error('hotel_phone') is-invalid @enderror"
                                        value="{{ old('hotel_phone', $settings['hotel_phone'] ?? '') }}">
                                    @error('hotel_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hotel Address</label>
                            <input type="text" name="hotel_address"
                                class="form-control @error('hotel_address') is-invalid @enderror"
                                value="{{ old('hotel_address', $settings['hotel_address'] ?? '') }}">
                            @error('hotel_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Hotel City</label>
                                    <input type="text" name="hotel_city"
                                        class="form-control @error('hotel_city') is-invalid @enderror"
                                        value="{{ old('hotel_city', $settings['hotel_city'] ?? '') }}">
                                    @error('hotel_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Hotel Country</label>
                                    <input type="text" name="hotel_country"
                                        class="form-control @error('hotel_country') is-invalid @enderror"
                                        value="{{ old('hotel_country', $settings['hotel_country'] ?? '') }}">
                                    @error('hotel_country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Google Maps Embed Code</label>
                            <textarea name="map_embed" class="form-control" rows="4"
                                placeholder='Paste the full &lt;iframe ...&gt; embed code from Google Maps here'>{{ old('map_embed', $settings['map_embed'] ?? '') }}</textarea>
                            <small class="text-muted">In Google Maps → Share → Embed a map → Copy HTML. This appears on the Contact page.</small>
                        </div>

                        <button type="submit" class="btn btn-primary" style="background:#C9A227;border:none;">Save
                            General Settings</button>
                    </div>

                    <!-- Appearance Tab -->
                    <div class="tab-pane fade" id="appearance" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Logo Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="site_logo_type" value="text"
                                    id="logo_text"
                                    {{ ($settings['site_logo_type'] ?? 'text') == 'text' ? 'checked' : '' }}>
                                <label class="form-check-label" for="logo_text">Text Logo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="site_logo_type" value="image"
                                    id="logo_image" {{ ($settings['site_logo_type'] ?? '') == 'image' ? 'checked' : '' }}>
                                <label class="form-check-label" for="logo_image">Image Logo</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-image-picker name="logo_image_id" label="Logo Image" type="logo" folder="bellevie_hotel/settings" :value="$logoImage ?? null" />
                            @error('logo_image_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <small class="text-muted">Select or upload a logo image (leave empty to keep current)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Primary Color</label>
                            <div class="input-group">
                                <input type="color" name="primary_color"
                                    class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                                    value="{{ old('primary_color', $settings['primary_color'] ?? '#C9A227') }}"
                                    style="max-width: 60px;">
                                <input type="text" class="form-control @error('primary_color') is-invalid @enderror"
                                    value="{{ old('primary_color', $settings['primary_color'] ?? '#C9A227') }}"
                                    id="colorHex" placeholder="#C9A227" readonly>
                                @error('primary_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Default gold color: #C9A227</small>
                        </div>

                        <div class="alert alert-info">
                            <strong>Website Font Preview:</strong> The website uses system fonts for optimal performance.
                        </div>

                        {{-- ── Featured Rooms Display ── --}}
                        <hr class="my-4">
                        <h6 class="fw-bold mb-3" style="color:#0D1B2A;">Featured Rooms Display</h6>

                        <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:10px;background:#fafafa;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="fw-semibold mb-1" style="font-size:.9rem;color:#0D1B2A;">
                                        Show Price on Featured Room Cards
                                    </p>
                                    <p class="text-muted mb-0" style="font-size:.8rem;">
                                        Controls whether the room price is visible on the homepage featured rooms section.
                                        When off, only the room name and details are displayed.
                                    </p>
                                </div>
                                <div class="form-check form-switch ms-4" style="flex-shrink:0;">
                                    <input class="form-check-input" type="checkbox"
                                           name="show_featured_room_price" value="1"
                                           id="show_featured_room_price"
                                           style="width:2.4em;height:1.3em;cursor:pointer;"
                                           {{ (\App\Models\Setting::get('show_featured_room_price', '1') === '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold ms-1" for="show_featured_room_price"
                                           style="font-size:.82rem;color:#C9A227;cursor:pointer;">
                                        On
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="background:#C9A227;border:none;">Save
                            Appearance Settings</button>
                    </div>

                    <!-- Pricing Tab -->
                    <div class="tab-pane fade" id="pricing" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Currency</label>
                                    <input type="text" name="currency"
                                        class="form-control @error('currency') is-invalid @enderror"
                                        value="{{ old('currency', $settings['currency'] ?? 'USD') }}"
                                        placeholder="e.g., USD">
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Currency Symbol</label>
                                    <input type="text" name="currency_symbol"
                                        class="form-control @error('currency_symbol') is-invalid @enderror"
                                        value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}"
                                        placeholder="e.g., $">
                                    @error('currency_symbol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" step="0.01" name="tax_rate"
                                class="form-control @error('tax_rate') is-invalid @enderror"
                                value="{{ old('tax_rate', $settings['tax_rate'] ?? '10') }}">
                            @error('tax_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Check-in Time</label>
                                    <input type="time" name="check_in_time"
                                        class="form-control @error('check_in_time') is-invalid @enderror"
                                        value="{{ old('check_in_time', $settings['check_in_time'] ?? '15:00') }}">
                                    @error('check_in_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Check-out Time</label>
                                    <input type="time" name="check_out_time"
                                        class="form-control @error('check_out_time') is-invalid @enderror"
                                        value="{{ old('check_out_time', $settings['check_out_time'] ?? '11:00') }}">
                                    @error('check_out_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="background:#C9A227;border:none;">Save
                            Pricing Settings</button>
                    </div>

                    <!-- Email / SMTP Tab -->
                    <div class="tab-pane fade" id="email" role="tabpanel">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i> These settings configure transactional emails (booking
                            confirmations, etc.)
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Booking Enquiry Email</label>
                            <input type="email" name="booking_enquiry_email"
                                class="form-control @error('booking_enquiry_email') is-invalid @enderror"
                                value="{{ old('booking_enquiry_email', $settings['booking_enquiry_email'] ?? '') }}"
                                placeholder="Where to send booking inquiries">
                            @error('booking_enquiry_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mail Host</label>
                                    <input type="text" name="mail_host"
                                        class="form-control @error('mail_host') is-invalid @enderror"
                                        value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                                        placeholder="e.g., smtp.mailtrap.io">
                                    @error('mail_host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mail Port</label>
                                    <input type="number" name="mail_port"
                                        class="form-control @error('mail_port') is-invalid @enderror"
                                        value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}">
                                    @error('mail_port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mail Username</label>
                                    <input type="text" name="mail_username"
                                        class="form-control @error('mail_username') is-invalid @enderror"
                                        value="{{ old('mail_username', $settings['mail_username'] ?? '') }}">
                                    @error('mail_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mail Password</label>
                                    <input type="password" name="mail_password"
                                        class="form-control @error('mail_password') is-invalid @enderror"
                                        value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                                        placeholder="Leave empty to keep current">
                                    @error('mail_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mail From Name</label>
                                    <input type="text" name="mail_from_name"
                                        class="form-control @error('mail_from_name') is-invalid @enderror"
                                        value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}"
                                        placeholder="e.g., Bellevie Hotel">
                                    @error('mail_from_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mail From Address</label>
                                    <input type="email" name="mail_from_address"
                                        class="form-control @error('mail_from_address') is-invalid @enderror"
                                        value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                                        placeholder="e.g., noreply@bellevie.com">
                                    @error('mail_from_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="background:#C9A227;border:none;">Save Email
                            Settings</button>
                    </div>

                    <!-- Social Media Tab -->
                    <div class="tab-pane fade" id="social" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Facebook URL</label>
                            <input type="url" name="facebook_url"
                                class="form-control @error('facebook_url') is-invalid @enderror"
                                value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}"
                                placeholder="https://facebook.com/belleviehotel">
                            @error('facebook_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instagram URL</label>
                            <input type="url" name="instagram_url"
                                class="form-control @error('instagram_url') is-invalid @enderror"
                                value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}"
                                placeholder="https://instagram.com/belleviehotel">
                            @error('instagram_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Twitter/X URL</label>
                            <input type="url" name="twitter_url"
                                class="form-control @error('twitter_url') is-invalid @enderror"
                                value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}"
                                placeholder="https://twitter.com/belleviehotel">
                            @error('twitter_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary" style="background:#C9A227;border:none;">Save
                            Social Media Settings</button>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-shield-lock"></i>
                            Protect the Booking, Contact and Admin Login forms from bots with Google reCAPTCHA
                            (the "I'm not a robot" checkbox).
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox"
                                   name="recaptcha_enabled" value="1" id="recaptcha_enabled"
                                   style="width:2.4em;height:1.3em;cursor:pointer;"
                                   {{ (old('recaptcha_enabled', \App\Models\Setting::get('recaptcha_enabled', '0')) == '1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold ms-1" for="recaptcha_enabled">
                                Enable Google reCAPTCHA
                            </label>
                        </div>

                        <div id="recaptchaFields"
                             style="{{ (\App\Models\Setting::get('recaptcha_enabled', '0') == '1') ? '' : 'display:none;' }}">
                            <div class="mb-3">
                                <label class="form-label">Site Key (Public Key)</label>
                                <input type="text" name="recaptcha_site_key"
                                    class="form-control @error('recaptcha_site_key') is-invalid @enderror"
                                    value="{{ old('recaptcha_site_key', $settings['recaptcha_site_key'] ?? '') }}"
                                    placeholder="6Lc...">
                                @error('recaptcha_site_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Secret Key (Client Secret)</label>
                                <input type="password" name="recaptcha_secret_key"
                                    class="form-control @error('recaptcha_secret_key') is-invalid @enderror"
                                    value="{{ old('recaptcha_secret_key', $settings['recaptcha_secret_key'] ?? '') }}"
                                    placeholder="6Lc...">
                                @error('recaptcha_secret_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Get your keys from the
                                    <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin Console</a>
                                    — choose reCAPTCHA v2, "I'm not a robot" Checkbox.
                                </small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="background:#C9A227;border:none;">Save
                            Security Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Remember active tab based on URL hash
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            if (hash) {
                const tabButton = document.querySelector(`button[data-bs-target="${hash}"]`);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }

            // Update URL hash when tab changes
            document.getElementById('settingsTabs').addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('data-bs-target');
                window.location.hash = target;
            });

            // Sync color input with hex text
            const colorInput = document.querySelector('input[name="primary_color"][type="color"]');
            const hexInput = document.getElementById('colorHex');
            if (colorInput && hexInput) {
                colorInput.addEventListener('change', function() {
                    hexInput.value = this.value;
                });
            }

            // Show/hide reCAPTCHA key fields based on the enable checkbox
            const recaptchaCheckbox = document.getElementById('recaptcha_enabled');
            const recaptchaFields = document.getElementById('recaptchaFields');
            if (recaptchaCheckbox && recaptchaFields) {
                recaptchaCheckbox.addEventListener('change', function() {
                    recaptchaFields.style.display = this.checked ? '' : 'none';
                });
            }
        });
    </script>
@endsection
