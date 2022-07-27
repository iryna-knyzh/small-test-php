<?php

namespace TestProject\Tests;

use PHPUnit\Framework\TestCase;
use TestProject\Src\CommissionsHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

final class CommissionsHelperTest extends TestCase
{
    public function testIsEu()
    {
        $helper = new CommissionsHelper(new Client());

        $this->assertTrue($helper->isEu('EU'));
    }

    public function testGetCountryShortName()
    {
        $mock = new MockHandler([
            new Response(200, [],
                '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Visa/Dankort","prepaid":false,"country":{"numeric":"208","alpha2":"DK","name":"Denmark","emoji":"🇩🇰","currency":"DKK","latitude":56,"longitude":10},"bank":{"name":"Jyske Bank","url":"www.jyskebank.dk","phone":"+4589893300","city":"Hjørring"}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler]);

        $countryShortName = (new CommissionsHelper($guzzleClient))->getCountryShortName('4745030');

        $this->assertEquals($countryShortName, 'DK');
    }

    public function testGetAmntFixed()
    {
        $mock = new MockHandler([
            new Response(200, [],
                '{"success":true,"timestamp":1658916484,"base":"EUR","date":"2022-07-27","rates":{"AED":3.725356,"AFN":91.749002,"ALL":116.631855,"AMD":413.743451,"ANG":1.829843,"AOA":438.45315,"ARS":132.474808,"AUD":1.459405,"AWG":1.823114,"AZN":1.726284,"BAM":1.956197,"BBD":2.050084,"BDT":96.177122,"BGN":1.956085,"BHD":0.382398,"BIF":2091.633502,"BMD":1.01425,"BND":1.409799,"BOB":6.99051,"BRL":5.42776,"BSD":1.01529,"BTC":4.7611866e-5,"BTN":80.918264,"BWP":12.836005,"BYN":2.562742,"BYR":19879.301129,"BZD":2.046584,"CAD":1.30396,"CDF":2031.037113,"CHF":0.974786,"CLF":0.033997,"CLP":938.080482,"CNY":6.848011,"COP":4515.045699,"CRC":681.907781,"CUC":1.01425,"CUP":26.877627,"CVE":110.284579,"CZK":24.571212,"DJF":180.751197,"DKK":7.444027,"DOP":55.163337,"DZD":148.413945,"EGP":19.232008,"ERN":15.213751,"ETB":53.413746,"EUR":1,"FJD":2.229677,"FKP":0.85434,"GBP":0.840387,"GEL":2.982327,"GGP":0.85434,"GHS":8.401792,"GIP":0.85434,"GMD":54.860722,"GNF":8790.463896,"GTQ":7.853494,"GYD":212.419805,"HKD":7.961493,"HNL":24.977557,"HRK":7.514579,"HTG":118.175057,"HUF":404.107449,"IDR":15210.150277,"ILS":3.475424,"IMP":0.85434,"INR":81.034017,"IQD":1481.897689,"IRR":42953.489807,"ISK":139.11443,"JEP":0.85434,"JMD":155.202318,"JOD":0.719917,"JPY":138.562751,"KES":120.442417,"KGS":82.114093,"KHR":4158.881833,"KMF":471.245913,"KPW":912.825043,"KRW":1332.907411,"KWD":0.311681,"KYD":0.846059,"KZT":487.411335,"LAK":15291.290012,"LBP":1535.365001,"LKR":360.429863,"LRD":154.672774,"LSL":16.066014,"LTL":2.994817,"LVL":0.613509,"LYD":4.955455,"MAD":10.415752,"MDL":19.595775,"MGA":4277.18149,"MKD":61.626548,"MMK":1879.884066,"MNT":3189.471578,"MOP":8.208438,"MRO":362.087096,"MUR":46.103638,"MVR":15.568395,"MWK":1042.140284,"MXN":20.690549,"MYR":4.522033,"MZN":64.739706,"NAD":16.066182,"NGN":421.16768,"NIO":36.45673,"NOK":9.961599,"NPR":129.469623,"NZD":1.625361,"OMR":0.390006,"PAB":1.01529,"PEN":3.976645,"PGK":3.578359,"PHP":56.421213,"PKR":236.445007,"PLN":4.783863,"PYG":7002.812238,"QAR":3.69287,"RON":4.934429,"RSD":117.23673,"RUB":61.133941,"RWF":1049.081413,"SAR":3.809578,"SBD":8.276249,"SCR":12.997284,"SDG":463.008548,"SEK":10.452435,"SGD":1.407358,"SHP":1.397027,"SLL":13357.673471,"SOS":592.83146,"SRD":24.527106,"STD":20992.92843,"SVC":8.883665,"SYP":2548.333122,"SZL":17.043594,"THB":37.335505,"TJS":10.356392,"TMT":3.560018,"TND":3.107156,"TOP":2.378467,"TRY":18.150202,"TTD":6.891589,"TWD":30.380643,"TZS":2365.231087,"UAH":37.364202,"UGX":3903.931547,"USD":1.01425,"UYU":42.455118,"UZS":11127.207719,"VND":23713.166347,"VUV":120.5903,"WST":2.759744,"XAF":656.076706,"XAG":0.054112,"XAU":0.000589,"XCD":2.741062,"XDR":0.769244,"XOF":656.070236,"XPF":114.761943,"YER":253.815706,"ZAR":17.094149,"ZMK":9129.467566,"ZMW":17.057596,"ZWL":326.588105}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler]);

        $amntFixed = (new CommissionsHelper($guzzleClient))->getAmntFixed(2000.00, 'GBP');

        $this->assertEquals($amntFixed, 2379.8559473195);
    }

    public function testGetCommission()
    {
        $mock = new MockHandler([
            new Response(200, [],
                '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Visa/Dankort","prepaid":false,"country":{"numeric":"208","alpha2":"DK","name":"Denmark","emoji":"🇩🇰","currency":"DKK","latitude":56,"longitude":10},"bank":{"name":"Jyske Bank","url":"www.jyskebank.dk","phone":"+4589893300","city":"Hjørring"}}'),

            new Response(200, [],
                '{"success":true,"timestamp":1658916484,"base":"EUR","date":"2022-07-27","rates":{"AED":3.725356,"AFN":91.749002,"ALL":116.631855,"AMD":413.743451,"ANG":1.829843,"AOA":438.45315,"ARS":132.474808,"AUD":1.459405,"AWG":1.823114,"AZN":1.726284,"BAM":1.956197,"BBD":2.050084,"BDT":96.177122,"BGN":1.956085,"BHD":0.382398,"BIF":2091.633502,"BMD":1.01425,"BND":1.409799,"BOB":6.99051,"BRL":5.42776,"BSD":1.01529,"BTC":4.7611866e-5,"BTN":80.918264,"BWP":12.836005,"BYN":2.562742,"BYR":19879.301129,"BZD":2.046584,"CAD":1.30396,"CDF":2031.037113,"CHF":0.974786,"CLF":0.033997,"CLP":938.080482,"CNY":6.848011,"COP":4515.045699,"CRC":681.907781,"CUC":1.01425,"CUP":26.877627,"CVE":110.284579,"CZK":24.571212,"DJF":180.751197,"DKK":7.444027,"DOP":55.163337,"DZD":148.413945,"EGP":19.232008,"ERN":15.213751,"ETB":53.413746,"EUR":1,"FJD":2.229677,"FKP":0.85434,"GBP":0.840387,"GEL":2.982327,"GGP":0.85434,"GHS":8.401792,"GIP":0.85434,"GMD":54.860722,"GNF":8790.463896,"GTQ":7.853494,"GYD":212.419805,"HKD":7.961493,"HNL":24.977557,"HRK":7.514579,"HTG":118.175057,"HUF":404.107449,"IDR":15210.150277,"ILS":3.475424,"IMP":0.85434,"INR":81.034017,"IQD":1481.897689,"IRR":42953.489807,"ISK":139.11443,"JEP":0.85434,"JMD":155.202318,"JOD":0.719917,"JPY":138.562751,"KES":120.442417,"KGS":82.114093,"KHR":4158.881833,"KMF":471.245913,"KPW":912.825043,"KRW":1332.907411,"KWD":0.311681,"KYD":0.846059,"KZT":487.411335,"LAK":15291.290012,"LBP":1535.365001,"LKR":360.429863,"LRD":154.672774,"LSL":16.066014,"LTL":2.994817,"LVL":0.613509,"LYD":4.955455,"MAD":10.415752,"MDL":19.595775,"MGA":4277.18149,"MKD":61.626548,"MMK":1879.884066,"MNT":3189.471578,"MOP":8.208438,"MRO":362.087096,"MUR":46.103638,"MVR":15.568395,"MWK":1042.140284,"MXN":20.690549,"MYR":4.522033,"MZN":64.739706,"NAD":16.066182,"NGN":421.16768,"NIO":36.45673,"NOK":9.961599,"NPR":129.469623,"NZD":1.625361,"OMR":0.390006,"PAB":1.01529,"PEN":3.976645,"PGK":3.578359,"PHP":56.421213,"PKR":236.445007,"PLN":4.783863,"PYG":7002.812238,"QAR":3.69287,"RON":4.934429,"RSD":117.23673,"RUB":61.133941,"RWF":1049.081413,"SAR":3.809578,"SBD":8.276249,"SCR":12.997284,"SDG":463.008548,"SEK":10.452435,"SGD":1.407358,"SHP":1.397027,"SLL":13357.673471,"SOS":592.83146,"SRD":24.527106,"STD":20992.92843,"SVC":8.883665,"SYP":2548.333122,"SZL":17.043594,"THB":37.335505,"TJS":10.356392,"TMT":3.560018,"TND":3.107156,"TOP":2.378467,"TRY":18.150202,"TTD":6.891589,"TWD":30.380643,"TZS":2365.231087,"UAH":37.364202,"UGX":3903.931547,"USD":1.01425,"UYU":42.455118,"UZS":11127.207719,"VND":23713.166347,"VUV":120.5903,"WST":2.759744,"XAF":656.076706,"XAG":0.054112,"XAU":0.000589,"XCD":2.741062,"XDR":0.769244,"XOF":656.070236,"XPF":114.761943,"YER":253.815706,"ZAR":17.094149,"ZMK":9129.467566,"ZMW":17.057596,"ZWL":326.588105}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler]);

        $amntFixed = (new CommissionsHelper($guzzleClient))->getCommission(4745030, 2000.00, 'GBP');

        $this->assertEquals($amntFixed, 23.8);
    }
}