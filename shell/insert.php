<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/9/22
 * Time: 13:42
 */

include './libs/include.list.php';
try{
    $mysqli=new MysqliStmt();
    for($i=1;$i<=5;$i++){
        $id=UuidUtils::uuid();
//        $id='test_'.$i;
        //字符串  百家姓
        $str2='赵钱孙李周吴郑王冯陈楮卫蒋沈韩杨朱秦尤许何吕施张孔曹严华金魏陶姜戚谢邹喻柏水窦章云苏潘葛奚范彭郎鲁韦昌马苗凤花方俞任袁柳酆鲍史唐费廉岑薛雷贺倪汤滕殷罗毕郝邬安常乐于时傅皮卞齐康伍余元卜顾孟平黄和穆萧尹姚邵湛汪祁毛禹狄米贝明臧计伏成戴谈宋茅庞熊纪舒屈项祝董梁杜阮蓝闽席季麻强贾路娄危江童颜郭梅盛林刁锺徐丘骆高夏蔡田樊胡凌霍虞万支柯昝管卢莫经房裘缪干解应宗丁宣贲邓郁单杭洪包诸左石崔吉钮龚程嵇邢滑裴陆荣翁荀羊於惠甄麹家封芮羿储靳汲邴糜松井段富巫乌焦巴弓牧隗山谷车侯宓蓬全郗班仰秋仲伊宫宁仇栾暴甘斜厉戎祖武符刘景詹束龙叶幸司韶郜黎蓟薄印宿白怀蒲邰从鄂索咸籍赖卓蔺屠蒙池乔阴郁胥能苍双闻莘党翟谭贡劳逄姬申扶堵冉宰郦雍郤璩桑桂濮牛寿通边扈燕冀郏浦尚农温别庄晏柴瞿阎充慕连茹习宦艾鱼容向古易慎戈廖庾终暨居衡步都耿满弘匡国文寇广禄阙东欧殳沃利蔚越夔隆师巩厍聂晁勾敖融冷訾辛阚那简饶空曾毋沙乜养鞠须丰巢关蒯相查后荆红游竺权逑盖益桓公万俟司马上官欧阳夏侯诸葛闻人东方赫连皇甫尉迟公羊澹台公冶宗政濮阳淳于单于太叔申屠公孙仲孙轩辕令狐锺离宇文长孙慕容鲜于闾丘司徒司空丌官司寇仉督子车颛孙端木巫马公西漆雕乐正壤驷公良拓拔夹谷宰父谷梁晋楚阎法汝鄢涂钦段干百里东郭南门呼延归海羊舌微生岳帅缑亢况后有琴梁丘左丘东门西门商牟佘佴伯赏南宫墨哈谯笪年爱阳佟';
        //1.获取字符串的长度
        $length = mb_strlen($str2)-1;
        //2.字符串截取开始位置
        $start=rand(0,$length-3);
        //3.字符串截取长度
        //4.随机截取字符串，取其中的一部分字符串
        $data=mb_substr($str2, $start,rand(2,4),'utf-8');
        $content=mb_substr($str2, $start,rand(20,50),'utf-8');
        list($microsecond, $timeStamp) = explode(" ", microtime());
        $ret=$mysqli->insert('users',array('id'=>$id,'name'=>$data,'sex'=>rand(0,1),'content'=>$content,'sort_order'=>rand(1000,99999),'microsecond'=>$microsecond,'create_time'=>date('Y-m-d H:i:s'),'update_time'=>date('Y-m-d H:i:s')));
        p($ret);
    }
}catch (Exception $e){
    echo '异常:^^^^^';
    echo $e->getMessage();
}