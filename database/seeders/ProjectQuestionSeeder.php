<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $villa = DB::table('project_types')
            ->where('slug', 'villa')
            ->first();

        if (!$villa) {
            throw new RuntimeException('Villa proje tipi bulunamadı.');
        }

        $questions = [
            [
                'question' => 'Arsanız kaç m²?',
                'slug' => 'arsa-alani',
                'type' => 'number',
                'description' => 'Arsanızın yaklaşık toplam alanını girin.',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'question' => 'Yaklaşık kaç m² inşaat yapmak istiyorsunuz?',
                'slug' => 'insaat-alani',
                'type' => 'number',
                'description' => 'Toplam yapılacak kapalı inşaat alanını girin.',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'question' => 'Kaç kat olacak?',
                'slug' => 'kat-sayisi',
                'type' => 'select',
                'description' => 'Bodrum kat varsa ayrıca daha sonra detaylandıracağız.',
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'question' => 'Kaç oda istiyorsunuz?',
                'slug' => 'oda-sayisi',
                'type' => 'select',
                'description' => 'Örneğin 4+1 veya 5+1.',
                'is_required' => true,
                'sort_order' => 4,
            ],
            [
                'question' => 'Kaç banyo olacak?',
                'slug' => 'banyo-sayisi',
                'type' => 'select',
                'description' => 'Toplam banyo/WC ihtiyacınızı belirtin.',
                'is_required' => true,
                'sort_order' => 5,
            ],
            [
                'question' => 'Yapı sistemi nedir?',
                'slug' => 'yapi-sistemi',
                'type' => 'select',
                'description' => 'Ana taşıyıcı sistem tercihinizi seçin.',
                'is_required' => true,
                'sort_order' => 6,
            ],
            [
                'question' => 'Yapı kalite seviyenizi nasıl düşünüyorsunuz?',
                'slug' => 'kalite-seviyesi',
                'type' => 'select',
                'description' => 'Maliyet hesabında kullanılacak yaklaşık kalite seviyesini seçin.',
                'is_required' => true,
                'sort_order' => 7,
            ],
            [
                'question' => 'Havuz istiyor musunuz?',
                'slug' => 'havuz',
                'type' => 'radio',
                'description' => 'Varsa havuz detaylarını sonraki adımda soracağız.',
                'is_required' => true,
                'sort_order' => 8,
            ],
            [
                'question' => 'Kapalı garaj veya otopark istiyor musunuz?',
                'slug' => 'otopark',
                'type' => 'radio',
                'description' => null,
                'is_required' => true,
                'sort_order' => 9,
            ],
            [
                'question' => 'Peyzaj yapılmasını istiyor musunuz?',
                'slug' => 'peyzaj',
                'type' => 'radio',
                'description' => null,
                'is_required' => true,
                'sort_order' => 10,
            ],
            [
                'question' => 'Çatı tercihiniz nedir?',
                'slug' => 'cati-tipi',
                'type' => 'select',
                'description' => null,
                'is_required' => true,
                'sort_order' => 11,
            ],
            [
                'question' => 'Bodrum kat düşünüyor musunuz?',
                'slug' => 'bodrum',
                'type' => 'radio',
                'description' => null,
                'is_required' => true,
                'sort_order' => 12,
            ],
            [
                'question' => 'Projenizle ilgili eklemek istediğiniz başka bir bilgi var mı?',
                'slug' => 'ek-aciklama',
                'type' => 'textarea',
                'description' => 'Varsa özel isteklerinizi, mimari beklentilerinizi veya önemli notları yazabilirsiniz.',
                'is_required' => false,
                'sort_order' => 13,
            ],
        ];

        foreach ($questions as $question) {
            DB::table('project_questions')->updateOrInsert(
                [
                    'project_type_id' => $villa->id,
                    'slug' => $question['slug'],
                ],
                [
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'description' => $question['description'],
                    'is_required' => $question['is_required'],
                    'is_active' => true,
                    'sort_order' => $question['sort_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $options = [
            'kat-sayisi' => [
                ['label' => 'Tek kat', 'value' => '1'],
                ['label' => '2 kat', 'value' => '2'],
                ['label' => '3 kat', 'value' => '3'],
                ['label' => '4 kat ve üzeri', 'value' => '4_plus'],
            ],

            'oda-sayisi' => [
                ['label' => '2+1', 'value' => '2_plus_1'],
                ['label' => '3+1', 'value' => '3_plus_1'],
                ['label' => '4+1', 'value' => '4_plus_1'],
                ['label' => '5+1', 'value' => '5_plus_1'],
                ['label' => '6+1 ve üzeri', 'value' => '6_plus_1'],
            ],

            'banyo-sayisi' => [
                ['label' => '1', 'value' => '1'],
                ['label' => '2', 'value' => '2'],
                ['label' => '3', 'value' => '3'],
                ['label' => '4 ve üzeri', 'value' => '4_plus'],
            ],

            'yapi-sistemi' => [
                ['label' => 'Betonarme', 'value' => 'betonarme'],
                ['label' => 'Çelik', 'value' => 'celik'],
                ['label' => 'Hibrit', 'value' => 'hibrit'],
            ],

            'kalite-seviyesi' => [
                ['label' => 'Standart', 'value' => 'standart'],
                ['label' => 'Üst segment', 'value' => 'ust_segment'],
                ['label' => 'Lüks', 'value' => 'luks'],
            ],

            'havuz' => [
                ['label' => 'Evet', 'value' => 'evet'],
                ['label' => 'Hayır', 'value' => 'hayir'],
            ],

            'otopark' => [
                ['label' => 'Evet', 'value' => 'evet'],
                ['label' => 'Hayır', 'value' => 'hayir'],
            ],

            'peyzaj' => [
                ['label' => 'Evet', 'value' => 'evet'],
                ['label' => 'Hayır', 'value' => 'hayir'],
            ],

            'cati-tipi' => [
                ['label' => 'Eğimli çatı', 'value' => 'egimli'],
                ['label' => 'Teras çatı', 'value' => 'teras'],
                ['label' => 'Özel mimari çatı', 'value' => 'ozel'],
            ],

            'bodrum' => [
                ['label' => 'Evet', 'value' => 'evet'],
                ['label' => 'Hayır', 'value' => 'hayir'],
            ],
        ];

        foreach ($options as $slug => $items) {
            $question = DB::table('project_questions')
                ->where('project_type_id', $villa->id)
                ->where('slug', $slug)
                ->first();

            if (!$question) {
                continue;
            }

            foreach ($items as $index => $item) {
                DB::table('project_question_options')->updateOrInsert(
                    [
                        'question_id' => $question->id,
                        'value' => $item['value'],
                    ],
                    [
                        'label' => $item['label'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
