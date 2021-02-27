<?php

namespace Database\Seeders;

use Botble\ACL\Models\User;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Slug\Models\Slug;
use Faker\Factory;
use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Str;
use SlugHelper;

class BlogSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('news');

        $posts = [
            [
                'name'        => '4 Expert Tips On How To Choose The Right Men’s Wallet',
                'description' => 'You should pay more attention when you choose your wallets. There are a lot of them on the market with the different designs and styles. When you choose carefully, you would be able to buy a wallet that is catered to your needs. Not to mention that it will help to enhance your style significantly.',
            ],
            [
                'name'        => 'Sexy Clutches: How to Buy & Wear a Designer Clutch Bag',
                'description' => 'There isn’t much a girl can’t handle with the right accessories. That’s why the perfect women’s clutch bag is a wardrobe essential for first dates and spring weddings.',
            ],
            [
                'name'        => 'The Top 2020 Handbag Trends to Know',
                'description' => 'For the handbag obsessives, let\'s dive into the latter a little more. This year will bring a fresh array of bag designs, and already we\'ve gotten a sneak peek of what both spring and fall 2020 collections have to offer/',
            ],
            [
                'name'        => 'How to Match the Color of Your Handbag With an Outfit',
                'description' => 'To match your multi-colored purses with your favorite outfits, simply select a complementary handbag hue that will make your outfit pop for work or the weekend.',
            ],
            [
                'name'        => 'How to Care for Leather Bags',
                'description' => 'A leather bag is a special, near-universally beloved object, appreciated in equal measure by bon-vivants of both sexes, who cherish the supple hand, understated burnish and heady smell that can only emanate from premium quality skin.',
            ],
            [
                'name'        => 'We\'re Crushing Hard on Summer\'s 10 Biggest Bag Trends',
                'description' => 'Summer is just around the corner, and there\'s no easier way to amp up your new-season look than with a chic, new handbag. Whether you\'re wearing basic sweat shorts and a tank top or a pretty floral maxi dress, a bag is an effortless way to add interest to all your summer ensembles.',
            ],
            [
                'name'        => 'Explore Fashion Trending For Guys In Autumn 2021',
                'description' => 'Summer is just around the corner, and there\'s no easier way to amp up your new-season look than with a chic, new handbag. Whether you\'re wearing basic sweat shorts and a tank top or a pretty floral maxi dress, a bag is an effortless way to add interest to all your summer ensembles.',
            ],
        ];

        $tags = [
            [
                'name' => 'General',
            ],
            [
                'name' => 'Design',
            ],
            [
                'name' => 'Jquery',
            ],
            [
                'name' => 'Branding',
            ],
            [
                'name' => 'Modern',
            ],
        ];

        $categories = [
            [
                'name'       => 'Ecommerce',
                'is_default' => true,
            ],
            [
                'name' => 'Fashion',
            ],
            [
                'name' => 'Electronic',
            ],
            [
                'name' => 'Commercial',
            ],
        ];

        Post::truncate();
        Category::truncate();
        Tag::truncate();
        Slug::where('reference_type', Post::class)->delete();
        Slug::where('reference_type', Category::class)->delete();
        Slug::where('reference_type', Tag::class)->delete();

        foreach ($tags as $item) {
            $item['author_id'] = 1;
            $item['author_type'] = User::class;
            $tag = Tag::create($item);

            Slug::create([
                'reference_type' => Tag::class,
                'reference_id'   => $tag->id,
                'key'            => Str::slug($tag->name),
                'prefix'         => SlugHelper::getPrefix(Tag::class),
            ]);
        }

        foreach ($categories as $item) {
            $item['author_id'] = 1;
            $item['author_type'] = User::class;

            $category = Category::create($item);

            Slug::create([
                'reference_type' => Category::class,
                'reference_id'   => $category->id,
                'key'            => Str::slug($category->name),
                'prefix'         => SlugHelper::getPrefix(Category::class),
            ]);
        }

        $faker = Factory::create();

        foreach ($posts as $index => $item) {
            $item['content'] = '<p>I have seen many people underestimating the power of their wallets. To them, they are just a functional item they use to carry. As a result, they often end up with the wallets which are not really suitable for them.</p>

<p>You should pay more attention when you choose your wallets. There are a lot of them on the market with the different designs and styles. When you choose carefully, you would be able to buy a wallet that is catered to your needs. Not to mention that it will help to enhance your style significantly.</p>

<p style="text-align:center"><img alt="f4" src="/storage/news/1.jpg" /></p>

<p><br />
&nbsp;</p>

<p><strong><em>For all of the reason above, here are 7 expert tips to help you pick up the right men&rsquo;s wallet for you:</em></strong></p>

<h4><strong>Number 1: Choose A Neat Wallet</strong></h4>

<p>The wallet is an essential accessory that you should go simple. Simplicity is the best in this case. A simple and neat wallet with the plain color and even&nbsp;<strong>minimalist style</strong>&nbsp;is versatile. It can be used for both formal and casual events. In addition, that wallet will go well with most of the clothes in your wardrobe.</p>

<p>Keep in mind that a wallet will tell other people about your personality and your fashion sense as much as other clothes you put on. Hence, don&rsquo;t go cheesy on your wallet or else people will think that you have a funny and particular style.</p>

<p style="text-align:center"><img alt="f5" src="/storage/news/2.jpg" /></p>

<p><br />
&nbsp;</p>

<hr />
<h4><strong>Number 2: Choose The Right Size For Your Wallet</strong></h4>

<p>You should avoid having an over-sized wallet. Don&rsquo;t think that you need to buy a big wallet because you have a lot to carry with you. In addition, a fat wallet is very ugly. It will make it harder for you to slide the wallet into your trousers&rsquo; pocket. In addition, it will create a bulge and ruin your look.</p>

<p>Before you go on to buy a new wallet, clean out your wallet and place all of the items from your wallet on a table. Throw away things that you would never need any more such as the old bills or the expired gift cards. Remember to check your wallet on a frequent basis to get rid of all of the old stuff that you don&rsquo;t need anymore.</p>

<p style="text-align:center"><img alt="f1" src="/storage/news/3.jpg" /></p>

<p><br />
&nbsp;</p>

<hr />
<h4><strong>Number 3: Don&rsquo;t Limit Your Options Of Materials</strong></h4>

<p>The types and designs of wallets are not the only things that you should consider when you go out searching for your best wallet. You have more than 1 option of material rather than leather to choose from as well.</p>

<p>You can experiment with other available options such as cotton, polyester and canvas. They all have their own pros and cons. As a result, they will be suitable for different needs and requirements. You should think about them all in order to choose the material which you would like the most.</p>

<p style="text-align:center"><img alt="f6" src="/storage/news/4.jpg" /></p>

<p><br />
&nbsp;</p>

<hr />
<h4><strong>Number 4: Consider A Wallet As A Long Term Investment</strong></h4>

<p>Your wallet is indeed an investment that you should consider spending a decent amount of time and effort on it. Another factor that you need to consider is how much you want to spend on your wallet. The price ranges of wallets on the market vary a great deal. You can find a wallet which is as cheap as about 5 to 7 dollars. On the other hand, you should expect to pay around 250 to 300 dollars for a high-quality wallet.</p>

<p>In case you need a wallet to use for a long time, it is a good idea that you should invest a decent amount of money on a wallet. A high quality wallet from a reputational brand with the premium quality such as cowhide leather will last for a long time. In addition, it is an accessory to show off your fashion sense and your social status.</p>

<p style="text-align:center"><img alt="f2" src="/storage/news/5.jpg" /></p>

<p>&nbsp;</p>
';
            $item['author_id'] = 1;
            $item['author_type'] = User::class;
            $item['views'] = $faker->numberBetween(100, 2500);
            $item['is_featured'] = true;
            $item['image'] = 'news/' . ($index + 1) . '.jpg';

            $post = Post::create($item);

            $post->categories()->sync([$faker->numberBetween(1, 4)]);

            $post->tags()->sync([1, 2, 3, 4, 5]);

            Slug::create([
                'reference_type' => Post::class,
                'reference_id'   => $post->id,
                'key'            => Str::slug($post->name),
                'prefix'         => SlugHelper::getPrefix(Post::class),
            ]);
        }
    }
}
