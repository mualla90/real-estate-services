<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\City;
use App\Models\DynamicField;
use App\Models\Favorite;
use App\Models\Report;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceDynamicFieldValue;
use App\Models\ServiceRequest;
use App\Models\Slider;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RealEstateDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->where('email', 'admin@example.com')->first()
            ?? Admin::query()->first();

        $cities = $this->seedCities();
        $activityTypes = $this->seedActivityTypes();
        [$categories, $subcategories] = $this->seedCategoriesAndSubcategories();
        $users = $this->seedUsers();
        $accounts = $this->seedBusinessAccounts($users, $cities, $activityTypes, $admin);
        $services = $this->seedServices($accounts, $categories, $subcategories, $cities, $admin);
        $fields = $this->seedDynamicFields($categories, $subcategories, $admin);

        $this->seedServiceDynamicValues($services, $fields);
        $this->seedRequestsReviewsFavoritesReports($accounts, $services, $admin);
        $this->seedSliders($admin);
    }

    protected function seedCities(): array
    {
        $items = [
            ['en' => 'Damascus', 'ar' => 'دمشق', 'sort' => 1],
            ['en' => 'Aleppo', 'ar' => 'حلب', 'sort' => 2],
            ['en' => 'Homs', 'ar' => 'حمص', 'sort' => 3],
            ['en' => 'Latakia', 'ar' => 'اللاذقية', 'sort' => 4],
            ['en' => 'Tartus', 'ar' => 'طرطوس', 'sort' => 5],
        ];

        $cities = [];
        foreach ($items as $item) {
            $cities[$item['en']] = City::query()->updateOrCreate(
                ['name->en' => $item['en']],
                [
                    'name' => ['en' => $item['en'], 'ar' => $item['ar']],
                    'is_active' => true,
                    'sort_order' => $item['sort'],
                ]
            );
        }

        return $cities;
    }

    protected function seedActivityTypes(): array
    {
        $items = [
            ['en' => 'Real Estate Office', 'ar' => 'مكتب عقاري', 'sort' => 1],
            ['en' => 'Property Developer', 'ar' => 'مطور عقاري', 'sort' => 2],
            ['en' => 'Construction Company', 'ar' => 'شركة مقاولات', 'sort' => 3],
            ['en' => 'Property Management', 'ar' => 'إدارة أملاك', 'sort' => 4],
            ['en' => 'Interior Design Studio', 'ar' => 'استوديو تصميم داخلي', 'sort' => 5],
        ];

        $types = [];
        foreach ($items as $item) {
            $types[$item['en']] = ActivityType::query()->updateOrCreate(
                ['name->en' => $item['en']],
                [
                    'name' => ['en' => $item['en'], 'ar' => $item['ar']],
                    'is_active' => true,
                    'sort_order' => $item['sort'],
                ]
            );
        }

        return $types;
    }

    protected function seedCategoriesAndSubcategories(): array
    {
        $data = [
            'Residential Properties' => [
                'ar' => 'عقارات سكنية',
                'description_ar' => 'خدمات وعروض العقارات السكنية',
                'subs' => [
                    ['en' => 'Apartment', 'ar' => 'شقة'],
                    ['en' => 'Villa', 'ar' => 'فيلا'],
                    ['en' => 'Residential Land', 'ar' => 'أرض سكنية'],
                ],
            ],
            'Commercial Properties' => [
                'ar' => 'عقارات تجارية',
                'description_ar' => 'خدمات وعروض العقارات التجارية',
                'subs' => [
                    ['en' => 'Office', 'ar' => 'مكتب'],
                    ['en' => 'Shop', 'ar' => 'محل'],
                    ['en' => 'Warehouse', 'ar' => 'مستودع'],
                ],
            ],
            'Construction & Finishing' => [
                'ar' => 'بناء وإكساء',
                'description_ar' => 'خدمات البناء والإكساء والتشطيب',
                'subs' => [
                    ['en' => 'Interior Design', 'ar' => 'تصميم داخلي'],
                    ['en' => 'Electrical Works', 'ar' => 'أعمال كهربائية'],
                    ['en' => 'Plumbing', 'ar' => 'أعمال صحية'],
                ],
            ],
            'Property Management Services' => [
                'ar' => 'خدمات إدارة الأملاك',
                'description_ar' => 'خدمات إدارة وتشغيل العقارات',
                'subs' => [
                    ['en' => 'Maintenance', 'ar' => 'صيانة'],
                    ['en' => 'Cleaning', 'ar' => 'تنظيف'],
                    ['en' => 'Security', 'ar' => 'حراسة'],
                ],
            ],
            'Legal & Advisory' => [
                'ar' => 'قانوني واستشاري',
                'description_ar' => 'خدمات قانونية واستشارية عقارية',
                'subs' => [
                    ['en' => 'Contract Drafting', 'ar' => 'صياغة عقود'],
                    ['en' => 'Property Valuation', 'ar' => 'تقييم عقاري'],
                    ['en' => 'Brokerage Advisory', 'ar' => 'استشارات وساطة'],
                ],
            ],
        ];

        $categories = [];
        $subcategories = [];
        $sort = 1;

        foreach ($data as $en => $meta) {
            $category = Category::query()->updateOrCreate(
                ['name->en' => $en],
                [
                    'name' => ['en' => $en, 'ar' => $meta['ar']],
                    'description' => [
                        'en' => "{$en} services and listings",
                        'ar' => $meta['description_ar'],
                    ],
                    'is_active' => true,
                    'sort_order' => $sort++,
                ]
            );

            $categories[$en] = $category;

            $subSort = 1;
            foreach ($meta['subs'] as $sub) {
                $subcategory = Subcategory::query()->updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name->en' => $sub['en'],
                    ],
                    [
                        'name' => ['en' => $sub['en'], 'ar' => $sub['ar']],
                        'is_active' => true,
                        'sort_order' => $subSort++,
                    ]
                );

                $subcategories[$sub['en']] = $subcategory;
            }
        }

        return [$categories, $subcategories];
    }

    protected function seedUsers(): array
    {
        $items = [
            ['name' => 'Ahmad Khatib', 'email' => 'ahmad.khatib@example.com', 'phone' => '0991000001'],
            ['name' => 'Lina Haddad', 'email' => 'lina.haddad@example.com', 'phone' => '0991000002'],
            ['name' => 'Samer Darwish', 'email' => 'samer.darwish@example.com', 'phone' => '0991000003'],
            ['name' => 'Rana Najjar', 'email' => 'rana.najjar@example.com', 'phone' => '0991000004'],
        ];

        $users = [];
        foreach ($items as $item) {
            $users[$item['email']] = User::query()->updateOrCreate(
                ['phone' => $item['phone']],
                [
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }

        return $users;
    }

    protected function seedBusinessAccounts(array $users, array $cities, array $activityTypes, ?Admin $admin): array
    {
        $rows = [
            [
                'key' => 'Al Noor Realty',
                'license' => 'RE-DA-1001',
                'user' => 'ahmad.khatib@example.com',
                'city' => 'Damascus',
                'activity' => 'Real Estate Office',
                'status' => 'approved',
                'lat' => 33.5138,
                'lng' => 36.2765,
            ],
            [
                'key' => 'Sham Homes Agency',
                'license' => 'RE-AL-1002',
                'user' => 'lina.haddad@example.com',
                'city' => 'Aleppo',
                'activity' => 'Real Estate Office',
                'status' => 'approved',
                'lat' => 36.2021,
                'lng' => 37.1343,
            ],
            [
                'key' => 'Cedar Build Co',
                'license' => 'RE-HO-1003',
                'user' => 'samer.darwish@example.com',
                'city' => 'Homs',
                'activity' => 'Construction Company',
                'status' => 'pending',
                'lat' => 34.7324,
                'lng' => 36.7137,
            ],
            [
                'key' => 'Blue Coast Properties',
                'license' => 'RE-LA-1004',
                'user' => 'rana.najjar@example.com',
                'city' => 'Latakia',
                'activity' => 'Property Developer',
                'status' => 'rejected',
                'lat' => 35.5307,
                'lng' => 35.7916,
            ],
            [
                'key' => 'Tartus Prime Realty',
                'license' => 'RE-TA-1005',
                'user' => 'ahmad.khatib@example.com',
                'city' => 'Tartus',
                'activity' => 'Property Management',
                'status' => 'approved',
                'lat' => 34.8934,
                'lng' => 35.8898,
            ],
            [
                'key' => 'Aleppo Design Hub',
                'license' => 'RE-AL-1006',
                'user' => 'samer.darwish@example.com',
                'city' => 'Aleppo',
                'activity' => 'Interior Design Studio',
                'status' => 'approved',
                'lat' => 36.2154,
                'lng' => 37.1227,
            ],
        ];

        $accounts = [];
        foreach ($rows as $row) {
            $isReviewed = in_array($row['status'], ['approved', 'rejected'], true);
            $accounts[$row['key']] = BusinessAccount::query()->updateOrCreate(
                ['license_number' => $row['license']],
                [
                    'user_id' => $users[$row['user']]->id,
                    'activity_type_id' => $activityTypes[$row['activity']]->id,
                    'city_id' => $cities[$row['city']]->id,
                    'license_number' => $row['license'],
                    'name' => ['en' => $row['key'], 'ar' => $this->toArabicCompanyName($row['key'])],
                    'phone' => $users[$row['user']]->phone,
                    'email' => $users[$row['user']]->email,
                    'description' => [
                        'en' => "{$row['key']} provides trusted real-estate services.",
                        'ar' => "تقدم {$this->toArabicCompanyName($row['key'])} خدمات عقارية موثوقة.",
                    ],
                    'address' => "{$row['city']} - Main District",
                    'latitude' => $row['lat'],
                    'longitude' => $row['lng'],
                    'status' => $row['status'],
                    'rejection_reason' => $row['status'] === 'rejected' ? 'Incomplete legal documents.' : null,
                    'reviewed_by_admin_id' => $isReviewed ? $admin?->id : null,
                    'reviewed_at' => $isReviewed ? now()->subDays(3) : null,
                ]
            );
        }

        return $accounts;
    }

    protected function seedServices(array $accounts, array $categories, array $subcategories, array $cities, ?Admin $admin): array
    {
        $items = [
            [
                'key' => 'Damascus Apartment for Sale',
                'account' => 'Al Noor Realty',
                'category' => 'Residential Properties',
                'subcategory' => 'Apartment',
                'city' => 'Damascus',
                'type' => 'sale',
                'price' => 85000,
                'currency' => 'USD',
                'status' => 'approved',
                'address' => 'Malki, Damascus',
                'lat' => 33.5152,
                'lng' => 36.2894,
            ],
            [
                'key' => 'Aleppo Office for Rent',
                'account' => 'Sham Homes Agency',
                'category' => 'Commercial Properties',
                'subcategory' => 'Office',
                'city' => 'Aleppo',
                'type' => 'rent',
                'price' => 1200,
                'currency' => 'USD',
                'status' => 'approved',
                'address' => 'Aziziyeh, Aleppo',
                'lat' => 36.2079,
                'lng' => 37.1448,
            ],
            [
                'key' => 'Modern Villa in Damascus',
                'account' => 'Al Noor Realty',
                'category' => 'Residential Properties',
                'subcategory' => 'Villa',
                'city' => 'Damascus',
                'type' => 'sale',
                'price' => 210000,
                'currency' => 'USD',
                'status' => 'pending',
                'address' => 'Yaafour, Damascus Countryside',
                'lat' => 33.4831,
                'lng' => 36.1392,
            ],
            [
                'key' => 'Tartus Property Maintenance Package',
                'account' => 'Tartus Prime Realty',
                'category' => 'Property Management Services',
                'subcategory' => 'Maintenance',
                'city' => 'Tartus',
                'type' => 'rent',
                'price' => 250,
                'currency' => 'USD',
                'status' => 'approved',
                'address' => 'Corniche District, Tartus',
                'lat' => 34.8897,
                'lng' => 35.8811,
            ],
            [
                'key' => 'Aleppo Interior Design Consultation',
                'account' => 'Aleppo Design Hub',
                'category' => 'Construction & Finishing',
                'subcategory' => 'Interior Design',
                'city' => 'Aleppo',
                'type' => 'rent',
                'price' => 180,
                'currency' => 'USD',
                'status' => 'approved',
                'address' => 'Sabil District, Aleppo',
                'lat' => 36.1962,
                'lng' => 37.1558,
            ],
            [
                'key' => 'Property Valuation Service in Damascus',
                'account' => 'Al Noor Realty',
                'category' => 'Legal & Advisory',
                'subcategory' => 'Property Valuation',
                'city' => 'Damascus',
                'type' => 'rent',
                'price' => 120,
                'currency' => 'USD',
                'status' => 'pending',
                'address' => 'Abu Rummaneh, Damascus',
                'lat' => 33.5204,
                'lng' => 36.2879,
            ],
        ];

        $services = [];
        foreach ($items as $index => $item) {
            $reviewed = in_array($item['status'], ['approved', 'rejected'], true);
            $services[$item['key']] = Service::query()->updateOrCreate(
                [
                    'business_account_id' => $accounts[$item['account']]->id,
                    'title->en' => $item['key'],
                ],
                [
                    'business_account_id' => $accounts[$item['account']]->id,
                    'category_id' => $categories[$item['category']]->id,
                    'subcategory_id' => $subcategories[$item['subcategory']]->id,
                    'city_id' => $cities[$item['city']]->id,
                    'title' => ['en' => $item['key'], 'ar' => $this->toArabicServiceTitle($item['key'])],
                    'description' => [
                        'en' => "Well-maintained listing with strong location and pricing advantages.",
                        'ar' => 'عرض عقاري بحالة ممتازة وموقع مميز وسعر مناسب.',
                    ],
                    'service_type' => $item['type'],
                    'price' => $item['price'],
                    'currency' => $item['currency'],
                    'address' => $item['address'],
                    'latitude' => $item['lat'],
                    'longitude' => $item['lng'],
                    'status' => $item['status'],
                    'rejection_reason' => $item['status'] === 'rejected' ? 'Listing photos are unclear.' : null,
                    'reviewed_by_admin_id' => $reviewed ? $admin?->id : null,
                    'reviewed_at' => $reviewed ? now()->subDays(2) : null,
                    'published_at' => $item['status'] === 'approved' ? now()->subDays(1) : null,
                    'average_rating' => $item['status'] === 'approved' ? 4.5 : 0,
                    'review_count' => $item['status'] === 'approved' ? 1 : 0,
                    'views_count' => 80 + ($index * 35),
                    'is_active' => $item['status'] === 'approved',
                    'sort_order' => $index + 1,
                ]
            );
        }

        return $services;
    }

    protected function seedDynamicFields(array $categories, array $subcategories, ?Admin $admin): array
    {
        $fields = [];

        $fields['bedrooms'] = DynamicField::query()->updateOrCreate(
            ['field_key' => 'bedrooms'],
            [
                'admin_id' => $admin?->id,
                'category_id' => $categories['Residential Properties']->id,
                'subcategory_id' => null,
                'name' => ['en' => 'Bedrooms', 'ar' => 'عدد غرف النوم'],
                'field_type' => 'number',
                'is_required' => true,
                'options' => null,
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        $fields['bathrooms'] = DynamicField::query()->updateOrCreate(
            ['field_key' => 'bathrooms'],
            [
                'admin_id' => $admin?->id,
                'category_id' => $categories['Residential Properties']->id,
                'subcategory_id' => null,
                'name' => ['en' => 'Bathrooms', 'ar' => 'عدد الحمامات'],
                'field_type' => 'number',
                'is_required' => true,
                'options' => null,
                'sort_order' => 2,
                'status' => 'active',
            ]
        );

        $fields['furnishing_level'] = DynamicField::query()->updateOrCreate(
            ['field_key' => 'furnishing_level'],
            [
                'admin_id' => $admin?->id,
                'category_id' => $categories['Residential Properties']->id,
                'subcategory_id' => $subcategories['Apartment']->id,
                'name' => ['en' => 'Furnishing Level', 'ar' => 'مستوى الإكساء'],
                'field_type' => 'select',
                'is_required' => false,
                'options' => ['Unfurnished', 'Semi Furnished', 'Fully Furnished'],
                'sort_order' => 3,
                'status' => 'active',
            ]
        );

        $fields['office_area_m2'] = DynamicField::query()->updateOrCreate(
            ['field_key' => 'office_area_m2'],
            [
                'admin_id' => $admin?->id,
                'category_id' => $categories['Commercial Properties']->id,
                'subcategory_id' => $subcategories['Office']->id,
                'name' => ['en' => 'Office Area (m2)', 'ar' => 'مساحة المكتب (م2)'],
                'field_type' => 'number',
                'is_required' => true,
                'options' => null,
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        return $fields;
    }

    protected function seedServiceDynamicValues(array $services, array $fields): void
    {
        $apartmentService = $services['Damascus Apartment for Sale'];
        $officeService = $services['Aleppo Office for Rent'];

        ServiceDynamicFieldValue::query()->updateOrCreate(
            ['service_id' => $apartmentService->id, 'dynamic_field_id' => $fields['bedrooms']->id],
            ['field_type' => 'number', 'value_number' => 3, 'value_text' => null, 'value_json' => null]
        );

        ServiceDynamicFieldValue::query()->updateOrCreate(
            ['service_id' => $apartmentService->id, 'dynamic_field_id' => $fields['bathrooms']->id],
            ['field_type' => 'number', 'value_number' => 2, 'value_text' => null, 'value_json' => null]
        );

        ServiceDynamicFieldValue::query()->updateOrCreate(
            ['service_id' => $apartmentService->id, 'dynamic_field_id' => $fields['furnishing_level']->id],
            ['field_type' => 'select', 'value_number' => null, 'value_text' => null, 'value_json' => ['value' => 'Semi Furnished']]
        );

        ServiceDynamicFieldValue::query()->updateOrCreate(
            ['service_id' => $officeService->id, 'dynamic_field_id' => $fields['office_area_m2']->id],
            ['field_type' => 'number', 'value_number' => 120, 'value_text' => null, 'value_json' => null]
        );
    }

    protected function seedRequestsReviewsFavoritesReports(array $accounts, array $services, ?Admin $admin): void
    {
        $request = ServiceRequest::query()->updateOrCreate(
            [
                'service_id' => $services['Damascus Apartment for Sale']->id,
                'requester_business_account_id' => $accounts['Sham Homes Agency']->id,
                'provider_business_account_id' => $accounts['Al Noor Realty']->id,
            ],
            [
                'status' => 'accepted',
                'quantity' => 1,
                'needed_at' => now()->addDays(5),
                'message' => 'Client is interested in a site visit this week.',
                'price_offer' => 83000,
                'responded_at' => now()->subDay(),
            ]
        );

        Review::query()->updateOrCreate(
            ['service_request_id' => $request->id],
            [
                'service_id' => $services['Damascus Apartment for Sale']->id,
                'reviewer_business_account_id' => $accounts['Sham Homes Agency']->id,
                'rating' => 5,
                'comment' => 'Professional handling and clear details.',
            ]
        );

        Favorite::query()->updateOrCreate(
            [
                'business_account_id' => $accounts['Sham Homes Agency']->id,
                'service_id' => $services['Damascus Apartment for Sale']->id,
            ],
            ['note' => 'Strong option for relocation client']
        );

        Report::query()->updateOrCreate(
            [
                'reporter_business_account_id' => $accounts['Sham Homes Agency']->id,
                'reportable_type' => Service::class,
                'reportable_id' => $services['Aleppo Office for Rent']->id,
            ],
            [
                'reason' => 'Possible duplicate listing',
                'description' => 'Title and details look similar to another published office listing.',
                'status' => 'pending',
                'reviewed_by_admin_id' => null,
                'reviewed_at' => null,
            ]
        );

        Report::query()->updateOrCreate(
            [
                'reporter_business_account_id' => $accounts['Al Noor Realty']->id,
                'reportable_type' => Service::class,
                'reportable_id' => $services['Modern Villa in Damascus']->id,
            ],
            [
                'reason' => 'Outdated content',
                'description' => 'The listing is still pending review and needs updated photos.',
                'status' => 'reviewed',
                'reviewed_by_admin_id' => $admin?->id,
                'reviewed_at' => now()->subHours(8),
            ]
        );
    }

    protected function seedSliders(?Admin $admin): void
    {
        $items = [
            [
                'title_en' => 'Find Your Perfect Home',
                'title_ar' => 'ابحث عن منزلك المثالي',
                'subtitle_en' => 'Verified listings with trusted business accounts.',
                'subtitle_ar' => 'عروض موثوقة من حسابات أعمال معتمدة.',
                'link' => 'https://example.com/app/home',
                'sort' => 1,
            ],
            [
                'title_en' => 'Commercial Spaces Ready for Rent',
                'title_ar' => 'مساحات تجارية جاهزة للإيجار',
                'subtitle_en' => 'Offices, shops, and warehouses across major cities.',
                'subtitle_ar' => 'مكاتب ومحلات ومستودعات في أهم المدن.',
                'link' => 'https://example.com/app/commercial',
                'sort' => 2,
            ],
            [
                'title_en' => 'Construction and Finishing Services',
                'title_ar' => 'خدمات البناء والإكساء',
                'subtitle_en' => 'Connect with specialized contractors quickly.',
                'subtitle_ar' => 'تواصل مع مقاولين متخصصين بسرعة.',
                'link' => 'https://example.com/app/construction',
                'sort' => 3,
            ],
        ];

        foreach ($items as $item) {
            Slider::query()->updateOrCreate(
                ['title->en' => $item['title_en']],
                [
                    'title' => ['en' => $item['title_en'], 'ar' => $item['title_ar']],
                    'subtitle' => ['en' => $item['subtitle_en'], 'ar' => $item['subtitle_ar']],
                    'link' => $item['link'],
                    'is_active' => true,
                    'sort_order' => $item['sort'],
                    'starts_at' => now()->subDays(2),
                    'ends_at' => now()->addMonths(4),
                    'created_by_admin_id' => $admin?->id,
                ]
            );
        }
    }

    protected function toArabicCompanyName(string $english): string
    {
        return match ($english) {
            'Al Noor Realty' => 'النور العقارية',
            'Sham Homes Agency' => 'وكالة شام هومز',
            'Cedar Build Co' => 'سيدر للبناء',
            'Blue Coast Properties' => 'بلو كوست العقارية',
            'Tartus Prime Realty' => 'تارتوس برايم العقارية',
            'Aleppo Design Hub' => 'مركز حلب للتصميم',
            default => $english,
        };
    }

    protected function toArabicServiceTitle(string $english): string
    {
        return match ($english) {
            'Damascus Apartment for Sale' => 'شقة للبيع في دمشق',
            'Aleppo Office for Rent' => 'مكتب للإيجار في حلب',
            'Modern Villa in Damascus' => 'فيلا حديثة في دمشق',
            'Tartus Property Maintenance Package' => 'باقة صيانة عقارية في طرطوس',
            'Aleppo Interior Design Consultation' => 'استشارة تصميم داخلي في حلب',
            'Property Valuation Service in Damascus' => 'خدمة تقييم عقاري في دمشق',
            default => $english,
        };
    }
}
