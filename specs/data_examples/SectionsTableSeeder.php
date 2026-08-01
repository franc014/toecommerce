<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sections')->insert([
            ['id' => 1, 'title' => 'Hero Section', 'description' => 'Main hero banner section', 'slug' => 'hero-section', 'content' => '[{"type":"heading","data":{"content":"Welcome to Our Store","level":"h1","handle":"heading"}},{"type":"paragraph","data":{"content":"Discover the best products for your pets"}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 2, 'title' => 'Features', 'description' => 'Key features section', 'slug' => 'features', 'content' => '[{"type":"feature","data":{"title":"Free Shipping","message":"Free shipping on all orders over $50","image":null}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 3, 'title' => 'About Us', 'description' => 'About the company section', 'slug' => 'about-us', 'content' => '[{"type":"paragraph","data":{"content":"We are a pet clothing company dedicated to providing high-quality, comfortable clothing for your pets."}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 4, 'title' => 'Footer', 'description' => 'Footer section', 'slug' => 'footer', 'content' => '[{"type":"paragraph","data":{"content":"© 2025 ToEcommerce. All rights reserved."}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 5, 'title' => 'Newsletter', 'description' => 'Newsletter signup section', 'slug' => 'newsletter', 'content' => '[{"type":"heading","data":{"content":"Subscribe to our newsletter","level":"h2","handle":"heading"}},{"type":"paragraph","data":{"content":"Stay updated with our latest products and offers."}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 6, 'title' => 'Testimonials', 'description' => 'Customer testimonials section', 'slug' => 'testimonials', 'content' => '[{"type":"paragraph","data":{"content":"What our customers say about us."}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 7, 'title' => 'Product Grid', 'description' => 'Featured products grid', 'slug' => 'product-grid', 'content' => '[{"type":"heading","data":{"content":"Featured Products","level":"h2","handle":"heading"}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 8, 'title' => 'Categories', 'description' => 'Product categories section', 'slug' => 'categories', 'content' => '[{"type":"heading","data":{"content":"Shop by Category","level":"h2","handle":"heading"}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 9, 'title' => 'Banner', 'description' => 'Promotional banner section', 'slug' => 'banner', 'content' => '[{"type":"heading","data":{"content":"Summer Sale","level":"h1","handle":"heading"}},{"type":"paragraph","data":{"content":"Up to 50% off on selected items."}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 10, 'title' => 'Brands', 'description' => 'Brands section', 'slug' => 'brands', 'content' => '[{"type":"heading","data":{"content":"Our Brands","level":"h2","handle":"heading"}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 11, 'title' => 'Contact Info', 'description' => 'Contact information section', 'slug' => 'contact-info', 'content' => '[{"type":"heading","data":{"content":"Contact Us","level":"h2","handle":"heading"}},{"type":"paragraph","data":{"content":"Email: info@toecommerce.com | Phone: +593 968741465"}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 12, 'title' => 'Social Media', 'description' => 'Social media links section', 'slug' => 'social-media', 'content' => '[{"type":"heading","data":{"content":"Follow Us","level":"h2","handle":"heading"}}]', 'status' => 'active', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
        ]);
    }
}