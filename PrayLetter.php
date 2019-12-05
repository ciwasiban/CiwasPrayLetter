<?php
class PrayLetter
{
    /** 設定寄件者資訊 **/
    public $mailer = "同工曉芬(LDT 領袖門訓宣教計畫)";
    public $username = "xxx@gmail.com";
    public $password = "password";

    /** 設定信件內容 **/
    public $subject = '平安，這是門訓事工11月的代禱信';
    public $body = '';

    // 招呼語
    public $hola = '感謝神帶領我們在11月的門訓計劃上有美好的突破，';

    // 本月焦點
    public $letterUrl = 'http://bit.ly/2sI9Z59';
    public $letterUrl1 = 'http://bit.ly/2LoyZER';
    public $letterUrl2 = 'http://bit.ly/2Pg2rOI';
    public $letterUrl3 = 'http://bit.ly/2LoJVT4';
    public $focusTitle1 = '見證神使軟弱的我變為剛強';
    public $focusTitle2 = '看見我們是屬靈生命的園丁';
    public $focusTitle3 = '伸出鐮刀收割屬神成熟莊稼';

    public $focusPoint1 = Array();  // setting in function setFocusPoint()
    public $focusPoint2 = Array();
    public $focusPoint3 = Array();

    // 奉獻情形
    public $donationOfThisMonth = '25,000';    // 本月收到奉獻
    public $expenditureOfThisMonth = '24,414'; // 每月支出
    public $yearlyDonation = '307,044';        // 年度總共需要奉獻
    public $donationOfThisYear = '206,306';    // 實際收到奉獻
    public $annualShortfall = '100,738';       // 年度尚缺奉獻金額

    function __construct() {
        // init
        $this->setFocusPoint();
    }

    // 產生信件內容
    function generate() {
        $bodyTemplate = file_get_contents( __DIR__ . '/letterTemplate.html');
        // 產生內容
        $find = Array('{%hola%}',
            '{%focuspoint1.alt%}', '{%focuspoint1.url%}',
            '{%focuspoint2.alt%}', '{%focuspoint2.url%}',
            '{%focuspoint3.alt%}', '{%focuspoint3.url%}',
            '{%letterUrl%}',
            '{%donationOfThisMonth%}', '{%expenditureOfThisMonth%}', '{%yearlyDonation%}', '{%donationOfThisYear%}', '{%annualShortfall%}'
            );
        $replace = Array($this->hola,
            $this->focusPoint1['alt'], $this->focusPoint1['url'],
            $this->focusPoint2['alt'], $this->focusPoint2['url'],
            $this->focusPoint3['alt'], $this->focusPoint3['url'],
            $this->letterUrl,
            $this->donationOfThisMonth, $this->expenditureOfThisMonth, $this->yearlyDonation, $this->donationOfThisYear, $this->annualShortfall
        );
        $this->body = str_replace($find , $replace, $bodyTemplate);
    }

    // 取得發送清單
    function getRecipentList() {
        $userList = [];
        $datas = [];
        $tsvFile = __DIR__ . '/' . 'recipentList.tsv';

        $data = file_get_contents($tsvFile, false);

        $cnt = count($data);
        if ($cnt > 0) {
            $lineList = explode(PHP_EOL, $data);
            foreach ($lineList AS $k => $line) {
                // ingnore line 0
                if (0 == $k ) {
                    continue;
                } else {
                    // get name
                    $val = explode("\t", $line);
                    $name = trim(array_shift($val));
                    $email = trim(array_shift($val));

                    // make array $bibleList
                    if (!empty($name) && !empty($email)) {
                        $userList[$name] = $email;
                    }
                }
            }
        }

        return $userList;
    }

    function setFocusPoint() {
        $this->focusPoint1 = Array('alt'=> $this->focusTitle1, 'url'=> $this->letterUrl1);
        $this->focusPoint2 = Array('alt'=> $this->focusTitle2, 'url'=> $this->letterUrl2);
        $this->focusPoint3 = Array('alt'=> $this->focusTitle3, 'url'=> $this->letterUrl3);
    }
}
