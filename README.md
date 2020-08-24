# netspeakapi-php

[EN] A PHP class for interacting with the net-speak.pl (http://www.net-speak.pl) API. The class can handle both the Shop API as well as the Account and TeamSpeak server endpoints.<br/>
[PL] Klasa PHP służąca do interakcji z API firmy net-speak.pl (http://www.net-speak.pl). Klasa jest w stanie obsłużyć zapytania API Sklepowego, API Konta oraz endpointy związane ze slotowymi serwerami TeamSpeak.

## Getting Started

[EN] All you need to do is include the netspeakapi.class.php file anywhere you would like to access the class, as show below.
[PL] Wystarczy include'ować plik netspeakapi.class.php gdziekolwiek gdzie chcemy skorzystać z klasy, tak jak pokazano poniżej.

```
include("netspeakapi.class.php");
```

### Prerequisites

[EN] You must be running a modern version of PHP. The class is developed and tested on PHP 7.2 but should work on PHP 7.0 and above. Anything below PHP 7.0 is NOT supported.<br/><br/>
[PL] Zalecane jest korzystanie z nowoczesnej wersji PHP. Klasa tworzona i testowana jest na wersji PHP 7.2 lecz powinna działać na każdej wersji PHP powyżej 7.0. Wersje PHP poniżej 7.0 NIE SĄ wspierane.

### Examples
### English

[EN] To initialise the class, the following example could be used -

```
$test = new netspeakapi();
```

As aforementioned, the class can handle all of the API's offered by net-speak.pl. HOWEVER, net-speak.pl's API uses two different API keys.<br/><br/>

1 key is for the Shop and Customer Account API (a key is available from http://www.net-speak.pl/loged/panel/clientarea/set.php?cmd=api after you login)<br/><br/>
The other key is for interacting with TeamSpeak servers sold by slots (a key is available from http://www.net-speak.pl/loged/panel/clientarea/set.php?cmd=api_ts3 once you are logged in and have purchased a slotted server)<br/><br/>

To be able to use the Shop and Customer API's you will need to provide the instance of the class with the appriopriate key, using:

```
$test->setAPIKey("YOUR_KEY");
```

To use the TeamSpeak server endpoints, you will need to provide the instance of the class with the appriopriate key, using:

```
$test->setTeamSpeakAPIKey("YOUR_KEY");
```

Never share these keys with anyone, as they give total control over your account and servers.

### Polski

[PL] Aby rozpocząć korzystanie z klasy, można skorzystać z podanego poniżej przykładu -

```
$test = new netspeakapi();
```

Jak wcześniej wspominano, klasa obsługuje wszystkie API oferowane przez net-speak.pl. JEDNAKŻE, API net-speak.pl używa dwóch różnych kluczy.<br/><br/>

1 klucz służy do API Sklepu i Konta Użytkownika (dostępny jest na stronie http://www.net-speak.pl/loged/panel/clientarea/set.php?cmd=api po zalogowaniu do panelu klienta)<br/><br/>
Drugi klucz służy do API Serwera TeamSpeak - tylko i wyłącznie do serwerów slotowych (ten klucz jest widoczny na stronie http://www.net-speak.pl/loged/panel/clientarea/set.php?cmd=api_ts3 również po zalogowaniu do panelu klienta)<br/><br/>

Aby używać endpointów Sklepowych oraz Konta Klienta, musisz podać instancji klasy klucz API, używając:

```
$test->setAPIKey("YOUR_KEY");
```

Aby używać endpointy serwerów TeamSpeak, musisz podać instancji klucz API TeamSpeak 3, używając:

```
$test->setTeamSpeakAPIKey("YOUR_KEY");
```

Nigdy nie dziel się tymi kluczami, gdyż dają one pełną kontrolę nad Twoim kontem i usługami.

## Coding Style

[EN] The class has been written to fully copy the response style of net-speak's API, therefore you should not need to change your implementations.<br/><br/>
[PL] Klasa została napisania w taki sposób, aby w pełni odwzorowywała styl odpowiedzi na zapytania który używa API net-speak, więc nie powinno się zmieniać swojego kodu poza samymi zapytaniami do klasy.

## Contributing

[EN] Please submit issue requests for any features or changes, to be discussed prior to them being implemented. Once they are fully discussed, explained and authorised, you can submit PR's with the implementation.<br/><br/>
[PL] Proszę stworzyć wątek w zakładce "issues" aby proponować zmiany lub dodatki. Każda zmiana lub dodatek bmuszą być najpierw przedyskutowane, wyjaśnione oraz zaakceptowane - wtedy dopiero proszę tworzyć PR z nowym kodem zródłowym.

## Versioning

[EN] The class has a pretty loose versioning system. Any major changes or updates are signalled by changing the minor number (e.g 1.x.0), with smaller changes or additions shown by incrementing the build number (e.g. 1.0.x).<br/><br/>
[PL] Klasa posiada dość luzny system wersji. Wszystkie poważne zmiany lub aktualizacje są sygnalizowane zmianą numeru pomocniczego (np. 1.x.0), a mniejsze zmiany lub aktualizacje są przekazywane za pośrednictwem numeru kompilacji (np. 1.0.x)

## Authors

* **Adam Szczygieł** - [Pantoflarz](https://github.com/Pantoflarz)


## License

This project is licensed under the GPL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details
