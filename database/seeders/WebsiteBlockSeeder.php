<?php

namespace Database\Seeders;

use App\Models\WebsiteBlock;
use Illuminate\Database\Seeder;

class WebsiteBlockSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            [
                'name' => 'Hero Banner',
                'slug' => 'hero-banner',
                'category' => 'Layout',
                'html_template' => '
                    <section class="position-relative py-5 d-flex align-items-center justify-content-center" style="background-image: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url(\'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?q=80&w=1200\'); background-size: cover; background-position: center; color: white; min-height: 500px;">
                        <div class="container py-5">
                            <div class="row justify-content-center">
                                <div class="col-lg-9 p-5 rounded-4 shadow-lg text-center" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.15);">
                                    <h1 class="display-4 fw-bold mb-3 text-white text-shadow">Welcome to Our School</h1>
                                    <p class="lead mb-4 text-white-50">Empowering minds, shaping futures, and building leaders of tomorrow with premium standard education.</p>
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="#admissions" class="btn btn-warning btn-lg px-4 fw-bold text-dark shadow-sm">Apply Online</a>
                                        <a href="#about" class="btn btn-outline-light btn-lg px-4">Learn More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                ',
                'is_dynamic' => false,
                'dynamic_source' => null,
                'display_order' => 1,
            ],
            [
                'name' => 'About Us Section',
                'slug' => 'about-us',
                'category' => 'Content',
                'html_template' => '
                    <section class="py-6 bg-white" id="about">
                        <div class="container">
                            <div class="row align-items-center g-5">
                                <div class="col-lg-6">
                                    <span class="badge bg-light text-primary border px-3 py-2 mb-3 fw-bold text-uppercase tracking-wider">About Us</span>
                                    <h2 class="fw-bold text-dark mb-4 display-5">Shaping a Better Tomorrow</h2>
                                    <p class="text-muted mb-4 lead">Founded with a vision to provide GES-compliant standard holistic education, our school offers a warm, safe, and academically stimulating learning ecosystem.</p>
                                    <p class="text-muted mb-4">We focus on intellectual growth, physical training, and moral discipline. Our modern infrastructure, specialized labs, and passionate teaching staff provide everything needed to foster lifelong learning and excellence.</p>
                                    <div class="d-flex align-items-center gap-4 mt-4">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-shield-check text-success fs-3 me-2"></i>
                                            <span class="fw-semibold text-dark">GES Certified</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-patch-check text-primary fs-3 me-2"></i>
                                            <span class="fw-semibold text-dark">Premium Labs</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="position-relative">
                                        <div class="position-absolute bg-warning rounded-3 shadow-lg" style="width: 100%; height: 100%; top: 15px; left: 15px; z-index: 1;"></div>
                                        <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?q=80&w=800" class="img-fluid rounded-3 shadow border position-relative" alt="About our school" style="z-index: 2;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                ',
                'is_dynamic' => false,
                'dynamic_source' => null,
                'display_order' => 2,
            ],
            [
                'name' => 'Statistics Bar',
                'slug' => 'statistics-bar',
                'category' => 'Content',
                'html_template' => '
                    <section class="py-5 text-white text-center" style="background: linear-gradient(135deg, var(--primary-color) 0%, #0d1e3d 100%);">
                        <div class="container">
                            <div class="row g-4">
                                <div class="col-md-3 col-6">
                                    <div class="p-4 rounded-3" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                        <h3 class="display-5 fw-bold mb-1 text-warning">500+</h3>
                                        <div class="small text-uppercase opacity-75">Active Students</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-4 rounded-3" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                        <h3 class="display-5 fw-bold mb-1 text-warning">40+</h3>
                                        <div class="small text-uppercase opacity-75">Professional Staff</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-4 rounded-3" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                        <h3 class="display-5 fw-bold mb-1 text-warning">15+</h3>
                                        <div class="small text-uppercase opacity-75">Specializations</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-4 rounded-3" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                        <h3 class="display-5 fw-bold mb-1 text-warning">100%</h3>
                                        <div class="small text-uppercase opacity-75">BECE/WASSCE Pass</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                ',
                'is_dynamic' => false,
                'dynamic_source' => null,
                'display_order' => 3,
            ],
            [
                'name' => 'CTA Banner',
                'slug' => 'cta-banner',
                'category' => 'Content',
                'html_template' => '
                    <section class="py-5 text-dark text-center shadow-inner position-relative overflow-hidden" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;">
                        <div class="container py-3">
                            <h2 class="fw-bold mb-2 text-white text-shadow-sm">Admissions are Open for the Current Term!</h2>
                            <p class="lead mb-4 text-white-50">Enroll your child today to experience premium learning tailored to standard curricula.</p>
                            <a href="/admissions/apply" target="_blank" class="btn btn-dark btn-lg px-5 py-3 rounded-pill fw-bold shadow">Online Admissions Form <i class="bi bi-arrow-right ms-2"></i></a>
                        </div>
                    </section>
                ',
                'is_dynamic' => false,
                'dynamic_source' => null,
                'display_order' => 4,
            ],
            [
                'name' => 'Latest News Feed (Dynamic)',
                'slug' => 'dynamic-news',
                'category' => 'Dynamic',
                'html_template' => '
                    <section class="py-6 bg-light" id="news">
                        <div class="container">
                            <div class="text-center mb-5">
                                <span class="badge bg-light text-primary border px-3 py-2 mb-3 fw-bold text-uppercase">Announcements</span>
                                <h2 class="fw-bold text-dark display-6">School News & Updates</h2>
                            </div>
                            <div class="row g-4" id="dynamic-news-container">
                                <div class="col-12 text-center text-muted py-4">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Loading announcements...
                                </div>
                            </div>
                        </div>
                    </section>
                ',
                'is_dynamic' => true,
                'dynamic_source' => 'news',
                'display_order' => 5,
            ],
            [
                'name' => 'Upcoming Events List (Dynamic)',
                'slug' => 'dynamic-events',
                'category' => 'Dynamic',
                'html_template' => '
                    <section class="py-6 bg-white" id="events">
                        <div class="container">
                            <div class="text-center mb-5">
                                <span class="badge bg-light text-primary border px-3 py-2 mb-3 fw-bold text-uppercase">Schedules</span>
                                <h2 class="fw-bold text-dark display-6">Upcoming School Events</h2>
                            </div>
                            <div class="row g-4" id="dynamic-events-container">
                                <div class="col-12 text-center text-muted py-4">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Loading event schedules...
                                </div>
                            </div>
                        </div>
                    </section>
                ',
                'is_dynamic' => true,
                'dynamic_source' => 'events',
                'display_order' => 6,
            ],
            [
                'name' => 'Staff Directory (Dynamic)',
                'slug' => 'dynamic-staff',
                'category' => 'Dynamic',
                'html_template' => '
                    <section class="py-6 bg-light" id="staff">
                        <div class="container">
                            <div class="text-center mb-5">
                                <span class="badge bg-light text-primary border px-3 py-2 mb-3 fw-bold text-uppercase">Faculty</span>
                                <h2 class="fw-bold text-dark display-6">Our Management & Instructors</h2>
                            </div>
                            <div class="row g-4 justify-content-center" id="dynamic-staff-container">
                                <div class="col-12 text-center text-muted py-4">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Loading faculty directory...
                                </div>
                            </div>
                        </div>
                    </section>
                ',
                'is_dynamic' => true,
                'dynamic_source' => 'staff',
                'display_order' => 7,
            ],
            [
                'name' => 'Photo Gallery Feed (Dynamic)',
                'slug' => 'dynamic-gallery',
                'category' => 'Dynamic',
                'html_template' => '
                    <section class="py-6 bg-white" id="gallery">
                        <div class="container">
                            <div class="text-center mb-5">
                                <span class="badge bg-light text-primary border px-3 py-2 mb-3 fw-bold text-uppercase">Moments</span>
                                <h2 class="fw-bold text-dark display-6">School Life Gallery</h2>
                            </div>
                            <div class="row g-4" id="dynamic-gallery-container">
                                <div class="col-12 text-center text-muted py-4">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Loading media items...
                                </div>
                            </div>
                        </div>
                    </section>
                ',
                'is_dynamic' => true,
                'dynamic_source' => 'gallery',
                'display_order' => 8,
            ],
            [
                'name' => 'Contact Form & Map',
                'slug' => 'contact-form',
                'category' => 'Contact',
                'html_template' => '
                    <section class="py-6 bg-light" id="contact">
                        <div class="container">
                            <div class="row g-5">
                                <div class="col-lg-6">
                                    <div class="bg-white p-5 rounded-4 shadow-sm border">
                                        <h3 class="fw-bold text-dark mb-4">Send Us a Message</h3>
                                        <form id="public-contact-form" method="POST" action="/public-site/contact">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Full Name</label>
                                                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Email Address</label>
                                                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Message Details</label>
                                                <textarea name="message" rows="4" class="form-control" placeholder="Type your query here..." required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-lg px-4 py-2 w-100 fw-bold">Submit Message</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <span class="badge bg-light text-primary border px-3 py-2 mb-3 fw-bold text-uppercase">Visit Us</span>
                                            <h3 class="fw-bold text-dark mb-3">Our Campus Location</h3>
                                            <p class="text-muted">Feel free to drop by during working hours to meet our faculty and tour our facilities.</p>
                                        </div>
                                        <div class="ratio ratio-16x9 border rounded-4 bg-white shadow-sm overflow-hidden mt-4" style="min-height: 320px;">
                                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127065.75338356987!2d-0.26477169420063236!3d5.591244243640242!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9084b2b7a773%3A0xbed14ed8650e2dd3!2sAccra%2C%20Ghana!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                ',
                'is_dynamic' => false,
                'dynamic_source' => null,
                'display_order' => 9,
            ]
        ];

        foreach ($blocks as $block) {
            WebsiteBlock::updateOrCreate(
                ['slug' => $block['slug']],
                [
                    'name' => $block['name'],
                    'category' => $block['category'],
                    'html_template' => $block['html_template'],
                    'is_dynamic' => $block['is_dynamic'],
                    'dynamic_source' => $block['dynamic_source'],
                    'is_active' => true,
                    'display_order' => $block['display_order'],
                ]
            );
        }
    }
}
