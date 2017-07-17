<?php


class bhTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
        $key = null;
        $id = 3;
        $flag = 0;
        $this->assertNotEmpty($id);
        $this->assertEquals(null,$key);
        $this->assertEquals(0,$flag);
        if(!empty($key)){
            $condition = sprintf('app_id=%s and flag=%s and key_id=%s',
                $id,$flag,$key);
        }else{
            $condition = sprintf('app_id=%s and flag=%s',
                $id,$flag);
        }
        $this->assertNotEmpty($condition);

        //处理文本消息格式
        $data = [];
        $articles = [];
        $this->assertEquals(0,count($data));
        $this->assertEquals(0,count($articles));
        $time = time();

        if(!empty($articles)){
            foreach ($articles as $key){
                $data[] = $key;
            }
        }
        return $data;
    }


    /**
     * @dataProvider additionProvider
     */
    public function testAdd($a, $b, $expected)
    {
        $this->assertEquals($expected, $a + $b);
    }

    public function additionProvider()
    {
        return [
            'adding zeros'  => [0, 0, 0],
            'zero plus one' => [0, 1, 1],
            'one plus zero' => [1, 0, 1],
            'one plus one'  => [2, 1, 3]
        ];
    }
}