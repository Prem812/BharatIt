<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('blog_categories')->delete();
        $blog_categories = array(
            array('id' => 1, 'name' => "Tech News & Updates"),
            array('id' => 2, 'name' => "How-To & Tutorials"),
            array('id' => 3, 'name' => "Reviews"),
            array('id' => 4, 'name' => "Explainer & Insights"),
            array('id' => 5, 'name' => "Opinion & Analysis"),
            array('id' => 6, 'name' => "Listicle & Comparison"),
            array('id' => 7, 'name' => "Tech for [Specific Niche]"),
            array('id' => 8, 'name' => "Tech Humor & Entertainment"),
        );
        DB::table('blog_categories')->insert($blog_categories);
    }
}
