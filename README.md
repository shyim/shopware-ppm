# Shopware with [PHP Process Manager](https://github.com/php-pm/php-pm)

# This project is very experimental!

### Why?
* [Performance boost up to 15x (due no bootstrap)](https://github.com/php-pm/php-pm#features)


### Known problems

* Shopware has some memory leaks


### How to setup

* Checkout this repository
* Install shopware (composer project setup)
* [Setup PHP-PM](https://github.com/php-pm/php-pm/wiki/Use-without-Docker)
* Start ppm ``./vendor/bin/ppm start --bootstrap=Shyim\\PPM\\Bootstraps\\Shopware --static-directory=. --port 80``

### Benchmark

Shopware 5.5.2 (without HttpCache, 16 workers)
```
shyim@yuuuuuukiiiiiiiiiiiiiiiii ~/C/shopware-pm> ab -n 10000 -c 20  http://ppm/
This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking ppm (be patient)
Completed 1000 requests
Completed 2000 requests
Completed 3000 requests
Completed 4000 requests
Completed 5000 requests
Completed 6000 requests
Completed 7000 requests
Completed 8000 requests
Completed 9000 requests
Completed 10000 requests
Finished 10000 requests


Server Software:        
Server Hostname:        ppm
Server Port:            80

Document Path:          /
Document Length:        28773 bytes

Concurrency Level:      20
Time taken for tests:   24.527 seconds
Complete requests:      10000
Failed requests:        0
Total transferred:      291079970 bytes
HTML transferred:       287730000 bytes
Requests per second:    407.71 [#/sec] (mean)
Time per request:       49.054 [ms] (mean)
Time per request:       2.453 [ms] (mean, across all concurrent requests)
Transfer rate:          11589.61 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.0      0       1
Processing:    23   49  15.4     46     288
Waiting:       23   49  15.4     46     288
Total:         23   49  15.4     46     288

Percentage of the requests served within a certain time (ms)
  50%     46
  66%     50
  75%     52
  80%     54
  90%     61
  95%     68
  98%     84
  99%    107
 100%    288 (longest request)

```
