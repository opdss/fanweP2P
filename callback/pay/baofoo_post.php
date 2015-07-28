<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>自动处理提交页</title>
<?php
$MemberID=$_POST['MemberID'];//商户号
$TransID=$_POST['TransID'];//流水号
$PayID=$_POST['PayID'];//支付方式
$TradeDate=$_POST['TradeDate'];//交易时间
$OrderMoney=$_POST['OrderMoney']*100;//订单金额
$ProductName=$_POST['ProductName'];//产品名称
$Amount=$_POST['Amount'];//商品数量
$Username=$_POST['Username'];//支付用户名
$AdditionalInfo=$_POST['AdditionalInfo'];//订单附加消息
$PageUrl=$_POST['PageUrl'];//通知商户页面端地址
$ReturnUrl=$_POST['ReturnUrl'];//服务器底层通知地址
$NoticeType=$_POST['NoticeType'];//通知类型	
//此处加入判断，如果前面出错了跳转到其他地方而不要进行提交

$Signature=$_POST['Signature'];//通知类型	 

$payUrl="http://gw.baofoo.com/payindex";//借贷混合
$TerminalID = $_POST['TerminalID']; 
$InterfaceVersion = "4.0";
$KeyType = "1";
?>
</head>

<body onload="document.form1.submit()">
<form id="form1" name="form1" method="post" action="<?php echo $payUrl; ?>">
        <input type='hidden' name='MemberID' value="<?php echo $MemberID; ?>" />
		<input type='hidden' name='TerminalID' value="<?php echo $TerminalID; ?>"/>
		<input type='hidden' name='InterfaceVersion' value="<?php echo $InterfaceVersion; ?>"/>
		<input type='hidden' name='KeyType' value="<?php echo $KeyType; ?>"/>
        <input type='hidden' name='PayID' value="<?php echo $PayID; ?>" />
        <input type='hidden' name='TradeDate' value="<?php echo $TradeDate; ?>" />
        <input type='hidden' name='TransID' value="<?php echo $TransID; ?>" />
        <input type='hidden' name='OrderMoney' value="<?php echo $OrderMoney; ?>" />
        <input type='hidden' name='ProductName' value="<?php echo $ProductName; ?>" />
        <input type='hidden' name='Amount' value="<?php echo $Amount; ?>" />
        <input type='hidden' name='Username' value="<?php echo $Username; ?>" />
        <input type='hidden' name='AdditionalInfo' value="<?php echo $AdditionalInfo; ?>" />
        <input type='hidden' name='PageUrl' value="<?php echo $PageUrl; ?>" />
        <input type='hidden' name='ReturnUrl' value="<?php echo $ReturnUrl; ?>" />
        <input type='hidden' name='Signature' value="<?php echo $Signature; ?>" />
		<input type='hidden' name='NoticeType' value="<?php echo $NoticeType; ?>" />
</form>
</body>
</html>
