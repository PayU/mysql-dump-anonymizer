<?php
declare(strict_types=1);

//namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Parser\InsertLineStringParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class InsertLineStringParserTest extends TestCase
{
    /**
     * @var InsertLineStringParser|MockObject
     */
    private $sut;

    public function setUp() : void
    {
        parent::setUp();

        $this->sut = new InsertLineStringParser();
    }

    public function testActionIndexSuccess(): void
    {
        //TODO finish test
        $query = <<<'EOD'
INSERT INTO `example_1` (`id`, `fname`, `json`, `dated`, `comment`) VALUES (1,'Alex','{}','2020-02-20 02:20:02','Lorem ipsum dolor sit amet'),(2,'Gigi','{\"key1\":\"test data\",\"key2\":{\"0\":1,\"1\":2,\"2\":3,\"key2-1\":\"yellow\"},\"0\":1,\"1\":2,\"2\":3}','1998-02-02 02:02:02','My email is asd@asd.com'),(3,'Bjorg','[]','1980-10-10 10:10:10','Hello world'),(4,'Artemis','[1,2,3]','2020-01-01 01:01:01','Lorem Ipsum este pur şi simplu o machetă pentru text a industriei tipografice. Lorem Ipsum a fost macheta standard a industriei încă din secolul al XVI-lea, când un tipograf anonim a luat o planşetă de litere şi le-a amestecat pentru a crea o carte demonstrativă pentru literele respective. Nu doar că a supravieţuit timp de cinci secole, dar şi a facut saltul în tipografia electronică practic neschimbată. A fost popularizată în anii \'60 odată cu ieşirea colilor Letraset care conţineau pasaje Lorem Ipsum, iar mai recent, prin programele de publicare pentru calculator, ca Aldus PageMaker care includeau versiuni de Lorem Ipsum.'),(5,'QuoteMe',NULL,'2020-02-25 12:05:08','I want 2 backslash here [\\\\] and backslash quote here [\\\"]'),(6,'NewLine',NULL,'2020-01-01 01:11:12','After this there is a new line-text-newline [\\\r\nhello\r\n\\]  double-single [\"\']');
EOD;

        echo "[$query]";


        $actual = $this->sut->parse($query);
        foreach ($actual->getValuesList() as $qwe) {
            foreach ($qwe as $value) {
                echo $value->getRawValue()." == ".$value->getUnEscapedValue()."\n";
            }
        echo "==============\n";
        }
        //var_dump($actual);




    }
}
