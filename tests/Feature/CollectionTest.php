<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3]);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        foreach ($collection as $key => $value) {
            $this->assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect();
        $collection->push(1, 2, 3);
        $this->assertEqualsCanonicalizing($collection->all(), [1, 2, 3]);

        $result = $collection->pop();
        $this->assertEquals($result, 3);
        $this->assertEqualsCanonicalizing($collection->all(), [1, 2]);
    }

    public function testMap() {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });

        $this->assertEqualsCanonicalizing($result->all(), [2, 4, 6]);
    }

    public function testMapInto()
    {
        $collection = collect(['hans']);
        $result = $collection->mapInto(Person::class);
        $this->assertEqualsCanonicalizing($result->all(), [new Person('hans')]);
    }

    public function testMapSpread()
    {
        $collection = collect([['farhan', 'wahyudi'], ['akmal', 'ramadhan']]);
        $result = $collection->mapSpread(function ($firstname, $lastname) {
            return $firstname . ' ' . $lastname;
        });

        $this->assertEqualsCanonicalizing($result->all(), ['farhan wahyudi', 'akmal ramadhan']);
    }

    public function testMapToGroup()
    {
        $collection = collect([
            [
                'name' => 'farhan',
                'department' => 'it'
            ],
            [
                'name' => 'wahyu',
                'department' => 'it'
            ],
            [
                'name' => 'akmal',
                'department' => 'hr'
            ]
        ]);

        $result = $collection->mapToGroups(function ($item) {
            return [$item['department'] => $item['name']];
        });

        $this->assertEquals($result->all(), [
            'it' => collect(['farhan', 'wahyu']),
            'hr' => collect(['akmal'])
        ]);
    }

    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEqualsCanonicalizing($collection3->all(), [
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6]),
        ]);
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing($collection3->all(), [1, 2, 3, 4, 5, 6]);
    }

    public function testCombine()
    {
        $collection1 = collect(['name', 'country']);
        $collection2 = collect(['farhan', 'indonesia']);
        $collection3 = $collection1->combine($collection2);

        $this->assertEqualsCanonicalizing($collection3->all(), [
            'name' => 'farhan',
            'country' => 'indonesia'
        ]);
    }

    public function testCollapse()
    {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9]
        ]);
        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing($result->all(), [1,2,3,4,5,6,7,8,9]);
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                'name' => 'farhan',
                'hobbies' => ['coding', 'drawing']
            ],
            [
                'name' => 'akmal',
                'hobbies' => ['gaming', 'drawing']
            ]
        ]);
        $result = $collection->flatMap(function ($item) {
            return $item['hobbies'];
        });
        $this->assertEqualsCanonicalizing($result->all(), ['coding', 'drawing', 'gaming', 'drawing']);
    }

    public function testStringRepresentation()
    {
        $collection = collect(['farhan', 'wahyu', 'yudi']);
        
        $this->assertEquals($collection->join('-'), 'farhan-wahyu-yudi');
        $this->assertEquals($collection->join('-', '_'), 'farhan-wahyu_yudi');
    }
}
