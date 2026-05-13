<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $topLevelCategories = [
            '23336' => 'Computer & IT',
            '23632' => 'Electronics & AV',
            '22152' => 'Music & Instruments',
            '21600' => 'Books & Magazines',
            '21964' => 'Movies & Video',
            '25464' => 'Toys & Games',
            '24242' => 'Hobby & Culture',
            '20000' => 'Antiques & Collectibles',
            '24698' => 'Sports & Leisure',
            '26318' => 'Automotive & Motorcycle',
            '23000' => 'Fashion & Apparel',
            '23140' => 'Accessories & Watches',
            '42177' => 'Beauty & Health',
            '23976' => 'Food & Beverages',
            '24198' => 'Home & Interior',
            '2084055844' => 'Pets & Animals',
            '22896' => 'Office & Business',
            '26086' => 'Flower & Gardening',
            '2084043920' => 'Tickets & Coupons',
            '24202' => 'Baby & Maternity',
            '2084032594' => 'Talent & Celebrity',
            '20060' => 'Anime & Comics',
            '2084217893' => 'Charity',
            '26084' => 'Miscellaneous',
        ];

        // Map English names to Japanese original names for internal reference if needed
        $japaneseNames = [
            '23336' => 'コンピュータ',
            '23632' => '家電、AV、カメラ',
            '22152' => '音楽',
            '21600' => '本、雑誌',
            '21964' => '映画、ビデオ',
            '25464' => 'おもちゃ、ゲーム',
            '24242' => 'ホビー、カルチャー',
            '20000' => 'アンティーク、コレクション',
            '24698' => 'スポーツ、レジャー',
            '26318' => '自動車、オートバイ',
            '23000' => 'ファッション',
            '23140' => 'アクセサリー、時計',
            '42177' => 'ビューティー、ヘルスケア',
            '23976' => '食品、飲料',
            '24198' => '住まい、インテリア',
            '2084055844' => 'ペット、生き物',
            '22896' => '事務、店舗用品',
            '26086' => '花、園芸',
            '2084043920' => 'チケット、金券、宿泊予約',
            '24202' => 'ベビー用品',
            '2084032594' => 'タレントグッズ',
            '20060' => 'コミック、アニメグッズ',
            '2084217893' => 'チャリティー',
            '26084' => 'その他',
        ];

        foreach ($topLevelCategories as $id => $name) {
            Category::updateOrCreate(
                ['yahoo_category_id' => $id],
                [
                    'name' => $name,
                    'depth' => 0,
                    'priority' => 100, // Top level gets high priority
                ]
            );
        }
    }
}
