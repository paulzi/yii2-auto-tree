# Yii2 Auto Tree Trait

Allow apply multiple tree behavior for ActiveRecord in Yii2.

For selecting methods used by the most rapid behavior. For modifying methods - call all attached behaviors.

If you are using the adjacency list for a get list of the parents or the descendants as relation will be used getParentsOrdered() or getDescendantsOrdered().  

## Install

Install via Composer:

```bash
composer require paulzi/yii2-auto-tree
```

or add

```bash
"paulzi/yii2-auto-tree" : "^1.0"
```

to the `require` section of your `composer.json` file.

## Configuring

Install and configure one or more behaviors:

- [Adjacency List](https://github.com/paulzi/yii2-adjacency-list)
- [Nested Sets](https://github.com/paulzi/yii2-nested-sets)
- [Nested Intervals](https://github.com/paulzi/yii2-nested-intervals)
- [Materialized Path](https://github.com/paulzi/yii2-materialized-path)

And use AutoTreeTrait in model.

```php
use paulzi\adjacencylist\AdjacencyListBehavior;
use paulzi\nestedsets\NestedSetsBehavior;
use paulzi\autotree\AutoTreeTrait;

class Sample extends \yii\db\ActiveRecord
{
    use AutoTreeTrait;
    
    public function behaviors() {
        return [
            ['class' => AdjacencyListBehavior::className()],
            ['class' => NestedSetsBehavior::className()],
        ];
    }
}
```

## Extending

If you want to change the order call behavior, override the appropriate methods:

```php
use paulzi\autotree\AutoTreeTrait;

class Sample extends \yii\db\ActiveRecord
{
    use AutoTreeTrait;
    
    /**
     * @param int|null $depth
     * @return \yii\db\ActiveQuery
     */
    public function getParents($depth = null)
    {
        return $this->autoTreeCall('getParents', ['al', 'mp', 'ns', 'ni'], [$depth]);
    }
}
```

If you want to change the list of classes of behaviors:

```php
use paulzi\autotree\AutoTreeTrait;

class Sample extends \yii\db\ActiveRecord
{
    use AutoTreeTrait;
    
    /**
     * @return array
     */
    private static function autoTreeAliases()
    {
        return [
            'example\adjacencylist\AdjacencyListBehavior'       => 'al',
            'example\nestedsets\NestedSetsBehavior'             => 'ns',
            'example\nestedintervals\NestedIntervalsBehavior'   => 'ni',
            'example\materializedpath\MaterializedPathBehavior' => 'mp',
        ];
    }
}
```

## Behaviors speed comparison
```
Name                                            DB Queries  DB Time     Time        Mem

Test 1. Insert. Filling (3 level by 12 child)
    Adjacency List                              5811        1,567 ms    9,591 ms    71.3 MB
    Nested Sets                                 7697        6,672 ms    25,019 ms   105.9 MB
    Nested Intervals amount=24                  5813        1,765 ms    10,442 ms   78.7 MB
    Nested Intervals amount=12 noPrepend noIns. 5813        1,750 ms    10,223 ms   78.7 MB
    Materialized Path (identity pr. key mode)   7696        3,169 ms    13,726 ms   92.5 MB
    Materialized Path (attribute mode)          5811        1,690 ms    9,504 ms    71.6 MB

Test 2. Insert. Filling (6 level by 3 child)
    Adjacency List                              3642        982 ms      5,812 ms    44.5 MB
    Nested Sets                                 4736        5,447 ms    17,859 ms   65.0 MB
    Nested Intervals amount=10                  3644        1,275 ms    5,976 ms    48.9 MB
    Nested Intervals amount=3 noPrepend noIns.  3644        1,271 ms    5,993 ms    48.9 MB
    Materialized Path (identity pr. key mode)   4735        1,316 ms    6,920 ms    57.3 MB
    Materialized Path (attribute mode)          3642        1,129 ms    5,507 ms    44.6 MB

Test 3. Insert in begin <4% (20 in 19657 nodes)
    Adjacency List                              100         36 ms       190 ms      4.6 MB
    PaulZi                                      100         15,768 ms   16,712 ms   4.7 MB
    Nested Intervals                            82          21 ms       150 ms      4.7 MB
    Materialized Path (identity pr. key mode)   120         98 ms       355 ms      4.8 MB
    Materialized Path (attribute mode)          100         74 ms       334 ms      4.6 MB

Test 4. Insert in middle >46% <50% (20 in 19657 nodes)
    Adjacency List                              100         24 ms       150 ms      4.6 MB
    Nested Sets                                 100         8,212 ms    8,799 ms    4.7 MB
    Nested Intervals                            82          269 ms      593 ms      4.7 MB
    Materialized Path (identity pr. key mode)   120         35 ms       196 ms      4.9 MB
    Materialized Path (attribute mode)          100         287 ms      494 ms      4.6 MB

Test 5. Insert in end >96% (20 in 19657 nodes)
    Adjacency List                              100         31 ms       214 ms      4.5 MB
    Nested Sets                                 100         487 ms      899 ms      4.7 MB
    Nested Intervals                            83          46 ms       187 ms      4.7 MB
    Materialized Path (identity pr. key mode)   120         34 ms       229 ms      4.8 MB
    Materialized Path (attribute mode)          100         470 ms      718 ms      4.6 MB

Test 6. Delete from begin <4% (20 in 19657 nodes)
    Adjacency List parentJoin=0 childrenJoin=0  60          169 ms      257 ms      3.8 MB
    Adjacency List parentJoin=3 childrenJoin=3  60          87 ms       162 ms      3.8 MB
    Nested Sets                                 100         16,480 ms   16,902 ms   4.7 MB
    Nested Intervals                            60          164 ms      250 ms      4.2 MB
    Materialized Path (identity pr. key mode)   60          87 ms       201 ms      4.0 MB
    Materialized Path (attribute mode)          60          122 ms      219 ms      4.0 MB

Test 7. Insert. Random append (5 levels, 1000 nodes)
    Adjacency List                              4001        1,062 ms    5,976 ms    46.1 MB
    Nested Sets                                 5003        5,428 ms    17,334 ms   64.8 MB
    Nested Intervals amount=10                  8497        23,301 ms   41,060 ms   120.7 MB
    Nested Intervals x64 amount=10              7092        11,330 ms   23,618 ms   97.5 MB
    Nested Intervals amount=200,25 noPrep noIns 4009        1,431 ms    6,490 ms    50.2 MB
    Nested Intervals x64 a=250,30 noPrep noIns  4003        1,421 ms    6,615 ms    50.0 MB
    Materialized Path (identity pr. key mode)   5003        1,621 ms    8,184 ms    57.8 MB
    Materialized Path (attribute mode)          4002        1,269 ms    6,169 ms    46.2 MB
    
Test 8. Insert. Random operation (5 levels, 1000 nodes)
    Adjacency List                              4383        1,330 ms    6,147 ms    53.0 MB
    Nested Sets                                 5003        9,577 ms    24,334 ms   64.8 MB
    Nested Intervals amount=10                  7733        8,123 ms    24,031 ms   107.2 MB
    Nested Intervals x64 default amount=10      5663        3,761 ms    14,084 ms   75.6 MB
    Nested Intervals amount=200,25              4175        1,548 ms    7,223 ms    52.8 MB
    Nested Intervals x64 a=250,30 reserve=2     4003        1,541 ms    6,753 ms    50.0 MB
    Materialized Path (identity pr. key mode)   5392        3,211 ms    11,771 ms   65.0 MB
    Materialized Path (attribute mode)          4377        2,902 ms    10,132 ms   53.2 MB
    
Test 9. Move random in begin <4% (20 in 19657 nodes)
    Adjacency List                              112         39 ms       261 ms      4.5 MB
    Nested Sets                                 140         218 ms      566 ms      5.5 MB
    Nested Intervals                            160         180 ms      573 ms      6.0 MB
    Materialized Path (identity pr. key mode)   128         38 ms       307 ms      4.9 MB
    Materialized Path (attribute mode)          128         159 ms      495 ms      4.9 MB

Test 10. Move random from end to begin <4% >96% (20 of 19657 nodes)
    Nested Sets                                 140         16,991 ms   17,845 ms   5.5 MB
    Nested Intervals                            160         16,972 ms   17,854 ms   6.0 MB
    Materialized Path (identity pr. key mode)   132         49 ms       319 ms      4.9 MB
    Materialized Path (attribute mode)          132         205 ms      502 ms      4.9 MB
    Adjacency List                              112         33 ms       217 ms      4.5 MB
    
Test 11. Select all nodes (19657 nodes)
    Adjacency List                              1           33 ms       890 ms      179.1 MB
    Nested Sets                                 1           40 ms       1,208 ms    215.2 MB
    Nested Intervals                            1           42 ms       1,247 ms    225.3 MB
    Materialized Path (identity pr. key mode)   1           46 ms       1,240 ms    209.0 MB
    Materialized Path (attribute mode)          1           44 ms       1,106 ms    209.0 MB
    
Test 12. Select children and descendants (for 819 nodes in middle of 19657 nodes)
    Adjacency List parentJoin=0 childrenJoin=0  2562        720 ms      1,969 ms    36.9 MB
    Adjacency List parentJoin=3 childrenJoin=3  2461        704 ms      1,966 ms    35.3 MB
    Nested Sets                                 1641        522 ms      1,585 ms    25.0 MB
    Nested Intervals                            1641        579 ms      1,657 ms    25.0 MB
    Materialized Path (identity pr. key mode)   1641        569 ms      1,626 ms    23.4 MB
    Materialized Path (attribute mode)          1641        793 ms      6,552 ms    44.7 MB

Test 13. Select parents (for 819 nodes in middle of 19657 nodes)
    Adjacency List parentJoin=0 childrenJoin=0  3180        948 ms      2,304 ms    51.2 MB
    Adjacency List parentJoin=3 childrenJoin=3  1641        486 ms      1,495 ms    30.8 MB
    Nested Sets                                 821         3,238 ms    3,979 ms    20.7 MB
    Nested Intervals                            821         3,292 ms    4,147 ms    22.0 MB
    Materialized Path (identity pr. key mode)   821         292 ms      902 ms      21.2 MB
    Materialized Path (attribute mode)          821         582 ms      4,574 ms    24.7 MB

Test 14. Select next/prev (for 819 nodes in middle of 19657 nodes)
    Adjacency List parentJoin=0 childrenJoin=0  1641        535 ms      1,442 ms    23.7 MB
    Adjacency List parentJoin=3 childrenJoin=3  1641        508 ms      1,421 ms    23.6 MB
    Nested Sets                                 1641        513 ms      1,428 ms    24.5 MB
    Nested Intervals                            1641        19,681 ms   21,326 ms   27.5 MB
    Materialized Path (identity pr. key mode)   1641        730 ms      1,695 ms    24.3 MB
    Materialized Path (attribute mode)          1641        1,892 ms    2,964 ms    24.3 MB

Test 15. Select leaves (for 819 nodes in middle of 19657 nodes)
    Adjacency List parentJoin=0 childrenJoin=0  1833        568 ms      1,743 ms    32.6 MB
    Adjacency List parentJoin=3 childrenJoin=3  1732        556 ms      1,891 ms    31.3 MB
    Nested Sets                                 821         305 ms      908 ms      18.4 MB
    Nested Intervals                            821         10,450 ms   11,166 ms   18.8 MB
    Materialized Path (identity pr. key mode)   821         7,968 ms    8,434 ms    18.5 MB
    Materialized Path (attribute mode)          821         14,349 ms   19,105 ms   21.3 MB
```