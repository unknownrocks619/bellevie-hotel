# Bellevie Hotel - Complete Laravel 11 Project

## Project Overview

A comprehensive hotel management and reservation system with integrated CRM, built with Laravel 11 and Bootstrap 5.

**Location:** `/sessions/compassionate-confident-mccarthy/mnt/bellevie/`

**Total Files:** 124

## Key Features

### 1. Frontend Website
- Luxury hotel website with responsive design
- Homepage with hero section and featured rooms
- Room listing and detail pages with availability checking
- Online booking system with real-time price calculation
- Blog publishing platform
- Contact form
- Dynamic pages system
- Guest testimonials carousel
- Gallery showcase

### 2. Admin CRM Dashboard
- Complete admin authentication system
- Dashboard with analytics and KPIs
- Room management (CRUD operations)
- Room type management
- Amenity management
- Booking management with calendar view
- Booking status tracking
- Guest CRM with VIP tier system
- Guest notes and history
- Blog management
- Page management
- Gallery management
- Testimonial management
- Menu management
- Settings management
- CSV export functionality

### 3. Database Structure
- 12 database migrations
- 15 Eloquent models
- Complete relationship mapping
- Guest tracking with VIP status
- Booking history and analytics

### 4. File Organization

#### Controllers (19 files)
- **Frontend (6):** Home, Rooms, Booking, Blog, Contact, Page
- **Admin (13):** Auth, Dashboard, Rooms, RoomTypes, Bookings, Guests, Blog, Gallery, Testimonials, Menus, Pages, Amenities, Settings

#### Models (15 files)
- User, Room, RoomType, Amenity, Guest, GuestNote
- Booking, BlogPost, BlogCategory
- Menu, MenuItem, Gallery, Testimonial, Page, Setting

#### Migrations (12 files)
- Users, Settings, Room Types, Amenities, Rooms
- Guests, Guest Notes, Bookings
- Blog Posts, Blog Categories, Menus, Gallery, Testimonials, Pages

#### Views (60 files)
- Admin panel views (47 files)
- Frontend views (11 files)
- Layout templates (2 files)

#### Services & Middleware
- CloudinaryService for image management
- AdminAuth middleware for authorization
- Additional authentication middlewares

## Installation & Setup

1. **Install Dependencies:**
   ```bash
   composer install
   ```

2. **Configure Environment:**
   ```bash
   cp .env.example .env
   ```

3. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

4. **Run Migrations & Seed:**
   ```bash
   php artisan migrate --seed
   ```

5. **Configure Cloudinary:**
   Update `.env` with your Cloudinary credentials

6. **Access Application:**
   - Frontend: `http://localhost/`
   - Admin: `http://localhost/admin/login`

## Default Credentials

- **Email:** admin@belleviehotel.com
- **Password:** admin123

## Technology Stack

- **Framework:** Laravel 11
- **Frontend:** Bootstrap 5, jQuery, Flatpickr
- **Database:** MySQL
- **Image Management:** Cloudinary
- **Charts:** Chart.js
- **Tables:** DataTables
- **Authentication:** Laravel Sanctum

## Key Routes

### Frontend
- `/` - Homepage
- `/rooms` - Room listing
- `/booking` - Booking system
- `/blog` - Blog listing
- `/contact` - Contact form
- `/page/{slug}` - Dynamic pages

### Admin
- `/admin/login` - Admin login
- `/admin/dashboard` - Dashboard
- `/admin/rooms` - Room management
- `/admin/bookings` - Booking management
- `/admin/guests` - Guest CRM
- `/admin/blog` - Blog management
- `/admin/settings` - Settings

## Configuration Files

- **composer.json** - PHP dependencies
- **.env.example** - Environment configuration template
- **bootstrap/app.php** - Application bootstrap
- **config/cloudinary.php** - Cloudinary configuration
- **routes/web.php** - Web routes
- **routes/console.php** - Console commands

## Database Tables

1. `users` - Admin users
2. `settings` - Application settings
3. `room_types` - Room type categories
4. `amenities` - Room amenities
5. `rooms` - Hotel rooms
6. `amenity_room` - Room-amenity pivot
7. `guests` - Guest information
8. `guest_notes` - Guest notes and history
9. `bookings` - Booking records
10. `blog_categories` - Blog categories
11. `blog_posts` - Blog articles
12. `menus` - Navigation menus
13. `menu_items` - Menu items
14. `galleries` - Gallery images
15. `testimonials` - Guest testimonials
16. `pages` - Static pages

## Features Breakdown

### Admin Dashboard
- Real-time occupancy rate
- Monthly revenue tracking
- Pending bookings alerts
- Check-in/check-out management
- Guest statistics
- VIP guest highlighting

### Guest CRM
- Full guest profiles
- Booking history
- Internal notes system
- VIP tier management (Regular, Silver, Gold, Platinum)
- Guest search and filtering
- Blacklist functionality

### Booking System
- Real-time availability checking
- Automatic price calculation with tax
- Guest information collection
- Booking confirmation emails
- Cancellation with token validation
- Booking reference generation

### Content Management
- Blog with categories and featured posts
- Dynamic page creation
- Gallery with Cloudinary integration
- Testimonial management
- Menu management for frontend

## Security Features

- CSRF protection
- Password encryption
- Admin authentication middleware
- Role-based access control
- Booking cancellation token validation

## Performance Optimization

- Query optimization with eager loading
- Caching for settings
- Cloudinary for image optimization
- Pagination for large datasets

## Future Enhancement Opportunities

- Payment gateway integration
- Email notifications
- SMS alerts
- Advanced reporting
- Multi-language support
- API development
- Mobile app integration

---

**Project Created:** 2026-03-02
**Framework Version:** Laravel 11
**PHP Version:** 8.2+
