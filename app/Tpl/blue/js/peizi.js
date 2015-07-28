
/*
 * 转入实盘(借款)金额，返回倍数列表
 *array(
	[0]=>array(id=0,lever=5,money=100)	
	[1]=>array(id=1,lever=10,money=200)
	[2]=>array(id=2,lever=15,money=350)
 )
 * 
 */
function getPeiziLeverList(money) {	
	for (var i=0;i<lever_list.length;i++){
		if (money > lever_list[i].min_money && money <= lever_list[i].max_money){
			for (var j=0;j<lever_list[i].lever_array.length;j++){
				//var item = lever_list[i].lever_array[j];
				//item.cc = 'ddd';
				lever_list[i].lever_array[j].money = Math.floor(money / lever_list[i].lever_array[j].lever);
				//alert(lever_list[i].lever_array[j].cc);
			}
			
			return lever_list[i].lever_array;
		}
	}
	
	return new Array();
}

/**
 * 包月 使用
 * 输入本金，返回 可以获得的 实盘(借款)金额
 * @param money 本金
 * @returns
 * 
 *  array(
	[0]=>array(id=1,lever=1,money=100,forbidden=true)	
	[1]=>array(id=2,lever=2,money=200,forbidden=true)
	[2]=>array(id=3,lever=3,money=300,forbidden=true)
 )
 */
function getPeizi2LeverList(money) {	
	var money_list = new Array();
	for (var i=peizi_conf.min_lever;i<=peizi_conf.max_lever;i++){
		var r1 = new Object();
		
		r1.id = i;
		r1.lever = i;
		r1.money = money * r1.lever;
		if (r1.money <= peizi_conf.max_money){
			r1.forbidden = true;
		}else{
			r1.forbidden = false;
		}
		money_list.push(r1);
	}
	
	return money_list;
}

/**
 * money:实盘金额(借款金额）
 * lever:位数
 * 
 * 返回
 * 风险保证金(本金)
 * 
 */
function getPeiziLeverMoney(money,lever){
	return  Math.floor(money / lever);
	
	/*
	var lever_array = getPeiziLeverList(money);

	for (var j=0;j<lever_array.length;j++){
		if (lever_array[j].lever == lever){
			return 	lever_array[j].money;			
		}
	}
	
	return 0;
	*/
}

/**
 * 输入参数
 * borrow_money: 实盘金额(借款金额)
 * lever: 倍数
 * month: 月份（资金使用期限）
 * rate: 利率字段（1,2,3,4 应对 rate1,rate2,rate3,rate4)
 *
 * 输出参数
 * total_money:总操盘资金
 * warning_line:亏损警戒线
 * open_line:亏损平仓线
 * rate_id: 利率ID 
 * rate: 利率
 * rate_format: 利率格式化
 * rate_money:账户管理费
 * rate_money_format: 账户管理费格式化后
 * limit_info: 仓位限制消息
 *
 * rate_list: 多利率，选择列表
 *		id
 *		rate
 *		rate_format
 * 
 */
function getPeiziCacl(borrow_money,lever,month,rate_id) {
	
	//平仓金额=投入本金*倍数 + 投入本金 * 倍数 * 平仓系数
	//警戒金额=投入本金*倍数 + 投入本金 * 倍数 * 警戒系数
	
	money = parseInt(borrow_money);
	//获得本金
	var lmoney = getPeiziLeverMoney(money,lever);
	
	var parmar = new Object();
	
	for (var i=0;i<lever_money_list.length;i++){
		var lm = lever_money_list[i];
		
		if (lm.lever == lever && month >= lm.min_month && month <= lm.max_month && money >= lm.min_money && money <= lm.max_money){
			//总操盘资金
			parmar.total_money = money + lmoney;
			//亏损警戒线
			parmar.warning_line = Math.floor(money + money * lm.warning_coefficient);
			//亏损平仓线
			parmar.open_line = Math.floor(money + money  * lm.open_coefficient);
					
			//账户管理费
			var rate_f;
			if (rate_id == 2){
				parmar.rate = lm.rate2;
			}else if (rate_id == 3){
				parmar.rate = lm.rate3;
			}else if (rate_id == 4){
				parmar.rate = lm.rate4;
			}else{
				parmar.rate = lm.rate1;
				rate_id = 1;
			}
			
			
			var rate_f = getPeiziRateFormat(rate_id,parmar.rate,lm.type);
			parmar.rate_id = rate_f.id;
			parmar.rate = rate_f.rate;
			parmar.rate_format = rate_f.rate_format;

			parmar.rate_money = money * parmar.rate;
			if (parmar.rate_money == 0)
				parmar.rate_money_format = '免费';
			else
				parmar.rate_money_format = parmar.rate_money;
			
			//仓位限制消息
			parmar.limit_info = lm.limit_info;
			
			var rate_list = new Array();
			if (lm.rate1 > 0){				
				rate_list.push(getPeiziRateFormat(1,lm.rate1,lm.type));
			}
			if (lm.rate2 > 0){		
				//alert(lm.rate2);
				rate_list.push(getPeiziRateFormat(2,lm.rate2,lm.type));
			}
			if (lm.rate3 > 0){				
				rate_list.push(getPeiziRateFormat(3,lm.rate3,lm.type));
			}
			if (lm.rate4 > 0){				
				rate_list.push(getPeiziRateFormat(4,lm.rate4,lm.type));
			}			
			
			//alert(rate_list.length);
			parmar.rate_list = rate_list;
			
			//alert('total_money:' + parmar.total_money + ';warning_money:' + parmar.warning_money+ ';open_money:' + parmar.open_money+ ';rate_money:' + parmar.rate_money+ ';warning_coefficient:' + lm.warning_coefficient+ ';open_coefficient:' + lm.open_coefficient);
			
			return parmar;
		}
	}	
	
	return parmar;
}

//利率格式化
function getPeiziRateFormat(rate_id,rate,type) {
	var r1 = new Object();
	r1.id = rate_id;
	r1.rate = rate;
	if (r1.rate == 0){
		r1.rate_format = '免';
	}else{
		if (type == 2){
			r1.rate_format = (r1.rate * 100) + '分 / 每月';
		}else{
			r1.rate_format = (r1.rate * 1000)  + '分 / 每日';
		}
	}	
	
	return r1;
}