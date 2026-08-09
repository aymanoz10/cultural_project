<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Library;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $titles = [
            'مقدمة ابن خلدون', 'كليلة ودمنة', 'الأيام', 'رجال في الشمس',
            'موسم الهجرة إلى الشمال', 'ذاكرة الجسد', 'البخلاء', 'ألف ليلة وليلة',
            'مدن الملح', 'ثرثرة فوق النيل', 'دعاء الكروان', 'زقاق المدق',
            'الحرافيش', 'عزازيل', 'ساق البامبو', 'فرانكشتاين في بغداد',
        ];

        $authors = [
            'عبد الرحمن بن خلدون', 'عبد الله بن المقفع', 'طه حسين', 'غسان كنفاني',
            'الطيب صالح', 'أحلام مستغانمي', 'الجاحظ', 'نجيب محفوظ',
            'عبد الرحمن منيف', 'يوسف زيدان', 'سعود السنعوسي', 'أحمد سعداوي',
        ];

        $categories = ['تاريخ', 'أدب', 'فلسفة', 'علوم', 'فنون', 'دين', 'رواية', 'شعر'];

        return [
            'library_id'   => Library::inRandomOrder()->value('id'),
            'cover_image'  => null,
            'title'        => fake()->randomElement($titles),
            'author'       => fake()->randomElement($authors),
            'category'     => fake()->randomElement($categories),
            'description'  => fake()->optional()->paragraph(),
            'pages_count'  => fake()->numberBetween(80, 850),
            'file_size'    => fake()->numberBetween(2, 40) . ' ميجابايت',
            'language'     => 'العربية',
            'is_available' => fake()->boolean(80),
        ];
    }
}
