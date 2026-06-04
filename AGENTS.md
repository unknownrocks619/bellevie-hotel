# Bellevie Hotel — AI Agent Context Guide

> Read this file at the start of every session. It tells you exactly what this project is, how it's structured, and the conventions to follow so you don't repeat work or break things.

---

## Project Overview

**Bellevie** is a full-stack hotel management CMS built with **Laravel 11**. It covers:
- Public-facing hotel website (rooms, blog, booking, contact, pages)
- Full admin panel (`/admin`) with CRUD for all entities
- Drag-and-drop **Page Builder** for the home page and CMS pages
- **CRM** for guests and bookings
- Cloudinary image management

**Stack:**
- PHP 8.2 / Laravel 11
- Blade templates (no Livewire, no Inertia, no Vue/React)
- Bootstrap 5 + Bootstrap Icons (`bi-*`)
- Cloudinary for image storage (`cloudinary-labs/cloudinary-laravel ^3.0`)
- MySQL database
- No build pipeline (Vite/Mix not used — CDN assets only)

**Admin URL:** `/admin` — protected by `admin.auth` middleware (`AdminAuth.php`)  
**Admin login:** `AuthController` → custom session-based auth (not Laravel's built-in Auth guard)

---

## Directory Structure

```
app/
  Http/
    Controllers/
      Admin/          — all admin CRUD controllers
      Frontend/       — public-facing page controllers
    Middleware/
      AdminAuth.php   — checks session('admin_user')
  Models/             — Eloquent models
  Services/
    CloudinaryService.php   — Cloudinary upload/delete
    ImageService.php        — wraps CloudinaryService, used by controllers
  Traits/
    HasImages.php     — polymorphic image relationship trait

database/
  migrations/         — numbered 000000–000021

resources/views/
  layouts/
    admin.blade.php   — admin shell with sidebar nav
    app.blade.php     — frontend shell
  admin/              — all admin views (CRUD)
  frontend/           — public pages
    blocks/           — Page Builder block partials (one per block type)
    builder-content.blade.php — renders builder_data sections on frontend
  components/
    image-picker.blade.php

routes/web.php        — all routes (frontend + admin)
```

---

## Models & Database

| Model | Table | Key Fields |
|---|---|---|
| User | users | name, email, password, is_admin |
| Room | rooms | room_type_id, name, slug, price_per_night, is_active, is_featured, builder_data |
| RoomType | room_types | name, slug |
| Amenity | amenities | name, icon, is_active |
| Booking | bookings | room_id, guest_id, check_in, check_out, status, total_price, cancellation_token |
| Guest | guests | first_name, last_name, email, phone, is_vip |
| GuestNote | guest_notes | guest_id, user_id, note, type |
| BlogPost | blog_posts | title, slug, content, user_id, blog_category_id, is_published, featured_image |
| BlogCategory | blog_categories | name, slug, description, is_active |
| Gallery | gallery | title, url, url_thumb, category, sort_order, is_active |
| Testimonial | testimonials | guest_name, guest_title, content, rating, is_featured, is_active, avatar |
| Page | pages | title, slug, content, use_builder, builder_data (JSON), is_published |
| Menu | menus | name, location, is_active |
| MenuItem | menu_items | menu_id, parent_id, title, url, sort_order, is_active |
| Setting | settings | key, value, group — accessed via `Setting::get('key', 'default')` / `Setting::set('key', 'value')` |
| SysSeo | sys_seo | page, title, description, og_image_url |
| Image | images | url, url_thumb, original_filename, cloudinary_public_id |
| ImageRelation | image_relations | polymorphic pivot |
| **Faq** | **faqs** | **title, description, category, is_active, sort_order** |

### Faq Model — Categories
```php
Faq::CATEGORIES = [
    'booking'    => 'Booking',
    'hotel'      => 'Hotel',
    'restaurant' => 'Restaurant',
    'conference' => 'Conference',
    'finance'    => 'Finance',
    'general'    => 'General',
]
```
Scopes: `scopeActive()`, `scopeByCategory()`  
Accessor: `$faq->category_label`

---

## Routes Reference

### Frontend
| Method | URI | Name | Controller |
|---|---|---|---|
| GET | / | home | HomeController@index |
| GET | /about | about | HomeController@about |
| GET | /contact | contact | ContactController@index |
| POST | /contact | contact.send | ContactController@send |
| GET | /rooms | rooms.index | RoomController@index |
| GET | /rooms/{room} | rooms.show | RoomController@show |
| GET | /booking | booking.create | BookingController@create |
| POST | /booking | booking.store | BookingController@store |
| GET | /blog | blog.index | BlogController@index |
| GET | /blog/{post} | blog.show | BlogController@show |
| GET | /blog/category/{category} | blog.category | BlogController@category |
| GET | /page/{slug} | page.show | PageController@show |

### Admin (all prefixed `/admin`, named `admin.*`, behind `admin.auth` middleware)

**Rooms & Types**
- `resource /room-types` → `RoomTypeController`
- `resource /rooms` → `RoomAdminController` + `POST /rooms/{room}/toggle-status`

**Bookings**
- Custom routes (index, calendar, export, show, status patch, destroy, create, store)

**Guests**
- index, show, edit, update, destroy, note

**Content**
- `resource /blog` → `BlogAdminController`
- `resource /blog-categories` → `BlogCategoryController`
- `resource /pages` → `PageAdminController`
- `resource /gallery` → `GalleryController` + `DELETE /gallery/bulk`
- `resource /testimonials` → `TestimonialController`
- `resource /faqs` → `FaqController`
- `GET /faqs/by-category` → `FaqController@byCategory` (JSON, used by page builder)
- `resource /menus` → `MenuController` (acts on `MenuItem` model)
- `resource /amenities` → `AmenityController`

**Page Builder**
- `GET /pages/{page}/builder` → `PageBuilderController@editPage`
- `POST /pages/{page}/builder/save` → `PageBuilderController@savePage`
- `GET /home/builder` → `PageBuilderController@editHome`
- `POST /home/builder/save` → `PageBuilderController@saveHome`

**Other**
- `resource /images` → `ImageController` (Cloudinary image library)
- `GET /pricing`, `POST /pricing/{room}/apply|reset|clear-cooldown` → `PricingOptimizerController`
- `GET /settings`, `POST /settings` → `SettingsController`
- `POST /seo/{seo}/remove-image` → `SeoController`

---

## Admin Sidebar Navigation (admin.blade.php)

Sections in order:
1. **Dashboard** — `admin.dashboard`
2. **Hotel** — Rooms, Room Types, Amenities
3. **Bookings** — Bookings, Calendar
4. **CRM** — Guests
5. **Content** — Blog, Blog Categories, Pages, Gallery, Testimonials, **FAQ**, Menus
6. **System** — Page Builder, Settings

---

## Page Builder

**Editor view:** `resources/views/admin/builder/editor.blade.php`  
Standalone HTML page (does NOT extend `layouts.admin`) — has its own Bootstrap/SortableJS CDN imports.

### Block Types (BLOCK_TYPES JS object)
| Key | Label | Icon |
|---|---|---|
| hero | Hero Banner | bi-image |
| hero-slider | Hero Image Slider | bi-collection-play |
| about | About Section | bi-building |
| why-choose | Why Choose Us | bi-star-fill |
| rooms | Rooms Grid | bi-door-open |
| testimonials | Testimonials | bi-chat-quote |
| gallery | Gallery | bi-images |
| text | Text Block | bi-file-text |
| cta | Call to Action | bi-megaphone |
| video | Video Block | bi-play-circle |
| floating-btn | Floating Button | bi-cursor-fill |
| contact | Contact Info | bi-geo-alt |
| **faq** | **FAQ Section** | **bi-question-circle** |
| divider | Divider | bi-dash-lg |

Each block type has: `label`, `icon`, `desc`, `defaults` (config object), `fields` (array), `preview(cfg)` function.

**Field types:** `text`, `number`, `textarea`, `richtext`, `select`, `toggle`, `color`, `range`, `image`, `feature-list`

**FAQ block config:**
```js
{
  title: 'Frequently Asked Questions',
  subtitle: '',
  category: 'all',   // all|booking|hotel|restaurant|conference|finance|general
  layout: 'expandable',  // expandable = accordion click-to-reveal | rows = always show title+description
  maxItems: '10',
  bgColor: '#ffffff',
}
```

### Frontend Block Rendering
Each block type has a corresponding Blade partial at:
`resources/views/frontend/blocks/{type}.blade.php`

These are rendered by `resources/views/frontend/builder-content.blade.php` which loops through `builder_data` sections.

> **TODO:** `frontend/blocks/faq.blade.php` still needs to be created to render the FAQ block on the public site. It should query `Faq::active()->byCategory($cfg['category'])->take($cfg['maxItems'])->get()` and render based on `$cfg['layout']`.

### How builder data is saved
- **Pages:** stored as `pages.builder_data` (JSON column), `pages.use_builder = true`
- **Home page:** stored as `settings.home_builder_data` via `Setting::set()`

---

## Design System

| Token | Value |
|---|---|
| Primary Gold | `#C9A227` |
| Dark Navy | `#0D1B2A` |
| Framework | Bootstrap 5 |
| Icons | Bootstrap Icons (`bi-*`) |

### Admin View Conventions
- All views `@extend('layouts.admin')`
- Section: `@section('page-title', '...')` and `@section('content')`
- Buttons: `<button class="btn text-white" style="background:#C9A227;border:none;">`
- Gold badge: `<span class="badge" style="background:#C9A22720;color:#C9A227;border:1px solid #C9A22740;">`
- Forms always include `@csrf`; PUT/DELETE use `@method('PUT')` / `@method('DELETE')`
- File uploads: `enctype="multipart/form-data"`
- Error display: `@error('field')<div class="invalid-feedback">{{ $message }}</div>@enderror`

---

## Services

### ImageService
Wraps Cloudinary. Used by controllers that handle image uploads.
```php
$this->images->upload($request->file('image'), 'folder-name');
// returns Image model
```

### CloudinaryService
Direct Cloudinary SDK wrapper. Used internally by ImageService.

### Setting Model
Key-value store for site configuration.
```php
Setting::get('hotel_name', 'Default');
Setting::set('key', 'value');
```

---

## Auth System

- Custom session-based auth (not Laravel's `Auth` facade)
- `AdminAuth` middleware checks `session('admin_user')`
- Login stores admin user data in session
- Middleware registered as `admin.auth` in `Kernel.php`

---

## Known Pending Work

- [ ] Seeders for FAQ sample data
- [ ] Any new Page Builder block needs a matching `frontend/blocks/{type}.blade.php`

---

## Session History Summary

| Session | Key Work |
|---|---|
| "Build hotel CRM website with Laravel" | Core CMS: rooms, bookings, blog, pages, gallery, testimonials, menus, guests, settings, pricing optimizer, SEO, image library, Cloudinary, blog categories, page builder (all blocks except FAQ) |
| "Build Laravel CMS with admin backend" | Earlier architecture session |
| Current | FAQ CRUD (model, controller, migration, views), FAQ sidebar nav, FAQ routes, FAQ page builder block, AGENTS.md |

---

## Quick Start for New Sessions

1. Read this file (`AGENTS.md`) first
2. Check `routes/web.php` to understand all available endpoints
3. Check `resources/views/layouts/admin.blade.php` for sidebar nav (add new sections here)
4. When adding a new content type: model → migration → controller → routes → views → sidebar link
5. When adding a new page builder block: add to `BLOCK_TYPES` in `editor.blade.php` AND create `frontend/blocks/{type}.blade.php`
6. Always follow the Design System (gold `#C9A227`, Bootstrap 5, Bootstrap Icons)
