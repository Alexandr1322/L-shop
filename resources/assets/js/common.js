/**
 * JavaScript file with main logic
 */

/**
 * Perform user authentication attempts by pressing the enter key
 */
$('#si-username, #si-password').keyup(function (event) {
    if (event.keyCode == 13) {
        signin(this);
    }
});

/**
 * Attempt to auth user
 */
$('#btn-sign-in').click(function () {
    signin(this);
});

function signin(self) {
    var username = $('#si-username').val();
    var password = $('#si-password').val();

    if (username.length === 0) {
        msg.danger('Имя пользователя не заполнено');

        return;
    }

    if (password.length === 0) {
        msg.danger('Пароль не заполнен');

        return;
    }

    // Request
    $.ajax({
        url: $('#sign-in').attr('data-url'),
        method: 'POST',
        data: ({
            username: username,
            password: password,
            _token: getToken()
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            enable(self);
            var status = response.status;

            if (status === 'success') {
                // If a user has successfully logged in
                var to = getUrlParams()['to'];

                if (to) {
                    // If the GET parameters "to" exist, then forwards it to the address specified in the parameter
                    document.location.href = to;
                } else {
                    // Otherwise, it forwards the link established with the page rendering
                    document.location.href = $('#sign-in').attr('data-redirect');
                }
            } else {
                if (status === 'invalid_credentials') {
                    msg.danger('Пользователь с такими данными не найден');
                } else if (status === 'frozen') {
                    msg.danger('Вы произвели слишком большое количество попыток входа. ' +
                        'Возможность авторизации будет недоступна последующие ' +
                        response.delay + ' секунд.');
                } else if (status === 'not activated') {
                    msg.danger('Ваш аккаунт не активирован. Проверьте свою почту на наличие нашего письма.');
                } else if (status === 'only for admins') {
                    msg.danger('Вход обычным пользователям запрещен');
                } else if (status === 'banned') {
                    msg.danger(response.message);
                }
            }
        },

        // Request error
        error: function () {
            enable(self);
            requestError();
        }
    });
}

/**
 * Perform user registration attempts by pressing the enter key
 */
$('#su-username, #su-email, #su-password, #su-password-confirm').keyup(function (event) {
    if (event.keyCode === 13) {
        signup(this);
    }
});

/**
 * Attempt to register user
 */
$('#btn-sign-up').click(function () {
    signup(this);
});

function signup(self) {

}

/**
 * Catalog section
 */
if ($('#content').length) {
    var servVisible = false;

    byId('content').style.width = 'calc(100% - ' + byId('sidebar').clientWidth + 'px)';
    byId('content').style.marginLeft = byId('sidebar').clientWidth + 'px';

    var badHeight = Math.floor(byId('topbar').clientHeight + byId('footer').clientHeight);
    byId('content-container').style.minHeight = 'calc(100vh - ' + badHeight + 'px)';

    byId('side-content').style.width = byId('sidebar').clientWidth + 'px';

    byId('chose-server').onclick = function () {
        switch (servVisible) {
            case true:
                byId('server-list').style.transform = 'translateX(-150%)';
                byId('chose-server').getElementsByTagName('I')[0].style.transform = 'rotateZ(0deg)';
                servVisible = false;
                break;
            case false:
                byId('server-list').style.transform = 'translateX(0)';
                byId('chose-server').getElementsByTagName('I')[0].style.transform = 'rotateZ(180deg)';
                servVisible = true;
        }
    };

    /**
     * Sidebar on mobile
     */
    byId('btn-menu').onclick = function () {
        byId('side-content').style.transform = 'translateX(0)';
    };

    byId('btn-menu-c').onclick = function () {
        byId('side-content').style.transform = 'translateX(-100%)';
    };


    byId('btn-menu').onclick = function () {
        byId('side-content').style.transform = 'translateX(0)';
    };

    byId('btn-menu-c').onclick = function () {
        byId('side-content').style.transform = 'translateX(-100%)';
    };

    $('.product-container').hide().eq(0).show();
    $('.ad-btn-list').hide();

    $('.admin-menu-btn').click(function () {
        $(this).parent().siblings().find('.ad-btn-list').slideUp();
        $(this).siblings().slideToggle();
    });

    $('.cat-btn').click(function () {
        var tabNumber = $(this).index();
        if ($('.product-container').eq(tabNumber).css('display') == 'none') {
            $('.product-container').eq(tabNumber).siblings().hide();
            $(this).siblings().css({'background-color': '#ffbb33'});
            $(this).css({'background-color': '#FF8800'});
            $('.product-container').eq(tabNumber).fadeIn();
        }
    });

    var scrollWidth = document.getElementById('news-content').offsetWidth - document.getElementById('news-content').clientWidth;

    document.getElementById('news-content').style.marginRight = -scrollWidth + 'px';

    $('#btn-news').click(function () {
        $('#news-content').css({'transform': 'translateX(0)'})
    });

    $('#news-back').click(function () {
        $('#news-content').css({'transform': 'translateX(100%)'})
    });
}
/**
 * End
 */

/**
 * Put product in cart
 */
$('.catalog-to-cart').click(function () {
    var url = $(this).attr('data-url');
    var self = this;

    // Request
    $.ajax({
        url: url,
        method: 'POST',
        data: ({
            _token: getToken()
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            var status = response.status;

            if (status == 'success') {
                msg.success('Товар добавлен в корзину');
                disable(self);
                $(self).children('span').text('Уже в корзине');

            } else if (status == 'cart is full') {
                enable(self);
                msg.warning('Невозможно добавить товар. Корзина переполнена.');
            } else if (status == 'already in cart') {
                enable(self);
                msg.warning('Товар уже есть в корзине');
            } else {
                enable(self);
                msg.danger('Неудалось положить товар в корзину');
            }
        },

        // Request error
        error: function () {
            enable(self);
            requestError();
        }
    })
});

/**
 * Buy product from catalog page
 */
(function () {
    var stack;
    var url;
    var price;

    $('.catalog-to-buy').click(function () {
        stack = Number($(this).parent().find('.product-count>span').text());
        url = $(this).attr('data-url');
        price = Number($(this).parent().find('.catalog-price-span').text());
        $('#catalog-to-buy-name').html($(this).parent().find('.product-name').html());
        if (isNaN(stack)) {
            $('#catalog-to-buy-count-input').hide();
            $('#catalog-to-buy-cbuttons').hide();
        } else {
            $('#catalog-to-buy-count-input').show();
            $('#catalog-to-buy-cbuttons').show();
            $('#catalog-to-buy-count-input').val(stack);
        }
        $('#catalog-to-buy-modal').modal('show');
        $('#catalog-to-buy-summ').text(price);
    });

    $('#catalog-to-buy-minus-btn').click(function () {
        var input = $(this).parent().parent().find('#catalog-to-buy-count-input');
        var val = Number(input.val());
        if (val - stack > 0) {
            var result = val - stack;
            input.val(result);
            $(this).parent().parent().parent().find('#catalog-to-buy-summ').text(result / stack * price);
        }
    });

    $('#catalog-to-buy-plus-btn').click(function () {
        var input = $(this).parent().parent().find('#catalog-to-buy-count-input');
        var val = Number(input.val());
        var result = val + stack;
        input.val(result);
        $(this).parent().parent().parent().find('#catalog-to-buy-summ').text(result / stack * price);
    });

    $('#catalog-to-buy-count-input').blur(function () {
        var val = Number($(this).val());

        if (val % stack != 0) {
            // Normalize input value
            var result = Math.round(val / stack) * stack;
            if (isNaN(result)) {
                result = 0;
            }

            if (result != 0) {
                $(this).val(result);
                $(this).parent().parent().find('#catalog-to-buy-summ').text(result / stack * price);
            } else {
                $(this).val(stack);
                $(this).parent().parent().parent().find('#catalog-to-buy-summ').text(price);
            }
        }

        // If input was empty
        if (val <= 0) {
            $(this).val(stack);
        }
    });

    $('#catalog-to-buy-accept').click(function () {
        var self = this;
        var captcha = getCaptcha();
        if (captcha == '') {
            msg.warning('Вы должны подтвердить то, что не являетесь роботом!');
            return;
        }

        // Request
        $.ajax({
            url: url,
            method: 'POST',
            data: ({
                username: $('#catalog-to-buy-username').val(),
                count: $('#catalog-to-buy-count-input').val(),
                captcha: captcha,
                _token: getToken()
            }),
            dataType: 'json',
            beforeSend: function () {
                disable(self);
            },
            success: function (response) {
                enable(self);
                var status = response.status;
                grecaptcha.reset();

                if (status == 'success') {
                    if (response.quick) {
                        msg.success('Покупка успешно совершена!');
                        $('#catalog-to-buy-modal').modal('hide');
                        $('#balance-span').text(response.new_balance);
                    } else {
                        document.location.href = response.redirect;
                    }
                } else {
                    if (status == 'invalid username') {
                        msg.danger('Имя пользователя слишком короткое или содержит недопустимые символы');
                    }

                    if (status == 'invalid products count') {
                        msg.danger('Неверное количество товара');
                    }
                }
            },

            // Request error
            error: function () {
                enable(self);
                requestError();
            }
        })
    });
})();

/**
 * Cart section
 */

/**
 * Remove product from cart
 */
$('.cart-remove').click(function () {
    var url = $(this).attr('data-url');
    var self = this;

    // Request
    $.ajax({
        url: url,
        method: 'POST',
        data: ({
            _token: getToken()
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            var status = response.status;

            if (status == 'success') {
                msg.info('Товар убран из корзины');

                // Change total cost
                var cost = Number($(self).parents('.c-product').find('.c-p-pay-money>span').text());
                var total = Number($('#total-money>span').text());
                $('#total-money>span').text(total - cost);
                $(self).parents('.c-product').remove();

                // If there are no more elements
                if ($('.c-product').length == 0) {
                    $('#total').remove();
                    $('#cart-products').html('<h3>Корзина пуста</h3>');
                }
            } else if (status == 'product not found') {
                msg.warning('Товар в корзине не найден');
            } else {
                msg.danger('Неудалось убрать товар из корзины');
            }
        },

        // Request error
        error: function () {
            enable(self);
            requestError();
        }
    });
});

/**
 * Increase the number of goods in a cart
 */
$('.cart-minus-btn').click(function () {
    // Number of items in one stack
    var stack = Number($(this).parent().attr('data-stack'));
    // Price for one stack
    var price = Number($(this).parent().attr('data-price'));
    // Current number of goods
    var input = $(this).parent().parent().find('.c-p-count-input');
    var val = Number(input.val());
    if (val - stack > 0) {
        var result = val - stack;
        input.val(result);
        $(this).parent().parent().find('.c-p-pay-money>span').text(result / stack * price);

        var total = Number($('#total-money>span').text());
        $('#total-money>span').text(total - price);
    }
});

/**
 * Reduce the quantity of goods in a cart
 */
$('.cart-plus-btn').click(function () {
    // Number of items in one stack
    var stack = Number($(this).parent().attr('data-stack'));
    // Price for one stack
    var price = Number($(this).parent().attr('data-price'));
    // Current number of goods
    var input = $(this).parent().parent().find('.c-p-count-input');
    var val = Number(input.val());
    var result = val + stack;
    input.val(result);
    $(this).parent().parent().find('.c-p-pay-money>span').text(result / stack * price);

    var total = Number($('#total-money>span').text());
    $('#total-money>span').text(total + price);
});

/**
 * Normalize the input value on input blur event
 */
$('.c-p-count-input').blur(function () {
    var stack = Number($(this).parents('.c-2-info').find('.c-p-cbuttons').attr('data-stack'));
    var price = Number($(this).parent().parent().find('.c-p-cbuttons').attr('data-price'));
    var val = Number($(this).val());
    console.log(price);
    if (val % stack != 0) {
        // Normalize input value
        var result = Math.round(val / stack) * stack;
        if (isNaN(result)) {
            result = 0;
        }

        if (result != 0) {
            $(this).val(result);
            $('#total-money>span').text(result / stack * price);
        } else {
            $(this).val(stack);
            $('#total-money>span').text(price);
        }
    }

    // If input was empty
    if (val <= 0) {
        $(this).val(stack);
    }
});

$('#btn-cart-go-pay').click(function () {
    var url = $(this).attr('data-url');
    var self = this;
    var products = new Object(null);
    var username;
    var captcha = getCaptcha();

    // Collect products from page
    $.each($('.c-product'), function (index, value) {
        products[index] = new Object(null);
        products[index].id = $(value).find('.c-p-name').attr('data-id');
        products[index].count = $(value).find('.c-p-count-input').val();
    });

    if ($('#c-login').length != 0) {
        username = $('#c-login').val();
        if (username.length < 4) {
            msg.warning('Вы должны указать имя того пользователя (не короче 4 символов), которому хотите приобрести товары.');
            return;
        }
    }

    if (captcha == '') {
        msg.warning('Вы должны подтвердить то, что не являетесь роботом!');
        return;
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: ({
            products: products,
            username: username,
            captcha: captcha,
            _token: getToken()
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            var status = response.status;
            grecaptcha.reset();

            if (status == 'success') {
                if (response.quick) {
                    msg.success('Покупка совершена успешно!');
                    $('#balance-span').text(response.new_balance);
                    $('#cart-products').empty();
                    $('#total').remove();
                    $('#cart-products').html('<h3>Корзина пуста</h3>');
                } else {
                    document.location.href = response.redirect;
                }
            } else {
                enable(self);

                if (status == 'invalid username') {
                    msg.danger('Имя пользователя слишком короткое или содержит недопустимые символы');
                }

                if (status == 'invalid products count') {
                    msg.danger('Неверное количество товара');
                }
            }
        },

        // Request error
        error: function () {
            enable(self);
            requestError();
        }
    })
});

/**
 * End
 */

/**
 * FillUpBalance
 */
$('#fub-btn').click(function () {
    var sum = Number($('#fub-input').val());
    var captcha = getCaptcha();
    var self = this;

    if (isNaN(sum)) {
        msg.warning('Сумма должна иметь числовое значение');
        return;
    }

    if (sum <= 0) {
        msg.warning('Сумма должна быть положительным числом');
        return;
    }

    if (captcha == '') {
        msg.warning('Вы должны подтвердить то, что не являетесь роботом!');
        return;
    }

    // Request
    $.ajax({
        url: '',
        method: 'POST',
        data: ({
            sum: sum,
            captcha: getCaptcha(),
            _token: getToken()
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        complete: function () {
            grecaptcha.reset();
        },
        success: function (response) {
            status = response.status;

            if (status == 'success') {
                document.location.href = response.redirect;
            } else {
                enable(self);
                if (status == 'invalid sum') {
                    msg.warning('Сумма должна быть положительным числом и быть не меньше ' + response.min);
                }
            }
        },

        // Request error
        error: function () {
            enable(self);
            requestError();
        }
    })
});

$('.profile-payments-info').click(function () {
    var url = $(this).attr('data-url');
    var self = this;

    $.ajax({
        url: url,
        method: 'POST',
        data: ({
            _token: getToken()
        }),
        dataType: 'json',
        complete: function () {
            enable(self);
            $('#pre-loader').fadeOut('fast');
        },
        beforeSend: function () {
            disable(self);
            $('#pre-loader').fadeIn('fast');
        },
        success: function (response) {
            var status = response.status;

            if (status == 'success') {
                var result = '';
                var products = response.products;

                if (!products.length) {
                    this.error();
                } else {
                    $('#profile-payments-modal').modal('show');

                    for (i = 0; i < products.length; i++) {
                        result += '<tr><td><img height="35" width="35" src="' + products[i].image + '"></td><td>' + products[i].name + '</td><td>' + products[i].count + '</td></tr>';
                    }
                }

                $('#profile-payments-modal-products').html(result);
            }
        },
        error: function () {
            msg.danger('Подробная информация об этом платеже недоступна');
        }
    })
});

$('#news-load-more').click(function () {
    var count = $('#news-block>div').length;
    var self = this;

    $.ajax({
        url: $(self).attr('data-url'),
        method: 'POST',
        data: ({
            _token: getToken(),
            count: count
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {

            if (response.status === 'news disabled') {
                msg.warning('Отображение новостей отключено');

                return;
            }

            if (response.status === 'no more news') {
                msg.info('Новостей больше нет');
                $(self).hide();

                return;
            }
            enable(self);

            if (response.status === 'last portion') {
                $(self).hide();
            }

            var content = response.news;
            var result = '';

            for (var i = 0; i < content.length; i++) {
                result += '<div class="news-preview z-depth-1"><h3 class="news-pre-header">' + content[i].title + '</h3><p class="news-pre-text">' + content[i].content + '</p> <a href="' + content[i].link + '" class="btn btn-info btn-sm btn-block mt-1">Подробнее...</a> </div>';
            }

            $('#news-block').append(result);
        },
        error: function () {
            requestError();
        }
    })
});

/**
 * Character section
 */

$('#profile-update-skin').click(function () {
    var self = this;
    var url = $(self).attr('data-url');

    $.ajax({
        url: url,
        method: 'POST',
        data: new FormData($('#skin-form')[0]),
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            enable(self);
            var status = response.status;

            if (status === 'success') {
                msg.success('Новый скин установлен успешно');

                var a = $('#skin-front').attr('src');
                var b = $('#skin-back').attr('src');

                $('#skin-front').attr('src', a);
                $('#skin-back').attr('src', b);

            } else if (status === 'invalid ratio') {
                msg.danger('Неверный размер изображения');
            }
        },
        complete: function (xhr) {
            if (xhr.status === 422) {
                var response = JSON.parse(xhr.responseText);
                showErrors(response.skin);
            }
        }
    })
});

$('#profile-update-cloak').click(function () {
    var self = this;
    var url = $(self).attr('data-url');

    $.ajax({
        url: url,
        method: 'POST',
        data: new FormData($('#cloak-form')[0]),
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            enable(self);
            var status = response.status;

            if (status === 'success') {
                msg.success('Новый плащ установлен успешно');

                var a = $('#cloak-front').attr('src');
                var b = $('#cloak-back').attr('src');

                $('#cloak-front').attr('src', a);
                $('#cloak-back').attr('src', b);

            } else if (status === 'invalid ratio') {
                msg.danger('Неверный размер изображения');
            }
        },
        complete: function (xhr) {
            if (xhr.status === 422) {
                var response = JSON.parse(xhr.responseText);
                showErrors(response.skin);
            }
        }
    })
});

/**
 * End character section
 */


$('#btn-monitoring').click(function () {
    $('#monitoring-modal').modal('show');
});


/**
 * Admin panel section
 */

$('.api-algo-dropdown-item').click(function () {
    $('#s-api-algo').val($(this).text());
});

// Dropdown
$('.dropdown-item.change').click(function () {
    $('#' + $(this).attr('data-parent')).html($(this).html());
});

$('#server-edit-add-category').click(function () {
    var url = $(this).attr('data-url');
    var category = $('#server-edit-add-category-input').val();
    var self = this;

    $.ajax({
        url: url,
        method: 'POST',
        data: ({
            _token: getToken(),
            category: category
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            location.reload();
        },
        error: function () {
            location.reload();
        }
    })
});

$('.server-edit-remove-category').click(function () {
    var url = $(this).attr('data-url');
    var category = $(this).attr('data-category');
    var self = this;

    $.ajax({
        url: url,
        method: 'POST',
        data: ({
            _token: getToken(),
            category: category
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            location.reload();
        },
        error: function () {
            location.reload();
        }
    })
});

(function () {
    var categoryBlockHtml = $('#server-add-categories').html();

    $('#server-add-add-category').click(function () {
        var selector = $('#server-add-categories');
        var a = $(categoryBlockHtml);
        var id = rndStr(4);
        a.find('.category-name').attr('id', id);
        a.find('.category-name-label').attr('for', id);

        selector.append(a);
    });
})();

$(document).on('click', '.server-add-remove-category', function () {
    if ($('.server-add-remove-category').length == 1) {
        return;
    }
    $(this).parent().remove();
});

$('.edit-products-clip-item').click(function () {
    $('#item').val($(this).attr('data-item'));
    var type = $(this).attr('data-item-type');

    if (type == 'item') {
        $('label[for=stack]').text('Количество товара в 1 стаке');
        $('label[for=price]').text('Цена за стак товара');
    } else if (type == 'permgroup') {
        $('label[for=stack]').text('Длительность привилегии (В днях). 0 - навсегда');
        $('label[for=price]').text('Цена одного периода привилегии');
    }
});

$('.edit-categories-clip-item').click(function () {
    $('#server').val($(this).attr('data-server'));
    $('#category').val($(this).attr('data-category'));
});

$('#item-set-default-image').change(function () {
    $('#item-load-image-block').fadeOut('fast');
});

$('#item-set-uploaded-image').change(function () {
    console.log('fd');
    $('#item-load-image-block').fadeIn('fast');
});

$('.robokassa-algo-item').click(function () {
    $('#robokassa-algo-input').val($(this).text());
});

// Dropdown
$('.access-mode-item').click(function () {
    $('#access-mode-input').val($(this).attr('data-value'));
});

// Dropdown
$('.products-sort-type-item').click(function () {
    $('#products-sort-type-input').val($(this).attr('data-value'));
});

$('#item-set-item-type').change(function () {
    $('label[for="item-name"]').text('Название предмета');
    $('label[for="item"]').text('ID или ID:DATA предмета');
    $('#extra').parent().fadeIn('fast');
});

$('#item-set-permgroup-type').change(function () {
    $('label[for="item-name"]').text('Название привилегии');
    $('label[for="item"]').text('Внутриигровой идентификатор привилегии');
    $('#extra').parent().fadeOut('fast');
});

transliterate = (function () {
    var
        rus = "щ   ш  ч  ц  ю  я  ё  ж  ъ  ы  э  а б в г д е з и й к л м н о п р с т у ф х ь".split(/ +/g),
        eng = "shh sh ch cz yu ya yo zh `` y' e` a b v g d e z i j k l m n o p r s t u f x `".split(/ +/g)
    ;
    return function (text, engToRus) {
        var x;
        for (x = 0; x < rus.length; x++) {
            text = text.split(engToRus ? eng[x] : rus[x]).join(engToRus ? rus[x] : eng[x]);
            text = text.split(engToRus ? eng[x].toUpperCase() : rus[x].toUpperCase()).join(engToRus ? rus[x].toUpperCase() : eng[x].toUpperCase());
        }
        return text;
    }
})();

function buildPageUrl(target) {
    if ($('#page-url-auto').is(':checked')) {
        var title = $(target).val();
        var result = title.replace(/[\s]/ig, '-');
        result = result.replace(/[^a-zа-я0-9\-]+/ig, '');
        result = transliterate(result);

        $('#page-url').val(result);
    }
}

$('#page-title').keydown(function () {
    buildPageUrl(this);
});

$('#page-url-auto').change(function () {
    if ($(this).is(':checked')) {
        buildPageUrl($('#page-title'));
    }
});

$('#page-url').keydown(function () {
    buildPageUrl(this);
});

$('#admin-api-enable-alert-close').click(function () {
    setRemember('api_enable', true);
});

$('#admin-api-docs-alert-close').click(function () {
    setRemember('api_docs', true);
});

$('#admin-security-debug-alert-close').click(function () {
    setRemember('security_debug', true);
});

$('#admin-users-edit-ban').click(function () {
    var self = this;
    var url = $(self).attr('data-url');

    $.ajax({
        url: url,
        method: 'POST',
        data: ({
            _token: getToken(),
            block_duration: $('#admin-users-edit-ban-duration').val(),
            reason: $('#admin-users-edit-ban-reason').val()
        }),
        dataType: 'json',
        beforeSend: function () {
            disable(self);
        },
        success: function (response) {
            var status = response.status;

            if (status === 'success') {
                $('#admin-users-edit-ban-modal').modal('hide');
                msg.info('Пользователь заблокирован');

                $('#admin-users-edit-ban-open-modal').hide();
                $('#admin-users-edit-already-ban').removeClass('d-none');
                $('#admin-users-edit-already-ban').find('span').html(response.unblock);
            } else if (status === 'user not found') {
                msg.danger('Пользователь не найден');
            } else if (status === 'You can not block yourself') {
                msg.warning('Вы не можете заблокировать самого себя');
            } else {
                msg.danger('Заблокировать пользователя неудалось');
            }
        },
        complete: function (xhr) {
            enable(self);

            if (xhr.status === 422) {
                var response = JSON.parse(xhr.responseText);
                showErrors(response.skin);
            }
        }
    })
});

/**
 * Search user section
 *
 * @type {{val: string, buf: string, checkResult: search.checkResult}}
 */
var search = {
    val: '',
    buf: '',
    checkResult: function () {
        if (this.buf !== this.val) {
            this.buf = this.val;

            $.ajax({
                url: $('#admin-users-search').attr('data-url'),
                type: "POST",
                data: ({
                    _token: getToken(),
                    search: search.val
                }),
                dataType: "json",
                beforeSend: function () {
                    if (search.val === '')
                        $('#admin-users-search-results').html('<a class="dropdown-item">Начните вводить...</a>');
                    else
                        $('#admin-users-search-results').html('<a class="dropdown-item">Поиск...</a>');
                },
                success: function (response) {
                    var status = response['status'];
                    var data = response['data'];

                    if (status === 'found') {
                        var results = '';
                        for (var i = 0; i < data.length; i++) {
                            results += '<a class="dropdown-item admin-users-search-item" href="' + data[i]['url'] + '"><span class="mr-4 font-weight-bold">' + data[i]['id'] + '</span><span class="mr-4">' + data[i]['username'] + '</span><span class="mr-4">' + data[i]['email'] +  '</span><span class="mr-4">' + data[i]['balance'] +  ' ' + data[i]['currency'] + '</span></a>';
                        }
                    }

                    if (status === 'not found') {
                        results = '<a class="dropdown-item disabled">Ничего не найдено</a>';
                    }

                    $('#admin-users-search-results').html(results);
                }
            })
        }
    }
};


$('#admin-users-search')
    .on('keyup change', function (event) {
        search.val = $(this).val();
    })
    .on('focusin', function (event) {
        search.interval = setInterval(function () {
            search.checkResult.call(search);
        }, 300);
    })
    .on('focusout', function (event) {
        clearInterval(search.interval);
    });
/**
 * End of search user section
 */
