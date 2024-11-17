<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\VarDumper\VarDumper;
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

    public function testFilter()
    {
        $collection = collect([
            'farhan' => 100,
            'akmal' => 90,
            'wahyu' => 67
        ]);

        $result = $collection->filter(function ($item, $key) {
            return $item > 80;
        });
        
        $this->assertEquals($result->all(), [
            'farhan' => 100,
            'akmal' => 90
        ]);
    }

    public function testFilterIndex()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->filter(function ($value, $key) {
            return $value % 2 == 0;
        })->values();

        $this->assertEqualsCanonicalizing([2,4,6,8], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            'farhan' => 100,
            'akmal' => 90,
            'wahyu' => 67
        ]);

        [$result1, $result2] = $collection->partition(function ($item, $key) {
            return $item > 80;
        });
        
        $this->assertEquals($result1->all(), [
            'farhan' => 100,
            'akmal' => 90
        ]);
        $this->assertEquals($result2->all(), [
            'wahyu' => 67
        ]);
    }

    public function testTesting()
    {
        $collection = collect(['farhan', 'yudi']);
        $this->assertTrue($collection->contains('farhan'));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == 'farhan';
        }));
    }

    public function testGrouping()
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

        $result = $collection->groupBy('department');

        $this->assertEquals($result->all(), [
            'it' => collect([
                [
                    'name' => 'farhan',
                    'department' => 'it'
                ],
                [
                    'name' => 'wahyu',
                    'department' => 'it'
                ]
            ]),
            'hr' => collect([
                [
                    'name' => 'akmal',
                    'department' => 'hr'
                ]
            ])
        ]);
    }

    public function testSlicing()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result1 = $collection->slice(3)->values();
        $result2 = $collection->slice(3, 2)->values();

        $this->assertEqualsCanonicalizing($result1->all(), [4,5,6,7,8,9]);
        $this->assertEqualsCanonicalizing($result2->all(), [4,5]);
    }

    public function testTake()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->take(3);
        $this->assertEqualsCanonicalizing($result->all(), [1,2,3]);

        $result2 = $collection->takeUntil(function ($value) {
            return $value == 3;
        });
        $this->assertEqualsCanonicalizing($result2->all(), [1,2]);

        $result3 = $collection->takeWhile(function ($value) {
            return $value < 3;
        });
        $this->assertEqualsCanonicalizing($result3->all(), [1,2]);
    }

    public function testSkip() {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->skip(3)->values();
        $this->assertEqualsCanonicalizing($result->all(), [4,5,6,7,8,9]);

        $result2 = $collection->skipUntil(function ($value) {
            return $value == 3;
        })->values();
        $this->assertEqualsCanonicalizing($result2->all(), [3,4,5,6,7,8,9]);

        $result3 = $collection->skipWhile(function ($value) {
            return $value < 3;
        })->values();
        $this->assertEqualsCanonicalizing($result3->all(), [3,4,5,6,7,8,9]);
    }

    public function testChunk()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->chunk(4);

        $this->assertEqualsCanonicalizing($result->all()[0]->all(), [1,2,3,4]);
        $this->assertEquals(array_values($result->all()[1]->all()), [5,6,7,8]);
        $this->assertEqualsCanonicalizing(array_values($result->all()[2]->all()), [9,10]);
    }

    public function testFirst() {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->first();
        $this->assertEquals(1, $result);

        $result = $collection->first(function ($value, $key) {
            return $value > 5;
        });
        $this->assertEquals(6, $result);
    }

    public function testLast() {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->last();
        $this->assertEquals(9, $result);

        $result = $collection->last(function ($value, $key) {
            return $value < 5;
        });
        $this->assertEquals(4, $result);
    }

    public function testRandom()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->random();
        $this->assertTrue(in_array($result, [1,2,3,4,5,6,7,8,9]));
    }

    public function testCheckingExistence()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $this->assertTrue($collection->isNotEmpty());
        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($collection->contains(8));
        $this->assertFalse($collection->contains(12));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == 8;
        }));
    }

    public function testOrdering()
    {
        $collection = collect([1,4,2,3,7,6,5,8,9]);
        $result = $collection->sort()->values();
        $this->assertEquals($result->all(), [1,2,3,4,5,6,7,8,9]);

        $result = $collection->sortDesc()->values();
        $this->assertEquals($result->all(), [9,8,7,6,5,4,3,2,1]);
    }

    public function testAggregate()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $result = $collection->sum();
        $this->assertEquals($result, 45);
        

        $result = $collection->count();
        $this->assertEquals($result, 9);

        $result = $collection->avg();
        $this->assertEquals($result, 5);

        $result = $collection->min();
        $this->assertEquals($result, 1);
        
        $result = $collection->max();
        $this->assertEquals($result, 9);
    }

    public function testReduce()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        });
        $this->assertEquals($result, 45);
    }
}
