/**
 * 删除购物车
 * @param cart_id
 */
function drop_cart_item(cart_id){
    var parent_tr = $('#cart_item_' + cart_id).parent();
    var amount_span = $('#cart_totals');
    $.getJSON(SITEURL+'/Home/cart/del/cart_id/' + cart_id, function(result){
        if(result.state){
            //删除成功
            if(result.quantity == 0){//判断购物车是否为空
                window.location.reload();    //刷新
            } else {
            	$('tr[nc_group="'+cart_id+'"]').remove();//移除本商品或本套装
            	$('.addcart-goods-num').text(result.quantity);
            	$('em#cartTotal').text(result.quantity);
        		if (parent_tr.children('tr').length == 2) {//只剩下店铺名头和店铺合计尾，则全部移除
        		    parent_tr.remove();
        		}
            }
        }else{
        	alert(result.msg);
        }
    });
}

/**
 * 更改购物车数量
 * @param cart_id
 * @param input
 */
function change_quantity(cart_id, input){
    var subtotal = $('#item' + cart_id + '_subtotal');
    //暂存为局部变量，否则如果用户输入过快有可能造成前后值不一致的问题
    var _value = input.value;
    $.getJSON(SITEURL+'/Home/cart/update/cart_id/' + cart_id + '/quantity/' + _value, function(result){
    	$(input).attr('changed', _value);
    	if(result.state == 'true'){
            $('#cart_id'+cart_id).val(cart_id+'|'+_value);
        }

        if(result.state == 'invalid'){
          $('#cart_id'+cart_id).remove();
          $('tr[nc_group="'+cart_id+'"]').addClass('item_disabled');
          $(input).parent().next().html('');
          $(input).parent().removeClass('ws0').html('已下线');
          showDialog(result.msg, 'error','','','','','','','','',2);
          return;
        }

        if(result.state == 'shortage'){
          $('#cart_id'+cart_id).val(cart_id+'|'+result.num);
          $(input).val(result.num);
          showDialog(result.msg, 'error','','','','','','','','',2);
          return;
        }

        if(result.state == '') {
            //更新失败
        	showDialog(result.msg, 'error','','','','','','','','',2);
            $(input).val($(input).attr('changed'));
        }
    });
}

/**
 * 购物车减少商品数量
 * @param cart_id
 */
function decrease_quantity(cart_id){
    var item = $('#input_item_' + cart_id);
    var orig = Number(item.val());
    if(orig > 1){
        item.val(orig - 1);
        item.keyup();
    }
}

/**
 * 购物车增加商品数量
 * @param cart_id
 */
function add_quantity(cart_id){
    var item = $('#input_item_' + cart_id);
    var orig = Number(item.val());
    item.val(orig + 1);
    item.keyup();
}

$(function(){
	$("#form_buy").validate({
	    errorPlacement: function(error, element){
	        var error_td = element.parents('td:first').find("input");
	        error_td.append(error);
	    },
	    rules : {
	        truename : {
	            required : true,
	        },
	        company : {
	            required : true,
				maxlength: 40
	        },
	        using : {
	            required : true
	        },
	        start_time: {
	            required : true
	        },
	        end_time: {
	            required : true
	        }
	    },
	    messages : {
	    	truename : {
	            required : '*姓名不能为空'
	        },
	        company : {
	            required : '*单位名称不能为空'
	        },
	        using  : {
	            required : '*用途不能为空'
	        },
	        start_time: {
	            required : '*开始时间不能为空'
	        },
	        end_time : {
	            required : '*结束时间不能为空'
	        }
	    }
	});
    $('#selectAll').on('click',function(){
        if ($(this).attr('checked')) {
            $('input[type="checkbox"]').attr('checked',true);
            $('input[type="checkbox"]:disabled').attr('checked',false);
        } else {
            $('input[type="checkbox"]').attr('checked',false);
        }
    });
    $('input[nc_type="eachGoodsCheckBox"]').on('click',function(){
        if (!$(this).attr('checked')) {
            $('#selectAll').attr('checked',false);
        }
    });
    $('#next_submit').on('click',function(){
    	if($("#form_buy").valid()){
	        if ($(document).find('input[nc_type="eachGoodsCheckBox"]:checked').size() == 0) {
	            showDialog('请选中要申请的物品', 'eror','','','','','','','','',2);
	            return false;
	        }else {
	            $('#form_buy').submit();
	        }
    	}
    });
});