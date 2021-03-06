{{-- Layout and design by WhileD0S <https://vk.com/whiled0s>  --}}
@extends('layouts.shop')

@section('title')
    Документация по API L - Shop
@endsection

@section('js')
    <script type="text/javascript">
        hljs.initHighlightingOnLoad();
    </script>
@endsection

@section('content')
    <div id="content-container">
        <div class="z-depth-1 content-header text-center">
            <h1><i class="fa fa-cog fa-left-big"></i>Документация по API L - Shop</h1>
        </div>
        <div class="card card-block">
            <h3 class="card-title">Введение</h3>
            <p class="card-text">
                API (<b>a</b>pplication <b>p</b>rogramming <b>i</b>nterface) предоставляет удобный интерфейс для взаимодействия
                вашего приложения с L-Shop. Вы можете работать с L-Shop по средствам отправки http - запросов, следуя
                определенным правилам. Например, вы сможете авторизовывать пользователя, для того, чтобы ему не приходилось
                авторизовываться в магазине, если он уже вошёл в свой аккаунт на вашем ресурсе.
            </p>
            <h3 class="card-title">Основы</h3>
            <p class="card-text">
                Для взаимодействия с API вам необходимо отправить http-запрос (GET или POST, не важно) по определенному адресу.
                Каждый адрес соответствует опредленной функции, которая выполняется после успешной проверки подлинности данных.
                В зпросе необходимо передать какие-либо параметры, а также, контрольную сумму(hash) этих параметров.
                <p>
                    Пример GET - запроса на авторизацию пользователя:
                    <code>http://example.ru/api/signin?username=d3lph1&hash=2080e2809b6ace6f6be9a1a22dba89aee2669ee1bb44e60476cfb9cf3d15c6a8</code>
                </p>
                <p>
                    Здесь:
                    <p><code>api</code> - эта часть url говорит о том, что запрос осуществяется к API</p>
                    <p><code>signin</code> - свидетельствует о том, что мы хоти авторизовать пользователя</p>
                    <p><code>username</code> - имя пользователя</p>
                    <p><code>hash</code> - контрольная сумма параметров запроса</p>
                </p>
            </p>
            <h4 class="card-title">Что такое хэш и с чем его едят</h4>
            <p class="card-text">
                Хэш (hash) или контрольная сумма - это строка фиксированной длины, полученная в результате выполнения
                определенного, необратимого алгоритма (алгоритмы бывают разные, <code>md5</code> - один из них) над
                исходной строкой. Захешировав строку,мы получаем хэш, однозначно (почти) идентифицирующий исходную строку.
                При этом получить эту самую исходную строку практически невозможно.
            <p>
                Например, в результате хэширования строки <code>test1234567890</code> алгоритмом sha256, мы получим
                вот такой хэш: <code>7afecb08fb6b7bab4bb45c755d857ae71d90ed2b37899f29b8bbc6ae758a5175</code>.
                Мы получили что-то вроде сигнатуры, однозначно идентифицирующей исходную строку,
                при этом, вычислить саму исходную строку, зная хэш, <strong>крайне</strong> затруднительно или
                невозможно вовсе.<br>Контрольная сумма берется от строки, построенной из параметров запроса
                и секретного API - ключа, который хранится у вас на сайте, а также здесь, в магазине. Изменить
                его вы можете в разделе <strong>Администрирование>Управление>API</strong>. Там же имеется возможность
                выбрать алгоритм расчета контрольной суммы, а также, установить разделитель параметров.
            </p>
            <h4 class="card-title">Составляем запрос</h4>
            <p>
                И так, представьте, что пользователь уже вошел в свой аккаунтам на вашем сайте. И вам необходимо
                выдать пользователю ссылку на магазин, работающей на L-Shop, да так, чтобы этот пользователь,
                перейдя по ссылке, автоматически вошел в свой акаунт магазина и был готов совершать покупки.
                Давайте составим запрос для этого дела.<br>
                Начало ссылки для API авторизации выглядит так: <code>http://example.ru/api/signin?</code><br>
                Далее, следует передать параметр <code>username</code>, начением которого является имя пользователя
                (логин), которого требуется авторизовать.<br>
                Теперь составим контрольнуя сумму нашего запроса, для этого, возьмем API - ключ, припишем к нему
                разделитель, и в конце концов, припишем к этому имя пользователя.<br>
                Например, если моим секреным ключем является строка <code>kR6rrpgUO2Hn3*aI?1~vHwvd~KcVUFIB</code>,
                разделителем - символ двоеточия (<code>:</code>), а имя пользователя - <code>d3lph1</code>,
                то в результате, мы получим вот такую строку: <code>kR6rrpgUO2Hn3*aI?1~vHwvd~KcVUFIB:d3lph1</code>.
                Возьмем хэш от этой строки тем алгоритмом, который вы указали в настройках API.<br>
                Например, если я использую алгоритм sha256, то результат будет таким:
                <code>22cc462bda02453b1bc7661a2045445756a9bffeb479d7668c3e03e7e4764da0</code>.<br>
                Припишем этот хэш, значением параметра <code>hash</code> нашего запроса.<br>
                В конце концов, получаем вот такой url:
                <code>http://example.ru/api/signin?username=d3lph1&hash=22cc462bda02453b1bc7661a2045445756a9bffeb479d7668c3e03e7e4764da0</code>
            </p>
            <h3 class="card-title">Реализация</h3>
            <p>
                Конечно же, потребуется, так сказать, воплотить этот алгоритм в жизнь. Я написал для этого PHP - код,
                который составляет ссылку для авторизации пользователя.
            <pre class="php">
                <code>
/**
 * Разумеется, все эти данные вы будете вытаскивать из БД, конфига и тд.
 */
$key = 'kR6rrpgUO2Hn3*aI?1~vHwvd~KcVUFIB:d3lph1';   // Секретный ключ. Должен соответствовать секретному ключу в магазине.
$delimiter = ':';   // Разделитель параметров
$algo = 'sha256';   // Алгоритм расчета контрольной суммы
$url = 'http://l-shop.ru/api/signin?';  // Адрес API-авторизации
$username = 'D3lph1';   // Имя пользователя, которого необходимо авторизовать
$str = sprintf('%s%s%s', $key, $delimiter, $username);
$hash = hash($algo, $str);
$paramsStr = http_build_query([
    'username' => $username,
    'hash' => $hash
]);
$link = $url . $paramStr;   // Ссылка по переходу по которой, пользователь будет авторизован
                </code>
            </pre>
            </p>

            <h4 class="card-title">Ответ</h4>
            <p>
                В качетсве ответа, сервер отдаст JSON строку. Распарсив JSON, получим массив. Он содержит 2 обязательных элемента:
                status - краткое описание результата запроса на английском и <code>code</code> - число, однозначно идентифицирующее результат
                запроса. Запрос на аутентификацию пользователя не отдает ответ, так как выполняется непосредственно самим пользователем.
                У разных запросов имеются разные вариации ответа, но некоторые из них схожи:
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Code</th>
                                <th>Описание</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>option disabled</td>
                                <td>-1</td>
                                <td>Данная функция отключена или недоступна</td>
                            </tr>
                            <tr>
                                <td>invalid hash</td>
                                <td>-2</td>
                                <td>Неверный хэш</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <p>

            <h3 class="card-title">Регистрация пользователей</h3>
            <p>
                Для вашего удобства, мы добавили возможность API-регистрации. Что это значит? Вы отправляете запрос к L-Shop'у,
                и в магазине создается пользователь с теми данными, которые вы передали в запросе. Делать это удобно, когда,
                пользователь регистрируется на вашем сайте. Таким образом, вы зарегистрируете его и в магазине тоже.
            </p>
            <h4 class="card-title">Url (без параметров):</h4>
            <p>
                <code><b>http://example.ru/api/signup</b></code>
            </p>
            <h4 class="card-title">Параметры</h4>
            <p>
                Запрос на регистрацию должен содержать следующие параметры: имя пользователя (<code>username</code>),
                адрес электронной почты (<code>email</code>), пароль (<code>password</code>), баланс (<code>balance</code>),
                нужно ли активировать пользователя мгновенно (<code>force_activate</code>) и, наконец, является ли он
                администратором (<code>admin</code>).<br>
                В итоге, получаем вот такую схему строки, от которой необходимо взять хэш (разделителями, в данном случае
                являются символы двоеточия (<code>:</code>)): <code>key:username:email:password:balance:force_activate:admin</code>.
            </p>
            <h4 class="card-title">Составление запроса</h4>
            <p>
                Предположим, что мы хотим зарегистрировать пользователя <code>GeraltOfRivia</code> с адресом электронной почты
                <code>GeraltOfRivia@sapkovskiy.pl</code>, паролем <code>LambertLambert</code>, выдать ему 100 рублей на счет и
                мгновенно активировать. В этом случае строка будет такой (Секретный ключ - <code>kR6rrpgUO2Hn3*aI?1~vHwvd~KcVUFIB</code>,
                разделитель - <code>:</code>): <code>kR6rrpgUO2Hn3*aI?1~vHwvd~KcVUFIB:GeraltOfRivia:GeraltOfRivia@sapkovskiy.pl:LambertLambert:100:1:0</code>.
                Так как значения таких параметром, как force_activate и admin являются логическими, то мы передаем в них либо ноль(<code>0</code>),
                либо - единицу(<code>1</code>). Если взять хэш от этой строки алгоритмом sha256, то получим нечто следующее:
                <code>592c19f8b889e33b9693f14957b341502728e5d3d281a53c3f032e93449bd154</code>.
                <p>
                    Таким образом, составим запрос:<br>
                    <code>
                        http://example.ru/api/signup?username=GeraltOfRivia&email=GeraltOfRivia@sapkovskiy.pl&<br>
                        password=LambertLambert&balance=100&force_activate=1&admin=0&<br>
                        hash=592c19f8b889e33b9693f14957b341502728e5d3d281a53c3f032e93449bd154
                    </code>
                </p>
            </p>
            <h4 class="card-title">Ответ</h4>
            <p>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Code</th>
                                <th>Описание</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>username already exists</td>
                                <td>1</td>
                                <td>Пользователь с таким логином уже существует</td>
                            </tr>
                            <tr>
                                <td>email already exists</td>
                                <td>2</td>
                                <td>Пользователь с таким адресом электронной почты уже существует</td>
                            </tr>
                            <tr>
                                <td>unable to create user</td>
                                <td>3</td>
                                <td>Не удалось создать пользователя по техническим причинам</td>
                            </tr>
                            <tr>
                                <td>success</td>
                                <td>0</td>
                                <td>Пользователь успешно зарегистрирован</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <p>
            </p>
        </div>
    </div>
@endsection
