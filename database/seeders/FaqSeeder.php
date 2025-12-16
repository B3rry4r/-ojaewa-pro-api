<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // General
            [
                'category' => 'General',
                'question' => 'What is Oja Ewa Pro?',
                'answer' => 'Oja Ewa Pro is a comprehensive e-commerce platform connecting buyers with African fashion, beauty, education, music, and sustainable products. We showcase authentic African products and services from verified sellers and businesses across the continent.',
            ],
            [
                'category' => 'General',
                'question' => 'How do I create an account?',
                'answer' => 'You can create an account by clicking the "Sign Up" button and providing your first name, last name, email, phone number, and password. You can also sign up using your Google account for faster registration.',
            ],
            [
                'category' => 'General',
                'question' => 'Is my personal information secure?',
                'answer' => 'Yes, we take data security seriously. All personal information is encrypted and stored securely. We comply with international data protection standards and never share your personal information with third parties without your consent.',
            ],

            // Shopping & Orders
            [
                'category' => 'Shopping',
                'question' => 'How do I place an order?',
                'answer' => 'Browse products, add items to your cart, proceed to checkout, enter your shipping information, and complete payment. You\'ll receive an order confirmation email with tracking information once your order is processed.',
            ],
            [
                'category' => 'Shopping',
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept various payment methods including credit/debit cards, mobile money (M-Pesa, Airtel Money, etc.), bank transfers, and digital wallets. Payment options may vary by region.',
            ],
            [
                'category' => 'Shopping',
                'question' => 'How can I track my order?',
                'answer' => 'After placing an order, you can track its status in your account under "Your Orders." You\'ll also receive email updates when your order status changes (processing, shipped, delivered).',
            ],
            [
                'category' => 'Shopping',
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day return policy for most items. Products must be in original condition with tags attached. Custom-made items and personalized products may not be returnable. Contact customer service to initiate a return.',
            ],
            [
                'category' => 'Shopping',
                'question' => 'How long does shipping take?',
                'answer' => 'Shipping times vary by location and seller. Local deliveries typically take 2-5 business days, while international shipping can take 7-21 business days. Express shipping options are available for faster delivery.',
            ],

            // Sellers
            [
                'category' => 'Sellers',
                'question' => 'How do I become a seller?',
                'answer' => 'Create a user account first, then apply to become a seller by providing business information, documentation, and bank details. Our team reviews applications within 3-5 business days.',
            ],
            [
                'category' => 'Sellers',
                'question' => 'What are the seller fees?',
                'answer' => 'We charge a small commission on completed sales plus payment processing fees. There are no upfront costs or monthly fees. Detailed fee structure is available in the seller dashboard.',
            ],
            [
                'category' => 'Sellers',
                'question' => 'How do I list my products?',
                'answer' => 'Once approved as a seller, access your seller dashboard to add products. Include clear photos, detailed descriptions, accurate pricing, and specify processing times. All products go through quality review before going live.',
            ],
            [
                'category' => 'Sellers',
                'question' => 'When do I get paid?',
                'answer' => 'Payments are processed weekly for completed orders. Funds are transferred to your registered bank account. New sellers may have a brief holding period for first few sales.',
            ],

            // Beauty Services
            [
                'category' => 'Beauty',
                'question' => 'How do I book beauty services?',
                'answer' => 'Browse beauty service providers in your area, select services, choose available time slots, and confirm booking. You\'ll receive confirmation with provider contact details and appointment information.',
            ],
            [
                'category' => 'Beauty',
                'question' => 'Can I cancel or reschedule beauty appointments?',
                'answer' => 'Yes, you can cancel or reschedule appointments at least 24 hours in advance through your account. Late cancellations may be subject to fees as per the service provider\'s policy.',
            ],

            // Education
            [
                'category' => 'Education',
                'question' => 'How do school registrations work?',
                'answer' => 'Browse available schools and programs, select your course, complete the registration form with your details, and make payment. You\'ll receive enrollment confirmation and course materials information.',
            ],
            [
                'category' => 'Education',
                'question' => 'Are the schools and courses accredited?',
                'answer' => 'We work with accredited institutions and verified training providers. Each school listing includes accreditation information and certifications offered. Always verify credentials that matter for your goals.',
            ],

            // Music
            [
                'category' => 'Music',
                'question' => 'What music services are available?',
                'answer' => 'Our platform offers various music services including DJ bookings, music production, artist collaborations, music lessons, and event entertainment. Browse by category or location to find services.',
            ],

            // Technical Support
            [
                'category' => 'Technical',
                'question' => 'I forgot my password. How do I reset it?',
                'answer' => 'Click "Forgot Password" on the login page, enter your email address, and check your email for reset instructions. Follow the link in the email to create a new password.',
            ],
            [
                'category' => 'Technical',
                'question' => 'Why can\'t I access my account?',
                'answer' => 'Account access issues can occur due to incorrect login credentials, suspended accounts, or technical problems. Try password reset first. If problems persist, contact customer support with your email address.',
            ],
            [
                'category' => 'Technical',
                'question' => 'The website is loading slowly. What should I do?',
                'answer' => 'Slow loading can be due to internet connection, browser cache, or high traffic. Try refreshing the page, clearing your browser cache, or using a different browser. Contact support if problems continue.',
            ],

            // Customer Service
            [
                'category' => 'Support',
                'question' => 'How do I contact customer service?',
                'answer' => 'Contact us via email at support@ojaewa.com, through the contact form on our website, or via our social media channels. We respond to inquiries within 24 hours during business days.',
            ],
            [
                'category' => 'Support',
                'question' => 'What are your customer service hours?',
                'answer' => 'Our customer service team is available Monday to Friday, 9 AM to 6 PM (WAT). Weekend support is available for urgent matters. We aim to respond to all inquiries within 24 hours.',
            ],
            [
                'category' => 'Support',
                'question' => 'Can I change my order after placing it?',
                'answer' => 'Order changes depend on the order status. If your order hasn\'t been processed yet, contact customer service immediately. Once an order is being prepared or shipped, changes may not be possible.',
            ],

            // Business Profiles
            [
                'category' => 'Business',
                'question' => 'How do I create a business profile?',
                'answer' => 'Navigate to "Show Your Business" section, choose your business category (Beauty, Brands, School, Music, Sustainability), and complete the registration form with required documentation and information.',
            ],
            [
                'category' => 'Business',
                'question' => 'What documents do I need for business registration?',
                'answer' => 'Required documents vary by business type but typically include business registration certificate, identity document, business logo, and proof of address. Specific requirements are listed in each category.',
            ],
            [
                'category' => 'Business',
                'question' => 'How long does business profile approval take?',
                'answer' => 'Business profile reviews typically take 5-7 business days. Complex applications may take longer. You\'ll receive email updates on your application status and any additional requirements.',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

        echo "Created " . count($faqs) . " FAQs across " . count(array_unique(array_column($faqs, 'category'))) . " categories.\n";
    }
}