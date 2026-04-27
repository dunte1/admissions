<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\School;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::withoutGlobalScopes()->get();
        
        $defaultFaqs = [
            [
                'question' => 'How do I apply for a program?',
                'answer' => 'To apply for a program, log in to your student portal and click on "New Application". Select your preferred program and intake, fill in the required information, upload necessary documents, and submit your application. You will receive a confirmation email once your application is submitted.',
                'category' => 'application',
                'sort_order' => 1,
            ],
            [
                'question' => 'What documents do I need to upload?',
                'answer' => 'Typically, you will need to upload: 1) National ID or Passport, 2) Academic certificates and transcripts, 3) Passport-sized photo, 4) Proof of payment for application fee. Some programs may require additional documents which will be specified during the application process.',
                'category' => 'application',
                'sort_order' => 2,
            ],
            [
                'question' => 'How long does the application process take?',
                'answer' => 'The application review process typically takes 2-4 weeks after the application deadline. You can track your application status in your student portal. You will be notified via email when there is an update on your application.',
                'category' => 'admission',
                'sort_order' => 3,
            ],
            [
                'question' => 'How do I pay the application fee?',
                'answer' => 'Application fees can be paid through M-Pesa or bank transfer. Select your preferred payment method during the application process. You will receive payment instructions including the M-Pesa paybill number or bank details.',
                'category' => 'payment',
                'sort_order' => 4,
            ],
            [
                'question' => 'Can I apply for multiple programs?',
                'answer' => 'Yes, you can apply for up to 3 programs. However, you will need to pay the application fee for each program separately. The institution will consider all your applications and offer you the best available option based on your qualifications.',
                'category' => 'application',
                'sort_order' => 5,
            ],
            [
                'question' => 'How do I know if I have been admitted?',
                'answer' => 'Once your application is reviewed and accepted, you will receive an official admission letter via email and it will also be available in your student portal under "My Offers". You can download and print your admission letter from there.',
                'category' => 'admission',
                'sort_order' => 6,
            ],
            [
                'question' => 'How do I accept my admission offer?',
                'answer' => 'To accept your admission offer, log in to your student portal and go to "My Offers". Click on the offer you wish to accept and then click "Accept Offer". Make sure to read the terms and conditions before accepting.',
                'category' => 'admission',
                'sort_order' => 7,
            ],
            [
                'question' => 'What happens if I decline the offer?',
                'answer' => 'If you decline the offer, your place will be given to another qualified applicant. Declining an offer is permanent and you cannot re-accept the same offer later. You may submit a new application for a future intake if you wish.',
                'category' => 'admission',
                'sort_order' => 8,
            ],
            [
                'question' => 'When do I need to respond to my offer?',
                'answer' => 'Each offer has a response deadline stated in your admission letter. Please respond before that date to secure your place. If you need an extension, contact the admissions office before the deadline.',
                'category' => 'admission',
                'sort_order' => 9,
            ],
            [
                'question' => 'How can I contact the admissions office?',
                'answer' => 'You can contact the admissions office through: 1) Email: admissions@institution.ac.ke, 2) Phone: +254-XXX-XXX-XXX, 3) Visit us during office hours (Monday-Friday, 8:00 AM - 5:00 PM). You can also submit an inquiry through your student portal.',
                'category' => 'support',
                'sort_order' => 10,
            ],
            [
                'question' => 'Is the application fee refundable?',
                'answer' => 'No, the application fee is non-refundable. It covers the cost of processing your application regardless of the outcome. Please ensure you meet all eligibility requirements before submitting your application.',
                'category' => 'payment',
                'sort_order' => 11,
            ],
            [
                'question' => 'Can I edit my application after submission?',
                'answer' => 'You cannot edit your application once submitted. However, if you need to update any information, please contact the admissions office immediately. They may be able to assist you before the review process begins.',
                'category' => 'application',
                'sort_order' => 12,
            ],
        ];

        foreach ($schools as $school) {
            foreach ($defaultFaqs as $faq) {
                Faq::create([
                    'school_id' => $school->id,
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'category' => $faq['category'],
                    'sort_order' => $faq['sort_order'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
