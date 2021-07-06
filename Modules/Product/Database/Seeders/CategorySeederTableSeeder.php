<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Category;

class CategorySeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $data = [
[
            'id' => 1,
            'name' => 'Electronic Device',
            'code' => '001',
            'description' => 'All of electronic device.',
            'parent_id' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 04:51:51',
            'updated_at' => '2020-10-14 04:51:51'
        ],


        [
            'id' => 2,
            'name' => 'Mobile',
            'code' => '002',
            'description' => 'Mobile',
            'parent_id' => 1,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 04:52:29',
            'updated_at' => '2020-10-14 04:52:29'
        ],


        [
            'id' => 3,
            'name' => 'Laptop',
            'code' => '003',
            'description' => 'Laptop',
            'parent_id' => 1,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 04:53:14',
            'updated_at' => '2020-10-14 04:53:14'
        ],


        [
            'id' => 4,
            'name' => 'Desktop',
            'code' => '004',
            'description' => 'Desktop pC.',
            'parent_id' => 1,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 04:53:51',
            'updated_at' => '2020-10-14 04:53:51'
        ],


        [
            'id' => 5,
            'name' => 'Gaming Console',
            'code' => '005',
            'description' => 'all of gaming device.',
            'parent_id' => 1,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 04:54:34',
            'updated_at' => '2020-10-14 04:54:34'
        ],


        [
            'id' => 6,
            'name' => 'Camera',
            'code' => '007',
            'description' => 'all of camera device.',
            'parent_id' => 1,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 04:56:19',
            'updated_at' => '2020-10-14 04:56:19'
        ],


        [
            'id' => 7,
            'name' => 'TV & Home Appliance',
            'code' => '008',
            'description' => 'Telivision and home appliance',
            'parent_id' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:05:48',
            'updated_at' => '2020-10-14 05:05:48'
        ],


        [
            'id' => 8,
            'name' => 'Television',
            'code' => '009',
            'description' => 'All types of Television',
            'parent_id' => 7,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:07:20',
            'updated_at' => '2020-10-14 05:08:51'
        ],


        [
            'id' => 9,
            'name' => 'LED Television',
            'code' => '010',
            'description' => 'All of led television',
            'parent_id' => 8,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:08:14',
            'updated_at' => '2020-10-14 05:08:14'
        ],


        [
            'id' => 10,
            'name' => 'Smart Television',
            'code' => '011',
            'description' => 'All off Smart Television',
            'parent_id' => 8,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:10:10',
            'updated_at' => '2020-10-14 05:10:10'
        ],


        [
            'id' => 11,
            'name' => 'Home Audio',
            'code' => '012',
            'description' => 'Home audio',
            'parent_id' => 11,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:10:53',
            'updated_at' => '2020-10-14 05:13:32'
        ],


        [
            'id' => 12,
            'name' => 'Refrigerator',
            'code' => '013',
            'description' => 'All off refrigerator',
            'parent_id' => 7,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:17:30',
            'updated_at' => '2020-10-14 05:17:30'
        ],


        [
            'id' => 13,
            'name' => 'Cooling & Heating',
            'code' => '014',
            'description' => 'all of cooling and heating device',
            'parent_id' => 7,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:23:03',
            'updated_at' => '2020-10-14 05:23:03'
        ],


        [
            'id' => 14,
            'name' => 'Air Purifiers',
            'code' => '015',
            'description' => 'Air Purifiers',
            'parent_id' => 13,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:23:58',
            'updated_at' => '2020-10-14 05:23:58'
        ],


        [
            'id' => 15,
            'name' => 'Air Conditioner',
            'code' => '016',
            'description' => 'Air Conditioner',
            'parent_id' => 13,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:24:42',
            'updated_at' => '2020-10-14 05:24:42'
        ],


        [
            'id' => 16,
            'name' => 'Air Cooler',
            'code' => '017',
            'description' => 'Air Cooler',
            'parent_id' => 13,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:26:26',
            'updated_at' => '2020-10-14 05:26:26'
        ],


        [
            'id' => 17,
            'name' => 'Health and beauty',
            'code' => '018',
            'description' => 'Health and beauty',
            'parent_id' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:27:33',
            'updated_at' => '2020-10-14 05:27:33'
        ],


        [
            'id' => 18,
            'name' => 'Bath & Body',
            'code' => '019',
            'description' => 'Bath and body',
            'parent_id' => 17,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:28:19',
            'updated_at' => '2020-10-14 05:28:19'
        ],


        [
            'id' => 19,
            'name' => 'Beauty Tools',
            'code' => '020',
            'description' => 'Beauty Tools',
            'parent_id' => 17,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:29:07',
            'updated_at' => '2020-10-14 05:29:07'
        ],


        [
            'id' => 20,
            'name' => 'Hair Care',
            'code' => '021',
            'description' => 'Hair Care',
            'parent_id' => 17,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:31:56',
            'updated_at' => '2020-10-14 05:31:56'
        ],


        [
            'id' => 21,
            'name' => 'Makeup',
            'code' => '022',
            'description' => 'Makeup',
            'parent_id' => 17,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:32:49',
            'updated_at' => '2020-10-14 05:32:49'
        ],


        [
            'id' => 22,
            'name' => 'Men Care',
            'code' => '023',
            'description' => 'Men Care',
            'parent_id' => 17,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 05:39:04',
            'updated_at' => '2020-10-14 05:39:04'
        ],


        [
            'id' => 23,
            'name' => 'Babies & Toys',
            'code' => '030',
            'description' => 'Babies & Toys',
            'parent_id' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:49:03',
            'updated_at' => '2020-10-14 07:49:03'
        ],


        [
            'id' => 24,
            'name' => 'Feeding',
            'code' => '031',
            'description' => 'Feeding',
            'parent_id' => 23,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:49:42',
            'updated_at' => '2020-10-14 07:49:42'
        ],


        [
            'id' => 25,
            'name' => 'Baby Gear',
            'code' => '032',
            'description' => 'Baby Gear',
            'parent_id' => 23,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:50:10',
            'updated_at' => '2020-10-14 07:50:10'
        ],


        [
            'id' => 26,
            'name' => 'Clothing and accessories',
            'code' => '033',
            'description' => 'Clothing and accessories',
            'parent_id' => 23,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:51:13',
            'updated_at' => '2020-10-14 07:51:13'
        ],


        [
            'id' => 27,
            'name' => 'Baby Toys',
            'code' => '035',
            'description' => 'Baby Toys',
            'parent_id' => 23,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:51:47',
            'updated_at' => '2020-10-14 07:51:47'
        ],


        [
            'id' => 28,
            'name' => 'Home & Lifestryle',
            'code' => '036',
            'description' => 'Home & Lifestryle',
            'parent_id' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:53:12',
            'updated_at' => '2020-10-14 07:53:12'
        ],


        [
            'id' => 29,
            'name' => 'Bath',
            'code' => '037',
            'description' => 'Bath',
            'parent_id' => 28,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:53:43',
            'updated_at' => '2020-10-14 07:53:43'
        ],


        [
            'id' => 30,
            'name' => 'Bedding',
            'code' => '039',
            'description' => 'Bedding',
            'parent_id' => 28,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:54:09',
            'updated_at' => '2020-10-14 07:54:09'
        ],


        [
            'id' => 31,
            'name' => 'Furniture',
            'code' => '040',
            'description' => 'Furniture',
            'parent_id' => 28,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:56:50',
            'updated_at' => '2020-10-14 07:56:50'
        ],


        [
            'id' => 32,
            'name' => 'Kitchen',
            'code' => '041',
            'description' => 'Kitchen',
            'parent_id' => 28,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:57:26',
            'updated_at' => '2020-10-14 07:57:26'
        ],


        [
            'id' => 33,
            'name' => 'Lighting',
            'code' => '042',
            'description' => 'Lighting',
            'parent_id' => 28,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:58:26',
            'updated_at' => '2020-10-14 07:58:26'
        ],


        [
            'id' => 34,
            'name' => 'Laundry & cleaning',
            'code' => '043',
            'description' => 'Laundry & cleaning',
            'parent_id' => 28,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:59:14',
            'updated_at' => '2020-10-14 07:59:14'
        ],


        [
            'id' => 35,
            'name' => 'Media, Music & Book',
            'code' => '046',
            'description' => 'Media, Music & Book',
            'parent_id' => 28,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 07:59:51',
            'updated_at' => '2020-10-14 07:59:51'
        ],


        [
            'id' => 36,
            'name' => 'Women\'s Fashion',
            'code' => '050',
            'description' => 'Women\'s Fashion',
            'parent_id' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:00:41',
            'updated_at' => '2020-10-14 08:00:41'
        ],


        [
            'id' => 37,
            'name' => 'Dress',
            'code' => '051',
            'description' => 'Dress',
            'parent_id' => 36,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:01:03',
            'updated_at' => '2020-10-14 08:01:03'
        ],


        [
            'id' => 38,
            'name' => 'kurti',
            'code' => '053',
            'description' => 'kurti',
            'parent_id' => 36,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:01:54',
            'updated_at' => '2020-10-14 08:01:54'
        ],


        [
            'id' => 39,
            'name' => 'Febrics',
            'code' => '055',
            'description' => 'Febrics',
            'parent_id' => 36,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:02:32',
            'updated_at' => '2020-10-14 08:02:32'
        ],


        [
            'id' => 40,
            'name' => 'Wedding Dress',
            'code' => '056',
            'description' => 'Wedding Dress',
            'parent_id' => 36,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:03:21',
            'updated_at' => '2020-10-14 08:03:21'
        ],


        [
            'id' => 41,
            'name' => 'Shoes',
            'code' => '057',
            'description' => 'Shoes',
            'parent_id' => 36,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:05:01',
            'updated_at' => '2020-10-14 08:05:01'
        ],


        [
            'id' => 42,
            'name' => 'Men\'s Fashion',
            'code' => '058',
            'description' => 'Men\'s Fashion',
            'parent_id' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:05:55',
            'updated_at' => '2020-10-14 08:05:55'
        ],


        [
            'id' => 43,
            'name' => 'Pant',
            'code' => '60',
            'description' => 'Pant',
            'parent_id' => 42,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:06:28',
            'updated_at' => '2020-10-14 08:06:28'
        ],


        [
            'id' => 44,
            'name' => 'Shirt',
            'code' => '61',
            'description' => 'Shirt',
            'parent_id' => 42,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:06:54',
            'updated_at' => '2020-10-14 08:06:54'
        ],


        [
            'id' => 45,
            'name' => 'T-Shirt',
            'code' => '62',
            'description' => 'T-Shirt',
            'parent_id' => 42,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:07:29',
            'updated_at' => '2020-10-14 08:07:29'
        ],


        [
            'id' => 46,
            'name' => 'Jeans',
            'code' => '66',
            'description' => 'Jeans',
            'parent_id' => 42,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:14:41',
            'updated_at' => '2020-10-14 08:14:41'
        ],


        [
            'id' => 47,
            'name' => 'Clothing',
            'code' => '67',
            'description' => 'Clothing',
            'parent_id' => 42,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:15:07',
            'updated_at' => '2020-10-14 08:15:07'
        ],


        [
            'id' => 48,
            'name' => 'Men\'s Bag',
            'code' => '68',
            'description' => 'Men\'s Bag',
            'parent_id' => 42,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2020-10-14 08:15:46',
            'updated_at' => '2020-10-14 08:15:46'
        ]
        ];


        Category::insert($data);
        
    }
}
