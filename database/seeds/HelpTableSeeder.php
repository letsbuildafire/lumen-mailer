<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\HelpArticle;

class HelpTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
     public function run()
     {

        // Seed some test lists
        $num_articles = 30;
        while($num_articles--){
            HelpArticle::create([
                'title' => "Help Article {$num_articles}",
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed mollis ultricies leo, vitae sodales mi bibendum sit amet. Maecenas in tellus nec metus semper mollis. Quisque a sapien non velit ullamcorper scelerisque vitae in elit. Nam scelerisque tortor in tempus egestas. Vivamus molestie tellus sed diam elementum rhoncus. Nullam ornare.',
                'section' => ['EMAILERS','TEMPLATES','LISTS','GENERAL'][rand(0,3)]
            ]);
        }

    }
}
