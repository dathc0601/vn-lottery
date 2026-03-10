<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $codeMap = [
        // Miền Bắc
        'ha-noi' => 'hn',
        'quang-ninh' => 'qni',
        'bac-ninh' => 'bni',
        'hai-phong' => 'hp',
        'nam-dinh' => 'nd',
        'thai-binh' => 'tb',
        // Miền Trung
        'binh-dinh' => 'bdi',
        'da-nang' => 'dna',
        'dak-lak' => 'dl',
        'dak-nong' => 'dno',
        'gia-lai' => 'gl',
        'khanh-hoa' => 'kh',
        'kon-tum' => 'kt',
        'ninh-thuan' => 'nt',
        'phu-yen' => 'py',
        'quang-binh' => 'qb',
        'quang-nam' => 'qna',
        'quang-ngai' => 'qng',
        'quang-tri' => 'qt',
        'thua-thien-hue' => 'th',
        // Miền Nam
        'an-giang' => 'ag',
        'bac-lieu' => 'bl',
        'ben-tre' => 'bt',
        'binh-duong' => 'bdu',
        'binh-phuoc' => 'bp',
        'ca-mau' => 'cm',
        'can-tho' => 'ct',
        'dong-nai' => 'dni',
        'dong-thap' => 'dt',
        'hau-giang' => 'hg',
        'kien-giang' => 'kg',
        'long-an' => 'la',
        'soc-trang' => 'st',
        'tay-ninh' => 'tn',
        'tien-giang' => 'tg',
        'tra-vinh' => 'tv',
        'vinh-long' => 'vl',
        'vung-tau' => 'vt',
    ];

    protected array $reverseMap = [
        // Miền Bắc
        'ha-noi' => 'miba',
        'quang-ninh' => 'quni',
        'bac-ninh' => 'bani',
        'hai-phong' => 'haph',
        'nam-dinh' => 'nadi',
        'thai-binh' => 'thbi',
        // Miền Trung
        'binh-dinh' => 'bidi',
        'da-nang' => 'dana',
        'dak-lak' => 'dalak',
        'dak-nong' => 'dano',
        'gia-lai' => 'gila',
        'khanh-hoa' => 'khho',
        'kon-tum' => 'kotu',
        'ninh-thuan' => 'nith',
        'phu-yen' => 'phye',
        'quang-binh' => 'qubi',
        'quang-nam' => 'quna',
        'quang-ngai' => 'qung',
        'quang-tri' => 'qutr',
        'thua-thien-hue' => 'thth',
        // Miền Nam
        'an-giang' => 'angi',
        'bac-lieu' => 'bali',
        'ben-tre' => 'betre',
        'binh-duong' => 'bidu',
        'binh-phuoc' => 'biph',
        'ca-mau' => 'cama',
        'can-tho' => 'cath',
        'dong-nai' => 'dona',
        'dong-thap' => 'doth',
        'hau-giang' => 'hagi',
        'kien-giang' => 'kigi',
        'long-an' => 'loan',
        'soc-trang' => 'sotr',
        'tay-ninh' => 'tani',
        'tien-giang' => 'tigi',
        'tra-vinh' => 'trvi',
        'vinh-long' => 'vilo',
        'vung-tau' => 'vuta',
    ];

    public function up(): void
    {
        foreach ($this->codeMap as $slug => $newCode) {
            DB::table('provinces')->where('slug', $slug)->update(['code' => $newCode]);
        }
    }

    public function down(): void
    {
        foreach ($this->reverseMap as $slug => $oldCode) {
            DB::table('provinces')->where('slug', $slug)->update(['code' => $oldCode]);
        }
    }
};
