# di

Контейнер с инжектором зависимостей

### Инжектор это:

1. Привязка к Psr/ContainerInterface и Psr/NotFoundException
1. Автоматическая подстановка аргументов в конструктор класса или в вызов функции
1. Универсальная фабрика. bind() регистрирует класс/замыкание, которое будет вызвано каждый раз при обращении по ключу. С помощью интерфейсов или строк можно регистрировать сколько угодно фабрик одного и того же класса
1. Единое хранилище для паттернов Одиночка, вместо хранения одиночек внутри самих себя
1. Возможность передать в вызываемый метод аргументы через специальный синтаксис без учета порядка их следования
1. Предзагрузка данных перед использованием класса. Используя методы boot() в провайдерах можно загружать дополнительные данные, когда этот обьект требуется
1. Копирование файлов из модулей в ядро проекта через те же самые провайдеры и методы define() и sync(). Вешаем файлы или целые папки на имена, а затем указываем в каких путях эти имена должны оказаться и перед вызовом boot() файлы скопируются в нужные места
1. Возможность отложенного создания объекта используя интерфейс DelegateInterface, то есть предзагрузка и копирование конфигов произойдет не при старте программы, а в момент, когда на объекте делегата будет вызван какой-то метод

### Виды провайдеров:

1. Provider - обычный файл настройки модуля, используя метод register() удобно задавать бинды, фабрики и одиночки
2. BootableProvider - поддерживает метод boot(), который будет вызван, когда приложение сделает $di->boot(), а в случае, если уже был сделан - то немедленно после регистрации провайдера
3. DeferableProvider - изменяет поведение метода boot() таким образом, что он вызывается в тот момент, когда инжектор пытается создать класс. Для указания, на какие именно классы он сработает используется метод provides()

### Исключения

* Если бинд не был зарегистрирован, но вы попытались его получить, будет выброшено `Gzhegow\Di\Exceptions\Runtime\Domain\NotFoundException`
* Если при указании зависимостей была допущена рекурсия, будет выброшено `Gzhegow\Di\Exceptions\Runtime\Domain\AutowireLoopException`
* Если при автоматическом инжектировании параметр невозможно предсказать, будет выброшено `Gzhegow\Di\Exceptions\Runtime\Domain\AutowireException`
* При попытке зарегистрировать бинд повторно без использования replace()/rebind() будет выброшено `Gzhegow\Di\Exceptions\Runtime\OverflowException`

### Основные возможности:

```
// создать с помощью инжектора
public function new($id, ...$arguments);

// создать с помощью инжектора, учитывая что бинд может быть функцией-замыканием (факторкой)
public function create($id, ...$arguments);

// получить из контейнера или создать, если не найдено
public function get($id);

// проверить наличие привязки в контейнере по строке
public function hasBind($id);

// проверить наличие значения в контейнере по строке
public function hasItem($id);

// проверить наличие в контейнере по строке
// проверит сначала бинд, потом значение, и в конце является ли загруженным ч-з autoload классом
public function has($id);

// установить/заменить в контейнере на конкретное значение
public function set(string $id, $item);
public function replace(string $id, $item);

// установить/заменить в контейнере привязку на создание при следующем вызове get()/create()
public function bind(string $id, $bind, bool $shared = false);
public function rebind(string $id, $bind, bool $shared = false);
public function bindShared(string $id, $bind);
public function rebindShared(string $id, $bind);
// пример:
public function bind(HelloInterface::class, function (\Gzhegow\Di\Node\Node $parent) {
  return $parent->get(Hello::class);
});

// вызвать функцию, заполнив её аргументы из контейнера
public function handle($func, ...$arguments);
public function call($newthis, $func, ...$arguments);

// зарегистрировать декоратор (после создания объекта его можно обернуть в другой) 
public function extend(string $id, $func);

// добавить провайдер в список загрузки
public function registerProvider($provider);

// запустить код инициализации провайдеров
public function boot();
```

### Примеры:

```
<?php

$di = (new DiFactory())->getDi();

$di->bind(HelloInterface::class, Hello::class);
$hello1 = $di->get(HelloInterface::class);
$hello2 = $di->get(HelloInterface::class);

$di->bindShared(HelloSharedInterface::class, Hello::class);
$hello31 = $di->get(HelloSharedInterface::class);
$hello32 = $di->get(HelloSharedInterface::class);
// $hello31 === $hello32 // true
```

```
<?php

$di = (new DiFactory())->getDi();

$di->bind(HelloInterface::class, function (\Gzhegow\Di\Node\Node $parent) {
  return $parent->get(Hello::class);
});
```

```
<?php

$di = (new DiFactory())->getDi();

$di->set('any', 123);
$di->set(HelloInterface::class, 123);

print_r($di->get('any')); // 123
print_r($di->get(HelloInterface::class)); // 123
```

```
<?php

$di = (new DiFactory())->getDi();

$di->registerProvider(HelloProvider::class);

$hello = $di->get(HelloInterface::class);
```

```
<?php

$di = (new DiFactory())->getDi();

$di->bind(HelloInterface::class, Hello::class);

$di->extend(HelloInterface::class, function (HelloInterface $hello, \Gzhegow\Di\Node\Node $parent) {
  return $parent->create(HelloDecorator::class, $hello);
});
```
