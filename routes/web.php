<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RoomController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomAdminController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\BlogAdminController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\PageAdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\PricingOptimizerController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\PageBuilderController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\FaqController;

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check');
Route::get('/booking/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/booking/cancel/{booking}/{token}', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{category}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/room-types', RoomTypeController::class);
        Route::resource('/rooms', RoomAdminController::class);
        Route::post('/rooms/{room}/toggle-status', [RoomAdminController::class, 'toggleStatus'])->name('rooms.toggle');
        Route::get('/bookings/create', [BookingAdminController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingAdminController::class, 'store'])->name('bookings.store');
        Route::get('/bookings', [BookingAdminController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/calendar', [BookingAdminController::class, 'calendar'])->name('bookings.calendar');
        Route::get('/bookings/export', [BookingAdminController::class, 'export'])->name('bookings.export');
        Route::get('/bookings/{booking}', [BookingAdminController::class, 'show'])->name('bookings.show');
        Route::patch('/bookings/{booking}/status', [BookingAdminController::class, 'updateStatus'])->name('bookings.status');
        Route::delete('/bookings/{booking}', [BookingAdminController::class, 'destroy'])->name('bookings.destroy');
        Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
        Route::get('/guests/{guest}', [GuestController::class, 'show'])->name('guests.show');
        Route::get('/guests/{guest}/edit', [GuestController::class, 'edit'])->name('guests.edit');
        Route::patch('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
        Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
        Route::post('/guests/{guest}/note', [GuestController::class, 'addNote'])->name('guests.note');
        Route::resource('/blog', BlogAdminController::class);
        Route::resource('/blog-categories', BlogCategoryController::class);
        // Menu routes — edit/update/destroy work on MenuItem (type hint resolves correctly)
        Route::post('/menus/reorder', [MenuController::class, 'reorder'])->name('menus.reorder');
        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
        Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        // SEO image removal
        Route::post('/seo/{seo}/remove-image', [SeoController::class, 'removeImage'])->name('seo.remove-image');
        // Image library
        Route::get('/images', [ImageController::class, 'index'])->name('images.index');
        Route::post('/images/save', [ImageController::class, 'save'])->name('images.save');
        Route::delete('/images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');
        Route::delete('/gallery/bulk', [GalleryController::class, 'bulkDestroy'])->name('gallery.bulkDestroy');
        Route::get('/gallery/cloudinary-library', [GalleryController::class, 'cloudinaryLibrary'])->name('gallery.cloudinaryLibrary');
        Route::post('/gallery/import', [GalleryController::class, 'importToGallery'])->name('gallery.import');
        Route::post('/gallery/reorder', [GalleryController::class, 'reorder'])->name('gallery.reorder');
        Route::resource('/gallery', GalleryController::class);
        Route::resource('/testimonials', TestimonialController::class);
        Route::resource('/pages', PageAdminController::class);
        // Page Builder routes
        Route::get('/pages/{page}/builder', [PageBuilderController::class, 'editPage'])->name('builder.editPage');
        Route::post('/pages/{page}/builder/save', [PageBuilderController::class, 'savePage'])->name('builder.savePage');
        Route::get('/home/builder', [PageBuilderController::class, 'editHome'])->name('builder.editHome');
        Route::post('/home/builder/save', [PageBuilderController::class, 'saveHome'])->name('builder.saveHome');
        Route::get('/contact/builder', [PageBuilderController::class, 'editContact'])->name('builder.editContact');
        Route::post('/contact/builder/save', [PageBuilderController::class, 'saveContact'])->name('builder.saveContact');
        Route::get('/faqs/by-category', [FaqController::class, 'byCategory'])->name('faqs.by-category');
        Route::resource('/faqs', FaqController::class);
        Route::resource('/amenities', AmenityController::class);
        Route::get('/pricing', [PricingOptimizerController::class, 'index'])->name('pricing.index');
        Route::post('/pricing/{room}/apply', [PricingOptimizerController::class, 'apply'])->name('pricing.apply');
        Route::post('/pricing/{room}/reset', [PricingOptimizerController::class, 'reset'])->name('pricing.reset');
        Route::post('/pricing/{room}/clear-cooldown', [PricingOptimizerController::class, 'clearCooldown'])->name('pricing.clear-cooldown');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});
